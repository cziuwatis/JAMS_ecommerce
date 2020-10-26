<?php

require_once "../../php/configuration.php";

session_start();
require '../../vendor/autoload.php';
$response = new stdClass();

if (isset($_SESSION['sesToken'])) {
    /* Connect to the database */
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
                $start = 0;
                $max = 10;
                if (isset($_GET['page'])) {
                    $start = $max * ltrim(rtrim(filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT)));
                }
                /* Perform Query */
//    $query = "SELECT user_id, email, mc_username FROM users u1";
                $query = "SELECT u.user_id, u.email, u.mc_username, SUM( ( SELECT SUM( ( SELECT p.unit_price * ol.quantity FROM products p, order_lines ol WHERE p.product_id = ol.product_id AND ol.order_id = o.order_id ) ) as total FROM orders o WHERE o.user_id = u.user_id AND o.date_ordered IS NOT null) ) as spent FROM users u GROUP BY u.user_id ";
                $statement = $dbConnection->prepare($query);
                $statement->bindParam(":start", $start, PDO::PARAM_INT);
                $statement->bindParam(":max", $max, PDO::PARAM_INT);
                $statement->execute();

                /* echo "<br>---------DEBUG--------<br>";
                  echo $statement->rowCount();
                  echo "<br>-------END DEBUG------<br><br><br>"; */

                if ($statement->rowCount() > 0) {
                    $result = $statement->fetchAll(PDO::FETCH_OBJ);

                    $response->apiVersion = "1.0";
                    $response->data = new stdClass();
                    $response->data->users = $result;

                    $query = "SELECT COUNT(*) as count FROM users";
                    $statement = $dbConnection->prepare($query);
                    $statement->execute();

                    $response->data->max_users = ($statement->fetch(PDO::FETCH_OBJ))->count;
                } else {
                    // Users not found
                    $error = new stdClass();
                    $error->code = 404;
                    $error->msg = "No users found.";

                    $response->apiVersion = "1.0";
                    $response->error = $error;

                    http_response_code(404);
                }
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
    $error->msg = "User must be logged in to access this data.";

    $response->apiVersion = "1.0";
    $response->error = $error;

    http_response_code(401);
}
echo json_encode($response);
?>