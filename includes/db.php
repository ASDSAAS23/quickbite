<?php
/**
 * Database connection — auto-detects environment.
 *
 * Production (Render + Supabase):  Uses PDO with PostgreSQL via DATABASE_URL env var.
 * Local (XAMPP):                   Falls back to classic mysqli with MySQL.
 *
 * The mysqli compatibility layer lets the rest of the codebase use the same
 * $conn->prepare / bind_param / get_result / fetch_assoc API everywhere.
 */

$databaseUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? '');

if ($databaseUrl !== '') {
    // ── Production: Supabase PostgreSQL via PDO ──────────────────────────
    require_once __DIR__ . '/mysqli_compat.php';

    $parts = parse_url($databaseUrl);

    $host   = $parts['host']   ?? 'localhost';
    $port   = $parts['port']   ?? 5432;
    $user   = $parts['user']   ?? '';
    $pass   = $parts['pass']   ?? '';
    $dbname = ltrim($parts['path'] ?? '', '/');

    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};sslmode=require";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $conn = new MysqliCompatConnection($pdo);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
} else {
    // ── Local development: XAMPP MySQL via mysqli ────────────────────────
    $host     = "localhost";
    $username = "root";
    $password = "";
    $database = "quickbite_db";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
}
