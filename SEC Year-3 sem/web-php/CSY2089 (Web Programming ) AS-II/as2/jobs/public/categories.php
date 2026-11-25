<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');
$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Categories</title>
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
<?php foreach ($categories as $category): ?>
					<li><a href="category.php?id=<?=$category['id']?>"><?=htmlspecialchars($category['name'])?></a></li>
<?php endforeach; ?>
				</ul>
			</li>
			<li><a href="/categories.php">Categories</a></li>
			<li><a href="/careers-advice.php">Careers Advice</a></li>
			<li><a href="/contact.php">Contact</a></li>
			<li><a href="/about.php">About Us</a></li>
		</ul>

	</nav>
	<img src="images/randombanner.php"/>
	<main class="home">
		<h2>Job Categories</h2>
		<p>Choose a category to see all current vacancies in that area.</p>
		<ul>
<?php foreach ($categories as $category): ?>
			<li><a href="category.php?id=<?=$category['id']?>"><?=htmlspecialchars($category['name'])?></a></li>
<?php endforeach; ?>
		</ul>
	</main>

	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
	</footer>
</body>
</html>

