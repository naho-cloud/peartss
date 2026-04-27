<?php
// Turn on all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Minimal PHP Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test basic PHP
$test_var = "Hello World";
echo "<p>Basic PHP test: $test_var</p>";

// Test file writing
$test_file = __DIR__ . '/test.txt';
if (file_put_contents($test_file, 'test')) {
    echo "<p style='color:green'>✅ Can write files</p>";
    unlink($test_file);
} else {
    echo "<p style='color:red'>❌ Cannot write files</p>";
}

// Test includes
echo "<p>Testing includes...</p>";
$files_to_test = [
    '/config/constants.php',
    '/config/functions.php',
    '/controllers/QRCodeController.php',
    '/vendor/phpqrcode/qrlib.php'
];

foreach ($files_to_test as $file) {
    $full_path = __DIR__ . $file;
    if (file_exists($full_path)) {
        echo "<p style='color:green'>✅ Found: $file</p>";
    } else {
        echo "<p style='color:red'>❌ Not found: $file</p>";
    }
}

// Test GD
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✅ GD loaded</p>";
} else {
    echo "<p style='color:red'>❌ GD not loaded</p>";
}
?>