<?php
// Pick a file at random from the 'banners' directory and display it
$files = [];
foreach (new DirectoryIterator(__DIR__ . '/banners') as $file) {
    if ($file->isDot() || !$file->isFile()) {
        continue;
    }

    if (stripos($file->getFileName(), '.jpg') === false) {
        continue;
    }

    $files[] = $file->getPathname();
}

if (empty($files)) {
    header('HTTP/1.1 404 Not Found');
    exit;
}

$selected = $files[array_rand($files)];

header('Content-Type: image/jpeg');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Length: ' . filesize($selected));

readfile($selected);

