<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['hospital_id'])) {
    header('Location: index.php');
    exit;
}

$hospital_id = $_SESSION['hospital_id'];

// Fetch hospital information
$stmt = $pdo->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->execute([$hospital_id]);
$hospital = $stmt->fetch();

// Fetch birth and death certificate counts
$stmt = $pdo->prepare("SELECT COUNT(*) as birth_count FROM birth_certificates WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$birth_count = $stmt->fetch()['birth_count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as death_count FROM death_certificates WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$death_count = $stmt->fetch()['death_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($hospital['name']); ?></h2>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Birth Certificates Issued</h5>
                        <p class="card-text"><?php echo $birth_count; ?></p>
                        <a href="generate_certificate.php?type=birth" class="btn btn-primary">Issue Birth Certificate</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Death Certificates Issued</h5>
                        <p class="card-text"><?php echo $death_count; ?></p>
                        <a href="generate_certificate.php?type=death" class="btn btn-primary">Issue Death Certificate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
