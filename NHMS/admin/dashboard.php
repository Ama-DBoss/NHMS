<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Fetch total counts
$stmt = $pdo->query("SELECT COUNT(*) as hospital_count FROM hospitals");
$hospital_count = $stmt->fetch()['hospital_count'];

$stmt = $pdo->query("SELECT COUNT(*) as birth_count FROM birth_certificates");
$birth_count = $stmt->fetch()['birth_count'];

$stmt = $pdo->query("SELECT COUNT(*) as death_count FROM death_certificates");
$death_count = $stmt->fetch()['death_count'];

// Fetch recent registrations
$stmt = $pdo->query("SELECT * FROM hospitals ORDER BY registration_date DESC LIMIT 5");
$recent_hospitals = $stmt->fetchAll();

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Admin Dashboard</h2>
    <?= display_flash_message() ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Hospitals</h5>
                    <p class="card-text"><?= $hospital_count ?></p>
                    <a href="view_hospitals.php" class="btn btn-primary">View All Hospitals</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Birth Certificates</h5>
                    <p class="card-text"><?= $birth_count ?></p>
                    <a href="view_births.php" class="btn btn-primary">View All Births</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Death Certificates</h5>
                    <p class="card-text"><?= $death_count ?></p>
                    <a href="view_deaths.php" class="btn btn-primary">View All Deaths</a>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-12">
            <h3>Recent Hospital Registrations</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Hospital ID</th>
                        <th>Hospital Name</th>
                        <th>State</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_hospitals as $hospital): ?>
                        <tr>
                            <td><?= htmlspecialchars($hospital['hosp_id']) ?></td>
                            <td><?= htmlspecialchars($hospital['name']) ?></td>
                            <td><?= htmlspecialchars($hospital['state']) ?></td>
                            <td><?= htmlspecialchars($hospital['registration_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>