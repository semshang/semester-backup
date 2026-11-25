<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS enquiries (
    id INT(11) NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    telephone VARCHAR(50) DEFAULT NULL,
    enquiry TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'new',
    assigned_to INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$message = '';
$messageType = '';

if (isset($_POST['submit'])) {
    $stmt = $pdo->prepare('INSERT INTO enquiries (first_name, surname, email, telephone, enquiry, status) VALUES (:first_name, :surname, :email, :telephone, :enquiry, :status)');
    $success = $stmt->execute([
        'first_name' => trim($_POST['first_name'] ?? ''),
        'surname' => trim($_POST['surname'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'enquiry' => trim($_POST['enquiry'] ?? ''),
        'status' => 'new'
    ]);

    if ($success) {
        $message = 'Your enquiry has been saved. We will be in touch soon.';
        $messageType = 'success';
    } else {
        $message = 'We could not save your enquiry. Please try again.';
        $messageType = 'error';
    }
}

$categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="/styles.css"/>
        <title>Jo's Jobs - Contact</title>
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
        <h2>Contact Jo's Jobs</h2>
        <p>Use the form below to leave an enquiry. Your message will be stored securely for our team to review.</p>

        <?php if (!empty($message)): ?>
            <div style="padding: 10px; margin-bottom: 15px; border-radius: 3px; <?= $messageType === 'success' ? 'background-color: #d4edda; color: #155724;' : 'background-color: #f8d7da; color: #721c24;' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="contact.php" method="post" style="max-width: 600px; background: #f9f9f9; padding: 20px; border-radius: 5px;">
            <label for="first_name">First name</label>
            <input type="text" id="first_name" name="first_name" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

            <label for="surname">Surname</label>
            <input type="text" id="surname" name="surname" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

            <label for="email">Email address</label>
            <input type="email" id="email" name="email" required style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

            <label for="telephone">Telephone</label>
            <input type="text" id="telephone" name="telephone" style="width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box;" />

            <label for="enquiry">Enquiry</label>
            <textarea id="enquiry" name="enquiry" required style="width: 100%; padding: 8px; min-height: 120px; box-sizing: border-box;"></textarea>

            <input type="submit" name="submit" value="Send enquiry" style="margin-top: 15px; padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;" />
        </form>
    </main>

    <footer>
        &copy; Jo's Jobs <?= date('Y') ?>
    </footer>
</body>
</html>
