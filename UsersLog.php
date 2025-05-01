<?php
session_start();
if (!isset($_SESSION['Login-name'])) {
    header("location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/IPB.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="css/userslog.css">
    
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
        /* Pastikan halaman dapat di-scroll jika konten melebihi tinggi layar */
        html, body {
            height: auto !important; /* Pastikan tinggi menyesuaikan konten */
            margin: 0;
            overflow-x: hidden; /* Mencegah scroll horizontal */
            overflow-y: auto !important; /* Pastikan scroll bar vertikal muncul */
        }
        
        /* Pastikan elemen main tidak membatasi tinggi */
        main {
            min-height: 100vh; /* Memastikan konten minimal setinggi layar */
            overflow: visible; /* Izinkan konten melebihi tinggi */
        }

        /* Gaya untuk tabel, tanpa batasan tinggi */
        #userslog {
            background-color: #f8f9fa;
            border-radius: 0 0 1rem 1rem;
        }
    </style>

    <script>
        $(window).on("load resize", function() {
            var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
            $('.tbl-header').css({'padding-right': scrollWidth});
        }).resize();

        $(document).ready(function() {
            // Store filter parameters
            let filterParams = {
                select_date: 1
            };
            let autoRefreshInterval = null;

            // Function to start auto-refresh
            function startAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
                autoRefreshInterval = setInterval(loadUserLogs, 5000);
                console.log("Auto-refresh started");
            }

            // Function to stop auto-refresh
            function stopAutoRefresh() {
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                    autoRefreshInterval = null;
                    console.log("Auto-refresh stopped");
                }
            }

            // Function to force layout recalculation
            function forceLayoutRecalculation() {
                // Trigger a reflow to force the browser to recalculate the layout
                document.body.style.height = 'auto';
                document.body.offsetHeight; // This forces a reflow
                window.dispatchEvent(new Event('resize')); // Trigger resize event
            }

            // Function to load user logs
            function loadUserLogs() {
                console.log("Loading logs with params:", filterParams);
                $.ajax({
                    url: "user_log_up.php",
                    type: 'POST',
                    data: filterParams,
                    timeout: 10000
                }).done(function(data) {
                    $('#userslog').html(data);
                    console.log("Logs loaded successfully");
                    // Force layout recalculation after loading data
                    forceLayoutRecalculation();
                }).fail(function(xhr, status, error) {
                    console.error("Error loading logs:", status, error);
                    $('#userslog').html('<div class="alert alert-danger">Failed to load logs. Please try again later.</div>');
                    // Force layout recalculation even on failure
                    forceLayoutRecalculation();
                });
            }

            // Initial load (default: today's logs)
            loadUserLogs();
            startAutoRefresh();

            // Apply filters when the "Apply Filters" button is clicked
            $('#user_log').on('click', function() {
                console.log("Applying filters...");
                stopAutoRefresh();

                // Update filter parameters with user input
                filterParams = {
                    log_date: 1,
                    date_sel_start: $('#date_sel_start').val(),
                    date_sel_end: $('#date_sel_end').val(),
                    time_sel: $('input[name="time_sel"]:checked').val(),
                    time_sel_start: $('#time_sel_start').val(),
                    time_sel_end: $('#time_sel_end').val(),
                    card_sel: $('#card_sel').val()
                };

                // Load logs with the new filter
                loadUserLogs();

                // Close the modal
                $('#Filter-export').modal('hide');
                startAutoRefresh();
            });

            // Ensure modal backdrop is removed when modal is hidden
            $('#Filter-export').on('hidden.bs.modal', function() {
                console.log("Modal hidden");
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');

                // If filterParams is not set to log_date, reset to default
                if (!filterParams.log_date) {
                    filterParams = { select_date: 1 };
                    loadUserLogs();
                }
                startAutoRefresh();
            });

            // Debug modal show event
            $('#Filter-export').on('shown.bs.modal', function() {
                console.log("Modal shown");
            });
        });
    </script>
</head>
<body>
<?php include 'header.php'; ?> 

<main class="container-fluid py-4">
    <div class="page-header text-center animate-fade-in mb-5">
        <h1 class="display-4">
            <i class="fas fa-clipboard-list me-3"></i>
            User Activity Logs
        </h1>
        <p class="lead">Track and monitor user attendance records</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-filter me-2"></i>Log Options</h4>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#Filter-export">
                            <i class="fas fa-file-export me-2"></i>Filter / Export
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <div id="userslog" class="table-responsive"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log filter Modal -->
    <div class="modal fade" id="Filter-export" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="filterModalLabel"><i class="fas fa-filter me-2"></i>Filter User Logs</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="Export_Excel.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-calendar-alt me-2"></i>Date Range</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_sel_start" class="form-label">Start Date:</label>
                                        <input type="date" class="form-control" name="date_sel_start" id="date_sel_start">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date_sel_end" class="form-label">End Date:</label>
                                        <input type="date" class="form-control" name="date_sel_end" id="date_sel_end">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-clock me-2"></i>Time Range</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">Filter By:</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input time_sel" type="radio" name="time_sel" id="radio-one" value="Time_in" checked>
                                            <label class="form-check-label" for="radio-one">Time-in</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input time_sel" type="radio" name="time_sel" id="radio-two" value="Time_out">
                                            <label class="form-check-label" for="radio-two">Time-out</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="time_sel_start" class="form-label">Start Time:</label>
                                        <input type="time" class="form-control" name="time_sel_start" id="time_sel_start">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="time_sel_end" class="form-label">End Time:</label>
                                        <input type="time" class="form-control" name="time_sel_end" id="time_sel_end">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-primary"><i class="fas fa-filter me-2"></i>Additional Filters</h5>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="card_sel" class="form-label">Filter By User:</label>
                                        <select class="form-select card_sel" name="card_sel" id="card_sel">
                                            <option value="0">All Users</option>
                                            <?php
                                            require 'connectDB.php';
                                            $sql = "SELECT * FROM users WHERE add_card=1 ORDER BY no ASC";
                                            $result = mysqli_stmt_init($conn);
                                            if (!mysqli_stmt_prepare($result, $sql)) {
                                                echo '<p class="error">SQL Error</p>';
                                            } else {
                                                mysqli_stmt_execute($result);
                                                $resultl = mysqli_stmt_get_result($result);
                                                while ($row = mysqli_fetch_assoc($resultl)) {
                                                    echo '<option value="' . htmlspecialchars($row['card_uid']) . '">' . htmlspecialchars($row['username']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" name="user_log" id="user_log" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filters
                        </button>
                        <button type="submit" name="To_Excel" class="btn btn-success">
                            <i class="fas fa-file-excel me-2"></i>Export to Excel
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
</body>
</html>