<?php
session_start();
require 'connectDB.php'; // Pastikan file ini ada untuk koneksi database

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        // Cek apakah email sudah terdaftar
        $sql = "SELECT * FROM login WHERE login_email = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $error = "Database error!";
        } else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $error = "Email already registered!";
            } else {
                // Simpan data pengguna baru ke tabel login
                $sql = "INSERT INTO login (login_name, login_email, login_pwd) VALUES (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $error = "Database error!";
                } else {
                    mysqli_stmt_bind_param($stmt, "sss", $name, $email, $password);
                    if (mysqli_stmt_execute($stmt)) {
                        header("Location: login.php?register=success");
                        exit();
                    } else {
                        $error = "Failed to register!";
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
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

        .text-decoration-none {
            color: #4a5568;
        }

        .text-decoration-none:hover {
            color: #2d3748;
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
                            <img src="images/tekom.jpg" class="img-fluid h-100 object-fit-cover" style="border-radius: 15px 0 0 15px;" alt="Register Background">
                        </div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <h1 class="h4 text-center mb-4">Register</h1>

                                <?php
                                if (isset($error)) {
                                    echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                                }
                                if (isset($_GET['register']) && $_GET['register'] == "success") {
                                    echo '<div class="alert alert-success">Registration successful! Please login.</div>';
                                }
                                ?>

                                <form action="" method="post" enctype="multipart/form-data">
                                    <div class="form-group mb-3">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Full Name" required/>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" required/>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Password" required/>
                                    </div>
                                    <button type="submit" name="register" class="btn btn-primary btn-block w-100 mb-3">Register</button>
                                    <hr>
                                    <div class="text-center">
                                        <p>Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                                    </div>
                                </form>
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