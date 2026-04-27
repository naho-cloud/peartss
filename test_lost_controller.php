<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>LostController Minimal Test</h2>";

// Include required files one by one
$files = [
    '/config/constants.php',
    '/config/database.php',
    '/config/functions.php',
    '/models/Asset.php',
    '/models/LostReport.php',
    '/models/GeneralLostItem.php',
    '/controllers/LostController.php'
];

echo "<h3>Including files:</h3>";
foreach ($files as $file) {
    $full_path = __DIR__ . $file;
    echo "<p>Loading: $file... ";
    if (file_exists($full_path)) {
        require_once $full_path;
        echo "<span style='color:green'>✓ Loaded</span></p>";
    } else {
        echo "<span style='color:red'>✗ File not found!</span></p>";
    }
}

echo "<h3>Testing LostController:</h3>";
try {
    $controller = new LostController();
    echo "<p style='color:green'>✓ LostController created successfully</p>";
    
    // Test showReportPage method
    if (method_exists($controller, 'showReportPage')) {
        echo "<p>✓ showReportPage method exists</p>";
        $assets = $controller->showReportPage();
        echo "<p>Returned: " . count($assets) . " assets</p>";
    } else {
        echo "<p style='color:red'>✗ showReportPage method missing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
} catch (Error $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>