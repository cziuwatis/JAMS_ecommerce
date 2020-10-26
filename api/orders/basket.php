<?php

require_once "../../php/configuration.php";

require '../../vendor/autoload.php';

session_start();
$snowflake = new \Godruoyi\Snowflake\Snowflake;
$snowflake->setStartTimeStamp(strtotime('2019-11-11') * 1000);
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId];
}

$response = new stdClass();
if ($userInfo) {
    /* Connect to the database */
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

    $user_id = $userInfo['sub'];
    /* Perform Query */
    $query = "SELECT order_id, user_id, date_created, date_ordered FROM orders WHERE user_id = :user_id AND date_ordered IS NULL";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
    $statement->execute();

    /* echo "<br>---------DEBUG--------<br>";
      echo $statement->rowCount();
      echo "<br>-------END DEBUG------<br><br><br>"; */

    if ($statement->rowCount() == 1) {
        $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($userInfo['sub'] == $result->user_id) {
            /* Get items in order */
            $query = "SELECT order_lines.product_id, order_lines.quantity, products.name, products.unit_price, products.stock FROM order_lines, products WHERE order_id = :order_id AND order_lines.product_id = products.product_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
            $statement->execute();

            $result->items = $statement->fetchAll(PDO::FETCH_OBJ);

            $response->apiVersion = "1.0";
            $response->data = $result;
        } else {
            $error = new stdClass();
            $error->code = 403;
            $error->msg = "You do not have permission to access this resource.";

            $response->apiVersion = "1.0";
            $response->error = $error;

            http_response_code(403);
        }
    } else {
        // Order not found
        $error = new stdClass();
        $error->code = 404;
        $error->msg = "Your cart is empty, try adding some items in!";

        $response->apiVersion = "1.0";
        $response->error = $error;

        http_response_code(404);
    }
} else { // Not logged in
    $error = new stdClass();
    $error->code = 403;
    $error->msg = "You need to be logged in to use the cart.";

    $response->apiVersion = "1.0";
    $response->error = $error;

    http_response_code(403);
}
echo json_encode($response);
?>