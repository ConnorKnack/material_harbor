<?php
// db_conn.php
session_start();
// $servername = "localhost";
// $username = "u956940883_materials";
// $password = "S;64NoYxc";
// $dbname = "u956940883_materials";

$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "material_harbour";

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
