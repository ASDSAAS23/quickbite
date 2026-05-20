<?php
/**
 * mysqli-over-PDO compatibility layer for PostgreSQL / Supabase.
 *
 * This file provides wrapper classes that emulate the subset of the mysqli
 * API used throughout this project, so that existing code referencing
 * $conn->prepare(), bind_param(), get_result(), fetch_assoc(), num_rows,
 * insert_id, etc. continues to work without modification.
 *
 * IMPORTANT: Only the methods actually used in this project are implemented.
 */

class MysqliCompatResult
{
    private array $rows;
    public int $num_rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc(): ?array
    {
        $row = current($this->rows);
        if ($row === false) {
            return null;
        }
        next($this->rows);
        return $row;
    }
}

class MysqliCompatStmt
{
    private \PDOStatement $pdoStmt;
    private \PDO $pdo;
    private array $params = [];
    private ?MysqliCompatResult $resultCache = null;
    public int $num_rows = 0;
    public int $insert_id = 0;

    public function __construct(\PDO $pdo, \PDOStatement $pdoStmt)
    {
        $this->pdo = $pdo;
        $this->pdoStmt = $pdoStmt;
    }

    /**
     * Emulates mysqli_stmt::bind_param().
     * Type string is accepted but ignored — PDO handles types automatically.
     */
    public function bind_param(string $types, &...$vars): bool
    {
        $this->params = [];
        foreach ($vars as $k => &$v) {
            $this->params[$k + 1] = &$v;
        }
        return true;
    }

    public function execute(): bool
    {
        foreach ($this->params as $index => &$value) {
            $this->pdoStmt->bindValue($index, $value);
        }

        $result = $this->pdoStmt->execute();

        // Capture insert_id if applicable
        try {
            $lastId = $this->pdo->lastInsertId();
            $this->insert_id = $lastId ? (int) $lastId : 0;
        } catch (\PDOException $e) {
            $this->insert_id = 0;
        }

        return $result;
    }

    public function get_result(): MysqliCompatResult
    {
        $rows = $this->pdoStmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->resultCache = new MysqliCompatResult($rows);
        $this->num_rows = $this->resultCache->num_rows;
        return $this->resultCache;
    }

    public function store_result(): bool
    {
        // PDO fetches all rows anyway; emulate by caching
        if ($this->resultCache === null) {
            $this->get_result();
        }
        return true;
    }
}

class MysqliCompatConnection
{
    private \PDO $pdo;
    public ?string $connect_error = null;
    public int $insert_id = 0;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function prepare(string $sql): MysqliCompatStmt|false
    {
        // Convert MySQL-style ? placeholders — PDO already uses ? so no conversion needed.
        // However, we need to handle MySQL ENUM references in INSERT statements.
        // PostgreSQL doesn't support ENUM inline; our schema uses VARCHAR/check constraints.
        try {
            $stmt = $this->pdo->prepare($sql);
            return new MysqliCompatStmt($this->pdo, $stmt);
        } catch (\PDOException $e) {
            error_log("PDO prepare error: " . $e->getMessage() . " — SQL: " . $sql);
            return false;
        }
    }

    public function query(string $sql): MysqliCompatResult|false
    {
        try {
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return new MysqliCompatResult($rows);
        } catch (\PDOException $e) {
            error_log("PDO query error: " . $e->getMessage() . " — SQL: " . $sql);
            return false;
        }
    }

    public function set_charset(string $charset): bool
    {
        // PostgreSQL handles charset at connection level; this is a no-op.
        return true;
    }

    public function real_escape_string(string $value): string
    {
        // Use PDO quote and strip surrounding quotes
        $quoted = $this->pdo->quote($value);
        return substr($quoted, 1, -1);
    }

    public function close(): void
    {
        // PDO connections close when set to null; no-op.
    }

    /**
     * Proxy for insert_id — updated after each query/prepare-execute.
     */
    public function __get(string $name)
    {
        if ($name === 'insert_id') {
            try {
                return (int) $this->pdo->lastInsertId();
            } catch (\PDOException $e) {
                return 0;
            }
        }
        return null;
    }
}
