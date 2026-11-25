<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');

function renderCategoryNav($currentCategoryId = null) {
    global $pdo;
    $categories = $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);

    echo "<li>Jobs\n<ul>\n";
    foreach ($categories as $category) {
        $class = $currentCategoryId == $category['id'] ? ' class="current"' : '';
        echo "<li{$class}><a href=\"category.php?id=" . $category['id'] . "\">" . htmlspecialchars($category['name']) . "</a></li>\n";
    }
    echo "</ul>\n</li>\n";
}
?>
