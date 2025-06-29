<?php
include '../config.php';
include '../inc/header.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$page = str_replace(['../', './', '..\\', '.\\'], '', $page);

$file = __DIR__ . '/' . $page . '.php';
if (!file_exists($file)) {
    $file = __DIR__ . '/' . $page . '/index.php';
}

if (file_exists($file)) {
    include $file;
} else {
    echo "<h3>Page not found!</h3>";
}

include '../inc/footer.php';
