<?php

require_once "../../php/configuration.php";

session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId];
}
if ($userInfo) {
    $session_id = trim(filter_input(INPUT_GET, "session_id", FILTER_SANITIZE_STRING));
    if ($session_id) {
        \Stripe\Stripe::setApiKey($stripeSK);
        $response = \Stripe\Checkout\Session::retrieve(
                        $session_id
        );

        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        $order_id = trim(filter_var($response->client_reference_id, FILTER_SANITIZE_NUMBER_INT));
        $query = "SELECT user_id FROM orders WHERE order_id = :order_id";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $results = $statement->fetch(PDO::FETCH_OBJ);
            if ($results->user_id == $userInfo['sub']) {
                echo json_encode($order_id);
                exit();
            }
        }
    }
}
http_response_code(401);
$response = new stdClass();
$error = new stdClass();
$error->code = 401;
$error->msg = "Something went wrong!";
$response->apiVersion = "1.0";
$response->error = $error;
echo json_encode($response);

