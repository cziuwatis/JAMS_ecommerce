<?php

require_once "../../php/configuration.php";

/* Connect to the database */
$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

session_start();
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId];
}


$whole_response = new stdClass();
$whole_response->apiVersion = "1.0";
$error = new stdClass();
if ($userInfo) {
    $minecraft_username = trim(filter_input(INPUT_GET, "nickname", FILTER_SANITIZE_STRING));
    if (empty($minecraft_username)) {
        $error->code = 400;
        $error->msg = "No minecraft username supplied";
        $whole_response->error = $error;
    } else {
# Our new data
        $data = array(
            $minecraft_username,
            'nonExistingPlayer'
        );
# Create a connection
        $url = 'https://api.mojang.com/profiles/minecraft';
        $ch = curl_init($url);
# Setting our options
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
# Get the response
        $response = curl_exec($ch);
        $response = json_decode($response);
        curl_close($ch);
        $whole_response->test = $response;
        if (empty($response) || sizeOf($response) < 1) {
            //error username doesn't exist
            $error->code = 404;
            $error->msg = "Such minecraft username doesn't exist or unable to connect to mojang.";
            $whole_response->error = $error;
        } else {
//            $query = "SELECT * FROM users WHERE mc_username = :mc_username";
//            $statement = $dbConnection->prepare($query);
//            $statement->bindParam(":mc_username", $response["name"], PDO::PARAM_STR);
//            $statement->execute();
//            if ($statement->rowCount() > 0) {
//                //error username taken by someone else
//                $error->code = 403;
//                $error->msg = "Another account already has this minecraft username set.";
//                $whole_response->error = $error;
//            } else {
            $data = new stdClass();
            $data->nickname = $minecraft_username;
            //check if username exists
            $query = "SELECT mc_username FROM users WHERE mc_username = :mc_username AND user_id != :user_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":mc_username", $minecraft_username, PDO::PARAM_STR);
            $statement->bindParam(":user_id", $userInfo["sub"], PDO::PARAM_STR);
            $statement->execute();
            if ($statement->rowCount() > 0) {
                //username is taken by another user
                $error->code = 403;
                $error->msg = "Username is already taken by another user.";
                $whole_response->error = $error;
            } else {
                //username is not already taken
                $query = "SELECT mc_username FROM users WHERE user_id = :user_id";
                $statement = $dbConnection->prepare($query);
                $statement->bindParam(":user_id", $userInfo["sub"], PDO::PARAM_STR);
                $statement->execute();
                if ($statement->rowCount() > 0) {
                    //user exists
                    $results = $statement->fetch(PDO::FETCH_OBJ);
                    if (strcasecmp($results->mc_username, $response[0]->name)) {
                        $query = "UPDATE users SET mc_username = :mc_username, mc_uuid = :mc_uuid WHERE user_id = :user_id";
                        $statement = $dbConnection->prepare($query);
                        $statement->bindParam(":mc_username", $response[0]->name, PDO::PARAM_STR);
                        $statement->bindParam(":mc_uuid", $response[0]->id, PDO::PARAM_STR);
                        $statement->bindParam(":user_id", $userInfo["sub"], PDO::PARAM_STR);
                        $statement->execute();
                        $token->nickname = $minecraft_username;
                        $_SESSION['sesToken'] = \Firebase\JWT\JWT::encode($token, $sessionSecret);
                        // $userInfo['nickname'] = $minecraft_username;
                        // $auth0->setUser($userInfo);
                    } else {
                        //user is trying to set the same mc username that they already have.
                        $error->code = 403;
                        $error->msg = "Trying to set the same minecraft username as already set.";
                        $whole_response->error = $error;
                    }
                } else {
                    //user doesn't exist
                    $error->code = 403;
                    $error->msg = "User doesn't exist, try to relog.";
                    $whole_response->error = $error;
                }
                // }
            }
        }
    }
} else {
    //error user not logged in
    $error->code = 401;
    $error->msg = "User is not logged in, please login.";
    $whole_response->error = $error;
}
//session here is session on that page but not on the category page (I don't think).
//echo json_encode($_SESSION);
echo json_encode($whole_response);






