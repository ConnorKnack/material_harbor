<?php
require 'db_conn.php';

if (!isLoggedIn()) {
    header('Location: ./index.php');
    exit();
}

$info = '';
$materialId = isset($_GET['materialId']) ? intval($_GET['materialId']) : 0;
$userId = $_SESSION['userId'];

// Fetch the material details to edit
$sql = "SELECT * FROM materials WHERE id = ? AND (manufacturer_id = ? OR supplier_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $materialId, $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $material = $result->fetch_assoc();
} else {
    $info = "<div class='alert alert-danger'>Material not found or you are not authorized to edit this material.</div>";
    $conn->close();
    exit($info);
}

// Extract material data for auto-population
$materialStandard = $material['material_standard'];
$subMaterial = $material['material_type'];
$alloy = $material['alloy'];
$type = $material['type'];
$condition = $material['condition'];
$form = $material['form'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Sanitize input
    $materialStandard = $_POST['material_standard'] ?? [];
    $subMaterials = $_POST['sub_material'] ?? [];
    $alloys = $_POST['alloy'] ?? [];
    $types = $_POST['type'] ?? [];
    $conditions = $_POST['condition'] ?? [];
    $forms = $_POST['form'] ?? [];

    $materialStandardValue = mysqli_real_escape_string($conn, $materialStandard[0] ?? '');
    $subMaterialValue = mysqli_real_escape_string($conn, $subMaterials[0] ?? '');
    $alloyValue = mysqli_real_escape_string($conn, $alloys[0] ?? '');
    $typeValue = mysqli_real_escape_string($conn, $types[0] ?? '');
    $conditionValue = mysqli_real_escape_string($conn, $conditions[0] ?? '');
    $formValue = mysqli_real_escape_string($conn, $forms[0] ?? '');

    // Update query
    $updateSql = "UPDATE materials SET material_standard = ?, material_type = ?, alloy = ?, `type` = ?, `condition` = ?, `form` = ?, updated_at = NOW() WHERE id = ? AND (manufacturer_id = ? OR supplier_id = ?)";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('ssssssiii', $materialStandardValue, $subMaterialValue, $alloyValue, $typeValue, $conditionValue, $formValue, $materialId, $userId, $userId);

    if ($stmt->execute()) {
        $info = "<div class='alert alert-success'>Material updated successfully!</div>";
    } else {
        $info = "<div class='alert alert-danger'>Error updating material: " . $stmt->error . "</div>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Material</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <?php include './header.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Edit Material</h2>
        <?php echo $info; ?>
        <form action="" method="POST" class="mt-4 needs-validation" novalidate>
            <div id="material-selection">
                <!-- This div will be populated dynamically with checkboxes in a grid format -->
            </div>
            <div class="text-center mt-3">
                <button type="submit" name="submit" class="btn btn-primary">Update Material</button>
            </div>
        </form>
    </div>

    <?php include './footer.php'; ?>
    <!-- jQuery and Bootstrap JS -->
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js?v=2"></script> <!-- Use the updated script.js for dynamic grid -->

</body>

</html>
