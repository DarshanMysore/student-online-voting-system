<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer classes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // ⚙️ SMTP Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'darshan.p.mys123@gmail.com';     // ✅ your Gmail
        $mail->Password   = 'xlld uavq iwid mhlk';        // ✅ your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // ⚙️ Optimize Speed
        $mail->SMTPKeepAlive = false;
        $mail->Timeout = 5;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // ✉️ Sender & Receiver
        $mail->setFrom('darshan.p.mys123@gmail.com', 'MITM Voting System');
        $mail->addAddress($to);

        // 📧 Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // 🔹 Send Email
        if ($mail->send()) {
            return true;
        } else {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log("Mailer Exception: " . $mail->ErrorInfo);
        return false;
    }
}
?>
