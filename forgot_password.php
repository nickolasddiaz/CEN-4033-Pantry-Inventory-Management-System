<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !empty($_POST['email']) &&
    isset($_POST['code']) && !empty($_POST['code']) &&
    isset($_POST['password']) && !empty($_POST['password'])
    ){

        $result = resetPassword($pdo, $_POST['email'], $_POST['code'], $_POST['password']);
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Password reset successful.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Invalid email or verification code.';
        }

    } else {
        $response['success'] = false;
        $response['message'] = 'Email, code, and password are required.';
        echo json_encode($response);
        exit;
    }

    echo json_encode($response);
}

function resetPassword($pdo, $email, $code, $password) {
    try {
        
        // User exists, update the password
        $salt = generateCode();
        $hashedPassword = hashPassword($password, $salt);
        $updateStmt = $pdo->prepare("UPDATE users SET password_hash = :password, salt = :salt WHERE email = :email AND verification_code = :code");
        $updateStmt->execute([
            'password' => $hashedPassword,
            'salt' => $salt,
            'email' => $email,
            'code' => $code
        ]);
        if ($updateStmt->rowCount() === 0) {
            // No rows updated, either email or code was incorrect
            return false;
        }
        return true; // Password reset successful
    } catch (PDOException $e) {
        error_log("Error resetting password: " . $e->getMessage());
        return false; // Return false on error
    }
}

?>