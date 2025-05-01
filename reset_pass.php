<?php
session_start();
require 'connectDB.php'; // Pastikan file ini ada untuk koneksi database

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

if (isset($_POST['reset_pass'])) {
    $email = trim($_POST['email']);

    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.php?error=invalidEmail");
        exit();
    }

    // Cek apakah email ada di tabel login
    $sql = "SELECT * FROM login WHERE login_email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: login.php?error=sqlerror");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: login.php?error=nouser");
        exit();
    }

    // Ambil kredensial SMTP dari tabel smtp_settings
    $sql = "SELECT * FROM smtp_settings LIMIT 1"; // Ambil baris pertama dari tabel smtp_settings
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: login.php?error=sqlerror&message=" . urlencode("Failed to retrieve SMTP settings."));
        exit();
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header("Location: login.php?error=mailerror&message=" . urlencode("SMTP settings not found in database."));
        exit();
    }

    $smtpSettings = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Kirim email dengan link reset password menggunakan PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Aktifkan debugging untuk melihat detail error
        $mail->SMTPDebug = 2; // 0 = off, 1 = client messages, 2 = client and server messages
        $mail->Debugoutput = function($str, $level) {
            error_log("PHPMailer Debug [$level]: $str\n", 3, "phpmailer.log");
        };

        // Konfigurasi SMTP dari database
        $mail->isSMTP();
        $mail->Host = $smtpSettings['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $smtpSettings['smtp_username'];
        $mail->Password = $smtpSettings['smtp_password'];
        $mail->SMTPSecure = $smtpSettings['smtp_secure'];
        $mail->Port = $smtpSettings['smtp_port'];

        // Pengaturan email
        $mail->setFrom('no-reply@presensirfid.com', 'Presensi RFID');
        $mail->addAddress($email); // Kirim ke email pengguna
        $mail->isHTML(false); // Set email format ke plain text

        // Konten email
        $resetLink = "http://localhost/Website TA/rfidattendance/new_password.php?email=" . urlencode($email);
        $mail->Subject = "Reset Password - Presensi RFID";
        $mail->Body = "Hello,\n\n";
        $mail->Body .= "You have requested to reset your password. Click the link below to set a new password:\n";
        $mail->Body .= $resetLink . "\n\n";
        $mail->Body .= "If you did not request this, please ignore this email.\n";
        $mail->Body .= "Best regards,\nPresensi RFID Team";

        // Kirim email
        $mail->send();
        header("Location: login.php?reset=success");
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo . "\n", 3, "phpmailer.log");
        header("Location: login.php?error=mailerror&message=" . urlencode("Failed to send email. Error: " . $mail->ErrorInfo));
    }

    mysqli_close($conn);
    exit();
}

header("Location: login.php");
exit();
?>