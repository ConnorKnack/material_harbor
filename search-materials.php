<?php
require './db_conn.php';

// if (!isLoggedIn()) {
//   header('Location: ./index.php');
// }

// print_r($_POST);
$show = $info = '';
$userType = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'supplier';

if (strtolower($userType) === 'manufacturer') {
    $page = 'manufacturer-search';
} elseif (strtolower($userType) === 'supplier') {
    $page = 'supplier-search';
}
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Retrieve user type from the session
    // Sanitize and retrieve input data
    $materialStandard = isset($_POST['material_standard']) ? trim($_POST['material_standard']) : '';
    $materialType = isset($_POST['material_type']) ? trim($_POST['material_type']) : '';
    $alloy = (isset($_POST['alloy']) && !empty($_POST['alloy'])) ? trim($_POST['alloy'][0]) : '';
    if (isset($_POST['type']) && !empty($_POST['type'])) {
        $type = trim($_POST['type'][0]);
    } else {
        $type = '';
    }
    $condition = (isset($_POST['condition']) && !empty($_POST['condition'])) ? trim($_POST['condition'][0]) : '';
    $form = (isset($_POST['form']) && !empty($_POST['form'])) ? trim($_POST['form'][0]) : '';
    $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : '';

    // Manufacturer: List all materials and corresponding suppliers

    if (strtolower($userType) === 'manufacturer') {
        $sql = "SELECT m.*, s.company_name AS manufacturer_name
    , s.id AS manufacturer_id
              FROM materials m
              JOIN manufacturers s ON m.manufacturer_id = s.id WHERE m.material_standard = '$materialStandard' AND m.material_type = '$materialType' AND m.alloy = '$alloy' AND m.type = '$type' AND m.form = '$form' AND m.condition = '$condition'";

    } elseif (strtolower($userType) === 'supplier') {
        $sql = "SELECT m.*, s.company_name AS supplier_name
    , s.id AS supplier_id
              FROM materials m
              JOIN suppliers s ON m.supplier_id = s.id WHERE m.material_standard = '$materialStandard' AND m.material_type = '$materialType' AND m.alloy = '$alloy' AND m.type = '$type' AND m.form = '$form' AND m.condition = '$condition'";

    }
    // SQL query to get all materials with their suppliers

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $show .= "<h2 class='text-center mt-4'>Available Materials and " . $userType . "s</h2>";
        $show .= "<table class='table table-bordered'>";
        $show .= "<thead><tr><th>Material Standard</th><th>Material Type</th><th>Alloy</th><th>Type</th><th>Condition</th><th>Form</th><th>" . $userType . "</th></tr></thead>";
        $show .= "<tbody>";
        while ($row = $result->fetch_assoc()) {
            $show .= "<tr>";
            $show .= "<td>" . htmlspecialchars($row['material_standard']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['material_type']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['alloy']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['type']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['condition']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['form']) . "</td>";
            if (strtolower($userType) === 'manufacturer') {
                $show .= "<td><a class='fw-bold text-primary' href='profile.php?userType=manufacturer&userID=" . $row['manufacturer_id'] . "'>" . htmlspecialchars($row['manufacturer_name']) . "</a></td>";
            } else {
                $show .= "<td><a class='fw-bold text-primary' href='profile.php?userType=supplier&userID=" . $row['supplier_id'] . "'>" . htmlspecialchars($row['supplier_name']) . "</a></td>";
            }
            $show .= "</tr>";
        }
        $show .= "</tbody>";
        $show .= "</table>";
    } else {
        $info = "<p class='alert alert-danger'>No materials found.</p>";
    }
}

// Close the database connection
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Material Harbor</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    </head>

    <body>
        <?php
        include './header.php';
        ?>
        <div class="container mt-5">
            <h2 class="text-center">Search Materials / <?php echo $userType; ?></h2>
            <?php echo $info; ?>
            <form action="" method="POST" novalidate class="needs-validation mb-5">
                <div id="material-selection">
                    <select id="material-select" required name="material_standard" class="form-control">
                    </select>
                </div>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                </div>
            </form>
            <?php echo $show; ?>
        </div>
        <!-- jQuery and Bootstrap JS -->
        <script src="./assets/js/jquery-3.6.1.min.js"></script>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script>
            let page = 'search-materials';
        </script>
        <script src="./assets/js/script.js?v=2"></script>
    </body>

</html>