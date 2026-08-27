<?php
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/settings.php';
requireLogin();

$facilityName = getFacilityName();

$message = '';
$messageType = '';

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') {
        $message = 'Patient added successfully!';
        $messageType = 'success';
    } elseif ($_GET['msg'] === 'updated') {
        $message = 'Patient updated successfully!';
        $messageType = 'success';
    } elseif ($_GET['msg'] === 'deleted') {
        $message = 'Patient deleted successfully!';
        $messageType = 'success';
    } elseif ($_GET['msg'] === 'error') {
        $message = 'An error occurred. Please try again.';
        $messageType = 'danger';
    }
}

$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

try {
    $whereClause = '';
    $params = [];

    if (!empty($search)) {
        $whereClause = "WHERE first_name LIKE ? OR middle_name LIKE ? OR sur_name LIKE ? OR patient_id LIKE ?";
        $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM patients $whereClause");
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, ceil($totalRows / $perPage));

    $stmt = $pdo->prepare("SELECT * FROM patients $whereClause ORDER BY id ASC LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $patients = [];
    $totalRows = 0;
    $totalPages = 1;
}

function e($v) { return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo e($facilityName); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-header {
            background-color: #1a5276;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h4 { margin: 0; font-weight: bold; }
        .stats-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stats-card h2 { color: #1a5276; margin: 0; }
        .stats-card p { color: #666; margin: 5px 0 0; }
        .table-admin th { background-color: #1a5276; color: white; font-size: 13px; }
        .table-admin td { font-size: 13px; vertical-align: middle; }
        .action-btns .btn { padding: 2px 8px; font-size: 12px; }
        .search-box { max-width: 400px; }
    </style>
</head>
<body>

<div class="admin-header">
    <div>
        <h4><i class="bi bi-hospital"></i> <?php echo e($facilityName); ?> - Admin Panel</h4>
        <small>Welcome, <?php echo e($_SESSION['admin_name'] ?? 'Admin'); ?></small>
    </div>
    <div>
        <a href="/" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-search"></i> Public Search</a>
        <a href="settings.php" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-gear"></i> Settings</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>

<div class="container-fluid px-4 mt-3">

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show py-2">
            <?php echo $message; ?>
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row mb-3">
        <div class="col-md-3">
            <div class="stats-card">
                <h2><?php echo number_format($totalRows); ?></h2>
                <p>Total Patients</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <h2><?php echo number_format($totalPages); ?></h2>
                <p>Total Pages</p>
            </div>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
            <a href="add_patient.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Add Patient</a>
            <a href="import.php" class="btn btn-primary"><i class="bi bi-upload"></i> Import CSV</a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm search-box" 
                       placeholder="Search patients..." value="<?php echo e($search); ?>">
                <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Search</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-sm btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-admin mb-0">
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:8%">Patient ID</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Surname</th>
                            <th style="width:5%">Sex</th>
                            <th style="width:10%">DOB</th>
                            <th>District</th>
                            <th>Community</th>
                            <th>Mobile</th>
                            <th style="width:12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($patients)): ?>
                            <tr>
                                <td colspan="11" class="text-center py-4 text-muted">No patients found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($patients as $i => $p): ?>
                                <tr>
                                    <td><?php echo $offset + $i + 1; ?></td>
                                    <td><?php echo e($p['patient_id']); ?></td>
                                    <td><?php echo e($p['first_name']); ?></td>
                                    <td><?php echo e($p['middle_name']); ?></td>
                                    <td><?php echo e($p['sur_name']); ?></td>
                                    <td><?php echo e($p['sex']); ?></td>
                                    <td><?php echo e($p['dob']); ?></td>
                                    <td><?php echo e($p['district_name']); ?></td>
                                    <td><?php echo e($p['community_name']); ?></td>
                                    <td><?php echo e($p['mobile_number']); ?></td>
                                    <td class="action-btns">
                                        <a href="edit_patient.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_patient.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('Delete this patient? This cannot be undone.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination pagination-sm justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Prev</a>
            </li>
            <?php for ($i = max(1, $page - 3); $i <= min($totalPages, $page + 3); $i++): ?>
                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
