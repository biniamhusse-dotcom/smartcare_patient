<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$patient) {
        header("Location: index.php");
        exit();
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("UPDATE patients SET patient_id = ?, first_name = ?, middle_name = ?, sur_name = ?, sex = ?, dob = ?, district_name = ?, community_name = ?, mobile_number = ? WHERE id = ?");
        
        $patientId = trim($_POST['patient_id'] ?? '');
        $firstName = strtoupper(trim($_POST['first_name'] ?? ''));
        $middleName = strtoupper(trim($_POST['middle_name'] ?? ''));
        $surName = strtoupper(trim($_POST['sur_name'] ?? ''));
        $sex = $_POST['sex'] ?? '';
        $dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
        $district = trim($_POST['district_name'] ?? '');
        $community = trim($_POST['community_name'] ?? '');
        $mobile = trim($_POST['mobile_number'] ?? '');

        if (empty($firstName) || empty($surName)) {
            $message = 'First name and surname are required.';
            $messageType = 'danger';
        } else {
            $stmt->execute([$patientId, $firstName, $middleName, $surName, $sex, $dob, $district, $community, $mobile, $id]);
            header("Location: index.php?msg=updated");
            exit();
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $message = 'Database error. Please try again.';
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
    <title>Edit Patient - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header { background-color: #1a5276; color: white; padding: 15px 20px; }
        .admin-header h4 { margin: 0; font-weight: bold; }
        .form-card { background: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-card label { font-weight: 600; font-size: 13px; color: #555; }
        .btn-save { background-color: #1a5276; border: none; padding: 10px 30px; }
        .btn-save:hover { background-color: #154360; }
    </style>
</head>
<body>

<div class="admin-header d-flex justify-content-between align-items-center">
    <h4><i class="bi bi-hospital"></i> Edit Patient</h4>
    <a href="index.php" class="btn btn-outline-light btn-sm"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
</div>

<div class="container-fluid px-4 mt-3">
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> py-2"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-card">
                <h5 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Patient #<?php echo $patient['patient_id']; ?></h5>
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Patient ID</label>
                            <input type="text" name="patient_id" class="form-control" value="<?php echo e($patient['patient_id']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name *</label>
                            <input type="text" name="first_name" class="form-control" required value="<?php echo e($patient['first_name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="middle_name" class="form-control" value="<?php echo e($patient['middle_name']); ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Surname *</label>
                            <input type="text" name="sur_name" class="form-control" required value="<?php echo e($patient['sur_name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sex</label>
                            <select name="sex" class="form-select">
                                <option value="M" <?php echo $patient['sex'] === 'M' ? 'selected' : ''; ?>>Male</option>
                                <option value="F" <?php echo $patient['sex'] === 'F' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="<?php echo e($patient['dob']); ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">District</label>
                            <input type="text" name="district_name" class="form-control" value="<?php echo e($patient['district_name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Community</label>
                            <input type="text" name="community_name" class="form-control" value="<?php echo e($patient['community_name']); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" name="mobile_number" class="form-control" value="<?php echo e($patient['mobile_number']); ?>">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-save"><i class="bi bi-check-circle"></i> Update Patient</button>
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
