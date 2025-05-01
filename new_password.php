<?php
session_start();
require 'connectDB.php'; // Pastikan file ini ada untuk koneksi database

// Inisialisasi variabel untuk pesan error
$error_message = '';

if (!isset($_GET['email'])) {
    $error_message = "Invalid or missing email. Please request a new password reset.";
} else {
    $email = urldecode($_GET['email']);

    // Validasi email dari tabel login
    $sql = "SELECT * FROM login WHERE login_email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $error_message = "Database error occurred. Please try again.";
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 0) {
            $error_message = "Email not found. Please request a new password reset.";
        }
    }
}

// Jika form disubmit, update password
if (isset($_POST['new_password']) && empty($error_message)) {
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash password baru

    // Update password di tabel login
    $sql = "UPDATE login SET login_pwd = ? WHERE login_email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $error_message = "Database error occurred. Please try again.";
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $newPassword, $email);
        if (!mysqli_stmt_execute($stmt)) {
            $error_message = "Failed to update password. Please try again.";
        } else {
            // Jika berhasil, redirect ke login.php
            header("Location: login.php?resetpassword=success");
            exit();
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/IPB.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #3a506b 50%, #5b6b7c 100%);
            animation: gradientBG 15s ease infinite;
            background-size: 400% 400%;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        }

        .btn-primary {
            background: linear-gradient(45deg, #4a5568, #718096);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: linear-gradient(45deg, #2d3748, #4a5568);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e0;
            padding: 12px;
            background: #f7fafc;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(74, 85, 104, 0.2);
            border-color: #718096;
        }

        .main-title {
            color: rgb(255, 255, 255);
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <h1 class="main-title mb-4">Presensi RFID</h1>
            <div class="card o-hidden border-0 shadow-lg">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-login-image">
                            <img src="images/tekom.jpg" class="img-fluid h-100 object-fit-cover" style="border-radius: 15px 0 0 15px;" alt="Login Background">
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <h1 class="h4 text-center mb-4">Set New Password</h1>

                                <?php
                                // Tampilkan pesan error jika ada
                                if (!empty($error_message)) {
                                    echo '<div class="alert alert-danger">' . htmlspecialchars($error_message) . '</div>';
                                }
                                ?>

                                <?php if (empty($error_message)) { ?>
                                    <form action="" method="post" enctype="multipart/form-data">
                                        <div class="form-group mb-3">
                                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password" required/>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block w-100 mb-3">Set Password</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>