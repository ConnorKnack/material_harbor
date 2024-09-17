<?php
require './db_conn.php';

if (!isLoggedIn()) {
  header('Location: ./index.php');
}

$page = 'home';

// print_r($_POST);
$show = $info = '';
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
  // Retrieve user type from the session
  $userType = isset($_SESSION['userType']) ? $_SESSION['userType'] : 'Guest';
  // Sanitize and retrieve input data
  $materialStandard = isset($_POST['material_standard']) ? trim($_POST['material_standard']) : '';
  $materialType = isset($_POST['material_type']) ? trim($_POST['material_type']) : '';
  $alloys = (isset($_POST['alloy']) && !empty($_POST['alloy'])) ? $_POST['alloy'] : [];
  if (isset($_POST['type']) && !empty($_POST['type'])) {
    $types = $_POST['type'];
  } else {
    $types = [];
  }

  $conditions = (isset($_POST['condition']) && !empty($_POST['condition'])) ? $_POST['condition'] : [];
  $forms = (isset($_POST['form']) && !empty($_POST['form'])) ? $_POST['form'] : [];


  $userId = isset($_SESSION['userId']) ? $_SESSION['userId'] : '';

  // I want to run loop for the array with higher elements count, so that I can get the values of all the arrays at the same time
  $alloyCounts = count($alloys);
  $typeCounts = count($types);
  $conditionCounts = count($conditions);
  $formCounts = count($forms);

  // Get the maximum count
  $maxItems = max($alloyCounts, $typeCounts, $conditionCounts, $formCounts);

  $largestArray = [];
  // Determine which array has the maximum count
  if ($maxItems === $alloyCounts) {
    $largestArray = $alloys;
  } elseif ($maxItems === $typeCounts) {
    $largestArray = $types;
  } elseif ($maxItems === $conditionCounts) {
    $largestArray = $conditions;
  } elseif ($maxItems === $formCounts) {
    $largestArray = $forms;
  }
  foreach ($largestArray as $subItemKey => $subItem) {
    $type = $types[$subItemKey] ?? '';
    $alloy = $alloys[$subItemKey] ?? '';
    $condition = $conditions[$subItemKey] ?? '';
    $form = $forms[$subItemKey] ?? '';
  }

  if (strtolower($userType) === 'manufacturer' || strtolower($userType) === 'supplier') {

    if (strtolower($userType) === 'manufacturer') {
      $sql_condition = "`manufacturer_id` = '$userId'";
      $sql_field = 'manufacturer_id';
    } elseif (strtolower($userType) === 'supplier') {
      $sql_condition = "`supplier_id` = '$userId'";
      $sql_field = 'supplier_id';
    }


    if (count($largestArray) == 0) {
      $alloy = $type = $condition = $form = '';
      // Check if the material with the same details already exists
      $sql1 = "SELECT * FROM materials WHERE material_standard = '$materialStandard' AND material_type = '$materialType' AND alloy = '$alloy' AND `type` = '$type' AND `form` = '$form' AND `condition` = '$condition' AND $sql_condition";
      $checkSql1 = mysqli_query($conn, $sql1);

      if (mysqli_num_rows($checkSql1) > 0) {
        $info .= "<div class='alert alert-danger'>This material already exists in the database from same $userType.</div>";
      } else {
        // Insert the new material
        $insertSql = "INSERT INTO materials (material_standard, material_type, alloy, `type`, `condition`, `form`, $sql_field, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($insertSql);

        $stmt->bind_param('ssssssi', $materialStandard, $materialType, $alloy, $type, $condition, $form, $userId);

        if ($stmt->execute()) {
          $info .= "<div class='alert alert-success'>Material added successfully!</div>";
        } else {
          $info .= "<div class='alert alert-danger'>Error adding material: " . $stmt->error . "</div>";
        }
      }
    } else {

      foreach ($largestArray as $subItemKey => $subItem) {
        $type = $types[$subItemKey] ?? '';
        $alloy = $alloys[$subItemKey] ?? '';
        $condition = $conditions[$subItemKey] ?? '';
        $form = $forms[$subItemKey] ?? '';
        // Check if the material with the same details already exists
        $sql1 = "SELECT * FROM materials WHERE material_standard = '$materialStandard' AND material_type = '$materialType' AND alloy = '$alloy' AND `type` = '$type' AND `form` = '$form' AND `condition` = '$condition' AND $sql_condition";
        $checkSql1 = mysqli_query($conn, $sql1);

        if (mysqli_num_rows($checkSql1) > 0) {
          $info .= "<div class='alert alert-danger'>This material already exists in the database from same $userType.</div>";
        } else {
          // Insert the new material
          $insertSql = "INSERT INTO materials (material_standard, material_type, alloy, `type`, `condition`, `form`, $sql_field, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
          $stmt = $conn->prepare($insertSql);

          $stmt->bind_param('ssssssi', $materialStandard, $materialType, $alloy, $type, $condition, $form, $userId);

          if ($stmt->execute()) {
            $info .= "<div class='alert alert-success'>Material added successfully!</div>";
          } else {
            $info .= "<div class='alert alert-danger'>Error adding material: " . $stmt->error . "</div>";
          }
        }
      }
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
    <title>Material Harbour</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
  </head>

  <body>
    <?php
    include './header.php';
    ?>
    <div class="container mt-5">
      <h2 class="text-center">Select Material <?php
      if (isSupplier()) {
        echo ' - To Supply';
      } elseif (isManufacturer()) {
        echo ' - To Manufacture';
      }
      ?></h2>
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
      let page = 'select-materials';
    </script>
    <script src="./assets/js/script.js?v=2"></script>
  </body>

</html>