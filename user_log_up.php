<?php
session_start();
?>

<div class="table-responsive" style="max-height: 500px;"> 
    <table class="table">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Serial Number</th>
                <th>Card UID</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody class="table-secondary">
            <?php
            // Connect to database
            require 'connectDB.php';

            // Initialize filter conditions and parameters
            $conditions = [];
            $params = [];
            $types = '';

            try {
                // Default: Show today's logs if no filters are applied
                if (isset($_POST['select_date']) && $_POST['select_date'] == 1) {
                    $conditions[] = "checkindate = ?";
                    $params[] = date("Y-m-d");
                    $types .= 's';
                } elseif (isset($_POST['log_date']) && $_POST['log_date'] == 1) {
                    // Date range filter
                    $start_date = !empty($_POST['date_sel_start']) ? $_POST['date_sel_start'] : date("Y-m-d");
                    $end_date = !empty($_POST['date_sel_end']) ? $_POST['date_sel_end'] : $start_date;

                    if ($start_date && $end_date) {
                        $conditions[] = "checkindate BETWEEN ? AND ?";
                        $params[] = $start_date;
                        $params[] = $end_date;
                        $types .= 'ss';
                    } elseif ($start_date) {
                        $conditions[] = "checkindate = ?";
                        $params[] = $start_date;
                        $types .= 's';
                    }

                    // Time range filter
                    $time_field = ($_POST['time_sel'] == "Time_out") ? "timeout" : "timein";
                    $start_time = !empty($_POST['time_sel_start']) ? $_POST['time_sel_start'] : null;
                    $end_time = !empty($_POST['time_sel_end']) ? $_POST['time_sel_end'] : null;

                    if ($start_time && $end_time) {
                        $conditions[] = "$time_field BETWEEN ? AND ?";
                        $params[] = $start_time;
                        $params[] = $end_time;
                        $types .= 'ss';
                    } elseif ($start_time) {
                        $conditions[] = "$time_field = ?";
                        $params[] = $start_time;
                        $types .= 's';
                    }

                    // Card UID filter
                    $card_sel = !empty($_POST['card_sel']) && $_POST['card_sel'] != '0' ? $_POST['card_sel'] : null;
                    if ($card_sel) {
                        $conditions[] = "card_uid = ?";
                        $params[] = $card_sel;
                        $types .= 's';
                    }
                } else {
                    // Auto-refresh: Show today's logs if no filter is applied
                    $conditions[] = "checkindate = ?";
                    $params[] = date("Y-m-d");
                    $types .= 's';
                }

                // Build the SQL query
                $sql = "SELECT * FROM users_logs";
                if (!empty($conditions)) {
                    $sql .= " WHERE " . implode(" AND ", $conditions);
                }
                $sql .= " ORDER BY 'no' DESC";

                // Prepare and execute the query
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    throw new Exception('SQL Error: ' . mysqli_error($conn));
                }

                if (!empty($params)) {
                    mysqli_stmt_bind_param($stmt, $types, ...$params);
                }
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception('SQL Execution Error: ' . mysqli_error($conn));
                }

                $result = mysqli_stmt_get_result($stmt);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['no']); ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['serialnumber']); ?></td>
                            <td><?php echo htmlspecialchars($row['card_uid']); ?></td>
                            <td><?php echo htmlspecialchars($row['checkindate']); ?></td>
                            <td><?php echo htmlspecialchars($row['timein']); ?></td>
                            <td><?php echo htmlspecialchars($row['timeout']); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="7" class="text-center">No logs found</td>
                    </tr>
                    <?php
                }
                mysqli_stmt_close($stmt);
            } catch (Exception $e) {
                echo '<tr><td colspan="7" class="text-center text-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            } finally {
                mysqli_close($conn);
            }
            ?>
        </tbody>
    </table>
</div>