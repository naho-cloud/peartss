<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

echo "<h2>QR Code Generation Test</h2>";
echo "<p>Starting test...</p>";

// Check if config files exist
$config_constants = __DIR__ . '/config/constants.php';
$config_functions = __DIR__ . '/config/functions.php';
$qr_controller = __DIR__ . '/controllers/QRCodeController.php';

echo "<h3>Checking Required Files:</h3>";
echo "<ul>";
echo "<li>constants.php: " . (file_exists($config_constants) ? '✅ Found' : '❌ NOT FOUND') . "</li>";
echo "<li>functions.php: " . (file_exists($config_functions) ? '✅ Found' : '❌ NOT FOUND') . "</li>";
echo "<li>QRCodeController.php: " . (file_exists($qr_controller) ? '✅ Found' : '❌ NOT FOUND') . "</li>";
echo "</ul>";

// Try to include files with error checking
try {
    echo "<p>Including constants.php...</p>";
    require_once $config_constants;
    echo "<p>✅ constants.php included</p>";
    
    echo "<p>Including functions.php...</p>";
    require_once $config_functions;
    echo "<p>✅ functions.php included</p>";
    
    echo "<p>Including QRCodeController.php...</p>";
    require_once $qr_controller;
    echo "<p>✅ QRCodeController.php included</p>";
    
} catch (Exception $e) {
    die("<p style='color:red'>Error including files: " . $e->getMessage() . "</p>");
}

echo "<h3>Checking Constants:</h3>";
echo "<ul>";
echo "<li>BASE_PATH: " . (defined('BASE_PATH') ? BASE_PATH : 'NOT DEFINED') . "</li>";
echo "<li>SITE_URL: " . (defined('SITE_URL') ? SITE_URL : 'NOT DEFINED') . "</li>";
echo "<li>UPLOAD_PATH: " . (defined('UPLOAD_PATH') ? UPLOAD_PATH : 'NOT DEFINED') . "</li>";
echo "</ul>";

// Define constants if not defined
if (!defined('BASE_PATH')) define('BASE_PATH', __DIR__);
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/papi/');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', __DIR__ . '/assets/uploads/');

echo "<h3>Testing GD Library:</h3>";
if (extension_loaded('gd')) {
    echo "<p style='color:green'>✅ GD Library is loaded</p>";
    $gd_info = gd_info();
    echo "<pre>";
    print_r($gd_info);
    echo "</pre>";
} else {
    echo "<p style='color:red'>❌ GD Library is NOT loaded</p>";
    echo "<p>Please enable GD in php.ini and restart Apache</p>";
}

// Test data
$test_asset = [
    'asset_id' => 999,
    'serial_number' => 'TEST123456',
    'brand' => 'Test Brand',
    'model' => 'Test Model',
    'color' => 'Black',
    'registration_date' => date('Y-m-d H:i:s'),
    'status' => 'registered'
];

$test_user = [
    'full_name' => 'Test User',
    'university_id' => 'TEST001',
    'email' => 'test@example.com',
    'phone_number' => '0912345678'
];

echo "<h3>Creating QRCodeController instance:</h3>";
try {
    $qr = new QRCodeController();
    echo "<p style='color:green'>✅ QRCodeController created successfully</p>";
} catch (Exception $e) {
    die("<p style='color:red'>Error creating QRCodeController: " . $e->getMessage() . "</p>");
}

// Test directory creation
$qr_dir = UPLOAD_PATH . 'qrcodes/';
echo "<h3>Testing Directory:</h3>";
echo "<p>QR Directory path: " . $qr_dir . "</p>";

if (!file_exists($qr_dir)) {
    echo "<p>Directory doesn't exist. Attempting to create...</p>";
    if (mkdir($qr_dir, 0777, true)) {
        echo "<p style='color:green'>✅ Directory created successfully</p>";
    } else {
        echo "<p style='color:red'>❌ Failed to create directory</p>";
    }
} else {
    echo "<p style='color:green'>✅ Directory exists</p>";
}

// Check write permissions
if (is_writable($qr_dir)) {
    echo "<p style='color:green'>✅ Directory is writable</p>";
} else {
    echo "<p style='color:red'>❌ Directory is NOT writable</p>";
}

// Test QR generation
echo "<h3>Testing QR Generation:</h3>";
try {
    $result = $qr->generateSecurityQR(
        $test_asset,
        $test_user,
        '',
        ''
    );
    
    echo "<pre style='background:#f4f4f4; padding:10px;'>";
    print_r($result);
    echo "</pre>";
    
    if ($result['success']) {
        echo "<p style='color:green'>✅ QR code generated successfully</p>";
        
        // Check if file exists
        $full_path = UPLOAD_PATH . 'qrcodes/' . $result['filename'];
        if (file_exists($full_path)) {
            echo "<p style='color:green'>✅ File exists at: $full_path</p>";
            echo "<p>File size: " . filesize($full_path) . " bytes</p>";
            
            // Display the QR code
            echo "<h3>Generated QR Code:</h3>";
            echo "<img src='" . SITE_URL . $result['filepath'] . "' style='max-width: 200px; border: 1px solid #ccc; padding: 10px;' onerror='this.onerror=null; this.src=\"\"; this.alt=\"Image failed to load\";'>";
        } else {
            echo "<p style='color:red'>❌ File does NOT exist at: $full_path</p>";
        }
    } else {
        echo "<p style='color:red'>❌ QR generation failed: " . $result['message'] . "</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>Exception: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// List all files in qrcodes directory
echo "<h3>Files in QR Code Directory:</h3>";
if (is_dir($qr_dir)) {
    $files = scandir($qr_dir);
    if (empty($files) || count($files) <= 2) {
        echo "<p>No files found in directory</p>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filepath = $qr_dir . $file;
                $filesize = file_exists($filepath) ? filesize($filepath) : 0;
                echo "<li>$file (" . round($filesize/1024, 2) . " KB)</li>";
            }
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color:red'>QR directory not found!</p>";
}

// Check error log
$error_log = __DIR__ . '/error.log';
if (file_exists($error_log)) {
    echo "<h3>Recent Errors:</h3>";
    $errors = file_get_contents($error_log);
    echo "<pre style='background:#f4f4f4; padding:10px; max-height:200px; overflow:auto;'>" . htmlspecialchars($errors) . "</pre>";
} else {
    echo "<p>No error log found</p>";
}

echo "<h3>Test Complete</h3>";
?>