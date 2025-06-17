<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST' && checkCRSFkey()) {
    if (isset($_POST['token']) && !empty($_POST['token']) &&
        isset($_POST['email']) && !empty($_POST['email'])) {

        $token = $_POST['token'];
        $email = $_POST['email'];
        $id = verifyJWT($pdo, $token);
        if(!$id) {
            $response['success'] = false;
            $response['message'] = 'Invalid or expired token.';
            echo json_encode($response);
            exit;
        }

        if(checkifemailexists($pdo, $email)) {
            $response['success'] = false;
            $response['message'] = 'Email already exists.';
            echo json_encode($response);
            exit;
        }

        // Generate a verification code
        $verification_code = generateCode(); // Generate a secure random verification
        $message = "http://localhost/verify_email_client.php?email=" . $email . "&code=" . $verification_code; // Verification link
        if($SEND_EMAIL && !send_email($email, $message)) {
            $response['success'] = false;
            $response['message'] = 'Failed to send verification email.';
            echo json_encode($response);
            exit;
        }

        if(setemail($pdo, $id, $email, $verification_code, $SEND_EMAIL)) {
            $response['success'] = true;
            $response['message'] = 'Email set successfully.';
            revokeJWT($pdo, $token); // Revoke the old token after setting the email
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to set email.';
        }

    }else{
        $response['success'] = false;
        $response['message'] = 'Token and email are required.';
    }
}
echo json_encode($response);

function checkifemailexists($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        
        if($stmt->rowCount() > 0) {
            return true; // Email exists
        } else {
            return false; // Email does not exist
        }

    } catch (PDOException $e) {
        error_log("Error checking email existence: " . $e->getMessage());
        return false; // Return false on error
    }
}

function setemail($pdo, $id, $email, $verification_code, $is_verified = 0) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET email = :email, is_verified = :is_verified, verification_code = :verification_code WHERE id = :id");
        $stmt->execute([
            'email' => $email,
            'id' => $id,
            'is_verified' => !$is_verified, 
            'verification_code' => $verification_code 
        ]);
        return true; // Email set successfully
    } catch (PDOException $e) {
        error_log("Error setting email: " . $e->getMessage());
        return false; // Return false on error
    }
}

?>