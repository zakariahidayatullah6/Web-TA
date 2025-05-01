<?php  
//Connect to database
require 'connectDB.php';
date_default_timezone_set('Asia/Jakarta');
$d = date("Y-m-d");
$t = date("H:i:sa");

if (isset($_GET['card_uid'])) {
    
    $card_uid = $_GET['card_uid'];

    // Langsung ke logika kartu tanpa validasi device
    $sql = "SELECT * FROM users WHERE card_uid=?";
    $result = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($result, $sql)) {
        echo "SQL_Error_Select_card";
        exit();
    }
    mysqli_stmt_bind_param($result, "s", $card_uid);
    mysqli_stmt_execute($result);
    $resultl = mysqli_stmt_get_result($result);
    
    if ($row = mysqli_fetch_assoc($resultl)) {
        // Kartu sudah terdaftar
        if ($row['add_card'] == 1) {
            $Uname = $row['username'];
            $Number = $row['serialnumber'];
            $sql = "SELECT * FROM users_logs WHERE card_uid=? AND checkindate=? AND card_out=0";
            $result = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($result, $sql)) {
                echo "SQL_Error_Select_logs";
                exit();
            }
            mysqli_stmt_bind_param($result, "ss", $card_uid, $d);
            mysqli_stmt_execute($result);
            $resultl = mysqli_stmt_get_result($result);
            
            // Login
            if (!$row = mysqli_fetch_assoc($resultl)) {
                $sql = "INSERT INTO users_logs (username, serialnumber, card_uid, checkindate, timein, timeout) VALUES (?, ?, ?, ?, ?, ?)";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_Select_login1";
                    exit();
                }
                $timeout = "00:00:00";
                mysqli_stmt_bind_param($result, "sdssss", $Uname, $Number, $card_uid, $d, $t, $timeout);
                mysqli_stmt_execute($result);

                echo "login" . $Uname;
                exit();
            }
            // Logout
            else {
                $sql = "UPDATE users_logs SET timeout=?, card_out=1 WHERE card_uid=? AND checkindate=? AND card_out=0";
                $result = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($result, $sql)) {
                    echo "SQL_Error_insert_logout1";
                    exit();
                }
                mysqli_stmt_bind_param($result, "sss", $t, $card_uid, $d);
                mysqli_stmt_execute($result);

                echo "logout" . $Uname;
                exit();
            }
        }
        else if ($row['add_card'] == 0) {
            echo "Not registered!";
            exit();
        }
    }
    else {
        // Kartu baru, tambahkan ke tabel users
        $sql = "UPDATE users SET card_select=0";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_insert";
            exit();
        }
        mysqli_stmt_execute($result);

        // Tambahkan kartu baru dengan add_card = 1
        $sql = "INSERT INTO users (card_uid, card_select, user_date, add_card) VALUES (?, 1, CURDATE(), 1)";
        $result = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($result, $sql)) {
            echo "SQL_Error_Select_add";
            exit();
        }
        mysqli_stmt_bind_param($result, "s", $card_uid);
        mysqli_stmt_execute($result);

        echo "successful";
        exit();
    }
}

echo "Invalid Request!";
exit();
?>