<?php
session_start();
$isAdmin = isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boru Meda General Hospital - MRN Search</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Main App Header */
        .app-header {
            background-color: #1a5276; 
            color: #ffffff;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            font-size: 24px;
            border-radius: 4px 4px 0 0;
            margin-top: 20px;
        }

        /* Search Row Styling (Yellow background like your screenshot) */
        .search-yellow-row {
            background-color: #ffff00 !important;
        }
        .search-yellow-row input, .search-yellow-row select {
            width: 100%;
            border: 1px solid #ccc;
            padding: 2px 4px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* The Custom Search Icon Button */
        .btn-search-container {
            margin: 10px 0;
        }
        .btn-search-icon {
            background: #fff;
            border: 2px solid #e67e22; /* Orange border like screenshot */
            border-radius: 8px;
            padding: 4px;
            transition: transform 0.2s;
        }
        .btn-search-icon:hover { transform: scale(1.05); background-color: #fff9f5; }
        .btn-search-icon img { width: 32px; height: 32px; }

        /* Table Headers */
        .table-primary-header th {
            background-color: #337ab7 !important;
            color: white !important;
            font-weight: normal;
            vertical-align: middle;
            text-align: center;
        }
        .table-dark-header th {
            background-color: #212529 !important;
            color: white !important;
        }

        /* Import Section */
        .import-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid px-4">

    <!-- 1. CSV IMPORT SECTION (Top) - Admin Only -->
    <?php if ($isAdmin): ?>
    <div class="row justify-content-center mt-3">
        <div class="col-md-11">
            <div class="import-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h6 class="text-muted mb-0"><i class="bi bi-shield-lock"></i> Admin: Import Patient Database (CSV)</h6>
                    <div>
                        <a href="/admin/index.php" class="btn btn-sm btn-success me-1"><i class="bi bi-gear"></i> Admin Panel</a>
                        <a href="/admin/logout.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </div>
                </div>
                
                <?php if(isset($_GET['status'])): ?>
                    <?php if($_GET['status'] == 'success'): ?>
                        <div class="alert alert-success py-2">✓ Import completed successfully!</div>
                    <?php elseif($_GET['status'] == 'invalid_file'): ?>
                        <div class="alert alert-danger py-2">Error: Please select a valid CSV file.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <form action="includes/import_csv.php" method="post" enctype="multipart/form-data" class="row g-3 align-items-center">
                    <div class="col-auto">
                        <input type="file" name="file" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" name="importSubmit" class="btn btn-sm btn-dark">Upload & Process CSV</button>
                    </div>
                    <div class="col-auto">
                        <small class="text-muted">Columns: Sno, PatientID, Fname, Mname, Sname, Sex, DOB(m/d/Y), District, Community, Mobile</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row justify-content-center mt-3">
        <div class="col-md-11">
            <div class="import-card shadow-sm d-flex justify-content-between align-items-center">
                <span class="text-muted"><i class="bi bi-lock"></i> Admin access required for CSV import</span>
                <a href="/admin/login.php" class="btn btn-sm btn-primary"><i class="bi bi-lock"></i> Admin Login</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- 2. SEARCH UI SECTION -->
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <div class="app-header shadow-sm">Boru meda General Hospital MRN Search Page</div>

            <div class="table-responsive bg-white shadow-sm">
                <table class="table table-bordered mb-0">
                    <thead class="table-primary-header">
                        <tr>
                            <th style="width: 8%;">Patient id</th>
                            <th>First name</th>
                            <th>Middle name</th>
                            <th>Sur name</th>
                            <th style="width: 6%;">Sex</th>
                            <th style="width: 10%;">Date of birth</th>
                            <th>District name</th>
                            <th>Community name</th>
                            <th>Mobile phone number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- The Search Filters (Yellow Row) -->
                        <tr class="search-yellow-row">
                            <td><input type="text" id="p_id" placeholder="id..."></td>
                            <td><input type="text" id="fname" placeholder="search first name..."></td>
                            <td><input type="text" id="mname" placeholder="search middle name..."></td>
                            <td><input type="text" id="sname" placeholder="search sur name..."></td>
                            <td>
                                <select id="sex">
                                    <option value=""></option>
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                </select>
                            </td>
                            <td><input type="date" id="dob"></td>
                            <td><input type="text" id="district" placeholder="district..."></td>
                            <td><input type="text" id="community" placeholder="community..."></td>
                            <td><input type="text" id="mobile" placeholder="mobile..."></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Search Button (Matching Icon style) -->
            <div class="btn-search-container">
                <button type="button" id="searchBtn" class="btn-search-icon shadow-sm">
                    <img src="https://cdn-icons-png.flaticon.com/512/622/622669.png" alt="Search">
                </button>
            </div>

            <!-- 3. RESULTS DISPLAY TABLE -->
            <div class="table-responsive bg-white shadow-sm">
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark table-dark-header">
                        <tr>
                            <th style="width: 8%;">Patient id</th>
                            <th>First name</th>
                            <th>Middle name</th>
                            <th>Sur name</th>
                            <th style="width: 6%;">Sex</th>
                            <th style="width: 10%;">Date of birth</th>
                            <th>District name</th>
                            <th>Community name</th>
                            <th>Mobile phone number</th>
                        </tr>
                    </thead>
                    <tbody id="resultBody">
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <em>Fill in criteria and click the magnifying glass to search records.</em>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Scripts: jQuery and Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    let searchTimer = null;

    function performSearch() {
        let searchData = {
            p_id:      $('#p_id').val(),
            fname:     $('#fname').val(),
            mname:     $('#mname').val(),
            sname:     $('#sname').val(),
            sex:       $('#sex').val(),
            dob:       $('#dob').val(),
            district:  $('#district').val(),
            community: $('#community').val(),
            mobile:    $('#mobile').val()
        };

        $("#resultBody").html('<tr><td colspan="9" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Searching...</td></tr>');

        $.ajax({
            url: 'includes/search_logic.php',
            method: 'POST',
            data: searchData,
            success: function(response) {
                $("#resultBody").html(response);
            },
            error: function() {
                $("#resultBody").html('<tr><td colspan="9" class="text-center text-danger">Server Error.</td></tr>');
            }
        });
    }

    function debouncedSearch() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(performSearch, 300);
    }

    // Live search on typing
    $(".search-yellow-row input").on('input', debouncedSearch);

    // Live search on select change
    $(".search-yellow-row select").on('change', performSearch);

    // Button click
    $("#searchBtn").click(performSearch);

    // Enter key
    $(".search-yellow-row input").keypress(function(e) {
        if (e.which == 13) {
            clearTimeout(searchTimer);
            performSearch();
        }
    });
});
</script>

</body>
</html>