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
        <title>Just Another Minecraft Store CART</title>

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
                        <h1>Shopping Cart</h1>
                        <nav class="d-flex align-items-center">
                            <a href="index.php">Home<span class="lnr lnr-arrow-right"></span></a>
                            <a href="category.php">Shop<span class="lnr lnr-arrow-right"></span></a>
                            <a href="cart.php">Cart</a>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
        <!-- End Banner Area -->

        <!--================Cart Area =================-->
        <section class="cart_area">
            <div class="container">
                <div class="cart_inner">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Product</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody id="cartlist">

                                <tr class="bottom_button">
                                    <td>
                                        <a id="update_cart_button" class="genric-btn primary-border e-large" style="font-size:1.2em;" onclick='updateCart()' href="#">Update</a>
                                        <a id="clear_cart_button2" class="genric-btn primary-border e-large" style="font-size:1.2em;" onclick='clearCart()' href="#">Clear</a>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                        <!--<h5 class='placeholder'>Total</h5>-->
                                        <a id="clear_cart_button" class="genric-btn primary-border e-large" style="font-size:1.2em;" onclick='clearCart()' href="#">Clear</a>
                                    </td>
                                    <td>
                                        <h5 class='placeholder'>€<span>000000.00</span></h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h5 class="ag_mobile_display">Total:</h5>
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <h5>Total:</h5>
                                    </td>
                                    <td>
                                        <h5>€<span id="total_price">0000.00</span></h5>
                                    </td>
                                </tr>
                                <tr class="out_button_area">
                                    <td>

                                        <a class="genric-btn primary-border e-large ag_mobile_display" href="category.php">Continue Shopping</a>
                                        <a id="checkout_button" onclick="location.href = 'choose_payment_method.php'" class="checkout_button genric-btn primary e-large ag_mobile_display" href="#">Proceed to checkout</a>
                                    </td>
                                    <td>

                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="checkout_btn_inner d-flex align-items-center">
                                            <a class="gray_btn" href="category.php">Continue Shopping</a>
                                            <a id="checkout_button" onclick="location.href = 'choose_payment_method.php'" class="checkout_button primary-btn" href="#">Proceed to checkout</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <!--================End Cart Area =================-->

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
        <script src="js/cart.js"></script>
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
