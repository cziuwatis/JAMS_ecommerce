<?php
//require_once "./php/configuration.php";

session_start();
//
require 'vendor/autoload.php';
//\Firebase\JWT\JWT::$leeway = 60;
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    header("Location: index.php");
}
?>
<html>
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
        <title>Just Another Minecraft Store REGISTER</title>

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

    <body>

        <?php
        include_once 'header.php';
        ?>

        <!-- Start Banner Area -->
        <section class="banner-area organic-breadcrumb">
            <div class="container">
                <div class="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                    <div class="col-first">
                        <h1>Register</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="profile_register.php">Register</a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->
        <!-- Register Area -->
        <section class="login_box_area section_gap">
            <div class="ssd_form_container">
                <div class="ssd_form_row">
                    <div class="ssd_form_col">
                        <div class="login_form_inner">
                            <h3>Register here</h3>
                            <form class="ssd_form_row login_form" action="index.php" method="post" id="registerForm" novalidate="novalidate">
                                <div class="ssd_input_container">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email'">
                                </div>
                                <div class="ssd_input_container">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Password'">
                                </div>
                                <div class="ssd_input_container">
                                    <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm password" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Confirm password'">
                                </div>
                                <div class="ssd_input_container">
                                    <button type="submit" value="submit" class="primary-btn">Register</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="ssd_form_col">
                        <ul class="register_form_checklist" id="register_form_errors">
                            <li class="checklist_item">Email must be of format email@email.com and not longer than 255 characters</li>
                            <li class="checklist_item">Password cannot be less than 10 characters in length</li>
                            <li class="checklist_item">Password must contain a lowercase letter</li>
                            <li class="checklist_item">Password must contain an uppercase letter</li>
                            <li class="checklist_item">Password must contain a digit</li>
                            <li class="checklist_item">Password must contain at least one of the following symbols [£!#€$%^&*]</li>
                            <li class="checklist_item">Confirmed password and password must match</li>
                        </ul>
                        <div class="login_box_img register_box_img">
                            <img class="img-fluid" src="img/banner/mcpic2.jpg" alt="">
                            <div class="hover">
                                <h4>Already have an account?</h4>
                                <p>Then login down below!</p>
                                <a class="primary-btn" href="profile_login.php">Log in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Register Area -->

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
        <script src="js/jquery.magnific-popup.min.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>
        <script src="js/main_register.js"></script>
        <script>
<?php
if (isset($_SESSION['error'])) {
    //TODO: Show error message
    /*
      $error->code = 500;
      $error->msg = "Order quantity ".$row->quantity."greater than stock ".$row->stock."for item ".$row->name;
      $response->error = $error;
      $response->apiVersion = "1.0";
     */
    echo "displayMessage('" . $_SESSION['error']->error->msg . "', 5000);";
    unset($_SESSION['error']);
}
?>
        </script>
    </body>
</html>

