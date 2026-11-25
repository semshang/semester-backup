<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');
$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Careers Advice</title>
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
<?php include __DIR__ . '/inc/category-nav.php'; renderCategoryNav(); ?>
			<li><a href="/categories.php">Categories</a></li>
			<li><a href="/careers-advice.php">Careers Advice</a></li>
			<li><a href="/contact.php">Contact</a></li>
			<li><a href="/about.php">About Us</a></li>
		</ul>

	</nav>
	<img src="images/randombanner.php"/>
	<main class="home">
		<h2>Careers Advice for Job Seekers</h2>
		<p>At Jo's Jobs, we want to help you make smarter career decisions. Whether you are just starting out or planning your next move, good preparation makes all the difference.</p>
		<p>Here are a few tips to help you stand out:</p>
		<ul>
			<li>Keep your CV concise and focused on your latest achievements.</li>
			<li>Research the company and tailor your application to the role.</li>
			<li>Practice interview questions and prepare examples of your success.</li>
			<li>Follow up professionally after interviews to show your interest.</li>
		</ul>
		<p>If you have questions about applying for a job, our team is always happy to help.</p>
	</main>


	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
	</footer>
</body>
</html>
