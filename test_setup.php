<?php
echo "<h2>PEARTS Setup Test</h2>";

// Test GD Library
echo "<h3>1. Testing GD Library:</h3>";
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✓ GD Library is loaded</p>";
    $gd_info = gd_info();
    echo "<pre>";
    print_r($gd_info);
    echo "</pre>";
} else {
    echo "<p style='color:red'>✗ GD Library is NOT loaded</p>";
    echo "<p>Please enable GD in php.ini and restart Apache</p>";
}

// Test Directories
echo "<h3>2. Testing Directories:</h3>";
$dirs = [
    '/assets/uploads',
    '/assets/uploads/qrcodes',
    '/assets/uploads/assets',
    '/assets/uploads/profiles',
    '/assets/images'
];

foreach ($dirs as $dir) {
    $full_path = __DIR__ . $dir;
    if (file_exists($full_path)) {
        echo "<p style='color:green'>✓ $dir exists</p>";
        // Test write permission
        $test_file = $full_path . '/test.txt';
        if (@file_put_contents($test_file, 'test')) {
            echo "<p style='color:green'>  - Writable ✓</p>";
            unlink($test_file);
        } else {
            echo "<p style='color:red'>  - Not writable ✗</p>";
        }
    } else {
        echo "<p style='color:red'>✗ $dir does not exist</p>";
    }
}

// Test QR Code Library
echo "<h3>3. Testing QR Code Library:</h3>";
if (file_exists(__DIR__ . '/vendor/phpqrcode/qrlib.php')) {
    echo "<p style='color:green'>✓ QR Code library found</p>";
    require_once __DIR__ . '/vendor/phpqrcode/qrlib.php';
    
    // Try to generate a test QR
    $test_dir = __DIR__ . '/assets/uploads/qrcodes/';
    $test_file = $test_dir . 'test.png';
    
    try {
        QRcode::png('test', $test_file, QR_ECLEVEL_L, 5);
        if (file_exists($test_file)) {
            echo "<p style='color:green'>✓ QR code generation successful</p>";
            unlink($test_file);
        } else {
            echo "<p style='color:red'>✗ QR code generation failed - file not created</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ QR code generation error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ QR Code library not found at vendor/phpqrcode/qrlib.php</p>";
}

// PHP Version
echo "<h3>4. PHP Version:</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Recommendations
echo "<h3>Recommendations:</h3>";
if (!extension_loaded('gd')) {
    echo "<p>1. Edit php.ini and uncomment: extension=gd</p>";
    echo "<p>2. Restart Apache</p>";
}
if (!file_exists(__DIR__ . '/vendor/phpqrcode/qrlib.php')) {
    echo "<p>3. Download phpqrcode from https://sourceforge.net/projects/phpqrcode/</p>";
    echo "<p>4. Extract to: " . __DIR__ . "/vendor/phpqrcode/</p>";
}
?>