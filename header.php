<style>
    .nav-link.active{
        color: #000000 !important;
        font-weight: 600;
    }
</style>
<nav class="navbar navbar-expand-xl navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="./index.php">Material Harbour</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php
                if (isLoggedIn()) {
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page == 'profile') ? 'active' : ''; ?>"
                            href="profile.php?userType=<?php echo strtolower($_SESSION['userType']); ?>&userID=<?php echo $_SESSION['userId']; ?>"><span class="text-dark">Hey
                            <span class="text-success"><?php echo $user_type = (isset($_SESSION['companyName'])) ? $_SESSION['companyName'] : 'Guest'; ?></span>!</span></a>
                    </li>
                    <?php
                }
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active = ($page == 'home') ? 'active' : ''; ?>" href="select-materials.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active = ($page == 'manufacturer-search') ? 'active' : ''; ?>" href="search-materials.php?type=Manufacturer">Search Manufacturers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $active = ($page == 'supplier-search') ? 'active' : ''; ?>" href="search-materials.php?type=Supplier">Search Suppliers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>