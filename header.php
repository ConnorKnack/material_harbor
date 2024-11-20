<nav class="navbar navbar-expand-xl navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="./index.php">Material Harbor</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()) { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page == 'profile') ? 'active' : ''; ?>"
                            href="profile.php?userType=<?php echo strtolower($_SESSION['userType']); ?>&userID=<?php echo $_SESSION['userId']; ?>">
                            <span class="text-dark">Hey <span class="text-success">
                            <?php echo $user_type = (isset($_SESSION['companyName'])) ? $_SESSION['companyName'] : 'Guest'; ?>
                            </span>!</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $active = ($page == 'edit-profile') ? 'active' : ''; ?>"
                            href="edit-profile.php?userType=<?php echo strtolower($_SESSION['userType']); ?>&userID=<?php echo $_SESSION['userId']; ?>">Edit Profile</a>
                    </li>
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
                <?php } else { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Login
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="loginDropdown">
                            <li><a class="dropdown-item" href="login.php?user=Supplier">Login as Supplier</a></li>
                            <li><a class="dropdown-item" href="login.php?user=Manufacturer">Login as Manufacturer</a></li>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>
