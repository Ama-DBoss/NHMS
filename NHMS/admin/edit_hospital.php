<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if (!is_admin()) {
    redirect('../index.php');
}

$id = $_GET['id'] ?? null;

if (!$id) {
    set_flash_message('danger', 'Invalid hospital ID.');
    redirect('view_hospitals.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $address = sanitize_input($_POST['address']);
    $state = sanitize_input($_POST['state']);
    $email = filter_var(sanitize_input($_POST['email']), FILTER_SANITIZE_EMAIL);

    $errors = [];

    if (empty($name) || empty($address) || empty($state) || empty($email)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email  || empty($email)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE hospitals SET name = ?, address = ?, state = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $address, $state, $email, $id]);
            set_flash_message('success', 'Hospital information updated successfully.');
            redirect('view_hospitals.php');
        } catch (PDOException $e) {
            $errors[] = "An error occurred while updating the hospital information. Please try again.";
        }
    }
}

// Fetch hospital information
$stmt = $pdo->prepare("SELECT * FROM hospitals WHERE id = ?");
$stmt->execute([$id]);
$hospital = $stmt->fetch();

if (!$hospital) {
    set_flash_message('danger', 'Hospital not found.');
    redirect('view_hospitals.php');
}

require_once '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Edit Hospital</h2>
    <?= display_flash_message() ?>
    <?php
    if (!empty($errors)) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo '</ul></div>';
    }
    ?>
    <form action="edit_hospital.php?id=<?= $id ?>" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Hospital Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($hospital['name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($hospital['address']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="state" class="form-label">State</label>
            <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($hospital['state']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($hospital['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Hospital</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
