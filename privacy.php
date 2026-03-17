<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <title>Privacy Policy | Stockara Tech</title>
    <meta name="description" content="Read Stockara's privacy policy to understand how we collect, use, and protect your business and personal data.">
    
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
            background-color: var(--light);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .navbar {
            padding: 15px 0;
            background: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.05);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }

        .page-header {
            background: var(--gradient);
            padding: 100px 0 60px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://www.transparenttextures.com/patterns/cubes.png');
            opacity: 0.1;
        }

        .content-card {
            background: #fff;
            padding: 60px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.05);
            margin-top: -60px;
            position: relative;
            z-index: 2;
        }

        h3 {
            font-weight: 700;
            color: var(--primary);
            margin-top: 40px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        h3 i {
            font-size: 1.2rem;
            margin-right: 15px;
            background: var(--light);
            padding: 10px;
            border-radius: 10px;
        }

        p, li {
            font-size: 1.05rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .legal-list {
            padding-left: 20px;
            list-style: none;
        }

        .legal-list li {
            position: relative;
            padding-left: 30px;
        }

        .legal-list li::before {
            content: "\f058";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            position: absolute;
            left: 0;
            color: var(--secondary);
        }

        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
        }

        .btn-primary-custom {
            background: var(--gradient);
            color: #fff;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            color: #fff;
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        @media (max-width: 768px) {
            .content-card {
                padding: 30px;
                border-radius: 20px;
            }
            .page-header {
                padding: 80px 0 40px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>index.php">
                <img src="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" alt="Stockara" height="35" class="me-2 rounded shadow-sm">
                Stockara
            </a>
            <div class="ms-auto">
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary-custom btn-sm">
                    <i class="fas fa-home me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <header class="page-header text-center">
        <div class="container" data-aos="fade-down">
            <h1 class="display-4 fw-800 mb-3">Privacy Policy</h1>
            <p class="lead opacity-75">Your privacy and data security are our top priority.</p>
            <p class="small opacity-50">Last updated: March 17, 2026</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mb-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="content-card" data-aos="fade-up">
                    <div class="mb-5 text-center px-lg-5">
                        <i class="fas fa-user-shield fa-4x text-primary mb-4"></i>
                        <p class="lead">At Stockara, we understand that for business owners, record-keeping and data privacy are non-negotiable. This policy explains how we handle your business information with the care and security it deserves.</p>
                    </div>

                    <div class="legal-section">
                        <h3><i class="fas fa-database"></i>1. Information Collection</h3>
                        <p>We collect information that you provide directly to us when you create an account, such as your business name, email address, and payment information. We also collect the data you enter into Stockara, including:</p>
                        <ul class="legal-list">
                            <li>Product details and inventory levels.</li>
                            <li>Sales transactions and customer records.</li>
                            <li>Service records and repair job details.</li>
                            <li>Staff accounts and activity logs.</li>
                        </ul>

                        <h3><i class="fas fa-cog"></i>2. Use of Information</h3>
                        <p>We use the collected information strictly to provide and improve our services. This includes:</p>
                        <ul class="legal-list">
                            <li>Providing access to your business dashboard.</li>
                            <li>Processing your subscription payments via Paystack.</li>
                            <li>Providing technical support and troubleshooting.</li>
                            <li>Sending critical updates about system performance.</li>
                        </ul>

                        <h3><i class="fas fa-lock"></i>3. Data Security & Encryption</h3>
                        <p>Security is baked into the foundation of Stockara. We implement enterprise-grade security measures:</p>
                        <p>Your business data is <strong>encrypted at rest and in transit</strong>. We use secure cloud storage and industry-standard protocols to ensure that only authorized users can access your sensitive records.</p>

                        <h3><i class="fas fa-handshake-slash"></i>4. Third-Party Sharing</h3>
                        <p>We do not sell your data. We only share information with trusted third-party providers (like Paystack for payments) who are strictly required to operate our services and have agreed to maintain confidentiality.</p>

                        <h3><i class="fas fa-user-edit"></i>5. Your Rights & Data Access</h3>
                        <p>You have full ownership of your data. You can export your records at any time through the reporting module. If you decide to cancel your service, you can request a dedicated data purge through our support team.</p>

                        <h3><i class="fas fa-cookie-bite"></i>6. Cookies & Tracking</h3>
                        <p>We use essential cookies to maintain your login session and store interface preferences (like dark mode). These are not used for targeted advertising.</p>

                        <div class="alert alert-primary mt-5 rounded-4 p-4 border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">Have Questions?</h5>
                                    <p class="mb-0 small">If you have any questions about how we handle your data, please contact our privacy team at <a href="mailto:privacy@stockara.toamultitech.tech" class="fw-bold">privacy@stockara.toamultitech.tech</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="pt-5 pb-3">
        <div class="container">
            <div class="row g-4 text-center text-lg-start">
                <div class="col-lg-3">
                    <h5 class="fw-800 text-white mb-4">Stockara</h5>
                    <p class="text-white-50">Helping businesses stay organized and grow with confidence.</p>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Platform</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>index.php#about" class="text-white-50 text-decoration-none small">About</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>index.php#features" class="text-white-50 text-decoration-none small">Features</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>index.php#pricing" class="text-white-50 text-decoration-none small">Pricing</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Support</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>faq.php" class="text-white-50 text-decoration-none small">Help & FAQ</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php" class="text-white-50 text-decoration-none small">Contact Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>terms.php" class="text-white-50 text-decoration-none small">Terms of Service</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>privacy.php" class="text-white-50 text-decoration-none small fw-bold text-white">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 opacity-10">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 text-white-50">
                <p class="small mb-0">&copy; 2026 Stockara Tech. All rights reserved.</p>
                <div class="mt-3 mt-md-0 d-flex gap-2">
                    <img src="https://checkout.paystack.com/assets/img/pstk-badge.png" alt="Paystack" height="25">
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init({ duration: 800, once: true });
        });
    </script>
</body>
</html>
