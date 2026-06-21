<?php 
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}
?>
<?php
$stmt = $pdo->prepare("SELECT * FROM subscription_plans  ORDER BY price ASC");
$stmt->execute();
$plans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <!-- Primary Meta Tags -->
    <title>Stockara | Manage Products, Sales & Services in One Place</title>
    <meta name="title" content="Stockara | All-in-One Inventory, POS & Service Management System">
    <meta name="description" content="Manage your products, sales, and services from a single powerful system. Stockara helps businesses stay organized, improve efficiency, and grow with confidence.">
    <meta name="keywords" content="Stockara, inventory management system, POS software, service record system, business management tool, barcode scanner POS, repair shop software, pharmacy inventory, warehouse management">
    <meta name="author" content="Stockara Tech">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://getstockara.com.ng/">
    <meta property="og:title" content="Stockara | Modern Inventory & POS">
    <meta property="og:description" content="Streamline your sales and services with our cloud-ready management platform.">
    <meta property="og:image" content="<?php echo BASE_URL; ?>assest/img/stockara.jpg">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --primary: #4e73df;
            --primary-dark: #224abe;
            --secondary: #1cc88a;
            --dark: #1a1c23;
            --light: #f8f9fc;
            --gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        html, body {
            font-family: 'Outfit', sans-serif;
            color: #444;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        
        /* Prevent AOS related horizontal overflow */
        .main-wrapper {
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }

        /* Navbar */
        .navbar {
            padding: 20px 0;
            transition: all 0.3s;
            background: transparent;
        }
        .navbar.scrolled {
            background: #fff;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 10px 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        .nav-link {
            font-weight: 600;
            margin: 0 10px;
            color: #333 !important;
        }

        /* Hero Section */
        .hero {
            padding: 160px 0 100px;
            background: linear-gradient(rgba(255,255,255,0.92), rgba(255,255,255,0.92)), url('https://images.unsplash.com/photo-1556740734-7f9a2b7a0f4d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 25px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 35px;
            max-width: 600px;
        }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            .section-title h2 {
                font-size: 1.8rem;
            }
            .hero {
                padding: 120px 0 60px;
                text-align: center;
            }
            .hero p {
                margin-left: auto;
                margin-right: auto;
            }
            .hero .d-flex {
                justify-content: center;
            }
            .navbar-collapse {
                border-radius: 15px;
                margin-top: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            }
        }

        /* Features Section */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-title h2 {
            font-weight: 800;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .feature-card {
            padding: 40px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            transition: 0.3s;
            height: 100%;
            border-bottom: 4px solid transparent;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            border-bottom: 4px solid var(--primary);
        }
        .feature-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        /* Stats Section */
        .stats-section {
            background: var(--gradient);
            padding: 80px 0;
            color: #fff;
        }
        .stat-item h3 {
            font-size: 3rem;
            font-weight: 800;
        }

        /* Mission Section */
        .mission-section {
            padding: 100px 0;
            background: #fff;
        }

        /* Footer */
        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
        }
        footer h5 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .btn-primary-custom {
            background: var(--gradient);
            color: #fff;
            padding: 14px 35px;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            transition: transform 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            color: #fff;
        }
        
        /* Mobile App Section */
        .mobile-app-section {
            background: #fff;
            padding: 100px 0;
            overflow: hidden;
        }
        .app-badge-btn {
            background: #000;
            color: #fff;
            padding: 12px 25px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: 0.3s;
        }
        .app-badge-btn:hover {
            background: #333;
            color: #fff;
            transform: scale(1.05);
        }
        .mobile-preview-img {
            max-width: 100%;
            height: auto;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(78, 115, 223, 0.2);
        }
    </style>
</head>
<body>
    <div class="main-wrapper">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Stockara" height="40" class="me-2 rounded shadow-sm">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse bg-white px-3 py-2" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="#download" class="text-primary fw-bold">Mobile App</a></li>
                    <li class="nav-item"><a class="nav-link" href="#why-us">Why Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>auth/login.php">Login</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary-custom btn-sm py-2 px-4 shadow">Get Started</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right">
                    <span class="badge bg-primary px-3 py-2 mb-3">About Stockara</span>
                    <h1>Manage Products, Sales & Services in One Place</h1>
                    <p>Stockara is a modern web-based business management platform designed to help you efficiently manage your inventory, sales, and services from a single powerful system.</p>
                    <p class="mb-4">Built with simplicity and flexibility in mind, Stockara enables businesses of all sizes to organize their operations, track products, record services, and monitor revenue with ease.</p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary-custom shadow-lg">Start Your Journey</a>
                        <a href="#features" class="btn btn-outline-primary btn-lg rounded-pill fw-bold py-3 px-4" style="text-decoration: none;">What Stockara Does</a>
                    </div>
                </div>
                <div class="col-lg-5 mt-5 mt-lg-0" data-aos="zoom-in">
                    <img src="https://images.unsplash.com/photo-1556742044-3c52d6e88c62?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80" alt="Stockara Platform" class="img-fluid rounded-4 shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- What Stockara Does -->
    <section class="py-5" id="features">
        <div class="container py-5">
            <div class="section-title" data-aos="fade-up">
                <h2>What Stockara Does</h2>
                <p class="text-muted mx-auto" style="max-width: 800px;">Stockara combines inventory management, point-of-sale (POS), and service tracking into one integrated platform. This allows businesses to handle both product sales and service records without needing multiple systems.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <i class="fas fa-sync-alt"></i>
                        <h4>Real-Time Inventory</h4>
                        <p>Manage and track your inventory levels in real time to ensure you never run out of critical items.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-barcode"></i>
                        <h4>Barcode Scanning</h4>
                        <p>Generate and scan product barcodes for faster sales transactions and improved accuracy.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <i class="fas fa-hand-holding-heart"></i>
                        <h4>Service Management</h4>
                        <p>Record and manage services provided to customers, from repairs to installation services.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <i class="fas fa-wallet"></i>
                        <h4>Revenue Tracking</h4>
                        <p>Detailed tracking of both sales and service revenue for a complete financial overview.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-history"></i>
                        <h4>Transaction History</h4>
                        <p>Maintain detailed customer records and full transaction history for both products and services.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <i class="fas fa-bell"></i>
                        <h4>Stock Alerts</h4>
                        <p>Monitor stock levels automatically and receive low-stock alerts before items disappear.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <h2 class="fw-800 mb-4">Our Mission</h2>
                    <p class="lead">Our mission is to provide businesses with a simple, reliable, and powerful platform that makes managing products, sales, and services easier.</p>
                    <p>Stockara helps businesses save time, reduce errors, and gain better control over their operations. Designed to work for any industry, from retail stores and pharmacies to computer repair centers and electronics dealers.</p>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="p-5 bg-white rounded-4 shadow-sm">
                        <h4 class="mb-4 text-primary">Designed for Modern Businesses</h4>
                        <p>Its clean and easy-to-use interface allows business owners and staff to quickly learn and operate the system without technical expertise.</p>
                        <ul class="list-unstyled mt-3">
                            <li><i class="fas fa-check text-success me-2"></i> Retail Stores & Supermarkets</li>
                            <li><i class="fas fa-check text-success me-2"></i> Electronics & Phone Shops</li>
                            <li><i class="fas fa-check text-success me-2"></i> Repair & Service Centers</li>
                            <li><i class="fas fa-check text-success me-2"></i> Pharmacies & Fashion Stores</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Pricing Section -->
    <section class="py-5 bg-white" id="pricing">
        <div class="container py-5">
            <div class="section-title" data-aos="fade-up">
                <h2>Simple & Transparent Pricing</h2>
                <p class="text-muted">Choose the plan that best fits your business needs. All accounts start with a One Year free trial.</p>
            </div>
            
            <div class="row g-4 justify-content-center">
                <?php 
                $delay = 100;
                    foreach($plans as $p): 
                        ?>
                        <!-- Trial Plan -->
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?php echo $delay; ?>">
                            <div class="p-4 rounded-4 border bg-light h-100 d-flex flex-column text-center">
                                <h4 class="fw-bold"><?php echo $p['name']; ?></h4>
                                <h2 class="text-primary fw-800">₦<?php echo number_format($p['price'], 2); ?><small class="text-muted" style="font-size: 0.5em;"><?php if($p['price'] == 0) echo '/ One Year'; ?></small></h2>
                                <hr>
                                <ul class="list-unstyled text-start small flex-grow-1">
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['max_products'] == -1 ? 'Unlimited' : $p['max_products']; ?> Products</li>
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['max_users'] == -1 ? 'Unlimited' : $p['max_users']; ?> Users</li>
                                    <li><i class="fas fa-check text-success me-2"></i> <?php echo $p['description']; ?></li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> POS & Inventory</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Service Tracking</li>
                                </ul>
                                <a href="<?php echo BASE_URL; ?>auth/register.php?plan_id=<?php echo $p['id']; ?>" class="btn btn-outline-primary rounded-pill mt-4 fw-bold"><?php echo $p['price'] == 0 ? 'Start Free Trial' : 'Choose Plan'; ?></a>
                            </div>
                        </div>
                        <?php 
                        $delay += 100;
                    endforeach; 
                ?>
           
                
            </div>
        </div>
    </section>

    <!-- Mobile App Section -->
    <section class="mobile-app-section py-5 bg-light" id="download">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="badge bg-primary px-3 py-2 mb-3">Stockara Mobile</span>
                    <h2 class="fw-800 mb-4 h1">Manage Your Business on the Go</h2>
                    <p class="lead mb-4">Take your sales and services anywhere. Our mobile app allows you to record sales, manage services, and track inventory directly from your smartphone.</p>
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <a href="<?php echo BASE_URL; ?>app/stockara.apk" class="app-badge-btn shadow-lg">
                            <i class="fab fa-android fa-2x me-3"></i>
                            <div class="text-start">
                                <small class="d-block" style="font-size: 10px; opacity: 0.7;">Download for</small>
                                <span class="fw-bold">Android APK</span>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="col-lg-5 mt-5 mt-lg-0 text-center" data-aos="fade-left">
                    <img src="<?php echo BASE_URL; ?>assest/img/stockara-mobile.jpeg" alt="Stockara Mobile App" class="mobile-preview-img mb-4" style="max-height: 550px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Stockara -->
    <section class="py-5 bg-white" id="why-us">
        <div class="container py-5">
            <div class="section-title" data-aos="fade-up">
                <h2>Why Choose Stockara</h2>
                <p>Stay organized, improve efficiency, and grow with confidence.</p>
            </div>
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="stat-item p-4" data-aos="zoom-in" data-aos-delay="100">
                        <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                        <h5>All-in-One System</h5>
                        <p class="small">Unified inventory and service management.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item p-4" data-aos="zoom-in" data-aos-delay="200">
                        <i class="fas fa-mouse-pointer fa-3x text-primary mb-3"></i>
                        <h5>Easy to Use</h5>
                        <p class="small">Designed for everyday business operations.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item p-4" data-aos="zoom-in" data-aos-delay="300">
                        <i class="fas fa-bolt fa-3x text-primary mb-3"></i>
                        <h5>Fast POS</h5>
                        <p class="small">Lightning-fast barcode-based sales system.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item p-4" data-aos="zoom-in" data-aos-delay="400">
                        <i class="fas fa-lightbulb fa-3x text-primary mb-3"></i>
                        <h5>Smart Insights</h5>
                        <p class="small">Detailed reporting for better decisions.</p>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-primary-custom btn-lg px-5">Get Started with Stockara Today</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pt-5 pb-3">
        <div class="container">
            <div class="row g-4 text-center text-lg-start">
                <div class="col-lg-3">
                    <h5 class="fw-800 text-white mb-4">
                        <img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Stockara" height="30" class="me-2 rounded">
                        Stockara
                    </h5>
                    <p class="text-white-50">Helping businesses stay organized, improve efficiency, and grow with confidence. Your all-in-one business management partner.</p>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Platform</h6>
                    <ul class="list-unstyled">
                        <li><a href="#about" class="text-white-50 text-decoration-none small">About</a></li>
                        <li><a href="#features" class="text-white-50 text-decoration-none small">What We Do</a></li>
                        <li><a href="#pricing" class="text-white-50 text-decoration-none small">Pricing</a></li>
                        <li><a href="#download" class="text-white-50 text-decoration-none small">Mobile App</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>faq.php" class="text-white-50 text-decoration-none small">Help & FAQ</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact.php" class="text-white-50 text-decoration-none small">Contact Support</a></li>
                        <li><a href="<?php echo BASE_URL; ?>auth/register.php" class="text-white-50 text-decoration-none small">Register</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Legal</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>terms.php" class="text-white-50 text-decoration-none small">Terms of Service</a></li>
                        <li><a href="<?php echo BASE_URL; ?>privacy.php" class="text-white-50 text-decoration-none small">Privacy Policy</a></li>
                    </ul>
                    <div class="d-flex justify-content-center justify-content-lg-start gap-3 mt-3">
                        <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin"></i></a>
                        <a href="mailto:support@stockara.com" class="text-white-50"><i class="fas fa-envelope"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 opacity-10">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3">
                <p class="small mb-0 text-white-50">&copy; 2026 Stockara. All rights reserved.</p>
                <div class="mt-3 mt-md-0">
                    <img src="https://checkout.paystack.com/assets/img/pstk-badge.png" alt="Paystack" height="25">
                </div>
            </div>
        </div>
    </footer>

    </div> <!-- End Main Wrapper -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init({ duration: 800, once: true });
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('#mainNav').addClass('scrolled');
                } else {
                    $('#mainNav').removeClass('scrolled');
                }
            });
        });
    </script>
</body>
</html>
