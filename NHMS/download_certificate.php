<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/tcpdf/tcpdf.php';

// Redirect if not logged in
if (!is_hospital() && !is_admin()) {
    header('HTTP/1.1 401 Unauthorized');
    die('Unauthorized access');
}

$certificate_id = $_GET['id'] ?? null;
$certificate_type = $_GET['type'] ?? 'birth';

// Validate inputs
if (!$certificate_id || !is_numeric($certificate_id)) {
    header('HTTP/1.1 400 Bad Request');
    die('Invalid certificate ID');
}

if (!in_array($certificate_type, ['birth', 'death'])) {
    header('HTTP/1.1 400 Bad Request');
    die('Invalid certificate type');
}

try {
    $hospital_id = is_hospital() ? $_SESSION['hospital_id'] : null;
    
    if ($certificate_type === 'birth') {
        if (is_hospital()) {
            $stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE id = ? AND hospital_id = ?");
            $stmt->execute([$certificate_id, $hospital_id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM birth_certificates WHERE id = ?");
            $stmt->execute([$certificate_id]);
        }
        $certificate = $stmt->fetch();
        $cert_title = 'Birth Certificate';
        $name_field = 'child_name';
    } else {
        if (is_hospital()) {
            $stmt = $pdo->prepare("SELECT * FROM death_certificates WHERE id = ? AND hospital_id = ?");
            $stmt->execute([$certificate_id, $hospital_id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM death_certificates WHERE id = ?");
            $stmt->execute([$certificate_id]);
        }
        $certificate = $stmt->fetch();
        $cert_title = 'Death Certificate';
        $name_field = 'deceased_name';
    }

    if (!$certificate) {
        header('HTTP/1.1 404 Not Found');
        die('Certificate not found');
    }

    // Create PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_PAGE_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(true, 15);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->AddPage();

    // Add header
    $pdf->SetFont('helvetica', 'B', 18);
    $pdf->Cell(0, 10, $cert_title, 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'National Birth & Death Management System', 0, 1, 'C');
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Ln(5);
    
    // Add certificate details
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Name:', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, htmlspecialchars($certificate[$name_field]), 0, 1);
    
    if ($certificate_type === 'birth') {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Date of Birth:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['date_of_birth']), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Place of Birth:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['place_of_birth']), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Father:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['father_name']), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Mother:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['mother_name']), 0, 1);
    } else {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Date of Death:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['date_of_death']), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Place of Death:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['place_of_death']), 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(50, 8, 'Cause of Death:', 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 8, htmlspecialchars($certificate['cause_of_death']), 0, 1);
    }
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Certificate Number:', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, htmlspecialchars($certificate['certificate_number']), 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(50, 8, 'Issue Date:', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 8, date('Y-m-d', strtotime($certificate['issue_date'])), 0, 1);

    // Output PDF
    $filename = strtolower($certificate_type) . '_cert_' . $certificate['certificate_number'] . '.pdf';
    $pdf->Output($filename, 'D');
} catch (Exception $e) {
    error_log('PDF generation error: ' . $e->getMessage());
    header('HTTP/1.1 500 Internal Server Error');
    die('Error generating certificate');
}
