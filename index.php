<?php
require './db_conn.php';
if (isLoggedIn()) {
    header("Location: select-materials.php");
}
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
    <style>
        body {
            background: linear-gradient(to right, #e0eafc, #cfdef3);
            color: #333;
            font-family: 'Graphik Trial', sans-serif;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            line-height: 1.6;
        }

        .container{
            margin-bottom: 80px;
        }
        .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%; /* Ensures cards have the same height */
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title {
            margin-bottom: 15px;
        }

        .card-text {
            flex-grow: 1;
        }

        .btn {
            margin-top: auto;
        }
    </style>
</head>

<body>
<?php include './header.php'; ?>
    <div class="container">
        <div class="header-text">
            <h1>Welcome to Material Harbor</h1>
            <p>Select your role to proceed or explore as a guest:</p>
        </div>
        <div class="row g-4 justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Manufacturer</h5>
                        <p class="card-text">Register to find materials and suppliers tailored to your needs.</p>
                        <a href="login.php?user=Manufacturer" class="btn btn-primary">I am a Manufacturer</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Supplier</h5>
                        <p class="card-text">Join to offer your materials to potential manufacturers.</p>
                        <a href="login.php?user=Supplier" class="btn btn-info">I am a Supplier</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- New Heading -->
        <div class="row justify-content-center mt-4">
            <div class="col-12 text-center">
                <h2 class="mt-3">Search as a Guest</h2>
            </div>
        </div>
        
        <div class="row g-4 justify-content-center mt-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Search for a Manufacturer</h5>
                        <a href="search-materials.php?type=Manufacturer" class="btn btn-primary">Search Manufacturer</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Search for a Supplier</h5>
                        <a href="search-materials.php?type=Supplier" class="btn btn-info">Search Supplier</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include './footer.php'; ?>
    <!-- jQuery and Bootstrap JS -->
    <script src="./assets/js/jquery-3.6.1.min.js"></script>
    <script src="./assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>
