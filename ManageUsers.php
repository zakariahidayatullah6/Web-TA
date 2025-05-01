<?php
session_start();
if (!isset($_SESSION['Login-name'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require 'connectDB.php';

// Initialize variables for messages and selected user data
$message = '';
$selected_user = null;

// Function to check if serial number is unique
function checkSerialNumber($conn, $serialnumber, $user_id = null) {
    $sql = "SELECT 'no' FROM users WHERE serialnumber = ?";
    if ($user_id) {
        $sql .= " AND 'no' != ?";
    }
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        if ($user_id) {
            mysqli_stmt_bind_param($stmt, "si", $serialnumber, $user_id);
        } else {
            mysqli_stmt_bind_param($stmt, "s", $serialnumber);
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_num_rows($result) == 0;
    }
    return false;
}

// Handle Add User
if (isset($_POST['user_add'])) {
    $username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $serialnumber = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ?: '';
    $gender = in_array($_POST['gender'], ['Male', 'Female']) ? $_POST['gender'] : 'Male';

    if (empty($username) || empty($serialnumber)) {
        $message = '<div class="alert alert-danger">Name and Serial Number are required.</div>';
    } elseif (!preg_match('/^[a-zA-Z\s]{2,50}$/', $username)) {
        $message = '<div class="alert alert-danger">Name must be 2-50 characters (letters and spaces only).</div>';
    } elseif (!preg_match('/^\d{1,15}$/', $serialnumber)) {
        $message = '<div class="alert alert-danger">Serial number must be 1-15 digits.</div>';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format.</div>';
    } else {
        // Check if serial number is unique
        if (checkSerialNumber($conn, $serialnumber)) {
            $sql = "INSERT INTO users (username, serialnumber, gender, email, user_date, add_card, card_select) VALUES (?, ?, ?, ?, CURDATE(), 1, 0)";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssss", $username, $serialnumber, $gender, $email);
                if (mysqli_stmt_execute($stmt)) {
                    $message = '<div class="alert alert-success">User added successfully.</div>';
                    // Reset form
                    $_POST = [];
                } else {
                    $message = '<div class="alert alert-danger">Failed to add user.</div>';
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = '<div class="alert alert-danger">Database error.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">The serial number is already taken.</div>';
        }
    }
}

// Handle Update User
if (isset($_POST['user_upd'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    $username = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $serialnumber = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ?: '';
    $gender = in_array($_POST['gender'], ['Male', 'Female']) ? $_POST['gender'] : 'Male';

    if (!$user_id) {
        $message = '<div class="alert alert-warning">No user selected for update.</div>';
    } elseif (empty($username) || empty($serialnumber)) {
        $message = '<div class="alert alert-danger">Name and Serial Number are required.</div>';
    } elseif (!preg_match('/^[a-zA-Z\s]{2,50}$/', $username)) {
        $message = '<div class="alert alert-danger">Name must be 2-50 characters (letters and spaces only).</div>';
    } elseif (!preg_match('/^\d{1,15}$/', $serialnumber)) {
        $message = '<div class="alert alert-danger">Serial number must be 1-15 digits.</div>';
    } elseif ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-danger">Invalid email format.</div>';
    } else {
        // Check if serial number is unique (excluding current user)
        if (checkSerialNumber($conn, $serialnumber, $user_id)) {
            $sql = "UPDATE users SET username = ?, serialnumber = ?, gender = ?, email = ? WHERE 'no' = ?";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $username, $serialnumber, $gender, $email, $user_id);
                if (mysqli_stmt_execute($stmt)) {
                    $message = '<div class="alert alert-success">User updated successfully.</div>';
                    // Reset form
                    $_POST = [];
                } else {
                    $message = '<div class="alert alert-danger">Failed to update user.</div>';
                }
                mysqli_stmt_close($stmt);
            } else {
                $message = '<div class="alert alert-danger">Database error.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">The serial number is already taken.</div>';
        }
    }
}

// Handle Remove User
if (isset($_POST['user_rmo'])) {
    $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
    if (!$user_id) {
        $message = '<div class="alert alert-warning">No user selected for deletion.</div>';
    } else {
        $sql = "DELETE FROM users WHERE 'no' = ?";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = '<div class="alert alert-success">User deleted successfully.</div>';
                // Reset form
                $_POST = [];
            } else {
                $message = '<div class="alert alert-danger">Failed to delete user.</div>';
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = '<div class="alert alert-danger">Database error.</div>';
        }
    }
}

// Handle Select User (when Card UID is clicked)
if (isset($_POST['select_user'])) {
    $card_uid = filter_var($_POST['card_uid'], FILTER_SANITIZE_STRING);
    $sql = "SELECT 'no', username, serialnumber, gender, email, card_uid FROM users WHERE card_uid = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $card_uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $selected_user = $row;
            // Populate form with selected user data
            $_POST['user_id'] = $row['no'];
            $_POST['name'] = $row['username'];
            $_POST['number'] = $row['serialnumber'];
            $_POST['email'] = $row['email'];
            $_POST['gender'] = $row['gender'];
            $_POST['card_uid'] = $row['card_uid']; // Tambahkan card_uid ke $_POST
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch users from the database for the table
$users = [];
$sql = "SELECT * FROM users ORDER BY 'no' DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_free_result($result);
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/IPB.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="css/Manageusers.css" rel="stylesheet">
    <style>
        .selected-user-info {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="container-fluid py-4">
    <div class="page-header text-center animate-fade-in mb-5">
        <h1 class="display-4">
            <i class="fas fa-user-cog me-3"></i>User Management
        </h1>
        <p class="lead">Add, update, or remove user information</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 rounded-lg mb-5">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>User Information Form</h4>
                </div>
                <div class="card-body">
                    <form method="POST" autocomplete="off">
                        <div class="alert_user">
                            <?php echo $message; ?>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                                <hr>
                            </div>
                            <input type="hidden" name="user_id" id="user_id" value="<?php echo isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : ''; ?>">
                            <div class="col-md-4 mb-3">
                                <label for="card_uid" class="form-label">Card UID</label>
                                <input type="text" class="form-control" name="card_uid" id="card_uid" placeholder="Card UID will appear here" value="<?php echo isset($_POST['card_uid']) ? htmlspecialchars($_POST['card_uid']) : ''; ?>" readonly>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">User Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter user name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="number" class="form-label">Serial Number</label>
                                <input type="text" class="form-control" name="number" id="number" placeholder="Enter serial number" pattern="[0-9]+" value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Display selected user data below Basic Information -->
                        <?php if ($selected_user): ?>
                            <div class="selected-user-info">
                                <h6>Selected User Information:</h6>
                                <p><strong>Card UID:</strong> <?php echo htmlspecialchars($selected_user['card_uid']); ?></p>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($selected_user['username']); ?></p>
                                <p><strong>Serial Number:</strong> <?php echo htmlspecialchars($selected_user['serialnumber']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($selected_user['email'] ?: 'N/A'); ?></p>
                                <p><strong>Gender:</strong> <?php echo htmlspecialchars($selected_user['gender']); ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="text-primary"><i class="fas fa-cog me-2"></i>Additional Information</h5>
                                <hr>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label d-block">Gender</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] === 'Female') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="genderFemale">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="genderMale" value="Male" <?php echo (!isset($_POST['gender']) || $_POST['gender'] === 'Male') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="genderMale">Male</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" name="user_add" class="btn btn-success me-2">
                                <i class="fas fa-plus-circle me-2"></i>Add User
                            </button>
                            <button type="submit" name="user_upd" class="btn btn-primary me-2">
                                <i class="fas fa-edit me-2"></i>Update User
                            </button>
                            <button type="submit" name="user_rmo" class="btn btn-danger">
                                <i class="fas fa-trash-alt me-2"></i>Remove User
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-lg mt-4">
                <div class="card-header bg-white">
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Registered Users</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="Manage_users">
                            <div class="table-responsive-sm" style="max-height: 870px; background-color: rgb(43, 73, 165); border-radius: 0 0 1rem 1rem;">
                                <table class="table">
                                    <thead class="table-primary">
                                        <tr>
                                            <th>Card UID</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Serial Number</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-secondary">
                                        <?php if (empty($users)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No users found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($users as $row): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($row['card_select'] == 1): ?>
                                                            <span><i class="fas fa-check" title="The selected UID"></i></span>
                                                        <?php endif; ?>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="card_uid" value="<?php echo htmlspecialchars($row['card_uid']); ?>">
                                                            <button type="submit" name="select_user" class="select_btn" title="Select this UID">
                                                                <?php echo htmlspecialchars($row['card_uid']); ?>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['serialnumber']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['user_date']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>