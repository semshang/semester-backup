<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql', 'student', 'student');

function getCategories() {
    global $pdo;
    return $pdo->query('SELECT * FROM category ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
}

function renderJobsDropdown($currentCategoryId = null) {
    $categories = getCategories();

    echo "\t\t<li>Jobs\n\t\t\t<ul>\n";
    foreach ($categories as $category) {
        $class = $currentCategoryId == $category['id'] ? ' class="current"' : '';
        echo "\t\t\t\t<li{$class}><a href=\"/category.php?id=" . $category['id'] . "\">" . htmlspecialchars($category['name']) . "</a></li>\n";
    }
    echo "\t\t\t</ul>\n\t\t</li>\n";
}
?>
