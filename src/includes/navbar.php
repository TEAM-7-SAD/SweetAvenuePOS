<?php
require_once 'includes/db-connector.php';

// Query user table to display the current user
$sql = $db->prepare("SELECT id, first_name FROM user WHERE id = ?");
$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();

$current_user = $result->fetch_assoc();

?>

<!--Navbar-->
<nav class="navbar navbar-expand-lg bg-rose-white shadow-sm sticky-top">
    <div class="container-fluid ps-5">

        <!--Shop Logo and Name-->
        <span class="navbar-brand" href="#">
            <img src="images/logo-removebg-preview.png" alt="Sweet Avenue Logo" width="60" height="60">
        </span>
        <div class="text-tiger-orange text-uppercase">
            <h4 class="mb-0 fw-semibold"><strong>sweet avenue</strong></h4>
            <h6 class="mb-0 fw-medium"><strong>coffee • bakeshop</strong></h6>
        </div>

        <!--Hamburger/Toggle icon for smaller screens-->
        <div class="pe-4">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav fw-semibold ms-auto column-gap-2 pe-5">

                <!--Create Order button-->
                <li class="nav-item">
                    <a href="create-order.php" ><button class="btn btn-sm btn-tiger-orange fw-semibold px-3 py-2">Create Order</button></a>
                </li>

                <!--Home-->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <!--Sale-->
                <li class="nav-item">
                    <a class="nav-link" href="sales.php">Sales</a>
                </li>

                <!--Products-->
                <li class="nav-item">
                    <a class="nav-link" href="#">Products</a>
                </li>

                <!--Reports-->
                <li class="nav-item">
                    <a class="nav-link" href="#">Reports</a>
                </li>
            </ul>

            <!--Logged in User-->
            <ul class="nav pe-3">
                <li class="nav-item dropdown">
                    <a class="fw-bolder text-tiger-orange nav-link dropdown-toggle text-capitalize" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $current_user['first_name']; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg-end">
                        <li><a class="dropdown-item" href="accounts.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4"/>
                            </svg>
                            Accounts</a>
                        </li>
                        <li><a class="dropdown-item" href="logout.php">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0z"/>
                            <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                            </svg>
                            Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        
    </div>
</nav>
<!--End of Header-->