<?php
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
session_start();

// ─── LOCAL (XAMPP) credentials — comment out before pushing ───────────────
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'gymdb');

// ─── HOSTINGER credentials — uncomment before pushing ─────────────────────
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'u535161146_gymapp');
define('DB_PASS', getenv('DB_PASS') ?: '@Nekki161011');
define('DB_NAME', getenv('DB_NAME') ?: 'u535161146_gymdb');

// Establish database connection.
try
{
$dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS);
}
catch (PDOException $e)
{
error_log("Database connection failed: " . $e->getMessage());
exit("A database error occurred. Please try again later.");
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// RBAC
define('RBAC_PERMISSIONS', [
    'super_admin' => ['manage_packages', 'manage_bookings', 'approve_users', 'view_reports', 'manage_admins'],
    'staff'       => ['manage_bookings'],
]);

function admin_can(string $permission): bool {
    $role = $_SESSION['adminrole'] ?? '';
    $perms = RBAC_PERMISSIONS[$role] ?? [];
    return in_array($permission, $perms, true);
}

function require_permission(string $permission): void {
    if (!admin_can($permission)) {
        $_SESSION['flash_error'] = 'Access denied. You do not have permission to view that page.';
        header('Location: index.php');
        exit();
    }
}
?>