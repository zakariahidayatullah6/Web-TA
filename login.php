<?php
session_start();
if (isset($_SESSION['Login-name'])) {
  header("location: index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Log In Menu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/IPB.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script>
      $(document).ready(function(){
        $(document).on('click', '.message a', function(e){
          e.preventDefault();
          console.log("Link clicked!");
          $('.reset-form, .login-form').toggle();
          $('.login-title, .reset-title').toggle();
        });
      });
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #3a506b 50%, #5b6b7c 100%);
            animation: gradientBG 15s ease infinite;
            background-size: 400% 400%;
        }
        
        @keyframes gradientBG {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
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

        .login-form {
            display: block;
        }

        .reset-form {
            display: none;
        }

        .reset-title {
            display: none;
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
                                <h1 class="h4 text-center mb-4 login-title">Welcome!</h1>
                                <h1 class="h4 text-center mb-4 reset-title">Reset Password</h1>

                                <?php  
                                if (isset($_GET['error'])) {
                                    if ($_GET['error'] == "invalidEmail") {
                                        echo '<div class="alert alert-danger">This E-mail is invalid!!</div>';
                                    }
                                    elseif ($_GET['error'] == "sqlerror") {
                                        echo '<div class="alert alert-danger">There a database error!!</div>';
                                    }
                                    elseif ($_GET['error'] == "wrongpassword") {
                                        echo '<div class="alert alert-danger">Wrong password!!</div>';
                                    }
                                    elseif ($_GET['error'] == "nouser") {
                                        echo '<div class="alert alert-danger">This E-mail does not exist!!</div>';
                                    }
                                    elseif ($_GET['error'] == "invalidtoken") {
                                        echo '<div class="alert alert-danger">Invalid or expired token. Please request a new password reset.</div>';
                                    }
                                    elseif ($_GET['error'] == "mailerror") {
                                        $errorMessage = isset($_GET['message']) ? urldecode($_GET['message']) : "Failed to send email. Please try again.";
                                        echo '<div class="alert alert-danger">' . htmlspecialchars($errorMessage) . '</div>';
                                    }
                                }
                                if (isset($_GET['reset'])) {
                                    if ($_GET['reset'] == "success") {
                                        echo '<div class="alert alert-success">Check your E-mail!</div>';
                                    }
                                }
                                if (isset($_GET['resetpassword'])) {
                                    if ($_GET['resetpassword'] == "success") {
                                        echo '<div class="alert alert-success">Password has been reset successfully! Please login.</div>';
                                    }
                                }
                                if (isset($_GET['account'])) {
                                    if ($_GET['account'] == "activated") {
                                        echo '<div class="alert alert-success">Please Login</div>';
                                    }
                                }
                                if (isset($_GET['active'])) {
                                    if ($_GET['active'] == "success") {
                                        echo '<div class="alert alert-success">The activation link has been sent!</div>';
                                    }
                                }
                                if (isset($_GET['register'])) {
                                    if ($_GET['register'] == "success") {
                                        echo '<div class="alert alert-success">Registration successful! Please login.</div>';
                                    }
                                }
                                ?>

                                <div class="alert1"></div>

                                <form class="reset-form" action="reset_pass.php" method="post" enctype="multipart/form-data">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control" name="email" placeholder="E-mail..." required/>
                                    </div>
                                    <button type="submit" name="reset_pass" class="btn btn-primary btn-block w-100">Reset Password</button>
                                    <hr>
                                    <div class="text-center">
                                        <p class="message"><a href="#" class="text-decoration-none">Back to Login</a></p>
                                    </div>
                                </form>

                                <form class="login-form" action="ac_login.php" method="post" enctype="multipart/form-data">
                                    <div class="form-group mb-3">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="E-mail..." required/>
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="password" class="form-control" name="pwd" id="pwd" placeholder="Password" required/>
                                    </div>
                                    <button type="submit" name="login" id="login" class="btn btn-primary btn-block w-100 mb-3">Login</button>
                                    <hr>
                                    <div class="text-center">
                                        <p class="message">Forgot your Password? <a href="#" class="text-decoration-none">Reset your password</a></p>
                                        <p>Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
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