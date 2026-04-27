<?php
echo "<h2>File Structure Check</h2>";
echo "<pre>";

$base = __DIR__;
echo "Base directory: " . $base . "\n\n";

// Check if directories exist
$dirs = ['config', 'includes', 'views', 'controllers', 'models'];
foreach ($dirs as $dir) {
    $path = $base . '/' . $dir;
    if (is_dir($path)) {
        echo "✓ Directory exists: $dir\n";
        
        // List files in directory
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "  - $file\n";
            }
        }
        echo "\n";
    } else {
        echo "✗ Directory missing: $dir\n\n";
    }
}

echo "</pre>";
?>