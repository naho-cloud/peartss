<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set test session
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Test User';
$_SESSION['user_role'] = 'student';
$_SESSION['user_email'] = 'test@example.com';

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Lost Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test Lost Item Submission</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Test General Item Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="controllers/LostController.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="lost_type" value="general">
                            <input type="hidden" name="item_type" value="Test Item">
                            <input type="hidden" name="item_description" value="Test Description">
                            <input type="hidden" name="last_seen_location" value="Test Location">
                            
                            <button type="submit" name="report_lost" class="btn btn-primary">Submit Test General Item</button>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5>Test Asset Form</h5>
                    </div>
                    <div class="card-body">
                        <form action="controllers/LostController.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="lost_type" value="asset">
                            <input type="hidden" name="asset_id" value="1">
                            <input type="hidden" name="last_seen_location" value="Test Location">
                            
                            <button type="submit" name="report_lost" class="btn btn-success">Submit Test Asset</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5>Session Info</h5>
                    </div>
                    <div class="card-body">
                        <pre><?php print_r($_SESSION); ?></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="views/lost/report.php" class="btn btn-warning">Go to Lost Report Page</a>
            <a href="view_errors.php" class="btn btn-secondary">View Error Log</a>
        </div>
    </div>
</body>
</html>