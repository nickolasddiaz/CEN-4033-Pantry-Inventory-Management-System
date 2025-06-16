<?php
require_once 'config.php'; // Include database configuration and functions

try{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['token']) && !empty($_POST['token'])){
            $token = $_POST['token'];
            $id = verifyJWT($pdo, $token);
            
            if(!$id) {
                exit;
            }
            revokeJWT($pdo, $token);
        }
    }
} catch (Exception $e) {
    error_log("Error during logout: " . $e->getMessage());
    exit;
}

?>
