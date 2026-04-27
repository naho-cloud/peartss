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

// Include database connection
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

// Get database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    $db = null;
}

// Get public lost items for users
$public_lost_items = [];
$public_general_lost = [];
$public_found_items = [];

if ($db) {
    try {
        $lost_query = "SELECT l.*, a.brand, a.model, a.serial_number, a.asset_image,
                       u.full_name as owner_name
                       FROM lost_reports l
                       JOIN assets a ON l.asset_id = a.asset_id
                       JOIN users u ON a.user_id = u.user_id
                       WHERE l.status IN ('pending', 'investigating') 
                       AND l.public_visibility = 1
                       ORDER BY l.reported_date DESC
                       LIMIT 6";
        $lost_stmt = $db->prepare($lost_query);
        $lost_stmt->execute();
        $public_lost_items = $lost_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $public_lost_items = [];
        error_log("Error fetching asset lost items: " . $e->getMessage());
    }

    // FIXED: Query from 'lost_items' table instead of 'general_lost_items'
    try {
        $general_query = "SELECT * FROM lost_items 
                         WHERE lost_type = 'general'
                         AND status IN ('pending', 'approved')
                         AND public_visibility = 1
                         ORDER BY reported_date DESC
                         LIMIT 6";
        $general_stmt = $db->prepare($general_query);
        $general_stmt->execute();
        $public_general_lost = $general_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $public_general_lost = [];
        error_log("Error fetching general lost items: " . $e->getMessage());
    }

    try {
        $found_query = "SELECT f.*, a.brand, a.model, a.serial_number, a.asset_image
                        FROM found_reports f
                        JOIN assets a ON f.asset_id = a.asset_id
                        WHERE f.status = 'pending'
                        ORDER BY f.found_date DESC
                        LIMIT 6";
        $found_stmt = $db->prepare($found_query);
        $found_stmt->execute();
        $public_found_items = $found_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $public_found_items = [];
        error_log("Error fetching found items: " . $e->getMessage());
    }
}

// Get statistics
$stats = [
    'total_assets' => 0,
    'active_users' => 0,
    'items_found' => 0,
    'recovery_rate' => 0
];

