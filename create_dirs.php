<?php
$base_path = __DIR__;

$directories = [
    '/assets/uploads',
    '/assets/uploads/assets',
    '/assets/uploads/profiles',
    '/assets/uploads/qrcodes',
    '/assets/uploads/temp'
];

echo "<h2>Creating Directories...</h2>";

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

// Set write permissions (Windows specific)
echo "<h3>Setting Permissions...</h3>";
$uploads_path = $base_path . '/assets/uploads';
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows - try to set writable
    echo "<p>On Windows, ensure the uploads folder has write permissions.</p>";
    echo "<p>Please right-click on the 'uploads' folder -> Properties -> Security -> Edit -> give 'Everyone' write permissions.</p>";
} else {
    // Linux/Mac
    chmod($uploads_path, 0777);
    echo "<p>Permissions set to 0777 on: $uploads_path</p>";
}

// Test if GD is enabled
echo "<h3>GD Library Status:</h3>";
if (extension_loaded('gd') && function_exists('gd_info')) {
    $gd_info = gd_info();
    echo "<p style='color:green'>✓ GD Library is ENABLED</p>";
    echo "<pre>";
    print_r($gd_info);
    echo "</pre>";
} else {
    echo "<p style='color:red'>✗ GD Library is NOT enabled</p>";
    echo "<p>Please enable GD in php.ini and restart Apache</p>";
}

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If GD is not enabled, enable it in php.ini and restart Apache</li>";
echo "<li>Refresh this page to confirm GD is enabled</li>";
echo "<li>Then try registering an asset again</li>";
echo "</ol>";
?>