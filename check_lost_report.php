<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>LostReport File Check</h2>";

$file_path = __DIR__ . '/models/LostReport.php';
echo "<p>Checking: $file_path</p>";

if (file_exists($file_path)) {
    echo "<p style='color:green'>✓ File exists</p>";
    
    // Check file permissions
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($file_path)), -4) . "</p>";
    
    // Read first few lines to check content
    $content = file_get_contents($file_path);
    $first_lines = substr($content, 0, 500);
    
    echo "<h3>First 500 characters of file:</h3>";
    echo "<pre>" . htmlspecialchars($first_lines) . "</pre>";
    
    // Check if class exists in file
    if (strpos($content, 'class LostReport') !== false) {
        echo "<p style='color:green'>✓ Class 'LostReport' found in file</p>";
    } else {
        echo "<p style='color:red'>✗ Class 'LostReport' NOT found in file</p>";
    }
    
    // Try to include the file
    echo "<h3>Testing inclusion:</h3>";
    try {
        require_once $file_path;
        echo "<p style='color:green'>✓ File included successfully</p>";
        
        if (class_exists('LostReport')) {
            echo "<p style='color:green'>✓ Class 'LostReport' exists after inclusion</p>";
        } else {
            echo "<p style='color:red'>✗ Class 'LostReport' does NOT exist after inclusion</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>Error including file: " . $e->getMessage() . "</p>";
    } catch (Error $e) {
        echo "<p style='color:red'>Error including file: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color:red'>✗ File does NOT exist</p>";
}

// Check other model files
echo "<h3>Other Model Files:</h3>";
$other_models = [
    'Asset.php',
    'User.php',
    'GeneralLostItem.php',
    'FoundReport.php',
    'GeneralFoundItem.php'
];

foreach ($other_models as $model) {
    $model_path = __DIR__ . '/models/' . $model;
    if (file_exists($model_path)) {
        echo "<p>✓ $model exists</p>";
    } else {
        echo "<p style='color:red'>✗ $model does NOT exist</p>";
    }
}
?>