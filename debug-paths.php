<?php
echo "<h2>PEARTS Path Debugger</h2>";

$base_path = __DIR__;
echo "<p><strong>Base Path:</strong> " . $base_path . "</p>";

// Check if directories exist
$directories = [
    'views',
    'views/user',
    'views/asset',
    'views/security',
    'views/admin',
    'views/auth',
    'controllers',
    'models',
    'config',
    'includes',
    'assets/uploads/assets',
    'assets/uploads/profiles',
    'assets/uploads/qrcodes'
];

echo "<h3>Directory Check:</h3>";
echo "<ul>";
foreach ($directories as $dir) {
    $full_path = $base_path . '/' . $dir;
    if (file_exists($full_path) && is_dir($full_path)) {
        echo "<li style='color:green'>✓ $dir exists</li>";
    } else {
        echo "<li style='color:red'>✗ $dir does NOT exist</li>";
    }
}
echo "</ul>";

// Check if files exist
$files = [
    'views/user/dashboard.php',
    'views/asset/register.php',
    'views/asset/success.php',
    'views/asset/view.php',
    'views/asset/history.php',
    'views/security/scan.php',
    'views/security/scan-result.php',
    'views/auth/login.php',
    'views/auth/register.php',
    'controllers/AssetController.php',
    'controllers/AuthController.php',
    'models/Asset.php',
    'models/User.php',
    'models/Registration.php',
    'config/constants.php',
    'config/database.php',
    'config/functions.php',
    'includes/navbar.php'
];

echo "<h3>File Check:</h3>";
echo "<ul>";
foreach ($files as $file) {
    $full_path = $base_path . '/' . $file;
    if (file_exists($full_path)) {
        echo "<li style='color:green'>✓ $file exists</li>";
    } else {
        echo "<li style='color:red'>✗ $file does NOT exist</li>";
    }
}
echo "</ul>";

echo "<h3>Current URL Structure:</h3>";
echo "<p>Your site URL should be: http://localhost/papi/</p>";
echo "<p>Try accessing: <a href='http://localhost/papi/views/user/dashboard.php'>http://localhost/papi/views/user/dashboard.php</a></p>";
?>