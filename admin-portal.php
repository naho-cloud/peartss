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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - PEARTS | Jimma University</title>
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
        .btn-home {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin-right: 15px;
        }
        .btn-home:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
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
        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="admin-portal.php">
                <i class="bi bi-shield-lock"></i> PEARTS Admin
            </a>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="hero-title">Admin Portal</h1>
                    <p class="hero-subtitle mb-4">System Administration, User Management, and Reporting</p>
                    <div class="button-group">
                        <a href="/papi/index.php" class="btn-home">
                            <i class="bi bi-house-door"></i> Back to Home
                        </a>
                        <a href="views/auth/admin_login.php" class="btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Admin Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-people"></i></div>
                    <h4>User Management</h4>
                    <p>Manage all user accounts, roles, and permissions</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-laptop"></i></div>
                    <h4>Asset Management</h4>
                    <p>View and manage all registered assets</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi bi-file-text"></i></div>
                    <h4>Reports & Analytics</h4>
                    <p>Generate comprehensive system reports</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Jimma University - PEARTS. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>