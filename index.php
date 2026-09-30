<?php
session_start();
require_once __DIR__ . '/hms/include/config.php';

// Ensure the database connection exists for static analysis and runtime checks.
if (!isset($con) || !($con instanceof mysqli)) {
    $con = null;
}

if (isset($_POST['submit'])) {
    hms_verify_csrf();
    $name = hms_post('fullname');
    $email = hms_post('emailid');
    $mobile = hms_post('mobileno');
    $message = hms_post('description');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9+\-\s]{7,15}$/', $mobile) || $message === '') {
        hms_flash('danger', 'Please enter valid contact details.');
    } else {
        hms_execute(
            $con,
            'INSERT INTO tblcontactus(fullname, email, contactno, message) VALUES(?, ?, ?, ?)',
            'ssss',
            array($name, $email, $mobile, $message)
        );
        $queryId = mysqli_insert_id($con);
        hms_notify($con, 'admin', null, 'New contact query', $name . ' submitted a contact query.');
        hms_log_audit($con, 'public', 0, 'contact_query_created', 'tblcontactus', $queryId, 'New public contact query.');
        hms_flash('success', 'Your information was submitted successfully.');
        hms_redirect('index.php#contact_us');
    }
}

$about = hms_fetch_one($con, "SELECT * FROM tblpage WHERE PageType = 'aboutus'");
$contact = hms_fetch_one($con, "SELECT * FROM tblpage WHERE PageType = 'contactus'");
$flash = hms_get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hospital Management System</title>
    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --site-ink: #1f2937;
            --site-muted: #667085;
            --site-line: #d9e2ec;
            --site-teal: #127c8a;
            --site-navy: #17324d;
            --site-amber: #b7791f;
            --site-bg: #f6f8fb;
        }
        body { color: var(--site-ink); background: var(--site-bg); letter-spacing: 0; }
        a { text-decoration: none; }
        .navbar { background: rgba(255, 255, 255, .96); border-bottom: 1px solid var(--site-line); backdrop-filter: blur(10px); }
        .brand-icon { width: 36px; height: 36px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; color: #fff; background: var(--site-teal); }
        .hero {
            min-height: 82vh;
            display: flex;
            align-items: center;
            position: relative;
            color: #fff;
            background:
                linear-gradient(90deg, rgba(23, 50, 77, .88), rgba(23, 50, 77, .46), rgba(18, 124, 138, .18)),
                url('assets/images/slider/slider_2.jpg') center/cover no-repeat;
        }
        .hero h1 { font-size: clamp(2.25rem, 5vw, 4.75rem); font-weight: 800; line-height: 1.02; }
        .hero-copy { max-width: 740px; padding-top: 86px; padding-bottom: 90px; }
        .section-pad { padding: 76px 0; }
        .section-title { max-width: 760px; margin-bottom: 30px; }
        .section-title h2 { font-weight: 800; }
        .feature-card, .login-card, .contact-panel {
            background: #fff;
            border: 1px solid var(--site-line);
            border-radius: 8px;
            box-shadow: 0 12px 30px rgba(16, 24, 40, .05);
        }
        .feature-card { padding: 24px; height: 100%; }
        .feature-icon { width: 44px; height: 44px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: #e6fffb; color: var(--site-teal); font-size: 22px; }
        .login-card img { width: 100%; aspect-ratio: 16 / 10; object-fit: cover; border-radius: 8px 8px 0 0; }
        .login-card-body { padding: 20px; }
        .about-band { background: #fff; }
        .about-image { min-height: 430px; background: url('assets/images/why.jpg') center/cover no-repeat; }
        .gallery-img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: 8px; border: 1px solid var(--site-line); }
        .btn-primary { background: var(--site-teal); border-color: var(--site-teal); }
        .btn-primary:hover, .btn-primary:focus { background: #0f6671; border-color: #0f6671; }
        .btn-outline-light:hover { color: var(--site-navy); }
        .form-control, .btn { border-radius: 7px; }
        footer { background: var(--site-navy); color: #dbeafe; padding: 38px 0; }
        footer a { color: #e6fffb; }
        @media (max-width: 767.98px) {
            .hero { min-height: 74vh; }
            .hero-copy { padding-top: 104px; padding-bottom: 64px; }
            .section-pad { padding: 52px 0; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="#">
            <span class="brand-icon"><i class="bi bi-hospital"></i></span>
            <span>HMS</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#about_us">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact_us">Contact</a></li>
                <li class="nav-item"><a class="nav-link" href="#logins">Logins</a></li>
                <li class="nav-item ms-lg-2"><a class="btn btn-primary" href="hms/user-login.php">Book Appointment</a></li>
            </ul>
        </div>
    </div>
</nav>

<header class="hero">
    <div class="container hero-copy">
        <div class="badge text-bg-light mb-3"><i class="bi bi-shield-check me-1"></i>Secure hospital workflow portal</div>
        <h1>Hospital Management System</h1>
        <p class="fs-5 mt-3 mb-4 col-lg-9">A responsive platform for patient appointments, doctor schedules, prescriptions, reports, and hospital communication.</p>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-primary btn-lg" href="hms/user-login.php"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
            <a class="btn btn-outline-light btn-lg" href="#logins">Open Portal</a>
        </div>
    </div>
</header>

<main>
    <section id="logins" class="section-pad">
        <div class="container">
            <?php if ($flash) { $type = $flash['type'] === 'error' ? 'danger' : $flash['type']; ?>
                <div class="alert alert-<?php echo hms_e($type); ?> alert-dismissible fade show" role="alert">
                    <?php echo hms_e($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
            <div class="section-title">
                <h2>Portal Logins</h2>
                <p class="text-muted mb-0">Choose the correct workspace for your role.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="login-card h-100">
                        <img src="assets/images/patient.jpg" alt="Patient consultation">
                        <div class="login-card-body">
                            <h3 class="h5">Patient</h3>
                            <p class="text-muted">Book appointments, track status, and download prescriptions.</p>
                            <a class="btn btn-primary w-100" href="hms/user-login.php">Patient Login</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="login-card h-100">
                        <img src="assets/images/doctor.jpg" alt="Doctor workspace">
                        <div class="login-card-body">
                            <h3 class="h5">Doctor</h3>
                            <p class="text-muted">Manage today appointments, prescriptions, and patient history.</p>
                            <a class="btn btn-primary w-100" href="hms/doctor/">Doctor Login</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="login-card h-100">
                        <img src="assets/images/admin.jpg" alt="Hospital admin desk">
                        <div class="login-card-body">
                            <h3 class="h5">Admin</h3>
                            <p class="text-muted">Monitor operations, reports, notifications, and audit logs.</p>
                            <a class="btn btn-primary w-100" href="hms/admin/">Admin Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="section-pad bg-white">
        <div class="container">
            <div class="section-title">
                <h2>Key Services</h2>
                <p class="text-muted mb-0">Designed for everyday hospital workflows and patient care coordination.</p>
            </div>
            <div class="row g-4">
                <?php
                $services = array(
                    array('bi-heart-pulse', 'Cardiology'),
                    array('bi-bandaid', 'Orthopaedic'),
                    array('bi-activity', 'Neurology'),
                    array('bi-capsule', 'Pharmacy Pipeline'),
                    array('bi-prescription2', 'Prescription Records'),
                    array('bi-hand-thumbs-up', 'Quality Treatment')
                );
                foreach ($services as $service) {
                    ?>
                    <div class="col-sm-6 col-lg-4">
                        <div class="feature-card">
                            <span class="feature-icon"><i class="bi <?php echo hms_e($service[0]); ?>"></i></span>
                            <h3 class="h5 mt-3 mb-0"><?php echo hms_e($service[1]); ?></h3>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section id="about_us" class="about-band">
        <div class="container-fluid px-0">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6 about-image"></div>
                <div class="col-lg-6">
                    <div class="p-4 p-md-5">
                        <h2 class="fw-bold"><?php echo hms_e($about['PageTitle'] ?? 'About Our Hospital'); ?></h2>
                        <p class="text-muted fs-5"><?php echo hms_e(strip_tags($about['PageDescription'] ?? 'The Hospital Management System keeps hospital workflows organized and accessible.')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="gallery" class="section-pad">
        <div class="container">
            <div class="section-title">
                <h2>Hospital Gallery</h2>
                <p class="text-muted mb-0">A quick look at care spaces and services.</p>
            </div>
            <div class="row g-3">
                <?php foreach (array('01', '02', '03', '04', '05', '06') as $image) { ?>
                    <div class="col-sm-6 col-lg-4">
                        <img class="gallery-img" src="assets/images/gallery/gallery_<?php echo hms_e($image); ?>.jpg" alt="Hospital gallery image <?php echo hms_e($image); ?>">
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section id="contact_us" class="section-pad bg-white">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="section-title mb-0">
                        <h2><?php echo hms_e($contact['PageTitle'] ?? 'Contact Us'); ?></h2>
                        <p class="text-muted"><?php echo hms_e(strip_tags($contact['PageDescription'] ?? 'Contact the hospital team for support.')); ?></p>
                    </div>
                    <div class="mt-4">
                        <p class="mb-2"><i class="bi bi-telephone me-2 text-success"></i><?php echo hms_e($contact['MobileNumber'] ?? ''); ?></p>
                        <p class="mb-2"><i class="bi bi-envelope me-2 text-success"></i><a href="mailto:<?php echo hms_e($contact['Email'] ?? ''); ?>"><?php echo hms_e($contact['Email'] ?? ''); ?></a></p>
                        <p class="mb-0"><i class="bi bi-clock me-2 text-success"></i><?php echo hms_e($contact['OpenningTime'] ?? ''); ?></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="contact-panel p-4">
                        <form method="post">
                            <?php echo hms_csrf_field(); ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="fullname">Name</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="emailid">Email</label>
                                    <input type="email" class="form-control" id="emailid" name="emailid" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="mobileno">Mobile number</label>
                                    <input type="text" class="form-control" id="mobileno" name="mobileno" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="description">Message</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-4" type="submit" name="submit">
                                <i class="bi bi-send me-1"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-3">
        <div>
            <div class="fw-bold text-white">Hospital Management System</div>
            <div class="small">Appointments, care records, prescriptions, and reports.</div>
        </div>
        <div class="d-flex flex-wrap gap-3">
            <a href="#services">Services</a>
            <a href="#about_us">About</a>
            <a href="#gallery">Gallery</a>
            <a href="#contact_us">Contact</a>
            <a href="#logins">Logins</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
