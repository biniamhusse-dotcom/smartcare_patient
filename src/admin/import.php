<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

$message = '';
$messageType = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $message = 'CSV imported successfully!';
        $messageType = 'success';
    } elseif ($_GET['status'] === 'invalid_file') {
        $message = 'Error: Please select a valid CSV file.';
        $messageType = 'danger';
    }
}

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import CSV - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header { background-color: #1a5276; color: white; padding: 15px 20px; }
        .admin-header h4 { margin: 0; font-weight: bold; }
        .import-card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
    </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-hospital"></i> Import Patient Database (CSV)</h4>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="container-fluid px-4 mt-3">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> py-2"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="import-card">
                <h5 class="mb-3"><i class="bi bi-upload"></i> Upload CSV File</h5>
                
                <div class="alert alert-info py-2 mb-3">
                    <strong>Expected CSV columns:</strong><br>
                    Sno, PatientID, Fname, Mname, Sname, Sex, DOB (m/d/Y), District, Community, Mobile
                </div>

                <form action="../includes/import_csv.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="file" name="file" class="form-control" required accept=".csv">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="importSubmit" class="btn btn-primary">
                            <i class="bi bi-upload"></i> Upload & Process CSV
                        </button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
