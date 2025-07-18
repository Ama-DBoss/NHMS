<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (is_admin()) {
    redirect('admin/dashboard.php');
}

if (is_hospital()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $user_type = sanitize_input($_POST['user_type']);

    if ($user_type === 'hospital') {
        $stmt = $pdo->prepare("SELECT id, password FROM hospitals WHERE email = ?");
    } else {
        $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE email = ?");
    }

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        if ($user_type === 'hospital') {
            $_SESSION['hospital_id'] = $user['id'];
            $update_stmt = $pdo->prepare("UPDATE hospitals SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
            $update_stmt->execute([$user['id']]);
            redirect('dashboard.php');
        } else {
            $_SESSION['admin_id'] = $user['id'];
            redirect('admin/dashboard.php');
        }
    } else {
        $error = 'Invalid email or password';
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Login</h2>
    <?= display_flash_message() ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form action="index.php" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="user_type" class="form-label">User Type</label>
            <select class="form-control" id="user_type" name="user_type" required>
                <option value="hospital">Hospital</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>
