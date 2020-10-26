<?php
require_once "./php/configuration.php";

session_start();
//
require 'vendor/autoload.php';
//\Firebase\JWT\JWT::$leeway = 60;
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId, 'nickname' => $token->nickname, 'access' => $token->access];
}
if (!$userInfo) {
    header("location: " . $siteName . "/profile_login.php");
    die();
} else {
    //why not use the token for this? Because if access level changed, token needs to be changed.
    /* Connect to the database */
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query = "SELECT access FROM users WHERE user_id = :userId LIMIT 1";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":userId", $userInfo['sub'], PDO::PARAM_STR);
    $statement->execute();
    if ($statement->rowCount() > 0) {
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($result->access < 4) {
            header("location: " . $siteName . "/");
            die();
        }
    } else {
        header("location: " . $siteName . "/");
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">

    <head>
        <!-- Mobile Specific Meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Favicon-->
        <link rel="shortcut icon" href="img/fav.png">
        <!-- Author Meta -->
        <meta name="author" content="CodePixar">
        <!-- Meta Description -->
        <meta name="description" content="">
        <!-- Meta Keyword -->
        <meta name="keywords" content="">
        <!-- meta character set -->
        <meta charset="UTF-8">
        <!-- Site Title -->
        <title>Just Another Minecraft Store Users Manager</title>

        <!--
            CSS
            ============================================= -->
        <link rel="stylesheet" href="css/linearicons.css">
        <link rel="stylesheet" href="css/owl.carousel.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/themify-icons.css">
        <link rel="stylesheet" href="css/nice-select.css">
        <link rel="stylesheet" href="css/nouislider.min.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body id="profile_settings">
        <?php
        include_once 'header.php';
        ?>

        <!-- Start Banner Area -->
        <section class="banner-area organic-breadcrumb">
            <div class="container">
                <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                    <div class="col-first">
                        <h1>Users manager page</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="users_manager.php">Users Manager<span class="lnr lnr-arrow-right"></span></a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->
        <section class="section_gap">
            <div class="container">
                <div class="pagination-container">
                    <div class="pagination">
                        <span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="container">
                <ul id="users" class="users_container">
                    <li class="users_row users_header">
                        <div class="users_column users_header_column">MC username</div>
                        <div class="users_column users_header_column">Email</div>
                        <div class="users_column users_header_column">User ID</div>
                        <div class="users_column users_header_column users_cash_spent">$ Spent</div>
                        <div class="users_column users_header_column users_context_option">&nbsp;</div>
                    </li>
                    <li class="users_row">
                        <div class="users_column">cziuwatis</div>
                        <div class="users_column">stockemail@email.com</div>
                        <div class="users_column">6162832a0d69333b17bdac3b3faf78922d1fb9d5b4af16c3ec3e7bd93598e92b5e98cacf0478f</div>
                        <div class="users_column users_cash_spent">0</div>
                        <div class="users_column users_context_option">
                            <span class="users_context_click" onClick="openUserManageMenu(event)">&#x22EE;</span>
                        </div>
                    </li>
                    <li class="users_row">
                        <div class="users_column">cziuwatis</div>
                        <div class="users_column">stockemail@email.com</div>
                        <div class="users_column">6162832a0d69333b17bdac3b3faf78922d1fb9d5b4af16c3ec3e7bd93598e92b5e98cacf0478f</div>
                        <div class="users_column users_cash_spent">20.43</div>
                        <div class="users_column users_context_option"><span class="users_context_click" onClick="openUserManageMenu(event)">&#x22EE;</span></div>
                    </li>
                    <li class="users_row">
                        <div class="users_column">cziuwatis</div>
                        <div class="users_column">stockemail@email.com</div>
                        <div class="users_column">6162832a0d69333b17bdac3b3faf78922d1fb9d5b4af16c3ec3e7bd93598e92b5e98cacf0478f</div>
                        <div class="users_column users_cash_spent">14464.44</div>
                        <div class="users_column users_context_option"><span class="users_context_click" onClick="openUserManageMenu(event)">&#x22EE;</span></div>
                    </li>
                </ul>
            </div>
            <div class="container">
                <div class="pagination-container">
                    <div class="pagination">
                        <span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span>
                    </div>
                </div>
            </div>
        </section>


        <ul id="context_menu" class="context_menu hide" data-userid="">
            <li class="context_menu_item" onclick="handleContextResetMcUsername(event)">Reset MC</li>
            <li class="context_menu_item" onclick="closeUserManageMenu()">Close</li>
        </ul>
        <?php
        include_once 'footer.php';
        ?>

        <div id="ag_user_message"></div>

        <script src="js/vendor/jquery-2.2.4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4"
        crossorigin="anonymous"></script>
        <script src="js/vendor/bootstrap.min.js"></script>
        <script src="js/jquery.ajaxchimp.min.js"></script>
        <script src="js/jquery.nice-select.min.js"></script>
        <script src="js/jquery.sticky.js"></script>
        <script src="js/nouislider.min.js"></script>
        <script src="js/jquery.magnific-popup.min.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>
        <script src="js/users_manager.js" type="text/javascript"></script>
    </body>
