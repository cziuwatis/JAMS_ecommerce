<?php

require_once "../../php/configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

$resetTokenB64 = trim(filter_input(INPUT_POST, "resetToken", FILTER_SANITIZE_STRING));

$response = new stdClass();
$error = new stdClass();
if (!empty($resetTokenB64) && $resetTokenB64 != "null") {
    try {
        $resetToken = \Firebase\JWT\JWT::decode(\Firebase\JWT\JWT::urlsafeB64Decode($resetTokenB64), $sessionSecret, array('HS256'));
        $password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));
        if (!empty($password)) {
            $password = password_hash($password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":password", $password, PDO::PARAM_STR);
            $statement->bindParam(":user_id", $resetToken->userId, PDO::PARAM_STR);
            $statement->execute();
            http_response_code(200);
            $query = "SELECT * FROM users WHERE user_id = :user_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":user_id", $resetToken->userId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_OBJ);
            $payloadArray = array();
            $payloadArray['userId'] = $resetToken->userId;
            $payloadArray['nickname'] = $result->mc_username ? $result->mc_username : $result->email;
            $payloadArray['email'] = $result->email;
            $payloadArray['exp'] = time() + (30 * 24 * 60 * 60);
            $token = \Firebase\JWT\JWT::encode($payloadArray, $sessionSecret);
            $_SESSION['sesToken'] = $token;
            $response->token = $token;
            $response->nickname = $result->mc_username ? $result->mc_username : $result->email;
        } else {
            $error->code = 400;
            $error->msg = "New password cannot be empty";
            http_response_code(400);
            $response->error = $error;
        }
    } catch (\Firebase\JWT\ExpiredException $e) {
        $error->code = 403;
        $error->msg = "Reset token has expired";
        http_response_code(403);
        $response->error = $error;
    }
} else {
    $error->code = 400;
    $error->msg = "No reset token present";
    http_response_code(400);
    $response->error = $error;
}
echo json_encode($response);
