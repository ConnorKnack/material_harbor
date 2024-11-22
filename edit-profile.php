<?php
// Include the database connection file
require 'db_conn.php';
$info = '';
// Get userType and userID from the URL
$userType = isset($_GET['userType']) ? strtolower(trim($_GET['userType'])) : '';
$userID = isset($_GET['userID']) ? intval($_GET['userID']) : 0;
$page = 'edit-profile';
// Initialize variables to hold user data
$userData = [];

// Determine the table to query based on the userType
if ($userType === 'manufacturer' || $userType === 'supplier') {
    $table = $userType === 'manufacturer' ? 'manufacturers' : 'suppliers';

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and update user profile information
        $companyName = $_POST['company_name'] ?? '';
        $location = $_POST['location'] ?? '';
        $email = $_POST['email'] ?? '';
        $contactPhone = $_POST['contact_phone'] ?? '';
        $offers = $_POST['offers'] ?? '';
        $certification = $_POST['certification'] ?? '';
        $description = $_POST['description'] ?? '';

        // Update query
        $updateSql = "UPDATE $table SET company_name = ?, location = ?, email = ?, contact_phone = ?, offers = ?, certification = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param('sssssssi', $companyName, $location, $email, $contactPhone, $offers, $certification, $description, $userID);

        if ($stmt->execute()) {
            $info = "<div class='alert alert-success'>Profile updated successfully!</div>";
        } else {
            $info = "<div class='alert alert-danger'>Error updating profile: " . $stmt->error . "</div>";
        }
    }
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
    <title>Edit Profile - Material Harbor</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(45deg, #0047ab, #0056e1);
            color: #fff;
            padding: 1.5rem;
        }

        .card-header h4 {
            margin: 0;
            font-weight: 700;
        }

        .card-body {
            padding: 2rem;
            background: #ffffff;
        }

        .card-footer {
            padding: 1rem 2rem;
            background: #f1f3f5;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            border-color: #0047ab;
            box-shadow: 0 0 5px rgba(0, 71, 171, 0.3);
        }

        .btn-primary {
            background-color: #0047ab;
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.2rem;
        }

        .btn-primary:hover {
            background-color: #003580;
        }

        .alert {
            font-size: 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include './header.php'; ?>
    <div class="container mt-5 mb-5">
        <?php echo $info; ?>
        <?php if (!empty($userData)): ?>
            <form action="" method="POST">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit <?php echo ucfirst($userType); ?> Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   value="<?php echo htmlspecialchars($userData['company_name'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location"
                                   value="<?php echo htmlspecialchars($userData['location'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                   value="<?php echo htmlspecialchars($userData['contact_phone'] ?? ''); ?>" required>
                        </div>
                        <input type="hidden" name="offers" value="Aluminum">
                        <div class="mb-3">
                            <label for="certification" class="form-label">Certification</label>
                            <input type="text" class="form-control" id="certification" name="certification"
                                   value="<?php echo htmlspecialchars($userData['certification'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($userData['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">No profile data available.</div>
        <?php endif; ?>
    </div>
    <?php include './footer.php'; ?>
    <!-- jQuery and Bootstrap JS -->
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
