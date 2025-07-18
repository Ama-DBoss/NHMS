<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/tcpdf/tcpdf.php'; // Include the TCPDF library

if (!isset($_SESSION['hospital_id'])) {
    header('Location: index.php');
    exit;
}

$hospital_id = $_SESSION['hospital_id'];
if (isset($_GET['id'])) {
    $certificate_id = $_GET['id']; // Get the certificate ID from the URL

    // Fetch the certificate details from the database
    $stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE id = ? AND hospital_id = ?");
    $stmt->execute([$certificate_id, $hospital_id]);
    $birth = $stmt->fetch();

    if ($birth) {
        // Create PDF document
        $pdf = new TCPDF();
        $pdf->AddPage();

        // Add title and certificate details to the PDF
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Birth Certificate', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);

        // Add certificate details (you can format this as needed)
        $pdf->Cell(0, 10, 'Name: ' . htmlspecialchars($birth['child_name']), 0, 1);
        $pdf->Cell(0, 10, 'Date of Birth: ' . htmlspecialchars($birth['date_of_birth']), 0, 1);
        $pdf->Cell(0, 10, 'Certificate Number: ' . htmlspecialchars($birth['certificate_number']), 0, 1);

        // Output the PDF for download
        $pdf->Output('certificate_' . $birth['certificate_number'] . '.pdf', 'D');
    } else {
        echo 'Certificate not found.';
    }
} else {
    echo 'Invalid certificate ID.';
}
?>