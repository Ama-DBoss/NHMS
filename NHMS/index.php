<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_admin()) {
    redirect('admin/dashboard.php');
}

if (is_hospital()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $error = 'Security token validation failed. Please try again.';
    } else {
        $email = sanitize_input($_POST['email']);
        $password = $_POST['password'] ?? '';
        $user_type = sanitize_input($_POST['user_type']);

        // Validate input
        if (empty($email) || empty($password) || empty($user_type)) {
            $error = 'Email, password, and user type are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format.';
        } elseif (!in_array($user_type, ['hospital', 'admin'])) {
            $error = 'Invalid user type.';
        } else {
            try {
                if ($user_type === 'hospital') {
                    $stmt = $pdo->prepare("SELECT id, password FROM hospitals WHERE email = ? LIMIT 1");
                } else {
                    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE email = ? LIMIT 1");
                }

                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    if ($user_type === 'hospital') {
                        $_SESSION['hospital_id'] = $user['id'];
                        // Log the login activity
                        $update_stmt = $pdo->prepare("UPDATE hospitals SET last_login = NOW() WHERE id = ?");
                        $update_stmt->execute([$user['id']]);
                        log_activity($user['id'], 'LOGIN', 'Hospital logged in');
                        redirect('dashboard.php');
                    } else {
                        $_SESSION['admin_id'] = $user['id'];
                        log_activity(null, 'ADMIN_LOGIN', 'Admin logged in');
                        redirect('admin/dashboard.php');
                    }
                } else {
                    // Don't reveal if email exists or password is wrong
                    $error = 'Invalid email or password. Please try again.';
                }
            } catch (PDOException $e) {
                error_log('Login error: ' . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">NHMS Login</h2>
                    <?= display_flash_message() ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                    <form action="index.php" method="post" novalidate>
                        <?= csrf_token_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="">Select User Type</option>
                                <option value="hospital">Hospital</option>
                                <option value="admin">Administrator</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
