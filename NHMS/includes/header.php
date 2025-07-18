<?php
$page_title = $page_title ?? "National Birth & Death Management System";
$page_description = $page_description ?? "Official platform for managing birth and death records in Nigeria";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> | NBDMS</title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="author" content="National Birth & Death Management System">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="/NHMS/images/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="twitter:image" content="/NHMS/images/twitter-image.jpg">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/NHMS/images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/NHMS/images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/NHMS/images/favicon/favicon-16x16.png">
    <link rel="manifest" href="/NHMS/images/favicon/site.webmanifest">
    <link rel="mask-icon" href="/NHMS/images/favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/NHMS/css/styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="<?= is_admin() ? '/NHMS/admin/dashboard.php' : '/NHMS/dashboard.php' ?>">
                <img src="/NHMS/images/coa.svg" alt="NBDMS Logo" width="30" height="30" class="d-inline-block align-top me-2">
                National Birth & Death Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/admin/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/admin/view_hospitals.php">Hospitals</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/admin/view_births.php">Births</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/admin/view_deaths.php">Deaths</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/logout.php">Logout</a>
                        </li>
                    <?php elseif (is_hospital()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/generate_certificate.php?type=birth">Issue Birth Certificate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/generate_certificate.php?type=death">Issue Death Certificate</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/index.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/NHMS/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
