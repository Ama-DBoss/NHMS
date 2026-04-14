<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (is_admin() || is_hospital()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf_token)) {
        $errors[] = 'Security token validation failed. Please try again.';
    } else {
        $name = sanitize_input($_POST['name'] ?? '');
        $address = sanitize_input($_POST['address'] ?? '');
        $state = sanitize_input($_POST['state'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($name) || empty($address) || empty($state) || empty($email) || empty($password)) {
            $errors[] = 'All fields are required.';
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }

        if (strlen($password) > 255) {
            $errors[] = 'Password is too long.';
        }

        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }

        if (strlen($name) > 255 || strlen($address) > 255 || strlen($state) > 100) {
            $errors[] = 'One or more fields exceed maximum length.';
        }

        // Check if email already exists
        if (!empty($errors) === false) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM hospitals WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $errors[] = 'Email already registered. Please use a different email or login.';
                }
            } catch (PDOException $e) {
                error_log('Registration check error: ' . $e->getMessage());
                $errors[] = 'An error occurred. Please try again later.';
            }
        }

        // Register hospital if no errors
        if (empty($errors)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $hosp_id = 'HOSP_' . strtoupper(bin2hex(random_bytes(4))) . '_' . time();

            try {
                $stmt = $pdo->prepare("INSERT INTO hospitals (hospital_id, name, address, state, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$hosp_id, $name, $address, $state, $email, $password_hash]);
                set_flash_message('success', 'Registration successful! You can now log in.');
                redirect('index.php');
            } catch (PDOException $e) {
                error_log('Registration error: ' . $e->getMessage());
                if ($e->getCode() == 23000) {
                    $errors[] = 'Email already registered. Please use a different email.';
                } else {
                    $errors[] = 'An error occurred during registration. Please try again later.';
                }
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Hospital Registration</h2>
                    <?= display_flash_message() ?>
                    <?php
                    if (!empty($errors)) {
                        echo '<div class="alert alert-danger" role="alert"><ul class="mb-0">';
                        foreach ($errors as $error) {
                            echo "<li>" . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . "</li>";
                        }
                        echo '</ul></div>';
                    }
                    ?>
                    <form action="register.php" method="post" novalidate>
                        <?= csrf_token_field() ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" id="name" name="name" maxlength="255" required autocomplete="organization">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" maxlength="255" required autocomplete="street-address">
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="8" required autocomplete="new-password">
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="8" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register Hospital</button>
                    </form>
                    <p class="mt-3 text-center">Already have an account? <a href="index.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
