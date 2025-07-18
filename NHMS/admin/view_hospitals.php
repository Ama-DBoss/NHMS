<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

// Fetch hospitals
$stmt = $pdo->prepare("SELECT * FROM hospitals ORDER BY registration_date DESC LIMIT :offset, :records_per_page");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->execute();
$hospitals = $stmt->fetchAll();

// Get total number of hospitals
$stmt = $pdo->query("SELECT COUNT(*) FROM hospitals");
$total_hospitals = $stmt->fetchColumn();
$total_pages = ceil($total_hospitals / $records_per_page);

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2>All Hospitals</h2>
    <?= display_flash_message() ?>
    <table class="table">
        <thead>
            <tr>
                <th>Hospital ID</th>
                <th>Name</th>
                <th>State</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($hospitals as $hospital): ?>
                <tr>
                    <td><?= htmlspecialchars($hospital['hosp_id']) ?></td>
                    <td><?= htmlspecialchars($hospital['name']) ?></td>
                    <td><?= htmlspecialchars($hospital['state']) ?></td>
                    <td><?= htmlspecialchars($hospital['email']) ?></td>
                    <td><?= htmlspecialchars($hospital['registration_date']) ?></td>
                    <td>
                        <a href="edit_hospital.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                        <a href="delete_hospital.php?id=<?= $hospital['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this hospital?')">Delete</a>
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