<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST' && checkCRSFkey()) {

    // Check if email and password are set and not empty
    if (isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['password']) && !empty($_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email exists and is verified
        $emailCheck = emailExistsLogin($pdo, $email);
        if( !$emailCheck['exists']) {
            $response['success'] = false;
            $response['message'] = 'Email not found.';
        } elseif (!$emailCheck['is_verified']) {
            $response['success'] = false;
            $response['message'] = 'Email not verified. Please check your inbox for the verification email.';
        } else {
            try{
                // Verify password
                $salt = $emailCheck['salt'];
                if (verifyPassword($password, $emailCheck['password_hash'], $salt)) {
                    $token = createJWT($email); // Create JWT token
                    // Password is correct, generate JWT token
                    // Optionally, you can store the token in the database or session for further use
                    $response['success'] = true;
                    $response['message'] = 'Login successful.';
                    $response['token'] = $token; // Return the token
                } 
                else {
                    $response['message'] = 'Incorrect password.';
                }
            } catch (Exception $e) {
                error_log("Exception in login process: " . $e->getMessage());
                $response['success'] = false;
                $response['message'] = 'Login failed. Please try again later.';
            }
        }
    }
    else {
        $response['success'] = false;
        $response['message'] = 'Email and password are required.';
    }
}
function emailExistsLogin($pdo, $email) {
    try {
        // Check if the email exists in the database, if they are verified, and retrieve the salt and password hash
        $stmt = $pdo->prepare("SELECT salt, is_verified, password_hash FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return [
            'exists' => false,
            'is_verified' => false,
            'salt' => null,
            'password_hash' => null
            ]; // Email not found
        }

        return [
            'exists' => true,
            'is_verified' => ((int)$result['is_verified'] == 1),
            'salt' => $result['salt'],
            'password_hash' => $result['password_hash']
        ];
    } catch (PDOException $e) {
        error_log("Error in emailExistsLogin: " . $e->getMessage());
        return [
            'exists' => false,
            'is_verified' => false,
            'salt' => null,
            'password_hash' => null
        ];
    }
}


echo json_encode($response);
?>