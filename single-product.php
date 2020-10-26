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
        <title>Just Another Minecrat Store Displaying product</title>
        <!--
                        CSS
                        ============================================= -->
        <link rel="stylesheet" href="css/linearicons.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/themify-icons.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="css/owl.carousel.css">
        <link rel="stylesheet" href="css/nice-select.css">
        <link rel="stylesheet" href="css/nouislider.min.css">
        <link rel="stylesheet" href="css/ion.rangeSlider.css" />
        <link rel="stylesheet" href="css/ion.rangeSlider.skinFlat.css" />
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
                        <h1>Product Details Page</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="category.php">Shop<span class="lnr lnr-arrow-right"></span></a>
                            <a href="#">product-details</a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->
        <div id="ag_single_product_container">
            <!--================Single Product Area =================-->
            <div class="product_image_area">
                <div class="container">
                    <div class="row s_product_inner">
                        <div class="col-lg-6">
                            <div id="ag_single_product_images" class="s_Product_carousel">&nbsp;</div>
                        </div>
                        <div class="col-lg-5 offset-lg-1">
                            <div class="s_product_text">
                                <h3 id="ag_single_product_title">&nbsp;</h3>
                                <h2 id="ag_single_product_price">&nbsp;</h2>
                                <ul class="list">
                                    <li id="ag_single_product_category">&nbsp;</li>
                                    <li id="ag_single_product_availablity"></li>
                                </ul>
                                <p id="ag_single_product_short_description">&nbsp;</p>
                                <div class="product_count">
                                    <label for="qty">Quantity:</label>
                                    <input type="text" name="qty" id="sst" maxlength="12" value="1" title="Quantity:" class="input-text qty">
                                    <button onclick="var result = document.getElementById('sst');
                                            var sst = result.value;
                                            var stock_level = document.getElementById('ag_single_product_stock_level').title;
                                            if (stock_level == 'unlimited')
                                            {
                                                stock_level = 999;
                                            } else
                                            {
                                                stock_level = parseInt(stock_level);
                                            }
                                            if (!isNaN(sst) && result.value < stock_level) {
                                                result.value++;
                                            } else
                                            {
                                                return false;
                                            }
                                            "
                                            class="increase items-count" type="button"><i class="lnr lnr-chevron-up"></i></button>
                                    <button onclick="var result = document.getElementById('sst');
                                            var sst = result.value;
                                            if (!isNaN(sst) && sst > 0)
                                                result.value--;
                                            return false;"
                                            class="reduced items-count" type="button"><i class="lnr lnr-chevron-down"></i></button>
                                </div>
                                <div class="card_area d-flex align-items-center">
                                    <a id="order_button" class="primary-btn" href="#">Add to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--================End Single Product Area =================-->
        </div>
        <section class="section_gap_bottom"></section>
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
        <script src="js/main_product.js" type="text/javascript"></script>

    </body>

</html>