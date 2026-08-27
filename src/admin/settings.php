<?php
require_once '../includes/auth.php';
require_once '../includes/settings.php';
requireLogin();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facilityName = trim($_POST['facility_name'] ?? '');
    
    if (empty($facilityName)) {
        $message = 'Facility name is required.';
        $messageType = 'danger';
    } else {
        setSetting('facility_name', $facilityName);
        $message = 'Settings saved successfully!';
        $messageType = 'success';
    }
}

$facilityName = getFacilityName();

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header { background-color: #1a5276; color: white; padding: 15px 20px; }
        .admin-header h4 { margin: 0; font-weight: bold; }
        .settings-card { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 600px; }
        .btn-save { background-color: #1a5276; border: none; padding: 10px 30px; }
        .btn-save:hover { background-color: #154360; }
    </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-hospital"></i> Admin Settings</h4>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="container-fluid px-4 mt-3">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> py-2"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="settings-card">
                <h5 class="mb-4"><i class="bi bi-gear"></i> Facility Settings</h5>
                <form method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Facility Name</label>
                        <input type="text" name="facility_name" class="form-control" required 
                               value="<?php echo e($facilityName); ?>"
                               placeholder="Enter facility name">
                        <small class="text-muted">This name appears on the public search page, admin panel, and login screen.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-save"><i class="bi bi-check-circle"></i> Save Settings</button>
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
