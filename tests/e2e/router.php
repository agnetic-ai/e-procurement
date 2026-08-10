<?php

$projectRoot = dirname(__DIR__, 2);
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$requestedFile = $projectRoot . DIRECTORY_SEPARATOR . ltrim($requestPath, '/');

if ($requestPath !== '/' && is_file($requestedFile)) {
    return false;
}

$_GET['url'] = trim($requestPath, '/');
require $projectRoot . DIRECTORY_SEPARATOR . 'index.php';
