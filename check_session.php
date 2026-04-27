<?php
session_start();
echo "<h2>Session Data</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Check QR Code Directory</h2>";
$qr_dir = __DIR__ . '/assets/uploads/qrcodes/';
if (is_dir($qr_dir)) {
    $files = scandir($qr_dir);
    echo "<h3>Files in qrcodes directory:</h3>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filepath = $qr_dir . $file;
            echo "$file - " . filesize($filepath) . " bytes<br>";
        }
    }
} else {
    echo "QR directory not found!";
}
?>