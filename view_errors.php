<?php
$error_log = __DIR__ . '/error.log';
$xampp_error_log = 'C:\xampp\php\logs\php_error_log';

echo "<h2>PHP Error Log Viewer</h2>";

// Check our custom error log
echo "<h3>Custom Error Log:</h3>";
if (file_exists($error_log)) {
    echo "<p>File: $error_log</p>";
    echo "<p>Size: " . filesize($error_log) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($error_log)) . "</p>";
    
    $content = file_get_contents($error_log);
    if (empty($content)) {
        echo "<p style='color:green'>No errors in custom log</p>";
    } else {
        echo "<pre style='background:#f4f4f4; padding:10px; max-height:400px; overflow:auto;'>";
        echo htmlspecialchars($content);
        echo "</pre>";
    }
} else {
    echo "<p>Custom error log not found. Will be created when errors occur.</p>";
    // Try to create it
    file_put_contents($error_log, "Error log created at " . date('Y-m-d H:i:s') . "\n");
    echo "<p>Created error log file.</p>";
}

// Check XAMPP error log
echo "<h3>XAMPP PHP Error Log:</h3>";
if (file_exists($xampp_error_log)) {
    echo "<p>File: $xampp_error_log</p>";
    echo "<p>Size: " . filesize($xampp_error_log) . " bytes</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($xampp_error_log)) . "</p>";
    
    // Show last 50 lines
    $lines = file($xampp_error_log);
    $last_lines = array_slice($lines, -50);
    echo "<pre style='background:#f4f4f4; padding:10px; max-height:400px; overflow:auto;'>";
    foreach ($last_lines as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    echo "<p>XAMPP error log not found at $xampp_error_log</p>";
}
?>