<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Death Certificate</title>
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
        <h1>Certificate of Death</h1>
        <div class="details">
            <div class="row">
                <span class="label">Deceased's Name:</span>
                <span><?php echo htmlspecialchars($deceased_name); ?></span>
            </div>
            <div class="row">
                <span class="label">Date of Death:</span>
                <span><?php echo htmlspecialchars($date_of_death); ?></span>
            </div>
            <div class="row">
                <span class="label">Place of Death:</span>
                <span><?php echo htmlspecialchars($place_of_death); ?></span>
            </div>
            <div class="row">
                <span class="label">Cause of Death:</span>
                <span><?php echo htmlspecialchars($cause_of_death); ?></span>
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