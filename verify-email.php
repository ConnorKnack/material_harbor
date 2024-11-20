<?php
include 'db_conn.php';
if (isLoggedIn()) {
    header("Location: select-materials.php");
}elseif(!isset($_SESSION['userId'])){
    header("Location: login.php");
}


$info = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verifyOtp'])) {
    // Retrieve the OTP entered by the user
    $enteredOtp = $_POST['verifyCode'];

    // Retrieve session variables
    $userEmail = $_SESSION['email'];
    $userType = $_SESSION['userType'];

    // Determine the correct table based on user type
    if (strtolower($userType) === 'manufacturer') {
        $table = 'manufacturers';
    } elseif (strtolower($userType) === 'supplier') {
        $table = 'suppliers';
    } else {
        die('Invalid user type.');
    }

    // Fetch the stored OTP from the appropriate table
    $sql = "SELECT otp, userActive FROM $table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($storedOtp, $userActive);
    $stmt->fetch();

    // Check if OTP matches and if the user is not already active
    if ($stmt->num_rows > 0) {
        if ($userActive) {
            $info = "<div class='alert alert-info'>Your account is already verified.</div>";
        } elseif ($storedOtp === $enteredOtp) {
            // OTP is correct, activate the user
            $updateSql = "UPDATE $table SET userActive = 1, otp = NULL WHERE email = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('s', $userEmail);
            if ($updateStmt->execute()) {
                $_SESSION['userActive'] = true; // Update session to reflect the user is now active
                $info = "<div class='alert alert-success'>Email verified successfully!</div>";
                header('Location: select-materials.php'); // Redirect to dashboard or any page
                exit();
            } else {
                $info = "<div class='alert alert-danger'>Failed to activate account. Please try again.</div>";
            }
        } else {
            $info = "<div class='alert alert-danger'>Invalid OTP. Please try again.</div>";
        }
    } else {
        $info = "<div class='alert alert-danger'>No user found with the provided email.</div>";
    }

    $stmt->close();
}

// print_r($_SESSION);
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
        <div class="container mt-5">
            <div class="row justify-content-center py-5">
                <div class="col-md-4">
                    <?php echo $info; ?>
                    <div class="card">
                        <!-- Login Form -->
                        <div class="card-body">
                            <h3 class="text-center">Email Verification</h3>
                            <form method="POST" action="verify-email.php" novalidate class="needs-validation">
                                <div class="mb-3">
                                    <label for="verifyCode" class="form-label">OTP Code</label>
                                    <input type="password" class="form-control" id="verifyCode" name="verifyCode"
                                        required placeholder="Enter your one time six digit passcode"
                                        pattern="[0-9]{6}">
                                        <p class="text-muted"><small>Use the otp sent to your e-mail addess <b><?php echo $_SESSION['email']; ?></b></small></p>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" name="verifyOtp" class="btn btn-primary">Verify</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include './footer.php'; ?>
        <!-- jQuery and Bootstrap JS -->
        <script src="./assets/js/jquery-3.6.1.min.js"></script>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/script.js?v=2"></script>
    </body>

</html>