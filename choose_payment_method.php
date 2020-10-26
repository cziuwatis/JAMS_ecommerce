<?php
require_once "./php/configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
session_start();
//
require 'vendor/autoload.php';
//\Firebase\JWT\JWT::$leeway = 60;
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['nickname' => $token->nickname, 'sub' => $token->userId, 'access' => $token->access];
}

//get user's basket
//get order id 
//get lines from basket from order id
//insert to line items
$error = new stdClass();
$response = new stdClass();
if ($userInfo) {
    $query = "SELECT order_id FROM orders WHERE user_id = :user_id AND date_ordered IS NULL";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":user_id", $userInfo["sub"], PDO::PARAM_STR);
    $statement->execute();
    if ($statement->rowCount() > 0) {
        $result = $statement->fetch(PDO::FETCH_OBJ);
        $query = "SELECT order_lines.quantity, products.product_id, products.name, products.description, products.unit_price FROM order_lines, products WHERE order_id = :order_id AND order_lines.product_id = products.product_id";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            
        } else {
            //order line empty???
            $error->code = 404;
            $error->msg = "Order empty";
            $response->error = $error;
            header("location: cart.php");
        }
    } else {
        //order_id not found
        $error->code = 404;
        $error->msg = "Order not found";
        $response->error = $error;
        header("location: cart.php");
    }
} else {
    header("location: api/users/login.php");
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
        <title>Just Another Minecraft Store PAYMENT METHOD</title>

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

        <script src="https://js.stripe.com/v3/"></script>
        <script>
        </script>

    </head>
    <body>
        <?php
        include_once 'header.php';
        ?>
        <div class="checkout-wrap">
            <ul class="checkout-bar">

                <li class="first active">Choose payment method</li>

                <li class="next">Process Transaction</li>

                <li class="">Payment Complete</li>

            </ul>
        </div>
        <div class="container ag_payment_method_container">
            <div class="row">
                <div class="col-12">
                    <div class="row ag_payment_method_header">
                        <h2>Choose payment method</h2>
                    </div>
                    <div class="row">
                        <div class="col-6 ag_payment_method">
                            <button class="genric-btn disable_page_button primary e-large">Paypal (unavailable)</button>
                            <!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"></td></tr><tr><td align="center"><a href="#" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup', 'WIPaypal', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700');"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg" border="0" alt="PayPal Acceptance Mark"></a></td></tr></table><!-- PayPal Logo -->
                        </div>
                        <div class="col-6 ag_payment_method">
                            <button onclick="location.href = 'payment.php'" class="genric-btn primary e-large">Stripe</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

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
        <script src="js/main.js" type="text/javascript"></script>
    </body>
</html>

