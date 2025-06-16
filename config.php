<?php

require __DIR__ . '/vendor/autoload.php'; // Autoload files using Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Database connection
$DB_HOST = $_ENV['DB_HOST']; // or $_ENV['DB_HOST'] if you rename it
$DB_USER = $_ENV['DB_USER']; 
$DB_PASSWORD = $_ENV['DB_PASSWORD']; 
$DB_NAME = $_ENV['DB_NAME'];
$JWTKey = $_ENV['JWT_KEY']; // JWT key for encoding and decoding tokens

define('SECRET_KEY', $JWTKey); // Ensure the secret key is a valid string



header('Content-Type: application/json');

// Better error handling setup
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('display_errors', 0); 
ini_set('error_log', __DIR__ . '/error.log');


// Custom error handler
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // Log the error first
    error_log("PHP Error: [$errno] $errstr in $errfile on line $errline");
    
    $errorResponse = [
        'success' => false,
        'message' => 'An error occurred.',
        'error' => [
            'type' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    echo json_encode($errorResponse);
    exit;
});

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME", $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}


$response = [ // Initialize a generalised response array for the entire application
    'success' => false,
    'message' => ''
];

function verifyJWT($pdo, $JWTKey){
    try{
        // Fixed JWT decode call - use Key object for newer versions of firebase/jwt
        $token = JWT::decode($JWTKey, new Key(SECRET_KEY, 'HS256'));

        $stmt = $pdo->prepare("SELECT id, `revoke` FROM users WHERE email = :email AND is_verified = 1");
        $stmt->execute([
            'email' => $token->email,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (int)$user['revoke'] < (int)$token->iat) { // Check if user exists and is not revoked
            return $user['id']; // Return user ID if verification is successful
        } else {
            error_log("JWT verification query executed for email: " . $token->email);

            return false; // Verification failed
        }
    } catch (Exception $e) {
        error_log("JWT verification failed: " . $e->getMessage());
        return false; // Return false if verification fails
    }
}

//HS256 is a symmetrical key-based hashing algorithm used to sign and verify JSON Web Tokens (JWTs). It utilizes a shared secret key to generate a signature, ensuring message integrity and authentication.
function createJWT($email) {
    $payload = [
        'email' => $email,
        'iat' => time(), // Issued at time
        'exp' => time() + (24 * 60 * 60) // Expires in 24 hours
    ];
    
    return JWT::encode($payload, SECRET_KEY, 'HS256');
}

function revokeJWT($pdo, $JWTKey) {
    $decoded = JWT::decode($JWTKey, new Key(SECRET_KEY, 'HS256'));
    $email = $decoded->email;
    try {
        $stmt = $pdo->prepare("UPDATE users SET revoke = :revoke WHERE email = :email");
        $stmt->execute([
            'revoke' => time(), // Set the revoke time to current time
            'email' => $email
        ]);
    } catch (PDOException $e) {
        error_log("Error revoking JWT: " . $e->getMessage());
    }
}

function checkCRSFkey() {
    session_start();
    error_log($_POST['csrf_token'] . " " . $_POST['csrf_token']);
    // Check if CSRF token is set in session and matches the one in POST request
    return (isset($_SESSION['csrf_token']) && isset($_POST['csrf_token']) && $_SESSION['csrf_token'] === $_POST['csrf_token']);
}

function generateCode() { 
    // Works like a random string generator can be used for verification codes or salts
    return bin2hex(random_bytes(8));
}

function hashPassword($password, $salt) {
    return password_hash($password . $salt, PASSWORD_BCRYPT); // bytcrpt includes a salt, but we add our own for extra security
}

function verifyPassword($password, $hashedPassword, $salt) {
    return password_verify($password . $salt, $hashedPassword); // Verify the password against the hashed password
}
?>