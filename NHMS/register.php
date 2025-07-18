<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $address = sanitize_input($_POST['address']);
    $state = sanitize_input($_POST['state']);
    $email = filter_var(sanitize_input($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    if (empty($name) || empty($address) || empty($state) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $hosp_id = 'HOSP_' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

        try {
            $stmt = $pdo->prepare("INSERT INTO hospitals (hospital_id, name, address, state, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$hosp_id, $name, $address, $state, $email, $password_hash]);
            set_flash_message('success', 'Registration successful. You can now log in.');
            redirect('index.php');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors[] = "Email already exists. Please use a different email.";
            } else {
                $errors[] = "An error occurred. Please try again later.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Hospital Registration</h2>
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
    <form action="register.php" method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Hospital Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="mb-3">
            <label for="state" class="form-label">State</label>
            <input type="text" class="form-control" id="state" name="state" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <p class="mt-3">Already have an account? <a href="index.php">Login here</a></p>
</div>

<?php require_once 'includes/footer.php'; ?>