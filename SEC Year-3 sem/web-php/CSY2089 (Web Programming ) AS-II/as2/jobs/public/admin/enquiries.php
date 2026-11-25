<?php
require_once 'auth.php';
ensureLoggedIn();

if (!isAdmin()) {
    header('Location: index.php');
    exit;
}

$message = '';
$messageType = '';

if (isset($_POST['update_enquiry'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'new';
    $assignedTo = isset($_POST['assigned_to']) && $_POST['assigned_to'] !== '' ? (int) $_POST['assigned_to'] : null;
    $stmt = $pdo->prepare('UPDATE enquiries SET status = :status, assigned_to = :assigned_to WHERE id = :id');
    $stmt->execute(['status' => $status, 'assigned_to' => $assignedTo, 'id' => $id]);
    $message = 'Enquiry updated';
    $messageType = 'success';
}

$enquiries = $pdo->query('SELECT e.*, u.username AS assigned_username FROM enquiries e LEFT JOIN users u ON e.assigned_to = u.id ORDER BY CASE WHEN e.status = "new" THEN 0 ELSE 1 END, e.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
$staff = $pdo->query('SELECT id, username FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/styles.css"/>
    <title>Jo's Jobs - Enquiries</title>
</head>
<body>
<header>
    <section>
        <aside>
            <h3>Office Hours:</h3>
            <p>Mon-Fri: 09:00-17:30</p>
            <p>Sat: 09:00-17:00</p>
            <p>Sun: Closed</p>
        </aside>
        <h1>Jo's Jobs</h1>
    </section>
</header>
<nav>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="index.php">Admin Home</a></li>
        <li><a href="jobs.php">Jobs</a></li>
        <li><a href="categories.php">Categories</a></li>
    </ul>
</nav>
<img src="/images/randombanner.php"/>
<main class="sidebar">
<section class="left">
    <ul>
        <li><a href="jobs.php">Jobs</a></li>
        <li><a href="categories.php">Categories</a></li>
        <li><a href="users.php">Manage Users</a></li>
        <li><a href="enquiries.php">Enquiries</a></li>
        <li><a href="index.php?logout=1">Log Out</a></li>
    </ul>
</section>
<section class="right">
    <h2>Enquiries</h2>
    <?php if (!empty($message)): ?>
        <div style="padding: 10px; margin-bottom: 15px; border-radius: 3px; <?= $messageType === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <?php foreach ($enquiries as $enquiry): ?>
        <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
            <h3><?= htmlspecialchars($enquiry['first_name'] . ' ' . $enquiry['surname']) ?></h3>
            <p><strong>Email:</strong> <?= htmlspecialchars($enquiry['email']) ?> | <strong>Telephone:</strong> <?= htmlspecialchars($enquiry['telephone'] ?? 'N/A') ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars($enquiry['created_at']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($enquiry['status']) ?></p>
            <p><strong>Assigned to:</strong> <?= htmlspecialchars($enquiry['assigned_username'] ?? 'Unassigned') ?></p>
            <p><?= nl2br(htmlspecialchars($enquiry['enquiry'])) ?></p>
            <form method="post" action="enquiries.php" style="margin-top: 10px;">
                <input type="hidden" name="id" value="<?= $enquiry['id'] ?>" />
                <label>Status</label>
                <select name="status">
                    <option value="new" <?= $enquiry['status'] === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="in_progress" <?= $enquiry['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="complete" <?= $enquiry['status'] === 'complete' ? 'selected' : '' ?>>Complete</option>
                </select>
                <label style="margin-left: 10px;">Assigned to</label>
                <select name="assigned_to">
                    <option value="">Unassigned</option>
                    <?php foreach ($staff as $member): ?>
                        <option value="<?= $member['id'] ?>" <?= (int) $enquiry['assigned_to'] === (int) $member['id'] ? 'selected' : '' ?>><?= htmlspecialchars($member['username']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_enquiry" style="margin-left: 10px;">Save</button>
            </form>
        </div>
    <?php endforeach; ?>
</section>
</main>
<footer>
    &copy; Jo's Jobs <?= date('Y') ?>
</footer>
</body>
</html>
