<?php
// auth.php - include at the TOP of every protected page:  require __DIR__ . '/auth.php';
require_once __DIR__ . '/session_boot.php';
require_once __DIR__ . '/db.php';
start_secure_session();

const IDLE_LIMIT = 1800;   // log out after 30 minutes of no activity

// BASE_URL = the web path of this project, e.g. "/wst2_auth".
// Redirecting with BASE_URL . '/login.php' works from every subfolder.
if (!defined('BASE_URL')) {
    $docRoot = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT']));
    $appDir  = str_replace('\\', '/', __DIR__);
    define('BASE_URL', stripos($appDir, $docRoot) === 0 ? substr($appDir, strlen($docRoot)) : '');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . $path);
    exit;                                    // stop the script right after every redirect
}

// Must be logged in, or go to the login page.
function require_login(): void
{
    global $conn;

    if (empty($_SESSION['user_id'])) {
        redirect('login.php');
    }

    // Idle timeout
    if (time() - ($_SESSION['last_active'] ?? 0) > IDLE_LIMIT) {
        session_unset();
        session_destroy();
        redirect('login.php');
    }
    $_SESSION['last_active'] = time();

    // Read the role from the database on every request, so a changed or deleted
    // account takes effect immediately.
    $stmt = $conn->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row) {
        session_unset();
        session_destroy();
        redirect('login.php');
    }
    $_SESSION['role'] = $row['role'];
}

// Must be logged in AND have one of the allowed roles (deny by default).
function require_role(string ...$roles): void
{
    require_login();
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        require __DIR__ . '/403.php';        // friendly 403 page
        exit;
    }
}

// Used by includes/header.php to show menu links by role.
function has_role(string ...$roles): bool
{
    return in_array($_SESSION['role'] ?? '', $roles, true);
}
