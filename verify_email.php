<?php
require_once 'config.php'; // Include database configuration and functions

// Initialize response array
$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['code']) && !empty($_POST['code'])) {

            $email = $_POST['email'];
            $code = $_POST['code'];
        if(verifyEmail($pdo, $email, $code)) {
            // If verification is successful
            $response['success'] = true;
            $response['message'] = 'Email verified successfully.';
        } else {
            // If verification fails
            $response['success'] = false;
            $response['message'] = 'Invalid email or verification code.';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Email and verification code are required.';
    }
    
    // Set JSON header
    header('Content-Type: application/json');
    echo json_encode($response);
}

function verifyEmail($pdo, $email, $code) {
    // Check if the email exists and the code matches
    try{
        $stmt = $pdo->prepare("UPDATE users SET is_verified = :is_verified WHERE email = :email AND verification_code = :code");
        $stmt->execute(['email' => $email, 'code' => $code, 'is_verified' => 1]);
        if( $stmt->rowCount() > 0) {
            // If the update was successful, return true
            return true;
        } else {
            // If no rows were updated, the email or code was incorrect
            return false;
        }
    } catch (PDOException $e) {
        error_log("Error in verifyEmail: " . $e->getMessage());
        return false; // Return false on error
    }
}
?>