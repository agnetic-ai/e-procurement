<?php
// Composer autoload (PHPMailer etc.)
require_once __DIR__ . '/../../vendor/autoload.php';

spl_autoload_register(function ($className) {
    $corePath = __DIR__ . '/../core/' . $className . '.php';
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }
    $modelPath = __DIR__ . '/../models/' . $className . '.php';
    if (file_exists($modelPath)) {
        require_once $modelPath;
        return;
    }

    $helperPath = __DIR__ . '/../helpers/' . $className . '.php';
    if (file_exists($helperPath)) {
        require_once $helperPath;
        return;
    }

    $servicePath = __DIR__ . '/../services/' . $className . '.php';
    if (file_exists($servicePath)) {
        require_once $servicePath;
        return;
    }
});
