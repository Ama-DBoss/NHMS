<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

if (!is_hospital()) {
    redirect('index.php');
}

$hospital_id = $_SESSION['hospital_id'];
$certificate_type = $_GET['type'] ?? '';

if (!in_array($certificate_type, ['birth', 'death'])) {
    set_flash_message('danger', 'Invalid certificate type.');
    redirect('dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    if ($certificate_type === 'birth') {
        $child_name = sanitize_input($_POST['child_name']);
        $date_of_birth = sanitize_input($_POST['date_of_birth']);
        $time_of_birth = sanitize_input($_POST['time_of_birth']);
        $place_of_birth = sanitize_input($_POST['place_of_birth']);
        $gender = sanitize_input($_POST['gender']);
        $weight = sanitize_input($_POST['weight']);
        $blood_group = sanitize_input($_POST['blood_group']);
        $genotype = sanitize_input($_POST['genotype']);
        $father_name = sanitize_input($_POST['father_name']);
        $mother_name = sanitize_input($_POST['mother_name']);
        $parents_address = sanitize_input($_POST['parents_address']);

        if (empty($child_name) || empty($date_of_birth) || empty($time_of_birth) || empty($place_of_birth) || 
            empty($gender) || empty($weight) || empty($blood_group) || empty($genotype) || 
            empty($father_name) || empty($mother_name) || empty($parents_address)) {
            $errors[] = "All fields are required.";
        }

        if (empty($errors)) {
            $certificate_number = generate_certificate_number('BIRTH');
            try {
                $stmt = $pdo->prepare("INSERT INTO birth_certificates (hospital_id, child_name, date_of_birth, time_of_birth, place_of_birth, gender, weight, blood_group, genotype, father_name, mother_name, parents_address, certificate_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$hospital_id, $child_name, $date_of_birth, $time_of_birth, $place_of_birth, $gender, $weight, $blood_group, $genotype, $father_name, $mother_name, $parents_address, $certificate_number]);
                set_flash_message('success', 'Birth certificate generated successfully.');
                redirect('dashboard.php');
            } catch (PDOException $e) {
                $errors[] = "An error occurred while generating the certificate. Please try again.";
            }
        }
    } elseif ($certificate_type === 'death') {
        $deceased_name = sanitize_input($_POST['deceased_name']);
        $date_of_death = sanitize_input($_POST['date_of_death']);
        $time_of_death = sanitize_input($_POST['time_of_death']);
        $place_of_death = sanitize_input($_POST['place_of_death']);
        $cause_of_death = sanitize_input($_POST['cause_of_death']);
        $age_at_death = sanitize_input($_POST['age_at_death']);
        $gender = sanitize_input($_POST['gender']);
        $occupation = sanitize_input($_POST['occupation']);
        $marital_status = sanitize_input($_POST['marital_status']);
        $next_of_kin = sanitize_input($_POST['next_of_kin']);
        $next_of_kin_relationship = sanitize_input($_POST['next_of_kin_relationship']);

        if (empty($deceased_name) || empty($date_of_death) || empty($time_of_death) || empty($place_of_death) || 
            empty($cause_of_death) || empty($age_at_death) || empty($gender) || empty($occupation) || 
            empty($marital_status) || empty($next_of_kin) || empty($next_of_kin_relationship)) {
            $errors[] = "All fields are required.";
        }

        if (empty($errors)) {
            $certificate_number = generate_certificate_number('DEATH');
            try {
                $stmt = $pdo->prepare("INSERT INTO death_certificates (hospital_id, deceased_name, date_of_death, time_of_death, place_of_death, cause_of_death, age_at_death, gender, occupation, marital_status, next_of_kin, next_of_kin_relationship, certificate_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$hospital_id, $deceased_name, $date_of_death, $time_of_death, $place_of_death, $cause_of_death, $age_at_death, $gender, $occupation, $marital_status, $next_of_kin, $next_of_kin_relationship, $certificate_number]);
                set_flash_message('success', 'Death certificate generated successfully.');
                redirect('dashboard.php');
            } catch (PDOException $e) {
                $errors[] = "An error occurred while generating the certificate. Please try again.";
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container mt-5">
    <h2>Generate <?= ucfirst($certificate_type) ?> Certificate</h2>
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
    <form action="generate_certificate.php?type=<?= $certificate_type ?>" method="post">
        <?php if ($certificate_type === 'birth'): ?>
            <div class="mb-3">
                <label for="child_name" class="form-label">Child's Name</label>
                <input type="text" class="form-control" id="child_name" name="child_name" required>
            </div>
            <div class="mb-3">
                <label for="date_of_birth" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" required>
            </div>
            <div class="mb-3">
                <label for="time_of_birth" class="form-label">Time of Birth</label>
                <input type="time" class="form-control" id="time_of_birth" name="time_of_birth" required>
            </div>
            <div class="mb-3">
                <label for="place_of_birth" class="form-label">Place of Birth</label>
                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight (kg)</label>
                <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
            </div>
            <div class="mb-3">
                <label for="blood_group" class="form-label">Blood Group</label>
                <select class="form-control" id="blood_group" name="blood_group" required>
                    <option value="">Select Blood Group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="genotype" class="form-label">Genotype</label>
                <select class="form-control" id="genotype" name="genotype" required>
                    <option value="">Select Genotype</option>
                    <option value="AA">AA</option>
                    <option value="AS">AS</option>
                    <option value="SS">SS</option>
                    <option value="AC">AC</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="father_name" class="form-label">Father's Name</label>
                <input type="text" class="form-control" id="father_name" name="father_name" required>
            </div>
            <div class="mb-3">
                <label for="mother_name" class="form-label">Mother's Name</label>
                <input type="text" class="form-control" id="mother_name" name="mother_name" required>
            </div>
            <div class="mb-3">
                <label for="parents_address" class="form-label">Parents' Address</label>
                <textarea class="form-control" id="parents_address" name="parents_address" required></textarea>
            </div>
        <?php elseif ($certificate_type === 'death'): ?>
            <div class="mb-3">
                <label for="deceased_name" class="form-label">Deceased's Name</label>
                <input type="text" class="form-control" id="deceased_name" name="deceased_name" required>
            </div>
            <div class="mb-3">
                <label for="date_of_death" class="form-label">Date of Death</label>
                <input type="date" class="form-control" id="date_of_death" name="date_of_death" required>
            </div>
            <div class="mb-3">
                <label for="time_of_death" class="form-label">Time of Death</label>
                <input type="time" class="form-control" id="time_of_death" name="time_of_death" required>
            </div>
            <div class="mb-3">
                <label for="place_of_death" class="form-label">Place of Death</label>
                <input type="text" class="form-control" id="place_of_death" name="place_of_death" required>
            </div>
            <div class="mb-3">
                <label for="cause_of_death" class="form-label">Cause of Death</label>
                <input type="text" class="form-control" id="cause_of_death" name="cause_of_death" required>
            </div>
            <div class="mb-3">
                <label for="age_at_death" class="form-label">Age at Death</label>
                <input type="number" class="form-control" id="age_at_death" name="age_at_death" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select class="form-control" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="occupation" class="form-label">Occupation</label>
                <input type="text" class="form-control" id="occupation" name="occupation" required>
            </div>
            <div class="mb-3">
                <label for="marital_status" class="form-label">Marital Status</label>
                <select class="form-control" id="marital_status" name="marital_status" required>
                    <option value="">Select Marital Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="next_of_kin" class="form-label">Next of Kin</label>
                <input type="text" class="form-control" id="next_of_kin" name="next_of_kin" required>
            </div>
            <div class="mb-3">
                <label for="next_of_kin_relationship" class="form-label">Relationship to Next of Kin</label>
                <input type="text" class="form-control" id="next_of_kin_relationship" name="next_of_kin_relationship" required>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Generate Certificate</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>