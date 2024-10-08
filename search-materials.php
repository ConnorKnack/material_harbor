<?php
require './db_conn.php';

// if (!isLoggedIn()) {
//   header('Location: ./index.php');
// }

// Initialize the display variables
$show = $info = '';
$userType = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'supplier';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $materialStandard = isset($_POST['material_standard']) ? $_POST['material_standard'] : []; // Array of selected raw materials
    $subMaterials = isset($_POST['sub_material']) ? $_POST['sub_material'] : []; // Array of selected sub-materials
    $alloys = isset($_POST['alloy']) ? $_POST['alloy'] : []; // Array of selected alloys
    $conditions = isset($_POST['condition']) ? $_POST['condition'] : []; // Array of selected conditions
    $forms = isset($_POST['form']) ? $_POST['form'] : []; // Array of selected forms

    // Convert arrays to comma-separated values for SQL IN query
    $materialStandardStr = implode("','", array_map('trim', $materialStandard));
    $subMaterialStr = implode("','", array_map('trim', $subMaterials));
    $alloyStr = implode("','", array_map('trim', $alloys));
    $conditionStr = implode("','", array_map('trim', $conditions));
    $formStr = implode("','", array_map('trim', $forms));

    // Check if it's a manufacturer or supplier search
    if (strtolower($userType) === 'manufacturer') {
        $sql = "SELECT m.*, s.company_name AS manufacturer_name
            FROM materials m
            JOIN manufacturers s ON m.manufacturer_id = s.id
            WHERE m.material_standard IN ('$materialStandardStr')
            AND m.material_type IN ('$subMaterialStr')
            AND m.alloy IN ('$alloyStr')
            AND m.condition IN ('$conditionStr')
            AND m.form IN ('$formStr')";
    } elseif (strtolower($userType) === 'supplier') {
        $sql = "SELECT m.*, s.company_name AS supplier_name
            FROM materials m
            JOIN suppliers s ON m.supplier_id = s.id
            WHERE m.material_standard IN ('$materialStandardStr')
            AND m.material_type IN ('$subMaterialStr')
            AND m.alloy IN ('$alloyStr')
            AND m.condition IN ('$conditionStr')
            AND m.form IN ('$formStr')";
    }

    // Execute the query and display results
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $show .= "<h2 class='text-center mt-4'>Available Materials and " . ucfirst($userType) . "s</h2>";
        $show .= "<table class='table table-bordered'>";
        $show .= "<thead><tr><th>Material Standard</th><th>Sub-Material</th><th>Alloy</th><th>Condition</th><th>Form</th><th>" . ucfirst($userType) . "</th></tr></thead>";
        $show .= "<tbody>";
        while ($row = $result->fetch_assoc()) {
            $show .= "<tr>";
            $show .= "<td>" . htmlspecialchars($row['material_standard']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['material_type']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['alloy']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['condition']) . "</td>";
            $show .= "<td>" . htmlspecialchars($row['form']) . "</td>";
            if (strtolower($userType) === 'manufacturer') {
                $show .= "<td><a class='fw-bold text-primary' href='profile.php?userType=manufacturer&userID=" . $row['manufacturer_id'] . "'>" . htmlspecialchars($row['manufacturer_name']) . "</a></td>";
            } else {
                $show .= "<td><a class='fw-bold text-primary' href='profile.php?userType=supplier&userID=" . $row['supplier_id'] . "'>" . htmlspecialchars($row['supplier_name']) . "</a></td>";
            }
            $show .= "</tr>";
        }
        $show .= "</tbody></table>";
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
        <link rel="stylesheet" href="./assets/css/style.css">
    </head>

    <body>
        <?php
        include './header.php';
        ?>
        <div class="container mt-5">
            <h2 class="text-center">Search Materials / <?php echo $userType; ?></h2>
            <?php echo $info; ?>
            <form action="" method="POST" novalidate class="needs-validation mb-5">
                <div id="material-selection"></div>
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