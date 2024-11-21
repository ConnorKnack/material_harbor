<?php
// db_conn.php
session_start();

$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        list($key, $value) = explode('=', $line, 2);
        putenv(trim("$key=$value"));
    }
}

// DB environment variables
$servername = getenv('DB_SERVER');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function isLoggedIn() {
    return isset($_SESSION['userType'], $_SESSION['userId'], $_SESSION['email'], $_SESSION['companyName']) && $_SESSION['userActive'] && $_SESSION['2fa_passed'];
}
function isActive() {
    return isset($_SESSION['userType'], $_SESSION['userId'], $_SESSION['email'], $_SESSION['companyName']) && ($_SESSION['userActive']);
}

function isPassed2fa() {
    return isset($_SESSION['userType'], $_SESSION['userId'], $_SESSION['email'], $_SESSION['companyName']) && ($_SESSION['2fa_passed']);
}


function isManufacturer() {
    return isLoggedIn() && isset($_SESSION['userType']) && $_SESSION['userType'] === 'Manufacturer';
}


function isSupplier() {
    return isLoggedIn() && isset($_SESSION['userType']) && $_SESSION['userType'] === 'Supplier';
}
?>
