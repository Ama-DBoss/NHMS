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

// Fetch all registered births for the hospital
$stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE hospital_id = ?");
$stmt->execute([$hospital_id]);
$births = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Registered Births</h2>
    <table class="table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Date of Birth</th>
                <th>Certificate Number</th>
                <th>Preview Certificate</th>
                <th>Download Certificate</th>
                <!-- Add any other necessary columns -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($births as $birth): ?>
                <tr>
                    <td><?php echo htmlspecialchars($birth['id']); ?></td>
                    <td><?php echo htmlspecialchars($birth['child_name']); ?></td>
                    <td><?php echo htmlspecialchars($birth['date_of_birth']); ?></td>
                    <td><?php echo htmlspecialchars($birth['certificate_number']); ?></td>
                    <td>
                        <!-- Link to preview the certificate in PDF -->
                        <a href="preview_certificate.php?type=birth&id=<?php echo $birth['id']; ?>" target="_blank" class="btn btn-info btn-sm">Preview</a>
                    </td>
                    <td>
                        <!-- Link to download the certificate as PDF -->
                        <a href="download_certificate.php?type=birth&id=<?php echo $birth['id']; ?>" class="btn btn-success btn-sm">Download</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>