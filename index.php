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
    </head>

    <body>
        <div class="container mt-5">
            <h1 class="text-center mb-4">Welcome to Material Harbor</h1>
            <p class="text-center mb-4">Please choose your role to proceed:</p>
            <div class="row justify-content-center">
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Manufacturer</h5>
                            <p class="card-text">Register as a Manufacturer to find materials and suppliers for your
                                needs.</p>
                            <a href="login.php?user=Manufacturer" class="btn btn-primary">I am a Manufacturer</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Supplier</h5>
                            <p class="card-text">Register as a Supplier to offer your materials to potential
                                manufacturers.</p>
                            <a href="login.php?user=Supplier" class="btn btn-info">I am a Supplier</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title">Search for a Manufacturer</h6>
                            <a href="search-materials.php?type=Manufacturer" class="btn btn-primary">Search
                                Manufacturer</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="card-title">Search for a Supplier</h6>
                            <a href="search-materials.php?type=Supplier" class="btn btn-info">Search Supplier</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- jQuery and Bootstrap JS -->
        <script src="./assets/js/jquery-3.6.1.min.js"></script>
        <script src="./assets/js/bootstrap.bundle.min.js"></script>
        <script src="./assets/js/script.js?v=2"></script>
    </body>

</html>