<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['token']) && !empty($_POST['token']) &&
        isset($_POST['password']) && !empty($_POST['password'])) {

        $token = $_POST['token'];
        $password = $_POST['password'];
        $id = verifyJWT($pdo, $token);
        if(!$id) {
            $response['success'] = false;
            $response['message'] = 'Invalid or expired token.';
            echo json_encode($response);
            exit;
        }

        // Set the password
        $result = setPassword($pdo, $id, $password);
        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Password set successfully.';
        } else {
            $response['success'] = false;
            $response['message'] = 'Failed to set password.';
        }

    } else {
        $response['success'] = false;
        $response['message'] = 'User ID and password are required.';
    }

    echo json_encode($response);

}

function setPassword($pdo, $id, $password) {
    try {
        $salt = generateCode();
        $hashedPassword = hashPassword($password, $salt);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = :password, salt = :salt WHERE id = :id");
        $stmt->execute([
            'password' => $hashedPassword,
            'salt' => $salt,
            'id' => $id
        ]);
        return true; // Password set successfully
    } catch (PDOException $e) {
        error_log("Error setting password: " . $e->getMessage());
        return false; // Return false on error
    }
}
?>