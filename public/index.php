<?php
require '../config/database.php';
require '../config/routes.php';

$language = $_SESSION['admin']['language'] ?? 'en';
$langPath = __DIR__ . "/lang/{$language}.php";

if (file_exists($langPath)) {
    $lang = include $langPath;
} else {
    $lang = include __DIR__ . "../../lang/en.php";
}
