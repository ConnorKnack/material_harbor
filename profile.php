<?php
// Include the database connection file
require 'db_conn.php';
$info = '';
// Get userType and userID from the URL
$userType = isset($_GET['userType']) ? strtolower(trim($_GET['userType'])) : '';
$userID = isset($_GET['userID']) ? intval($_GET['userID']) : 0;
$page = 'profile';
// Initialize variables to hold user data
$userData = [];

// print_r($_SESSION);
$ownProfile = false;
if ((isset($_SESSION['userType']) && $_SESSION['userType'] == ucfirst($userType)) && (isset($_SESSION['userId']) && $_SESSION['userId'] == $userID)) {
    $ownProfile = true;
}
// Determine the table to query based on the userType
if ($userType === 'manufacturer' || $userType === 'supplier') {
    $table = $userType === 'manufacturer' ? 'manufacturers' : 'suppliers';

    if (isset($_GET['deleteMaterialId'])) {
        $deleteMaterialId = $_GET['deleteMaterialId'];

        // Check if the material exists
        $checkSql1 = "SELECT * FROM materials WHERE id = ?";
        $stmt = $conn->prepare($checkSql1);
        if ($stmt === false) {
            $info = "<div class='alert alert-danger'>Error preparing query: " . $conn->error . "</div>";
        } else {
            $stmt->bind_param('i', $deleteMaterialId);
            $stmt->execute();
            $result = $stmt->get_result(); // Fetch result after execution

            if ($result->num_rows == 0) {
                $info = "<div class='alert alert-danger'>Material not found.</div>";
            } else {
                $materialData = $result->fetch_assoc(); // Get associative array

                // Check if the user is the owner (manufacturer or supplier)
                if ($materialData['manufacturer_id'] == $userID || $materialData['supplier_id'] == $userID) {
                    // Close the previous statement before preparing a new one
                    $stmt->close();

                    // Delete the material
                    $deleteSql = "DELETE FROM materials WHERE id = ?";
                    $stmt = $conn->prepare($deleteSql);

                    if ($stmt === false) {
                        $info = "<div class='alert alert-danger'>Error preparing delete query: " . $conn->error . "</div>";
                    } else {
                        $stmt->bind_param('i', $deleteMaterialId);
                        if ($stmt->execute()) {
                            $info = "<div class='alert alert-success'>Material deleted successfully.</div>";
                        } else {
                            $info = "<div class='alert alert-danger'>Error deleting material: " . $stmt->error . "</div>";
                        }
                    }
                } else {
                    $info = "<div class='alert alert-danger'>You are not authorized to delete this material.</div>";
                }
            }
            $stmt->close(); // Ensure the statement is closed
        }
    }

    // Prepare the SQL query to fetch the user data
    $sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $info = "<div class='alert alert-danger'>User not found.</div>";
    }

    if ($userType == 'supplier') {
        // Prepare the SQL query to select materials based on supplier_id
        $sql1 = "SELECT m.*, s.company_name AS supplier_name
        FROM materials m
        JOIN suppliers s ON m.supplier_id = s.id
        WHERE m.supplier_id = ?";

    } else {
        // Prepare the SQL query to select materials based on manufacturer_id
        $sql1 = "SELECT m.*, ma.company_name AS manufacturer_name
        FROM materials m
        JOIN manufacturers ma ON m.manufacturer_id = ma.id
        WHERE m.manufacturer_id = ?";
    }
    // Prepare the statement
    $stmt1 = $conn->prepare($sql1);

    // Bind the manufacturer_id parameter
    $stmt1->bind_param('i', $userID);

    // Execute the query
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    // Fetch all rows into an array
    $materials = $result1->fetch_all(MYSQLI_ASSOC);

    // print_r($materials);
    // Close the statement and connection
    $stmt1->close();

    $stmt->close();
} else {
    $info = "<div class='alert alert-danger'>Invalid user type specified.</div>";
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
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" />
        <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    </head>

    <body>
        <?php
        include './header.php';
        ?>
        <div class="container mt-5 mb-5">
            <?php echo $info; ?>
            <?php if (!empty($userData)): ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo ucfirst($userType); ?> Profile</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-bold text-decoration-underline">
                            <?php echo htmlspecialchars($userData['company_name']); ?>
                        </h5>
                        <p class="card-text"><strong>Location:</strong>
                            <?php echo htmlspecialchars($userData['location']); ?></p>
                        <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                        <p class="card-text"><strong>Phone:</strong>
                            <?php echo htmlspecialchars($userData['contact_phone']); ?></p>
                        <p class="card-text"><strong>Offers:</strong> <?php echo htmlspecialchars($userData['offers']); ?>
                        </p>
                        <p class="card-text"><strong>Certification:</strong>
                            <?php echo htmlspecialchars($userData['certification']); ?>
                        </p>
                        <p class="card-text"><strong>Description:</strong>
                            <?php echo htmlspecialchars($userData['description']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">No profile data available.</div>
            <?php endif; ?>
            <?php if ($userType == 'supplier' || $userType == 'manufacturer'): ?>
                <h4 class="mt-5 text-center fw-bold mb-4">Materials Offered by
                    <?php echo htmlspecialchars($userData['company_name']); ?></h4>
                <?php
                $actionTh = ($ownProfile) ? "<th>Action</th>" : "";
                echo "<table class='table table-bordered' id='dataTable'>";
                echo "<thead><tr><th>Material Standard</th><th>Material Type</th><th>Alloy</th><th>Type</th><th>Condition</th><th>Form</th>" . $actionTh . "</tr></thead>";
                echo "<tbody>";
                foreach ($materials as $material) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($material['material_standard']) . "</td>";
                    echo "<td>" . htmlspecialchars($material['material_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($material['alloy']) . "</td>";
                    echo "<td>" . htmlspecialchars($material['type']) . "</td>";
                    echo "<td>" . htmlspecialchars($material['condition']) . "</td>";
                    echo "<td>" . htmlspecialchars($material['form']) . "</td>";
                    if ($ownProfile == true) {
                        // <a href='edit-material.php?materialId=".$material['id']."' class='btn btn-primary btn-sm'>Edit</a>
                        echo "<td><span class='d-flex gap-1'><a onclick='return confirm(\"Do you really want to delete the material?\")' href='?userType=" . $userType . "&userID=" . $userID . "&deleteMaterialId=" . $material['id'] . "' class='btn btn-danger btn-sm'>Delete</a></span></td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                ?>
            <?php endif; ?>
        </div>
        <!-- jQuery and Bootstrap JS -->
        <script src="./assets/js/jquery-3.6.1.min.js"></script>
        <!-- DataTables CSS and JS CDN -->
        <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/script.js?v=2"></script>
        <script>
            $('#dataTable').DataTable();
        </script>
    </body>

</html>