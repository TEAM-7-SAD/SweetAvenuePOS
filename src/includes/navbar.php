<?php
include_once str_replace('/', DIRECTORY_SEPARATOR, 'file-utilities.php');
require_once  FileUtils::normalizeFilePath('includes/db-connector.php');
include_once FileUtils::normalizeFilePath('error-reporting.php');

// Query user table to display the current user
$sql = $db->prepare("SELECT id, last_name, middle_name, first_name FROM user WHERE id = ?");
$sql->bind_param('i', $_SESSION['id']);
$sql->execute();
$result = $sql->get_result();

$current_user = $result->fetch_assoc();

$full_name = $current_user['last_name'] . ', ' . $current_user['first_name'] . ' ' . $current_user['middle_name'];

$_SESSION['full_name'] = $full_name;

$current_page = basename($_SERVER['PHP_SELF']);

?>

<!--Navbar-->
<nav class="navbar navbar-expand-lg bg-medium-brown shadow-sm sticky-top">
    <div class="container">

        <!--Shop Logo and Name-->
        <span class="navbar-brand" href="#">
            <img src="images/sweet-avenue-logo.png" alt="Sweet Avenue Logo" width="60" height="60">
        </span>
        <div class="text-white text-uppercase">
            <h4 class="mb-0 fw-semibold"><strong>sweet avenue</strong></h4>
            <h6 class="mb-0 fw-medium">coffee • bakeshop</h6>
        </div>

        <!--Hamburger/Toggle icon for smaller screens-->
        <div class="pe-4">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-underline ms-auto column-gap-2 pe-5">

                <!-- Check if user is on create order page -->
                <?php if($current_page != 'create-order.php') : ?>

                <!--Create Order button-->
                <li class="nav-item me-2">
                    <a class="list-group-item" href="create-order">
                        <button class="btn btn-sm btn-light text-medium-brown fw-semibold px-3 py-2 d-flex align-items-center font-13">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle-fill me-2" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z"/>
                            </svg>
                            Create Order
                        </button>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Dashboard -->
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-white d-flex font-14 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-bar-chart-fill me-1" viewBox="0 0 16 16">
                            <path d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1z"/>
                        </svg>
                        Dashboard
                    </a>
                </li>

                <!-- Sale -->
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-white d-flex font-14 <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'active' : ''; ?>" href="sales">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#fff" class="bi bi-currency-exchange me-1" viewBox="0 0 16 16">
                            <path d="M0 5a5 5 0 0 0 4.027 4.905 6.5 6.5 0 0 1 .544-2.073C3.695 7.536 3.132 6.864 3 5.91h-.5v-.426h.466V5.05q-.001-.07.004-.135H2.5v-.427h.511C3.236 3.24 4.213 2.5 5.681 2.5c.316 0 .59.031.819.085v.733a3.5 3.5 0 0 0-.815-.082c-.919 0-1.538.466-1.734 1.252h1.917v.427h-1.98q-.004.07-.003.147v.422h1.983v.427H3.93c.118.602.468 1.03 1.005 1.229a6.5 6.5 0 0 1 4.97-3.113A5.002 5.002 0 0 0 0 5m16 5.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0m-7.75 1.322c.069.835.746 1.485 1.964 1.562V14h.54v-.62c1.259-.086 1.996-.74 1.996-1.69 0-.865-.563-1.31-1.57-1.54l-.426-.1V8.374c.54.06.884.347.966.745h.948c-.07-.804-.779-1.433-1.914-1.502V7h-.54v.629c-1.076.103-1.808.732-1.808 1.622 0 .787.544 1.288 1.45 1.493l.358.085v1.78c-.554-.08-.92-.376-1.003-.787zm1.96-1.895c-.532-.12-.82-.364-.82-.732 0-.41.311-.719.824-.809v1.54h-.005zm.622 1.044c.645.145.943.38.943.796 0 .474-.37.8-1.02.86v-1.674z"/>
                        </svg>
                        Sales
                    </a>
                </li>

                <!-- Products -->
                <li class="nav-item d-flex align-items-center">
                    <a class="nav-link text-white d-flex font-14 <?php echo basename($_SERVER['PHP_SELF']) == 'product.php' ? 'active' : ''; ?>" href="product">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="22" fill="#fff" class="bi bi-cup-fill me-1" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M.11 3.187A.5.5 0 0 1 .5 3h13a.5.5 0 0 1 .488.608l-.22.991a3.001 3.001 0 0 1-1.3 5.854l-.132.59A2.5 2.5 0 0 1 9.896 13H4.104a2.5 2.5 0 0 1-2.44-1.958L.012 3.608a.5.5 0 0 1 .098-.42Zm12.574 6.288a2 2 0 0 0 .866-3.899z"/>
                        </svg>
                        Products
                    </a>
                </li>
            </ul>

            <!-- Logged in User -->
            <ul class="nav fw-semibold">
                <li class="nav-item dropdown">
                    <a class="text-white nav-link dropdown-toggle text-capitalize font-14" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($current_user['first_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg-end">
                        <li><a class="dropdown-item text-carbon-grey" href="accounts">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#88531E" class="bi bi-people-fill pe-1" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                            </svg>
                            Accounts</a>
                        </li>
                        <li><a class="dropdown-item text-carbon-grey" href="includes/logout">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#88531E" class="bi bi-arrow-left-circle-fill pe-1" viewBox="0 0 16 16">
                                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
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