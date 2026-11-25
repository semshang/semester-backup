<?php
require_once 'auth.php';
ensureLoggedIn();
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="/styles.css"/>
		<title>Jo's Jobs - Job list</title>
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

	<section class="left">
		<ul>
			<li><a href="jobs.php">Jobs</a></li>
			<?php if (isAdmin()): ?>
				<li><a href="categories.php">Categories</a></li>
			<?php endif; ?>
		</ul>
	</section>

	<section class="right">

	<?php

		if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
		?>


			<h2>Jobs</h2>

			<a class="new" href="addjob.php">Add new job</a>

			<?php
			$cats = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
			echo '<form method="get" style="margin-bottom: 10px;">';
			echo '<label>Filter by category: </label>';
			echo '<select name="category">';
			echo '<option value="">All</option>';
			foreach ($cats as $c) {
				$sel = (isset($_GET['category']) && $_GET['category'] == $c['id']) ? ' selected' : '';
				echo '<option value="' . $c['id'] . '"' . $sel . '>' . htmlspecialchars($c['name']) . '</option>';
			}
			echo '</select>';
			echo ' <label>Show: </label><select name="show">';
			echo '<option value="all"' . ((($_GET['show'] ?? 'all') === 'all') ? ' selected' : '') . '>All jobs</option>';
			echo '<option value="active"' . ((($_GET['show'] ?? 'all') === 'active') ? ' selected' : '') . '>Active only</option>';
			echo '<option value="archived"' . ((($_GET['show'] ?? 'all') === 'archived') ? ' selected' : '') . '>Archived only</option>';
			echo '</select>';
			echo ' <input type="submit" value="Filter" />';
			echo '</form>';

			echo '<table>';
			echo '<thead>';
			echo '<tr>';
			echo '<th>Title</th>';
			echo '<th>Category</th>';
			echo '<th>Received</th>';
			echo '<th>Salary</th>';
			echo '<th style="width: 5%">&nbsp;</th>';
			echo '<th style="width: 15%">&nbsp;</th>';
			echo '<th style="width: 5%">&nbsp;</th>';
			echo '<th style="width: 5%">&nbsp;</th>';
			echo '</tr>';

			// build query with optional category filter and ownership restrictions
			$params = [];
			$sql = 'SELECT job.*, category.name AS categoryName FROM job LEFT JOIN category ON job.categoryId = category.id';
			$where = [];

			if (isClient()) {
				$where[] = 'job.ownerId = :ownerId';
				$params['ownerId'] = currentUserId();
			}

			if (isset($_GET['category']) && is_numeric($_GET['category']) && $_GET['category'] > 0) {
				$where[] = 'job.categoryId = :categoryId';
				$params['categoryId'] = (int)$_GET['category'];
			}

			$show = isset($_GET['show']) ? $_GET['show'] : 'all';
			if ($show === 'active') {
				$where[] = '(job.archived IS NULL OR job.archived = 0)';
			} elseif ($show === 'archived') {
				$where[] = 'job.archived = 1';
			}

			if (!empty($where)) {
				$sql .= ' WHERE ' . implode(' AND ', $where);
			}

			$sql .= ' ORDER BY receivedDate DESC, id DESC';

			$stmt = $pdo->prepare($sql);
			$stmt->execute($params);

			foreach ($stmt as $job) {
				$applicants = $pdo->prepare('SELECT count(*) as count FROM applicants WHERE jobId = :jobId');

				$applicants->execute(['jobId' => $job['id']]);

				$applicantCount = $applicants->fetch();

				echo '<tr>';
				echo '<td>' . htmlspecialchars($job['title']) . '</td>';
				echo '<td>' . htmlspecialchars($job['categoryName']) . '</td>';
				echo '<td>' . htmlspecialchars($job['receivedDate'] ?? '') . '</td>';
				echo '<td>' . $job['salary'] . '</td>';
				echo '<td><a style="float: right" href="editjob.php?id=' . $job['id'] . '">Edit</a></td>';
				echo '<td><a style="float: right" href="applicants.php?id=' . $job['id'] . '">View applicants (' . $applicantCount['count'] . ')</a></td>';
				if (!isset($job['archived']) || $job['archived'] == 0) {
					echo '<td><form method="post" action="deletejob.php">
					<input type="hidden" name="id" value="' . $job['id'] . '" />
					<input type="hidden" name="action" value="archive" />
					<input type="submit" name="submit" value="Archive" />
					</form></td>';
				} else {
					echo '<td><form method="post" action="deletejob.php">
					<input type="hidden" name="id" value="' . $job['id'] . '" />
					<input type="hidden" name="action" value="restore" />
					<input type="submit" name="submit" value="Restore" />
					</form></td>';
				}

				echo '<td><form method="post" action="deletejob.php" onsubmit="return confirm(\'Permanently delete this job?\');">
				<input type="hidden" name="id" value="' . $job['id'] . '" />
				<input type="hidden" name="action" value="delete" />
				<input type="submit" name="submit" value="Delete" />
				</form></td>';
				echo '</tr>';
			}

			echo '</thead>';
			echo '</table>';

		}

		else {
			?>
			<h2>Log in</h2>

			<form action="index.php" method="post">
				<label>Password</label>
				<input type="password" name="password" />

				<input type="submit" name="submit" value="Log In" />
			</form>
		<?php
		}
	?>

</section>
	</main>

	<footer>
		&copy; Jo's Jobs <?= date('Y') ?>
</body>
</html>



