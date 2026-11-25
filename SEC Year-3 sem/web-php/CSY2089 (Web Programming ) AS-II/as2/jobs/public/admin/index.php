<?php
require_once 'auth.php';

$loggedIn = isLoggedIn();
$loginError = '';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Handle login
if (isset($_POST['submit'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare('SELECT id, password, role FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['user_role'] = $user['role'] ?? 'admin';
            $_SESSION['loggedin'] = true;
            $loggedIn = true;
        } else {
            $loginError = 'Invalid username or password';
        }
    } else {
        $loginError = 'Please enter both username and password';
    }
}
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Admin Home</title>
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
			<li>Jobs
				<ul>
					<li><a href="/it.php">IT</a></li>
					<li><a href="/hr.php">Human Resources</a></li>
					<li><a href="/sales.php">Sales</a></li>

				</ul>
			</li>
			<li><a href="/about.php">About Us</a></li>
		</ul>

	</nav>
	<img src="/images/randombanner.php"/>
	<main class="sidebar">

	<?php
	if ($loggedIn && isset($_SESSION['user_id'])) {
	?>

	<section class="left">
		<ul>
			<li><a href="jobs.php">Jobs</a></li>
			<?php if (isAdmin()): ?>
				<li><a href="categories.php">Categories</a></li>
				<li><a href="users.php">Manage Users</a></li>
				<li><a href="enquiries.php">Enquiries</a></li>
			<?php endif; ?>
		</ul>
	</section>

	<section class="right">
	<h2>Welcome, <?=htmlspecialchars($_SESSION['username'])?></h2>
	<p>You are logged in to the admin area.</p>
	<p><a href="index.php?logout=1" style="color: #dc3545; text-decoration: none;"><strong>Log out</strong></a></p>
	</section>
	<?php
	} else {
		?>
		<section class="right">
		<h2>Admin Login</h2>
		<?php if (!empty($loginError)): ?>
			<div style="color: #dc3545; margin-bottom: 15px; padding: 10px; background-color: #f8d7da; border-radius: 3px;">
				<?=htmlspecialchars($loginError)?>
			</div>
		<?php endif; ?>

		<form action="index.php" method="post" style="padding: 20px; background-color: #f9f9f9; border-radius: 5px; max-width: 400px;">

			<label for="username">Username:</label>
			<input type="text" id="username" name="username" required style="width: 100%; padding: 8px; margin-bottom: 15px; box-sizing: border-box;" />

			<label for="password">Password:</label>
			<input type="password" id="password" name="password" required style="width: 100%; padding: 8px; margin-bottom: 15px; box-sizing: border-box;" />

			<input type="submit" name="submit" value="Log In" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;" />
		</form>
	<?php
	}
	?>


	</main>

	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
</body>
</html>


