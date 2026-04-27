<?php
echo "<h1>Test Page</h1>";
echo "<p>If you can see this, PHP is working and the project is in the right place.</p>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
?>