<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Fetch death_certificates
$stmt = $pdo->prepare("SELECT * FROM death_certificates ORDER BY date_of_death DESC LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$deaths = $stmt->fetchAll();

// Get total number of death_certificates
$stmt = $pdo->query("SELECT COUNT(*) FROM death_certificates");
$total_deaths = $stmt->fetchColumn();
$total_pages = ceil($total_deaths / $records_per_page);

require_once '../includes/header.php';
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