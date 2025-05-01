<?php
session_start();
if (!isset($_SESSION['Login-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Users</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/IPB.png">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-header {
            background: linear-gradient(135deg, #6c757d, #343a40);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 1rem 1rem;
        }
        .table-container {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .table thead th {
            background-color: #4e73df;
            color: white;
            border: none;
        }
        .table tbody tr:hover {
            background-color: #f8f9fc;
            transition: all 0.3s ease;
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<?php include'header.php'; ?>

<main class="container-fluid">
    <div class="page-header text-center animate-fade-in">
        <h1 class="display-4"><i class="fas fa-users me-3"></i>Users</h1>
        <p class="lead">Complete list of registered users in the system</p>
    </div>

    <div class="container">
        <div class="table-container animate-fade-in">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user-circle"></i>No</th>
                            <th><i class="fas fa-user-circle"></i>Name</th>
                            <th><i class="fas fa-hashtag me-2"></i>Serial Number</th>
                            <th><i class="fas fa-venus-mars me-2"></i>Gender</th>
                            <th><i class="fas fa-id-card me-2"></i>Card UID</th>
                            <th><i class="fas fa-calendar me-2"></i>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        //Connect to database
                        require'connectDB.php';

                        $sql = "SELECT * FROM users WHERE add_card=1 ORDER BY 'no' DESC";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo '<div class="alert alert-danger" role="alert">SQL Error</div>';
                        }
                        else{
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            if (mysqli_num_rows($resultl) > 0){
                                while ($row = mysqli_fetch_assoc($resultl)){
                        ?>
                                    <tr>
                                        <td><?php echo $row['no'];?></td>
                                        <td><strong><?php echo $row['username'];?></strong></td>
                                        <td><?php echo $row['serialnumber'];?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo $row['gender'];?>
                                            </span>
                                        </td>
                                        <td><code><?php echo $row['card_uid'];?></code></td>
                                        <td><?php echo $row['user_date'];?></td>
                                    </tr>
                        <?php
                                }   
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>