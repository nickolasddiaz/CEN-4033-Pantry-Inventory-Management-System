<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !empty($_POST['email'])){
        $email = $_POST['email'];

        $code = checkemailExists($pdo, $email);
        if ($code) {
            // If email exists and is verified, send the verification code
            $response['success'] = true;
            $response['message'] = 'Verification code sent to your email.';
            $response['verification_code'] = $code; // Include the verification code in the response as it is too hard to send an email
        } else {
            // If email does not exist or is not verified
            $response['success'] = false;
            $response['message'] = 'Email does not exist or is not verified.';
        }


    }else {
        $response['success'] = false;
        $response['message'] = 'Email is required.';
    }

}
echo json_encode($response);


function checkemailExists($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT is_verified FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if($result || ((int)$result['is_verified'] == 1)){ // Returns true if exists and verified is false
            $code = generateCode();
            // Update the verification code for the user
            $updateStmt = $pdo->prepare("UPDATE users SET verification_code = :verification_code WHERE email = :email");
            $updateStmt->execute([
                'verification_code' => $code,
                'email' => $email
            ]);
            return $code; // Email exists and is verified
            
        } else {
            return false; // Email does not exist or is not verified
        }


    } catch (PDOException $e) {
        error_log("Error checking email existence: " . $e->getMessage());
        return false; // Return false on error
    }
}

?>