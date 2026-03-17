<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <title>Frequently Asked Questions | Stockara Tech</title>
    <meta name="description" content="Find answers to common questions about Stockara inventory, POS, and service management.">
    
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

        .accordion-item {
            border: none;
            margin-bottom: 15px;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
            transition: 0.3s;
        }

        .accordion-item:hover {
            box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .accordion-button {
            padding: 25px;
            font-weight: 700;
            color: var(--dark);
            border-radius: 20px !important;
            font-size: 1.1rem;
        }

        .accordion-button:not(.collapsed) {
            background-color: #fff;
            color: var(--primary);
            box-shadow: none;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-body {
            padding: 0 25px 30px;
            color: #666;
            line-height: 1.8;
            font-size: 1rem;
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

        footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
        }

        @media (max-width: 768px) {
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
            <h1 class="display-4 fw-800 mb-3">Frequently Asked Questions</h1>
            <p class="lead opacity-75">Got questions? We've got answers.</p>
        </div>
    </header>

    <!-- FAQ Content -->
    <div class="container mb-5 pb-5">
        <div class="row justify-content-center" style="margin-top: -40px;">
            <div class="col-lg-9">
                <div class="accordion" id="faqAccordion" data-aos="fade-up">
                    
                    <!-- Q1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#q1">
                                <i class="fas fa-question-circle text-primary me-3"></i> What exactly does Stockara do?
                            </button>
                        </h2>
                        <div id="q1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Stockara is a comprehensive business management suite. It allows you to track your <strong>inventory</strong> (products), process <strong>POS sales</strong>, and manage <strong>service records</strong> (like repairs or installations) all in one dashboard. It's designed specifically for Nigerian businesses that handle both sales and services.
                            </div>
                        </div>
                    </div>

                    <!-- Q2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q2">
                                <i class="fas fa-mobile-alt text-primary me-3"></i> Do I need a computer to use Stockara?
                            </button>
                        </h2>
                        <div id="q2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                No! While Stockara works perfectly on large screens, it is <strong>fully mobile-responsive</strong>. You can manage your entire business from your smartphone or tablet. We even have a dedicated Android app for on-the-go management.
                            </div>
                        </div>
                    </div>

                    <!-- Q3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q3">
                                <i class="fas fa-barcode text-primary me-3"></i> Can I use my own barcode scanner?
                            </button>
                        </h2>
                        <div id="q3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes. Stockara supports standard USB and Bluetooth barcode scanners. If you don't have a scanner, you can use the mobile app's built-in camera scanner to quickly look up products or make sales.
                            </div>
                        </div>
                    </div>

                    <!-- Q4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q4">
                                <i class="fas fa-users-cog text-primary me-3"></i> Can I add staff members with limited access?
                            </button>
                        </h2>
                        <div id="q4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Absolutely. You can create multiple accounts for your staff and assign them specific roles like <strong>Cashier</strong>, <strong>Manager</strong>, or <strong>Technician</strong>. This ensures that only you (the Admin) can see sensitive financial reports.
                            </div>
                        </div>
                    </div>

                    <!-- Q5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q5">
                                <i class="fas fa-credit-card text-primary me-3"></i> How do I pay for my subscription?
                            </button>
                        </h2>
                        <div id="q5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Payments are handled securely through <strong>Paystack</strong>. You can pay via ATM card, bank transfer, or USSD. We offer a 14-day free trial, so you only pay when you're convinced Stockara is right for your business.
                            </div>
                        </div>
                    </div>

                    <!-- Q6 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#q6">
                                <i class="fas fa-lock text-primary me-3"></i> Is my data safe if I lose my phone/laptop?
                            </button>
                        </h2>
                        <div id="q6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes. Stockara is cloud-based. Your data is not stored on your device but on our secure servers. If you lose your device, simply log in from another device and all your records will be there instantly.
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Call to Action -->
                <div class="text-center mt-5 p-5 bg-white rounded-5 shadow-sm" data-aos="zoom-in">
                    <h4 class="fw-bold mb-3">Still have a question?</h4>
                    <p class="text-muted mb-4">Our support team is ready to help you with any technical or billing inquiry.</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-primary-custom px-4">
                            <i class="fas fa-envelope me-2"></i> Message Support
                        </a>
                        <a href="mailto:support@stockara.toamultitech.tech" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                            Email Us Directly
                        </a>
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
                    <p class="text-white-50">Simplified business records for the modern entrepreneur.</p>
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
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>faq.php" class="text-white-50 text-decoration-none small fw-bold text-white">Help & FAQ</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php" class="text-white-50 text-decoration-none small">Contact Support</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-4">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>terms.php" class="text-white-50 text-decoration-none small">Terms of Service</a></li>
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>privacy.php" class="text-white-50 text-decoration-none small">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <hr class="mt-4 opacity-10">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-3 text-white-50">
                <p class="small mb-0">&copy; 2026 Stockara Tech. All rights reserved.</p>
                <div class="mt-3 mt-md-0">
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
