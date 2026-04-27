<?php
$base_path = __DIR__;

// Create images directory
$images_dir = $base_path . '/assets/images';
if (!file_exists($images_dir)) {
    mkdir($images_dir, 0777, true);
    echo "<p>Created images directory</p>";
}

// Create a simple default asset image using base64
$default_asset = $images_dir . '/default-asset.png';
if (!file_exists($default_asset)) {
    // Create a simple colored PNG using GD if available
    if (extension_loaded('gd')) {
        $im = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($im, 102, 126, 234); // #667eea
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg);
        imagestring($im, 5, 50, 90, 'No Image', $white);
        imagepng($im, $default_asset);
        imagedestroy($im);
        echo "<p>Created default asset image</p>";
    }
}

// Create default user image
$default_user = $images_dir . '/default-user.png';
if (!file_exists($default_user)) {
    if (extension_loaded('gd')) {
        $im = imagecreatetruecolor(200, 200);
        $bg = imagecolorallocate($im, 118, 75, 162); // #764ba2
        $white = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $bg);
        imagestring($im, 5, 40, 90, 'No Photo', $white);
        imagepng($im, $default_user);
        imagedestroy($im);
        echo "<p>Created default user image</p>";
    }
}

echo "<h3>Default images created successfully!</h3>";
echo "<p>Asset image: assets/images/default-asset.png</p>";
echo "<p>User image: assets/images/default-user.png</p>";
?>