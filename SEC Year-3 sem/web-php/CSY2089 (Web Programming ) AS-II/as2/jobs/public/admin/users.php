<?php
require_once 'auth.php';
ensureAdmin();

$message = '';
$messageType = ''; // 'success' or 'error'

// Handle add user
if (isset($_POST['add_user'])) {
    $username = $_POST['new_username'] ?? '';
    $password = $_POST['new_password'] ?? '';
    $email = $_POST['new_email'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Check if username already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        if ($stmt->fetch()) {
            $message = 'Username already exists';
            $messageType = 'error';
        } else {
            // Hash password and add user
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $role = $_POST['role'] ?? 'staff';
            $stmt = $pdo->prepare('INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)');
            try {
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashedPassword,
                    'email' => $email,
                    'role' => $role
                ]);
                $message = 'User added successfully';
                $messageType = 'success';
            } catch (Exception $e) {
                $message = 'Error adding user: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    } else {
        $message = 'Username and password are required';
        $messageType = 'error';
    }
}

// Handle delete user
if (isset($_GET['delete_id'])) {
    $deleteId = (int) $_GET['delete_id'];
    
    // Prevent deleting yourself
    if ($deleteId === (int) $_SESSION['user_id']) {
        $message = 'You cannot delete your own account';
        $messageType = 'error';
    } else {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
        try {
            $stmt->execute(['id' => $deleteId]);
            $message = 'User deleted successfully';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error deleting user: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get all users
$stmt = $pdo->query('SELECT id, username, email, role, created_at FROM users ORDER BY username');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Manage Users</title>
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
			<li><a href="index.php?logout=1">Log Out</a></li>
		</ul>
	</section>

	<section class="right">

	<h2>Manage User Accounts</h2>

	<?php if (!empty($message)): ?>
		<div style="padding: 10px; margin-bottom: 15px; border-radius: 3px; <?=$messageType === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;'?>">
			<?=htmlspecialchars($message)?>
		</div>
	<?php endif; ?>

	<h3>Add New User</h3>
	<form action="users.php" method="post" style="padding: 15px; background-color: #f9f9f9; border-radius: 5px; margin-bottom: 30px;">
		<label for="new_username">Username:</label>
		<input type="text" id="new_username" name="new_username" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

		<label for="new_password">Password:</label>
		<input type="password" id="new_password" name="new_password" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

		<label for="new_email">Email (optional):</label>
		<input type="email" id="new_email" name="new_email" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

		<label for="new_role">Role:</label>
		<select id="new_role" name="role" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;">
			<option value="staff">Staff</option>
			<option value="admin">Admin</option>
			<option value="client">Client</option>
		</select>

		<button type="submit" name="add_user" style="padding: 8px 16px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">Add User</button>
	</form>

	<h3>Current Users</h3>
	<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
		<thead>
			<tr style="background-color: #f0f0f0; border-bottom: 2px solid #ddd;">
				<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Username</th>
				<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Email</th>
				<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Role</th>
				<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Created</th>
				<th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($users)): ?>
				<tr>
					<td colspan="4" style="padding: 10px; text-align: center; border: 1px solid #ddd;">No users found</td>
				</tr>
			<?php else: ?>
				<?php foreach ($users as $user): ?>
					<tr style="border-bottom: 1px solid #ddd;">
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?=htmlspecialchars($user['username'])?>
							<?php if ($user['id'] === (int) $_SESSION['user_id']): ?>
								<span style="color: #999; font-size: 0.9em;"> (you)</span>
							<?php endif; ?>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?=htmlspecialchars($user['email'] ?? 'N/A')?>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?=htmlspecialchars($user['role'] ?? 'staff')?>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd;">
							<?=htmlspecialchars(date('Y-m-d', strtotime($user['created_at'])))?>
						</td>
						<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
							<?php if ($user['id'] !== (int) $_SESSION['user_id']): ?>
								<a href="users.php?delete_id=<?=$user['id']?>" onclick="return confirm('Are you sure you want to delete this user?');" style="color: #dc3545; text-decoration: none;">Delete</a>
							<?php else: ?>
								<span style="color: #999;">-</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	</section>

	</main>

	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
	</footer>
</body>
</html>
