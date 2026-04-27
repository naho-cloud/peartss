<?php
// Start session
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') {
        header('Location: views/admin/dashboard.php');
        exit();
    } elseif ($_SESSION['user_role'] == 'security') {
        header('Location: views/security/dashboard.php');
        exit();
    } elseif (in_array($_SESSION['user_role'], ['student', 'staff'])) {
        header('Location: views/user/dashboard.php');
        exit();
    }
}

// Check for flash message
$flash_message = '';
$flash_type = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message']);
    unset($_SESSION['flash_type']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Portal - PEARTS | Jimma University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 0;
        }
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }
        .hero-section {
            padding: 80px 0;
            text-align: center;
            color: white;
        }
        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }
        .btn-login {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        .feature-card {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 1.8rem;
        }
        .footer {
            background: rgba(0,0,0,0.2);
            padding: 30px 0;
            text-align: center;
            color: rgba(255,255,255,0.7);
            margin-top: 50px;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero-title, .hero-subtitle {
            animation: fadeInUp 0.8s ease;
        }
        .alert-custom {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="security-portal.php">
                <i class="bi bi-shield-lock"></i> PEARTS Security
            </a>
        </div>
    </nav>

    <!-- Flash Message Display -->
    <?php if ($flash_message): ?>
    <div class="alert alert-<?php echo $flash_type; ?> alert-dismissible fade show alert-custom" role="alert">
        <i class="bi bi-<?php echo $flash_type == 'success' ? 'check-circle' : 'info-circle'; ?>"></i>
        <?php echo htmlspecialchars($flash_message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="hero-title">Security Portal</h1>
                    <p class="hero-subtitle mb-4">Asset Scanning, Entry/Exit Tracking, and Found Items Management</p>
                    <a href="views/auth/security_login.php" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i> Login to Security Portal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-qr-code-scan"></i></div>
                    <h4>Scan QR Code</h4>
                    <p>Quickly scan asset QR codes to verify ownership</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-check-circle"></i></div>
                    <h4>Entry/Exit Tracking</h4>
                    <p>Record entry and exit of assets at campus gates</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-search"></i></div>
                    <h4>Found Items</h4>
                    <p>Report and manage found items, notify owners</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Jimma University - PEARTS. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alert after 3 seconds
        setTimeout(function() {
            var alert = document.querySelector('.alert-custom');
            if (alert) {
                alert.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>