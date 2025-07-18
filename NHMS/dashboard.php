<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_hospital()) {
    redirect('index.php');
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

// Fetch recent births and deaths
$stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE hospital_id = ? ORDER BY issue_date DESC LIMIT 5");
$stmt->execute([$hospital_id]);
$recent_births = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM death_certificates WHERE hospital_id = ? ORDER BY issue_date DESC LIMIT 5");
$stmt->execute([$hospital_id]);
$recent_deaths = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Welcome, <?= htmlspecialchars($hospital['name']) ?></h2>
    <?= display_flash_message() ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Birth Certificates Issued</h5>
                    <p class="card-text"><?= $birth_count ?></p>
                    <a href="generate_certificate.php?type=birth" class="btn btn-primary">Issue Birth Certificate</a>
                    <a href="view_births.php" class="btn btn-secondary">View All Births</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Death Certificates Issued</h5>
                    <p class="card-text"><?= $death_count ?></p>
                    <a href="generate_certificate.php?type=death" class="btn btn-primary">Issue Death Certificate</a>
                    <a href="view_deaths.php" class="btn btn-secondary">View All Deaths</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <h3>Recent Births</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Certificate Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_births as $birth): ?>
                        <tr>
                            <td><?= htmlspecialchars($birth['child_name']) ?></td>
                            <td><?= htmlspecialchars($birth['date_of_birth']) ?></td>
                            <td><?= htmlspecialchars($birth['certificate_number']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h3>Recent Deaths</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Death</th>
                        <th>Certificate Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_deaths as $death): ?>
                        <tr>
                            <td><?= htmlspecialchars($death['deceased_name']) ?></td>
                            <td><?= htmlspecialchars($death['date_of_death']) ?></td>
                            <td><?= htmlspecialchars($death['certificate_number']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
