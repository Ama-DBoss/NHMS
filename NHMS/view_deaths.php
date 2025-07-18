<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['hospital_id'])) {
    header('Location: index.php');
    exit;
}

$hospital_id = $_SESSION['hospital_id']; // Get hospital ID from session or URL
if (isset($_GET['hospital_id'])) {
    $hospital_id = $_GET['hospital_id']; // Get hospital_id from URL if passed
}

// Fetch all registered deaths for the hospital
$stmt = $pdo->prepare("SELECT * FROM death_certificates WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$deaths = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Registered Deaths</h2>
    <table class="table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Date of Death</th>
                <th>Certificate Number</th>
                <!-- Add any other necessary columns -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deaths as $death): ?>
                <tr>
                    <td><?php echo htmlspecialchars($death['id']); ?></td>
                    <td><?php echo htmlspecialchars($death['deceased_name']); ?></td>
                    <td><?php echo htmlspecialchars($death['date_of_death']); ?></td>
                    <td><?php echo htmlspecialchars($death['certificate_number']); ?></td>
                    <!-- Add any other necessary columns -->
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
