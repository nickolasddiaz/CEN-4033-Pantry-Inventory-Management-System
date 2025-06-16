<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST' && checkCRSFkey()) {
    // Check if email and password are set and not empty
    if (isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['password']) && !empty($_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check if email already exists
        $emailCheck = emailExistsSignUp($pdo, $email);
        if( $emailCheck['is_verified']) {
            // If email exists and is verified, block signup
            $response['success'] = false;
            $response['message'] = 'This email is already registered and verified.';
            echo json_encode($response);
            exit;
        }
        else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format 
            $response['success'] = false;
            $response['message'] = 'Invalid email format.';
        } elseif (strlen($password) < 6) {
            $response['success'] = false;
            $response['message'] = 'Password must be at least 6 characters long.';
        } else {
            $salt = generateCode();
            $hashedPassword = hashPassword($password, $salt);

            try {
                // Create the new user
                $result = createUser($pdo, $email, $hashedPassword, $salt);
                if (!$result) {
                    $response['success'] = false;
                    $response['message'] = 'Registration failed. Please try again.';
                    error_log("createUser returned false for email: $email");
                } else {
                    $response['success'] = true;
                    $response['message'] = 'Signup successful!';
                }
            } catch (Exception $e) {
                error_log("Exception in signup process: " . $e->getMessage());
                $response['success'] = false;
                $response['message'] = 'Registration failed. Please try again later.';
            }
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Email and password are required.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method. Only POST requests are allowed.';
}

echo json_encode($response);

function emailExistsSignUp($pdo, $email) {
    try {
        // Check if the email exists in the database and optionally if it is verified
        $stmt = $pdo->prepare("SELECT is_verified FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [
                'exists' => false,
                'is_verified' => false
            ]; // Email not found - allow signup
        } 
        // If email exists, check if it's verified
        // Return true if email is verified (block signup), false if not verified (allow signup)
        if ((int)$result['is_verified'] == 1){
        return [
            'exists' => true,
            'is_verified' => true
        ]; // Email exists and is verified
        } else {
            return [
                'exists' => true,
                'is_verified' => false
            ]; // Email exists but is not verified - allow signup
        }
    } catch (PDOException $e) {
        error_log("Error in emailExistsSignUp: " . $e->getMessage());
        return [
            'exists' => true,
            'is_verified' => true
        ]; // Error occurred, treat as email exists and verified
    }
}

function createUser($pdo, $email, $hashedPassword, $salt) {
    try {
        $code = generateCode(); // Generate a verification code
        // Begin transaction

        $stmt = $pdo->prepare("REPLACE INTO users (email, password_hash, role, salt, verification_code) VALUES (:email, :hashedPassword, 'user', :salt, :verification_code)");
        return $stmt->execute([
            'email' => $email,
            'hashedPassword' => $hashedPassword,
            'salt' => $salt,
            'verification_code' => $code
        ]);
        // Commit the transaction
    } catch (PDOException $e) {
        // Rollback the transaction in case of error
        error_log("Error in createUser: " . $e->getMessage());
        return false;
    }
}
?>