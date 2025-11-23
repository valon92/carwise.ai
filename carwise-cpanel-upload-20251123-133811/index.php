<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// Support both deployments where the app root is the web root (cPanel) and the standard Laravel structure
// cPanel-style path
$maintenancePublic = __DIR__.'/storage/framework/maintenance.php';
// Standard Laravel root path
$maintenanceRoot = __DIR__.'/../storage/framework/maintenance.php';
if (file_exists($maintenancePublic)) {
    require $maintenancePublic;
} elseif (file_exists($maintenanceRoot)) {
    require $maintenanceRoot;
}

// Register the Composer autoloader...
$autoloadPublic = __DIR__.'/vendor/autoload.php';
$autoloadRoot = __DIR__.'/../vendor/autoload.php';
if (file_exists($autoloadPublic)) {
    require $autoloadPublic;
} else {
    require $autoloadRoot;
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$appPathPublic = __DIR__.'/bootstrap/app.php';
$appPathRoot = __DIR__.'/../bootstrap/app.php';
$app = require_once (file_exists($appPathPublic) ? $appPathPublic : $appPathRoot);

$app->handleRequest(Request::capture());
