<?php
session_start();

// Connect to database
require 'connectDB.php';

// Set headers for Excel file download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="user_logs_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Initialize filter conditions and parameters
$conditions = [];
$params = [];
$types = '';

// Apply filters based on form input
if (isset($_POST['To_Excel'])) {
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
}

// Build the SQL query
$sql = "SELECT * FROM users_logs";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY no DESC";

// Prepare and execute the query
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    die('SQL Error: ' . mysqli_error($conn));
}

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Output the Excel content
echo "No\tName\tSerial Number\tCard UID\tDate\tTime In\tTime Out\n"; // Header row
while ($row = mysqli_fetch_assoc($result)) {
    echo htmlspecialchars($row['no']) . "\t" .
         htmlspecialchars($row['username']) . "\t" .
         htmlspecialchars($row['serialnumber']) . "\t" .
         htmlspecialchars($row['card_uid']) . "\t" .
         htmlspecialchars($row['checkindate']) . "\t" .
         htmlspecialchars($row['timein']) . "\t" .
         htmlspecialchars($row['timeout']) . "\n";
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit();
?>