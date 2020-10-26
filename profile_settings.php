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
        <title>Just Another Minecraft Store Profile</title>

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
                        <h1>Profile settings page</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="#">Profile<span class="lnr lnr-arrow-right"></span></a>
                            <a href="profile_settings.php">Settings</a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->
        <section class="section_gap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="login_form_inner settings_form_inner">
                            <h1>Profile Settings</h1>
                            <img id="ag_profile_picture" alt="profile_image" src="<?php echo "img/avatars/" . $userInfo['sub'] . ".png"; ?>">
                            <div class="row login_form" id="contactForm">
                                <div class="col-md-12 form-group">
                                    <label for="nickname">Minecraft username:</label>
                                    <input type="text" class="form-control" id="nickname" name="nickname" minlength="3" maxlength="16" placeholder="<?php echo $userInfo["nickname"]; ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Minecraft Username'">
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="button" onclick="changeNickname()" class="primary-btn">Change minecraft username</button>
                                </div>
                                <div class="col-md-12 form-group">
                                    <button type="button" onclick="resetPassword()" class="primary-btn">Reset Password</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


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
        <script src="js/main_profile.js" type="text/javascript"></script>
    </body>
