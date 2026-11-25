<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');

$message = '';
$messageType = '';

if (isset($_POST['setup'])) {
    $newPassword = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($newPassword) || empty($confirmPassword)) {
        $message = 'Both password fields are required';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Password must be at least 6 characters long';
        $messageType = 'error';
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = 1');
        try {
            $stmt->execute(['password' => $hashedPassword]);
            $message = 'Admin password has been set successfully. You can now log in with username "admin".';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error setting password: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup - Jo's Jobs</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background-color: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        input[type="password"] { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ddd; border-radius: 3px; }
        button { padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 3px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Setup</h1>
        <p>Set the password for the admin account to get started.</p>

        <?php if (!empty($message)): ?>
            <div class="message <?=$messageType?>">
                <?=htmlspecialchars($message)?>
            </div>
        <?php endif; ?>

        <?php if ($messageType !== 'success'): ?>
            <form action="setup.php" method="post">
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" name="setup">Set Password</button>
            </form>
        <?php else: ?>
            <p><a href="admin/index.php">Go to Admin Login</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
