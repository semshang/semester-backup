<?php
// Ensure output buffering and session start before any HTML output.
if (session_status() === PHP_SESSION_NONE) {
    ob_start();
    session_start();
}

$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

function isLoggedIn(): bool
{
    return !empty($_SESSION['loggedin']) && !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

function isStaff(): bool
{
    return isLoggedIn() && in_array($_SESSION['user_role'] ?? '', ['admin', 'staff'], true);
}

function isClient(): bool
{
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'client';
}

function ensureLoggedIn(): void
{
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function ensureAdmin(): void
{
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function ensureStaffOrClient(): void
{
    if (!isStaff() && !isClient()) {
        header('Location: index.php');
        exit;
    }
}

function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentUserRole(): string
{
    return $_SESSION['user_role'] ?? '';
}

function userCanManageJob(array $job): bool
{
    return isAdmin() || ((int) ($job['ownerId'] ?? 0) === currentUserId());
}

// Ensure the database has the required schema for client ownership and roles.
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `category` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(45) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `job` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `title` varchar(45) DEFAULT NULL,
        `description` longblob,
        `salary` varchar(45) DEFAULT NULL,
        `closingDate` date DEFAULT NULL,
        `categoryId` int(11) DEFAULT NULL,
        `location` varchar(255) DEFAULT NULL,
        `receivedDate` datetime DEFAULT NULL,
        `archived` tinyint(1) NOT NULL DEFAULT 0,
        `ownerId` int(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `applicants` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(45) DEFAULT NULL,
        `email` varchar(45) DEFAULT NULL,
        `details` longblob,
        `jobId` int(11) DEFAULT NULL,
        `cv` varchar(255) DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `username` varchar(45) NOT NULL,
        `password` varchar(255) NOT NULL,
        `email` varchar(255) DEFAULT NULL,
        `role` varchar(20) NOT NULL DEFAULT 'staff',
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `enquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `first_name` varchar(100) NOT NULL,
        `surname` varchar(100) NOT NULL,
        `email` varchar(255) NOT NULL,
        `telephone` varchar(50) DEFAULT NULL,
        `enquiry` text NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'new',
        `assigned_to` int(11) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $column = $pdo->query("SHOW COLUMNS FROM `job` LIKE 'ownerId'")->fetch();
    if (!$column) {
        $pdo->exec("ALTER TABLE `job` ADD COLUMN `ownerId` int(11) NOT NULL DEFAULT 1");
    }

    $column = $pdo->query("SHOW COLUMNS FROM `job` LIKE 'archived'")->fetch();
    if (!$column) {
        $pdo->exec("ALTER TABLE `job` ADD COLUMN `archived` tinyint(1) NOT NULL DEFAULT 0");
    }

    $column = $pdo->query("SHOW COLUMNS FROM `job` LIKE 'receivedDate'")->fetch();
    if (!$column) {
        $pdo->exec("ALTER TABLE `job` ADD COLUMN `receivedDate` datetime NULL");
    }

    $column = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'role'")->fetch();
    if (!$column) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `role` varchar(20) NOT NULL DEFAULT 'staff'");
    }

    $adminUser = $pdo->query("SELECT id, password FROM `users` WHERE username = 'admin'")->fetch();
    if (!$adminUser) {
        $pdo->prepare("INSERT INTO `users` (username, password, email, role) VALUES ('admin', :password, 'admin@jojobs.com', 'admin')")
            ->execute(['password' => password_hash('letmein', PASSWORD_BCRYPT)]);
    } elseif (($adminUser['password'] ?? '') === 'YourHashedPasswordHere' || ($adminUser['password'] ?? '') === '') {
        $pdo->prepare("UPDATE `users` SET password = :password, role = 'admin' WHERE id = :id")
            ->execute(['password' => password_hash('letmein', PASSWORD_BCRYPT), 'id' => $adminUser['id']]);
    }
} catch (PDOException $e) {
    // If the schema migration cannot run, leave it to the import SQL to provide the columns.
}
