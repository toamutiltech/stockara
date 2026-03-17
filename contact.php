<?php 
require_once 'includes/functions.php'; 
require_once 'includes/db.php';

$message_sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $full_name = clean($_POST['full_name']);
    $email = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);

    if (!empty($full_name) && !empty($email) && !empty($subject) && !empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (full_name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $subject, $message]);
            $message_sent = true;
        } catch (PDOException $e) {
            $error = "Something went wrong. Please try again later.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?php echo BASE_URL; ?>assest/img/stockara-logo.jpg" />
    <title>Contact Support | Stockara Tech</title>
    <meta name="description" content="Get in touch with the Stockara support team for inquiries, bug reports, or feature requests.">
    
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

        .contact-card {
            background: #fff;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.05);
            height: 100%;
            transition: 0.3s;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .contact-icon {
            width: 70px;
            height: 70px;
            background: var(--light);
            color: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 25px;
            transition: 0.3s;
        }

        .contact-card:hover .contact-icon {
            background: var(--primary);
            color: #fff;
            transform: translateY(-5px);
        }

        .form-control {
            padding: 15px 20px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fdfdfd;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.1);
            border-color: var(--primary);
            background: #fff;
        }

        .btn-primary-custom {
            background: var(--gradient);
            color: #fff;
            padding: 15px 35px;
            border-radius: 50px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary-custom:hover {
            transform: translateY(-3px);
            color: #fff;
            box-shadow: 0 10px 20px rgba(78, 115, 223, 0.3);
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
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary-custom btn-sm py-2 px-4 shadow-sm" style="padding: 8px 20px !important;">
                    <i class="fas fa-home me-2"></i>Home
                </a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="page-header text-center">
        <div class="container" data-aos="fade-down">
            <h1 class="display-4 fw-800 mb-3">Support Center</h1>
            <p class="lead opacity-75">Have questions? We're here to help you grow your business.</p>
        </div>
    </header>

    <div class="container mb-5 pb-5" style="margin-top: -50px;">
        <div class="row g-4 justify-content-center">
            <!-- Contact Info Cards -->
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-card">
                    <div class="contact-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <h5 class="fw-bold">Email Support</h5>
                    <p class="text-muted small">Expect a response within 24 working hours.</p>
                    <a href="mailto:support@stockara.toamultitech.tech" class="text-primary fw-bold text-decoration-none">support@stockara.toamultitech.tech</a>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-card">
                    <div class="contact-icon"><i class="fas fa-comment-dots"></i></div>
                    <h5 class="fw-bold">General Enquiries</h5>
                    <p class="text-muted small">Questions about features or partnership.</p>
                    <a href="mailto:hello@stockara.toamultitech.tech" class="text-primary fw-bold text-decoration-none">hello@stockara.toamultitech.tech</a>
                </div>
            </div>

            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-card">
                    <div class="contact-icon"><i class="fas fa-share-alt"></i></div>
                    <h5 class="fw-bold">Social Media</h5>
                    <p class="text-muted small">Follow us for updates and tips.</p>
                    <div class="d-flex gap-3 mt-2">
                        <a href="#" class="btn btn-light rounded-circle p-2"><i class="fab fa-twitter text-primary"></i></a>
                        <a href="#" class="btn btn-light rounded-circle p-2"><i class="fab fa-facebook text-primary"></i></a>
                        <a href="#" class="btn btn-light rounded-circle p-2"><i class="fab fa-linkedin text-primary"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form Section -->
            <div class="col-lg-10 mt-5">
                <div class="contact-card p-lg-5" data-aos="fade-up">
                    <div class="text-center mb-5">
                        <h3 class="fw-800">Send us a Message</h3>
                        <p class="text-muted">Fill out the form below and our team will get back to you.</p>
                    </div>

                    <?php if ($message_sent): ?>
                        <div class="alert alert-success rounded-4 p-4 border-0 mb-4 animate__animated animate__fadeIn">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-1 fw-bold">Message Received!</h5>
                                    <p class="mb-0">Thank you for reaching out. We have received your message and will respond shortly.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-4 p-4 border-0 mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-600">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-600">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="How can we help?" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-600">Your Message</label>
                            <textarea name="message" class="form-control" rows="6" placeholder="Tell us more about your inquiry..." required></textarea>
                        </div>
                        <div class="col-12 text-center mt-5">
                            <button type="submit" name="send_message" class="btn btn-primary-custom px-5">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </div>
                    </form>
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
                    <p class="text-white-50">Empowering businesses with intelligent inventory and POS solutions.</p>
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
                        <li class="mb-2"><a href="<?php echo BASE_URL; ?>contact.php" class="text-white-50 text-decoration-none small fw-bold text-white">Contact Us</a></li>
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
