<?php
require './db_conn.php';

if (!isLoggedIn()) {
    header('Location: ./index.php');
}

$page = 'home';

$show = $info = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    // Retrieve user type from the session
    $userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : 'Guest';

    // Retrieve and sanitize input data (treating radio button inputs as single values)
    $materialStandard = isset($_POST['material_standard']) ? $_POST['material_standard'] : ''; // Single selected material
    $subMaterials = isset($_POST['sub_material']) ? $_POST['sub_material'] : ''; // Single selected sub-material
    $alloys = isset($_POST['alloy']) ? $_POST['alloy'] : ''; // Single selected alloy
    $types = isset($_POST['type']) ? $_POST['type'] : ''; // Single selected type
    $conditions = isset($_POST['condition']) ? $_POST['condition'] : ''; // Single selected condition
    $forms = isset($_POST['form']) ? $_POST['form'] : ''; // Single selected form

    $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : '';

    // Determine user type and set appropriate SQL condition
    if (strtolower($userType) === 'manufacturer') {
        $sql_condition = "`manufacturer_id` = '$userId'";
        $sql_field = 'manufacturer_id';
    } elseif (strtolower($userType) === 'supplier') {
        $sql_condition = "`supplier_id` = '$userId'";
        $sql_field = 'supplier_id';
    }

    // Escape values to prevent SQL injection
    $materialStandardEsc = mysqli_real_escape_string($conn, $materialStandard);
    $subMaterialEsc = mysqli_real_escape_string($conn, $subMaterials);
    $alloyEsc = mysqli_real_escape_string($conn, $alloys);
    $typeEsc = mysqli_real_escape_string($conn, $types);
    $conditionEsc = mysqli_real_escape_string($conn, $conditions);
    $formEsc = mysqli_real_escape_string($conn, $forms);

    // Check if the material with the same details already exists
    $sql1 = "SELECT * FROM materials WHERE material_standard = '$materialStandardEsc' 
             AND material_type = '$subMaterialEsc' 
             AND alloy = '$alloyEsc' 
             AND `type` = '$typeEsc' 
             AND `form` = '$formEsc' 
             AND `condition` = '$conditionEsc' 
             AND $sql_condition";
    $checkSql1 = mysqli_query($conn, $sql1);

    if (mysqli_num_rows($checkSql1) > 0) {
        $info .= "<div class='alert alert-danger'>This material already exists in the database from the same $userType.</div>";
    } else {
        // Insert the new material
        $insertSql = "INSERT INTO materials (material_standard, material_type, alloy, `type`, `condition`, `form`, $sql_field, created_at, updated_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param(
            'ssssssi',
            $materialStandardEsc,
            $subMaterialEsc,
            $alloyEsc,
            $typeEsc,
            $conditionEsc,
            $formEsc,
            $userId
        );

        if ($stmt->execute()) {
            $info .= "<div class='alert alert-success'>Material added successfully!</div>";
        } else {
            $info .= "<div class='alert alert-danger'>Error adding material: " . $stmt->error . "</div>";
        }
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
  <?php include './header.php'; ?>
  <div class="container mt-5">
    <h2 class="text-center">
      Select Material <?php echo isSupplier() ? ' - To Supply' : (isManufacturer() ? ' - To Manufacture' : ''); ?>
    </h2>
    <?php echo $info; ?>
    <form action="" method="POST" novalidate class="needs-validation mb-5">
      <div id="material-selection">
        <!-- This div will be populated dynamically with checkboxes in a grid format -->
      </div>
      <div class="text-center mt-3">
        <button type="submit" class="btn btn-primary" name="submit">Submit</button>
      </div>
    </form>
    <?php echo $show; ?>
  </div>
  <?php include './footer.php'; ?>
  <!-- jQuery and Bootstrap JS -->
  <script src="./assets/js/jquery-3.6.1.min.js"></script>
  <script src="./assets/js/bootstrap.bundle.min.js"></script>
  <script>
    let page = 'select-materials';
  </script>
  <script src="./assets/js/script.js?v=2"></script>
</body>
</html>
