<?php
require_once 'config.php'; // Include database configuration and functions

if ($_SERVER['REQUEST_METHOD'] === 'POST' && checkCRSFkey()) {
    // Check if user is authenticated
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
        $action = $_POST['action'];
        // Validate action
        if (!in_array($action, ['create','read','update','remove'])) {
            $response = [
                'success' => false,
                'message' => 'Invalid action specified. Use "create", "read", "update", or "remove".'
            ];
            echo json_encode($response);
            exit;
        }

        if ($action === 'read') {
            // Fetch pantry items for the authenticated user
            $pantryItems = getPantryItems($pdo, $id);
            // If fetching pantry items fails, $pantryItems will be false
            if ($pantryItems === false) {
                $response = [
                    'success' => false,
                    'message' => 'Failed to fetch pantry items.'
                ];
            } else {
                $response = [
                    'success' => true,
                    'message' => 'Pantry items retrieved successfully.',
                    'data' => $pantryItems
                ];
            }
            echo json_encode($response);
            exit;
        }

        // Check if items are provided in the request
        if (!isset($_POST['items']) || empty($_POST['items'])) {
            $response = [
                'success' => false,
                'message' => 'No items provided to modify.'
            ];
            echo json_encode($response);
            exit;
        }
        
        // Decode JSON items if it's a string
        $items = $_POST['items'];
        if (is_string($items)) {
            $items = json_decode($items, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response = [
                    'success' => false,
                    'message' => 'Invalid JSON format for items.'
                ];
                echo json_encode($response);
                exit;
            }
        }
        
        // Modify pantry items based on the action
        $result = modifyPantryItems($pdo, $id, $items, $action);
        if ($result) {
            $response = [
                'success' => true,
                'message' => 'Pantry items modified successfully.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Failed to modify pantry items.'
            ];
        }
    } catch (Exception $e) {
        error_log("JWT verification failed: " . $e->getMessage());
        $response = [
            'success' => false,
            'message' => 'Invalid authentication token.'
        ];
        echo json_encode($response);
        exit;
    }
    
    echo json_encode($response);
}

function getPantryItems($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("SELECT name, quantity, expiration_date, in_shopping_list, purchased FROM pantry_items WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching pantry items: " . $e->getMessage());
        return false;
    }
}

function modifyPantryItems($pdo, $id, $items, $action) {
    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Ensure $items is an array
        if (!is_array($items)) {
            $items = [$items];
        }

        foreach ($items as $item) {
            // Validate required fields for each item
            if (!isset($item['item_name']) || empty($item['item_name'])) {
                throw new Exception("Item name is required");
            }

            if ($action === 'create') {
                // For adding items, INSERT IGNORE to avoid duplicates
                $stmt = $pdo->prepare("INSERT IGNORE INTO pantry_items (user_id, name, quantity, expiration_date) VALUES (:user_id, :name, :quantity, :expiration_date)");
                $stmt->execute([
                    'user_id' => $id,
                    'name' => $item['item_name'],
                    'quantity' => isset($item['quantity']) ? $item['quantity'] : 0,
                    'expiration_date' => isset($item['expiration_date']) ? $item['expiration_date'] : null
                ]);
            } elseif ($action === 'remove') {
                // For removing items, delete them
                $stmt = $pdo->prepare("DELETE FROM pantry_items WHERE user_id = :user_id AND name = :name");
                $stmt->execute([
                    'user_id' => $id,
                    'name' => $item['item_name']
                ]);
            } else { // update
                // For modifying existing items, update the quantity
                if (isset($item['old_item_name'])) {
                    $stmt = $pdo->prepare("UPDATE pantry_items SET quantity = :quantity, name = :name, expiration_date = :expiration_date, in_shopping_list = :in_shopping_list, purchased = :purchased WHERE user_id = :user_id AND name = :old_item_name");
                    $stmt->execute([
                        'user_id' => $id,
                        'name' => $item['item_name'],
                        'quantity' => isset($item['quantity']) ? $item['quantity'] : 0,
                        'expiration_date' => $item['expiration_date'],
                        'old_item_name' => $item['old_item_name'], // Use old_name to find the item to update
                        'in_shopping_list' => isset($item['in_shopping_list']) ? $item['in_shopping_list'] : 0,
                        'purchased' => isset($item['purchased']) ? $item['purchased'] : 0
                    ]);
                } else{
                    error_log("Old items not provided for update.");
                    return false; // If old items are not provided for update, return false
                }
            }
        }

        // Commit the transaction
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $pdo->rollBack();
        error_log("Error modifying pantry items: " . $e->getMessage());
        return false;
    }
}
?>