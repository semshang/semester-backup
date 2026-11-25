<?php
require_once 'auth.php';
ensureLoggedIn();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'archive';

if ($action === 'archive') {
    $stmt = $pdo->prepare('UPDATE job SET archived = 1 WHERE id = :id');
    $stmt->execute(['id' => $id]);
} elseif ($action === 'restore') {
    $stmt = $pdo->prepare('UPDATE job SET archived = 0 WHERE id = :id');
    $stmt->execute(['id' => $id]);
} elseif ($action === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM job WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

header('Location: jobs.php');
exit;



