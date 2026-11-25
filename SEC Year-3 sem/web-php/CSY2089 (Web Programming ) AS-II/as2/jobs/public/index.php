<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Home</title>
	</head>
	<body>
<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');
$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
$closingSoonStmt = $pdo->prepare('SELECT * FROM job WHERE (archived IS NULL OR archived = 0) AND closingDate >= CURDATE() ORDER BY closingDate ASC, receivedDate DESC LIMIT 5');
$closingSoonStmt->execute();
$closingSoonJobs = $closingSoonStmt->fetchAll(PDO::FETCH_ASSOC);
?> 
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
		<p>Welcome to Jo's Jobs, we're a recruitment agency based in Northampton. We offer a range of different office jobs. Get in touch if you'd like to list a job with us.</p>

		<h2>Select the type of job you are looking for:</h2>
		<ul>
<?php foreach ($categories as $category): ?>
			<li><a href="category.php?id=<?=$category['id']?>"><?=htmlspecialchars($category['name'])?></a></li>
<?php endforeach; ?>
		</ul>

		<h2>Closing soon</h2>
		<ul>
<?php if (!empty($closingSoonJobs)): ?>
<?php foreach ($closingSoonJobs as $job): ?>
			<li><strong><?=htmlspecialchars($job['title'])?></strong> - closes <?=htmlspecialchars($job['closingDate'])?></li>
<?php endforeach; ?>
<?php else: ?>
			<li>No jobs closing soon.</li>
<?php endif; ?>
		</ul>
	</main>


	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
</body>
</html>

