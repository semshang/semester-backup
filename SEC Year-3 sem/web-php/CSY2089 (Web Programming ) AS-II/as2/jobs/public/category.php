<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /');
    exit;
}

$categoryId = (int) $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM category WHERE id = :id');
$stmt->execute(['id' => $categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    header('Location: /');
    exit;
}

// Get filter parameters
$titleFilter = isset($_GET['title']) ? $_GET['title'] : '';
$locationFilter = isset($_GET['location']) ? $_GET['location'] : '';

// Build query with filters
$query = 'SELECT * FROM job WHERE categoryId = :categoryId AND closingDate > :date AND (archived IS NULL OR archived = 0)';
$params = [
    'categoryId' => $categoryId,
    'date' => (new DateTime())->format('Y-m-d')
];

if (!empty($titleFilter)) {
    $query .= ' AND title LIKE :title';
    $params['title'] = '%' . $titleFilter . '%';
}

if (!empty($locationFilter)) {
    $query .= ' AND location LIKE :location';
    $params['location'] = '%' . $locationFilter . '%';
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - <?=htmlspecialchars($category['name'])?> Jobs</title>
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
<?php include __DIR__ . '/inc/category-nav.php'; renderCategoryNav($categoryId); ?>
			<li><a href="/careers-advice.php">Careers Advice</a></li>
			<li><a href="/contact.php">Contact</a></li>
			<li><a href="/about.php">About Us</a></li>
		</ul>

	</nav>
	<img src="images/randombanner.php"/>
	<main class="sidebar">

	<section class="left">
		<ul>
<?php foreach ($jobs as $job): ?>
			<li><a href="/apply.php?id=<?=$job['id']?>"><?=
                    htmlspecialchars($job['title'])
                ?></a></li>
<?php endforeach; ?>
		</ul>
	</section>

	<section class="right">

	<h1><?=htmlspecialchars($category['name'])?> Jobs</h1>

	<form method="get" style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 5px;">
		<input type="hidden" name="id" value="<?=$categoryId?>">
		
		<label for="title">Filter by Job Title:</label><br>
		<input type="text" id="title" name="title" value="<?=htmlspecialchars($titleFilter)?>" placeholder="Enter job title..." style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;"><br>
		
		<label for="location">Filter by Location:</label><br>
		<input type="text" id="location" name="location" value="<?=htmlspecialchars($locationFilter)?>" placeholder="Enter location..." style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;"><br>
		
		<button type="submit" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">Filter Jobs</button>
		<?php if (!empty($titleFilter) || !empty($locationFilter)): ?>
			<a href="category.php?id=<?=$categoryId?>" style="margin-left: 10px; padding: 8px 16px; background-color: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer; text-decoration: none; display: inline-block;">Clear Filters</a>
		<?php endif; ?>
	</form>

	<ul class="listing">
<?php foreach ($jobs as $job): ?>
	<li>
		<div class="details">
			<h2><?=htmlspecialchars($job['title'])?></h2>
			<h3><?=htmlspecialchars($job['salary'])?></h3>
			<p><?=nl2br(htmlspecialchars($job['description']))?></p>
			<a class="more" href="/apply.php?id=<?=$job['id']?>">Apply for this job</a>
		</div>
	</li>
<?php endforeach; ?>
	</ul>

	</section>
	</main>


	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
	</footer>
</body>
</html>

