<?php

require_once "../../php/configuration.php";

session_start();
require '../../vendor/autoload.php';
$response = new stdClass();
if (isset($_GET['userid'])) {
    $userId = ltrim(rtrim(filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT)));
}
if (isset($_SESSION['sesToken'])) {
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId];
    if ($userInfo) {
        //why not use the token for this? Because if access level changed, token needs to be changed.
        $query = "SELECT access FROM users WHERE user_id = :userId LIMIT 1";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":userId", $userInfo['sub'], PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $result = $statement->fetch(PDO::FETCH_OBJ);
            if ($result->access < 4) {
                $error = new stdClass();
                $error->code = 403;
                $error->msg = "Insufficient access";

                $response->apiVersion = "1.0";
                $response->error = $error;
            } else {
                $query = "UPDATE users SET mc_username = NULL, mc_uuid = NULL WHERE user_id = :userId; "
                        . "DELETE FROM order_lines WHERE order_id IN (SELECT order_id FROM orders WHERE user_id = :userId && date_ordered IS NULL);"
                        . "DELETE FROM orders WHERE user_id = :userId && date_ordered IS NULL"; //delete uncomplete orders to clear cart since not allowed to have items in cart without username
                $statement = $dbConnection->prepare($query);
                $statement->bindParam(":userId", $userInfo['sub'], PDO::PARAM_STR);
                $statement->execute();
                $response->msg = "User MC username complete";
            }
        } else {
            $error = new stdClass();
            $error->code = 401;
            $error->msg = "User must be logged in to access this data"; //user doesn't exist, shouldn't happen though.

            $response->apiVersion = "1.0";
            $response->error = $error;
        }
    } else {

        $error = new stdClass();
        $error->code = 403;
        $error->msg = "Invalid session token, please re-login.";

        $response->apiVersion = "1.0";
        $response->error = $error;

        http_response_code(403);
    }
} else {
    $error = new stdClass();
    $error->code = 401;
    $error->msg = "User must be logged in to be able to reset user MC username.";

    $response->apiVersion = "1.0";
    $response->error = $error;

    http_response_code(401);
}
echo json_encode($response);


