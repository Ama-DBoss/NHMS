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

try {
    $pdo->beginTransaction();

    // Delete associated birth certificates
    $stmt = $pdo->prepare("DELETE FROM birth_certificates WHERE hospital_id = ?");
    $stmt->execute([$id]);

    // Delete associated death certificates
    $stmt = $pdo->prepare("DELETE FROM death_certificates WHERE hospital_id = ?");
    $stmt->execute([$id]);

    // Delete the hospital
    $stmt = $pdo->prepare("DELETE FROM hospitals WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    set_flash_message('success', 'Hospital and associated records deleted successfully.');
} catch (PDOException $e) {
    $pdo->rollBack();
    set_flash_message('danger', 'An error occurred while deleting the hospital. Please try again.');
}

redirect('view_hospitals.php');