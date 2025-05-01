<?php
header('Content-Type: application/json');
require 'connectDB.php';

// Fungsi pembantu
function respond($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

function checkSerialNumber($conn, $serialnumber, $user_id) {
    $sql = "SELECT serialnumber FROM users WHERE serialnumber = ? AND no != ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "si", $serialnumber, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) == 0;
    }
    return false;
}

// Add user
if (isset($_POST['Add'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $serialnumber = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $gender = in_array($_POST['gender'], ['Male', 'Female']) ? $_POST['gender'] : 'Male';

    if (!$user_id || !$username || !$serialnumber || !$email) {
        respond(false, 'Empty or invalid fields');
    }

    $sql = "SELECT add_card FROM users WHERE no = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['add_card'] == 0) {
                if (checkSerialNumber($conn, $serialnumber, $user_id)) {
                    $sql = "UPDATE users SET username = ?, serialnumber = ?, gender = ?, email = ?, user_date = CURDATE(), add_card = 1 WHERE no = ?";
                    $stmt = mysqli_stmt_init($conn);
                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "ssssi", $username, $serialnumber, $gender, $email, $user_id);
                        mysqli_stmt_execute($stmt);
                        respond(true, 'User added successfully');
                    }
                } else {
                    respond(false, 'The serial number is already taken!');
                }
            } else {
                respond(false, 'This user already exists');
            }
        } else {
            respond(false, 'No selected card found');
        }
    }
    respond(false, 'SQL error');
}

// Update user
if (isset($_POST['Update'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $serialnumber = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $gender = in_array($_POST['gender'], ['Male', 'Female']) ? $_POST['gender'] : 'Male';

    if (!$user_id || !$username || !$serialnumber || !$email) {
        respond(false, 'Empty or invalid fields');
    }

    $sql = "SELECT add_card FROM users WHERE no = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['add_card'] == 0) {
                respond(false, 'First, you need to add the user!');
            }
            if (checkSerialNumber($conn, $serialnumber, $user_id)) {
                $sql = "UPDATE users SET username = ?, serialnumber = ?, gender = ?, email = ? WHERE no = ?";
                $stmt = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssssi", $username, $serialnumber, $gender, $email, $user_id);
                    mysqli_stmt_execute($stmt);
                    respond(true, 'User updated successfully');
                }
            } else {
                respond(false, 'The serial number is already taken!');
            }
        } else {
            respond(false, 'No selected user to be updated!');
        }
    }
    respond(false, 'SQL error');
}

// Select user
if (isset($_POST['select'])) {
    $card_uid = filter_var($_POST['card_uid'], FILTER_SANITIZE_STRING);
    $sql = "SELECT no, username, serialnumber, gender, email FROM users WHERE card_uid = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $card_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            respond(true, 'User selected successfully', $row);
        } else {
            respond(false, 'No user found with this card UID');
        }
    }
    respond(false, 'SQL error');
}

// Delete user
if (isset($_POST['delete'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if (!$user_id) {
        respond(false, 'No selected user to remove');
    }

    $sql = "DELETE FROM users WHERE no = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        respond(true, 'User deleted successfully');
    }
    respond(false, 'SQL error');
}

mysqli_close($conn);
respond(false, 'Invalid request');
?>