if ($db) {
    try {
        $stats_stmt = $db->query("SELECT COUNT(*) as count FROM assets");
        $stats['total_assets'] = $stats_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stats_stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE status = 'active' AND role IN ('student', 'staff')");
        $stats['active_users'] = $stats_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stats_stmt = $db->query("SELECT COUNT(*) as count FROM found_reports WHERE status = 'claimed'");
        $stats['items_found'] = $stats_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $total_lost = $db->query("SELECT COUNT(*) as count FROM lost_reports")->fetch(PDO::FETCH_ASSOC)['count'];
        if ($total_lost > 0) {
            $stats['recovery_rate'] = round(($stats['items_found'] / $total_lost) * 100);
        }
    } catch (PDOException $e) {
        // Ignore errors
        error_log("Error fetching stats: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Portal - PEARTS | Jimma University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Navbar */
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

        .navbar-brand i {
            margin-right: 8px;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white;
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0;
            text-align: center;
            color: white;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
            animation: fadeInUp 0.8s ease;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 30px;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Section */
        .stats-section {
            padding: 40px 0;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin-bottom: 50px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
        }

        .stat-label {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Feature Cards */
        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            margin-bottom: 40px;
            position: relative;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: white;
            border-radius: 3px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
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

        .feature-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }

        .feature-desc {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.5;
        }

        /* Lost Items Section */
        .lost-items-section {
            padding: 50px 0;
        }

        .lost-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            transition: all 0.3s;
            cursor: pointer;
            border-left: 4px solid #667eea;
        }

        .lost-card:hover {
            transform: translateX(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .general-card {
            border-left-color: #667eea;
        }

        .found-card {
            border-left-color: #667eea;
        }

        .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card-info {
            font-size: 0.75rem;
            color: #666;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-info i {
            width: 18px;
            color: #667eea;
        }

        .btn-login {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            color: #667eea;
        }

        .btn-register {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-register:hover {
            background: white;
            color: #667eea;
            transform: translateY(-2px);
        }

        /* Footer */
        .footer {
            background: rgba(0, 0, 0, 0.2);
            padding: 30px 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 50px;
        }

        /* Particle Background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            .stat-number {
                font-size: 1.8rem;
            }
            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="user-portal.php">
                <i class="bi bi-shield-lock"></i> PEARTS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#lost-items">Lost Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#found-items">Found Items</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="hero-title">Welcome to PEARTS</h1>
                    <p class="hero-subtitle">Personal Electronic Asset Registration and Tracking System</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="views/auth/login.php" class="btn-login">
                            <i class="bi bi-box-arrow-in-right"></i> Login to Account
                        </a>
                        <a href="views/auth/register.php" class="btn-register">
                            <i class="bi bi-person-plus"></i> Register Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <div class="container">
        <div class="stats-section">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['total_assets']); ?>+</div>
                        <div class="stat-label">Registered Assets</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['active_users']); ?>+</div>
                        <div class="stat-label">Active Users</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($stats['items_found']); ?>+</div>
                        <div class="stat-label">Items Found</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['recovery_rate']; ?>%</div>
                        <div class="stat-label">Recovery Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container" id="features">
        <h2 class="section-title">How It Works</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card" onclick="window.location.href='views/auth/register.php'">
                    <div class="feature-icon">
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <h3 class="feature-title">1. Register Account</h3>
                    <p class="feature-desc">Create your account using your university ID and email</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card" onclick="window.location.href='views/auth/login.php'">
                    <div class="feature-icon">
                        <i class="bi bi-laptop"></i>
                    </div>
                    <h3 class="feature-title">2. Register Asset</h3>
                    <p class="feature-desc">Register your laptop, tablet, or smartphone with photos</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card" onclick="window.location.href='views/auth/login.php'">
                    <div class="feature-icon">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h3 class="feature-title">3. Get QR Code</h3>
                    <p class="feature-desc">Receive unique QR code for your device</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lost Items Section -->
    <div class="container lost-items-section" id="lost-items">
        <h2 class="section-title">Recently Lost Items</h2>
        <?php if (empty($public_lost_items) && empty($public_general_lost)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-white-50"></i>
                <p class="text-white-50 mt-2">No lost items reported publicly</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($public_lost_items as $item): ?>
                    <div class="col-md-6">
                        <div class="lost-card" onclick="window.location.href='views/lost/public_view.php?id=<?php echo $item['lost_id']; ?>&type=asset'">
                            <div class="d-flex justify-content-between">
                                <div class="card-title"><?php echo htmlspecialchars($item['brand'] . ' ' . $item['model']); ?></div>
                                <span class="badge bg-warning"><?php echo ucfirst($item['status']); ?></span>
                            </div>
                            <div class="card-info">
                                <i class="bi bi-upc-scan"></i>
                                <span>Serial: <?php echo htmlspecialchars($item['serial_number']); ?></span>
                            </div>
                            <div class="card-info">
                                <i class="bi bi-geo-alt"></i>
                                <span>Last seen: <?php echo htmlspecialchars($item['last_seen_location']); ?></span>
                            </div>
                            <div class="card-info">
                                <i class="bi bi-calendar"></i>
                                <span>Reported: <?php echo date('d M Y', strtotime($item['reported_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php foreach ($public_general_lost as $item): ?>
                    <div class="col-md-6">
                        <div class="lost-card general-card" onclick="window.location.href='views/lost/public_view.php?id=<?php echo $item['id']; ?>&type=general'">
                            <div class="d-flex justify-content-between">
                                <div class="card-title"><?php echo htmlspecialchars($item['item_type']); ?></div>
                                <span class="badge bg-warning"><?php echo ucfirst($item['status']); ?></span>
                            </div>
                            <div class="card-info">
                                <i class="bi bi-info-circle"></i>
                                <span><?php echo htmlspecialchars(substr($item['item_description'], 0, 40)); ?></span>
                            </div>
                            <div class="card-info">
                                <i class="bi bi-geo-alt"></i>
                                <span>Last seen: <?php echo htmlspecialchars($item['last_seen_location']); ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Found Items Section -->
    <?php if (!empty($public_found_items)): ?>
    <div class="container" id="found-items">
        <h2 class="section-title">Recently Found Items</h2>
        <div class="row">
            <?php foreach ($public_found_items as $item): ?>
                <div class="col-md-6">
                    <div class="lost-card found-card" onclick="window.location.href='views/found/public_view.php?id=<?php echo $item['found_id']; ?>'">
                        <div class="card-title"><?php echo htmlspecialchars($item['brand'] . ' ' . $item['model']); ?></div>
                        <div class="card-info">
                            <i class="bi bi-upc-scan"></i>
                            <span>Serial: <?php echo htmlspecialchars($item['serial_number']); ?></span>
                        </div>
                        <div class="card-info">
                            <i class="bi bi-geo-alt"></i>
                            <span>Found at: <?php echo htmlspecialchars($item['found_location']); ?></span>
                        </div>
                        <div class="card-info">
                            <i class="bi bi-building"></i>
                            <span>Stored at: <?php echo htmlspecialchars($item['storage_location'] ?? 'Security Office'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 Jimma University - PEARTS. All rights reserved.</p>
            <p class="mt-2">
                <a href="#" class="text-white-50 text-decoration-none">About</a> &nbsp;|&nbsp;
                <a href="#" class="text-white-50 text-decoration-none">Privacy</a> &nbsp;|&nbsp;
                <a href="#" class="text-white-50 text-decoration-none">Contact</a>
            </p>
        </div>
    </footer>

    <script>
        // Particle Background Animation
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                
                const size = Math.random() * 6 + 2;
                const left = Math.random() * 100;
                const duration = Math.random() * 15 + 8;
                const delay = Math.random() * 10;
                
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = left + '%';
                particle.style.animationDuration = duration + 's';
                particle.style.animationDelay = delay + 's';
                particle.style.opacity = Math.random() * 0.4 + 0.1;
                
                particlesContainer.appendChild(particle);
            }
        }
        
        createParticles();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>