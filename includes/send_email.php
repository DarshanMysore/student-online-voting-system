<?php
require 'mail.php'; // PHPMailer setup

// Expect 3 arguments: email, subject, body
if (isset($argv[1], $argv[2], $argv[3])) {
    $to      = $argv[1];
    $subject = base64_decode($argv[2]);
    $body    = base64_decode($argv[3]);

    try {
        if (sendEmail($to, $subject, $body)) {
            // Optionally log success
            file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " ✅ Email sent to $to\n", FILE_APPEND);
        } else {
            file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " ⚠️ Failed to send email to $to\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents('email_log.txt', date('Y-m-d H:i:s') . " ❌ Error sending email to $to: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}
?>
