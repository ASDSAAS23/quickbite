<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function qb_base_url(): string
{
    static $base = null;

    if ($base !== null) {
        return $base;
    }

    // Production (Render): app is served from document root
    $dbUrl = getenv('DATABASE_URL');
    if (empty($dbUrl)) {
        $dbUrl = $_SERVER['DATABASE_URL'] ?? ($_ENV['DATABASE_URL'] ?? '');
    }
    $isProduction = $dbUrl !== '' || getenv('RENDER') !== false || isset($_SERVER['RENDER']);
    if ($isProduction) {
        $base = '';
        return $base;
    }

    // Local dev (XAMPP): detect relative path from document root
    $projectPath = realpath(__DIR__ . '/..');
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';

    if ($documentRoot && $projectPath && str_starts_with($projectPath, $documentRoot)) {
        $base = str_replace('\\', '/', substr($projectPath, strlen($documentRoot)));
        $base = $base === '' ? '' : $base;
        return $base;
    }

    $base = '/quickbite';
    return $base;
}

function qb_url(string $path = ''): string
{
    $base = rtrim(qb_base_url(), '/');
    $path = ltrim($path, '/');

    if ($path === '') {
        return $base ?: '/';
    }

    return $base . '/' . $path;
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function current_page_name(): string
{
    return basename($_SERVER['PHP_SELF'] ?? '');
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): string
{
    if (!isset($_SESSION['flash'][$key])) {
        return '';
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function cart_item_count(mysqli $conn): int
{
    if (!is_logged_in()) {
        return 0;
    }

    $userId = (int) $_SESSION['user_id'];
    $sql = "SELECT COALESCE(SUM(quantity), 0) AS total_items FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return (int) ($row['total_items'] ?? 0);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header("Location: " . qb_url('login.php'));
        exit();
    }
}

function require_admin(): void
{
    if (!is_logged_in() || !is_admin()) {
        header("Location: " . qb_url('login.php'));
        exit();
    }
}
