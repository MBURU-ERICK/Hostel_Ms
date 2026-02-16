<?php

$basePath = __DIR__ . '/app/Http';

// Standard Laravel middleware to check for
$standardMiddleware = [
    'CheckForMaintenanceMode.php',
    'TrimStrings.php',
    'EncryptCookies.php',
    'VerifyCsrfToken.php',
    'Authenticate.php',
];

// Check Kernel.php
echo "=== Kernel.php Check ===\n";
$kernelPath = $basePath . '/Kernel.php';
if (file_exists($kernelPath)) {
    echo "[OK] Kernel.php exists.\n";
} else {
    echo "[MISSING] Kernel.php not found!\n";
}

// List Middleware
echo "\n=== Middleware ===\n";
$middlewarePath = $basePath . '/Middleware';
if (is_dir($middlewarePath)) {
    $middlewareFiles = scandir($middlewarePath);
    foreach ($middlewareFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        echo "- $file";
        if (in_array($file, $standardMiddleware)) {
            echo " [STANDARD]";
        }
        echo "\n";
    }

    // Check for missing standard middleware
    $missing = array_diff($standardMiddleware, $middlewareFiles);
    if (!empty($missing)) {
        echo "\n[MISSING STANDARD MIDDLEWARE]:\n";
        foreach ($missing as $m) {
            echo "- $m\n";
        }
    }
} else {
    echo "[MISSING] Middleware directory not found!\n";
}

// List Controllers
echo "\n=== Controllers ===\n";
$controllersPath = $basePath . '/Controllers';
if (is_dir($controllersPath)) {
    $controllerFiles = scandir($controllersPath);
    foreach ($controllerFiles as $file) {
        if ($file === '.' || $file === '..') continue;
        echo "- $file\n";
    }
} else {
    echo "[MISSING] Controllers directory not found!\n";
}

echo "\n=== Check Complete ===\n";
