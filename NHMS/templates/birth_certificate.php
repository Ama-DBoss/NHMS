<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Birth Certificate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .certificate {
            border: 2px solid #000;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .details {
            margin-top: 20px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <h1>Certificate of Birth</h1>
        <div class="details">
            <div class="row">
                <span class="label">Child's Name:</span>
                <span><?php echo htmlspecialchars($child_name); ?></span>
            </div>
            <div class="row">
                <span class="label">Date of Birth:</span>
                <span><?php echo htmlspecialchars($date_of_birth); ?></span>
            </div>
            <div class="row">
                <span class="label">Place of Birth:</span>
                <span><?php echo htmlspecialchars($place_of_birth); ?></span>
            </div>
            <div class="row">
                <span class="label">Father's Name:</span>
                <span><?php echo htmlspecialchars($father_name); ?></span>
            </div>
            <div class="row">
                <span class="label">Mother's Name:</span>
                <span><?php echo htmlspecialchars($mother_name); ?></span>
            </div>
            <div class="row">
                <span class="label">Certificate Number:</span>
                <span><?php echo htmlspecialchars($certificate_number); ?></span>
            </div>
            <div class="row">
                <span class="label">Date of Issue:</span>
                <span><?php echo date('Y-m-d'); ?></span>
            </div>
        </div>
    </div>
</body>
</html>