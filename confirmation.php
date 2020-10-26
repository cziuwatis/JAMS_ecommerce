<?php
require_once "./php/configuration.php";

session_start();
//
require 'vendor/autoload.php';
//\Firebase\JWT\JWT::$leeway = 60;
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['nickname' => $token->nickname, 'access' => $token->access];
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
        <title>Just Another Minecraft Store CONFIRMED ORDER</title>

        <!--
                CSS
                ============================================= -->
        <link rel="stylesheet" href="css/linearicons.css">
        <link rel="stylesheet" href="css/owl.carousel.css">
        <link rel="stylesheet" href="css/themify-icons.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
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
                        <h1>Confirmation</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="cart.php">Cart<span class="lnr lnr-arrow-right"></span></a>
                            <a href="#">Confirmation</a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->

        <!--================Order Details Area =================-->

        <div class="checkout-wrap">
            <ul class="checkout-bar final">

                <li class="first visited">Choose payment method</li>

                <li class="visited">Process Transaction</li>

                <li class="visited final">Payment Complete</li>

            </ul>
        </div>

        <section class="order_details section_gap">
            <div class="container">
                <h3 class="title_confirmation">Thank you. Your order has been received.</h3>
                <div class="row order_d_inner">
                    <div class="col-lg-4">
                        <div class="details_item">
                            <h4>Order Info</h4>
                            <ul id="order_info" class="list">
                                <li><a href="#"><span>Order id</span> : </a></li>
                                <li><a href="#"><span>Date</span> : </a></li>
                                <li><a href="#"><span>Payment method</span> : </a></li>
                                <li><a href="#"><span>Minecraft username</span> : </a></li>
                                <li><a href="#"><span>Total</span> : </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="order_details_table">
                    <h2>Order Details</h2>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody id="product_list">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!--================End Order Details Area =================-->

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
        <script src="js/confirmation.js"></script>
    </body>

</html>