<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/Header.css" rel="stylesheet">
</head>

<header>
    <div class="header-brand">
        <div class="container">
            <a href="index.php" class="d-flex align-items-center">
                <i class="fas fa-id-card me-2"></i>
                RFID Attendance System
            </a>
        </div>
    </div>

    <?php  
    if (isset($_GET['error'])) {
        if ($_GET['error'] == "wrongpasswordup") {
            echo '<script type="text/javascript">
                    setTimeout(function () {
                        $(".up_info1").fadeIn(200);
                        $(".up_info1").text("The password is wrong!");
                        $("#login-account").modal("show");
                    }, 500);
                    setTimeout(function () {
                        $(".up_info1").fadeOut(1000);
                    }, 3000);
                </script>';
        }
    } 
    if (isset($_GET['success'])) {
        if ($_GET['success'] == "updated") {
            echo '<script type="text/javascript">
                    setTimeout(function () {
                        $(".up_info2").fadeIn(200);
                        $(".up_info2").text("Your Account has been updated");
                    }, 500);
                    setTimeout(function () {
                        $(".up_info2").fadeOut(1000);
                    }, 3000);
                </script>';
        }
    }
    if (isset($_GET['login'])) {
        if ($_GET['login'] == "success") {
            echo '<script type="text/javascript">
                    setTimeout(function () {
                        $(".up_info2").fadeIn(200);
                        $(".up_info2").text("You successfully logged in");
                    }, 500);
                    setTimeout(function () {
                        $(".up_info2").fadeOut(1000);
                    }, 4000);
                </script>';
        }
    }
    ?>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-users"></i>Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ManageUsers.php"><i class="fas fa-user-cog"></i>Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="UsersLog.php"><i class="fas fa-history"></i>Users Log</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php  
                    if (isset($_SESSION['Login-name'])) {
                        echo '<li class="nav-item">
                                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#login-account">
                                    <i class="fas fa-user-circle"></i>'.$_SESSION['Login-name'].'
                                </a>
                              </li>';
                        echo '<li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="fas fa-sign-out-alt"></i>Log Out
                                </a>
                              </li>';
                    }
                    else{
                        echo '<li class="nav-item">
                                <a class="nav-link" href="login.php">
                                    <i class="fas fa-sign-in-alt"></i>Log In
                                </a>
                              </li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="up_info1 alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><span></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="up_info2 alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><span></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</header>

<!-- Account Update Modal -->
<div class="modal fade" id="login-account" tabindex="-1" aria-labelledby="loginAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="loginAccountModalLabel">
                    <i class="fas fa-user-edit me-2"></i>Update Your Account Info
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="ac_update.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="up_name" class="form-label text-muted">Admin Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="up_name" name="up_name" placeholder="Enter your Name..." value="<?php echo $_SESSION['Login-name']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="up_email" class="form-label text-muted">Admin E-mail</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="up_email" name="up_email" placeholder="Enter your E-mail..." value="<?php echo $_SESSION['Login-email']; ?>" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="up_pwd" class="form-label text-muted">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="up_pwd" name="up_pwd" placeholder="Enter your Password..." required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
