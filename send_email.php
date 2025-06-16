<?php
function send_email($to_email, $message) {
    require_once 'config.php'; // Include database configuration and functions
    require_once 'vendor/autoload.php';

    $RESEND_API_KEY = $_ENV['RESEND_API_KEY'];
    $resend = Resend::client($RESEND_API_KEY);

    $resend->emails->send([
    'from' => 'onboarding@resend.dev',
    'to' => $to_email,
    'subject' => "Acount Verification",
    'html' => $message
    ]);
}
?>

