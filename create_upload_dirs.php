<?php
$base_path = __DIR__;

$directories = [
    '/assets/uploads/lost_items',
    '/assets/uploads/found_items',
    '/assets/uploads/temp'
];

echo "<h2>Creating Upload Directories</h2>";

foreach ($directories as $dir) {
    $full_path = $base_path . $dir;
    if (!file_exists($full_path)) {
        if (mkdir($full_path, 0777, true)) {
            echo "<p style='color:green'>✓ Created: $dir</p>";
            // Create index.html to prevent directory listing
            file_put_contents($full_path . '/index.html', '<!DOCTYPE html><html><head><title>Access Denied</title></head><body><h1>Access Denied</h1></body></html>');
        } else {
            echo "<p style='color:red'>✗ Failed to create: $dir</p>";
        }
    } else {
        echo "<p style='color:blue'>✓ Already exists: $dir</p>";
    }
}

echo "<h3>Done!</h3>";
echo "<p><a href='views/lost/report.php'>Go to Lost Report Page</a></p>";
?>