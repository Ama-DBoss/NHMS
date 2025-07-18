<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Fetch birth_certificates
$stmt = $pdo->prepare("SELECT * FROM birth_certificates ORDER BY date_of_birth DESC LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$births = $stmt->fetchAll();

// Get total number of birth_certificates
$stmt = $pdo->query("SELECT COUNT(*) FROM birth_certificates");
$total_births = $stmt->fetchColumn();
$total_pages = ceil($total_births / $records_per_page);

require_once '../includes/header.php';
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

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php require_once '../includes/footer.php'; ?>