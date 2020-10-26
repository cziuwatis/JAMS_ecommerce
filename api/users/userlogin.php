<?php

require_once "../../php/configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;


$response = new stdClass();
$error = new stdClass();
$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
$password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));
if (!empty($email) && !empty($password)) {
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":email", $email, PDO::PARAM_STR);
    $statement->execute();
    if ($statement->rowCount() > 0) {
        $results = $statement->fetch(PDO::FETCH_OBJ);
        if (password_verify($password, $results->password)) {
            //TODO sessions
            http_response_code(200);
            $payloadArray = array();
            $payloadArray['userId'] = $results->user_id;
            $payloadArray['nickname'] = $results->mc_username ? $results->mc_username : $results->email;
            $payloadArray['email'] = $results->email;
            $payloadArray['access'] = $results->access;
            $payloadArray['exp'] = time() + (30 * 24 * 60 * 60);
            $token = \Firebase\JWT\JWT::encode($payloadArray, $sessionSecret);
            $_SESSION['sesToken'] = $token;
            //$_SESSION['nickname'] = $results->mc_username ? $results->mc_username : $results->email;
            //$_SESSION['user_id'] = $results->user_id;
            //$_SESSION['email'] = $results->email;
            //$response->token = $_SESSION['sesToken'];
            $response->token = $token;
            $response->nickname = $results->mc_username ? $results->mc_username : $results->email;
        } else {
            $error->code = 403;
            $error->msg = "Credentials do not match";
            http_response_code(403);
            $response->error = $error;
        }
    } else {
        $error->code = 403;
        $error->msg = "Credentials do not match";
        http_response_code(403);
        $response->error = $error;
    }
} else {
    $error->code = 400;
    $error->msg = "Email and/or password is empty!";
    http_response_code(400);
    $response->error = $error;
}
echo json_encode($response);
