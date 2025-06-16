<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is authenticated
    // Parse DELETE request body
    if (!isset($_POST['token']) || empty($_POST['token'])) {
        $response = [
            'success' => false,
            'message' => 'Authentication token is required.'
        ];
        echo json_encode($response);
        exit;
    }
    try {
        $id = verifyJWT($pdo, $_POST['token']); // Verify JWT and get user ID
        // If verification fails, $id will be false
        if(!$id) {
            $response = [
                'success' => false,
                'message' => 'Invalid authentication token.'
            ];
            echo json_encode($response);
            exit;
        }

        if(deleteAccount($pdo, $id)) {
            $response = [
                'success' => true,
                'message' => 'Account and related data deleted successfully.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to delete account. Please try again later.'
            ];
        }
    
    } catch (Exception $e) {
        error_log("Exception in delete account process: " . $e->getMessage());
        $response = [
            'success' => false,
            'message' => 'An error occurred while processing your request.'
        ];
    }
}

function deleteAccount($pdo, $id) {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Delete related notifications
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = :id");
        $stmt->execute(['id' => $id]);

        // Delete related pantry items
        $stmt = $pdo->prepare("DELETE FROM pantry_items WHERE user_id = :id");
        $stmt->execute(['id' => $id]);

        // Delete user account
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Commit transaction
        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        error_log("Error deleting account and related data: " . $e->getMessage());
        return false;
    }
}


?>
