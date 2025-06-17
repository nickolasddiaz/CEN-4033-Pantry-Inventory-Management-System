<?php

require __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require 'vendor/autoload.php';

function send_email($to_email, $message) {
    require_once 'config.php'; // Include database configuration and functions
    try{

    $RESEND_API_KEY = getenv('RESEND_API_KEY');
    $resend = Resend::client($RESEND_API_KEY);


    $response = $resend->emails->send([
        'from' => 'onboarding@pantrymanager.store',
        'to' => [$to_email],
        'subject' => 'Hello world',
        'html' => $message,
        
    ]);
    }catch (Exception $e) {
        error_log("Error in send_email: " . $e->getMessage());
        return false; // Return false on error
    }
    return true; // Return true on success
}
?>