<?php

require_once "../../php/configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

$email = trim(filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL));
$password = trim(filter_input(INPUT_POST, "password", FILTER_SANITIZE_STRING));

$response = new stdClass();
$error = new stdClass();
if (!empty($email) && !empty($password)) {
    if (!preg_match('/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', $email) || strlen($password) < 10 || !preg_match('/[a-z]+/', $password) || !preg_match('/[A-Z]+/', $password) //is there a point in checking password here?
            || !preg_match('/[0-9]+/', $password) || !preg_match('/[£!#€$%^&*]+/', $password)) {
        $error->code = 400;
        $error->msg = "Some fields were filled out incorrectly!";
        http_response_code(400);
        $response->error = $error;
    } else {
        $query = "SELECT email FROM users WHERE email = :email";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":email", $email, PDO::PARAM_STR);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $error->code = 403;
            $error->msg = "User already exists, try to log in";
            http_response_code(403);
            $response->error = $error;
        } else {
            $userid = uniqid(hash("sha256", $email));
            createAvatarImage(strpos(substr($email, 0, 2), "@") == false ? substr($email, 0, 2) : substr($email, 0, 1), $userid);
            $password = password_hash($password, PASSWORD_BCRYPT);
            $query = "INSERT INTO users (user_id, email, password) VALUES(:user_id, :email, :password)";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":user_id", $userid, PDO::PARAM_STR);
            $statement->bindParam(":email", $email, PDO::PARAM_STR);
            $statement->bindParam(":password", $password, PDO::PARAM_STR);
            $statement->execute();
            http_response_code(201);
            $payloadArray = array();
            $payloadArray['userId'] = $userid;
            $payloadArray['nickname'] = $email;
            $payloadArray['email'] = $email;
            $payloadArray['access'] = 0;
            $payloadArray['exp'] = time() + (30 * 24 * 60 * 60);
            $token = \Firebase\JWT\JWT::encode($payloadArray, $sessionSecret);
            $_SESSION['sesToken'] = $token;
            //$_SESSION['nickname'] = $email;
            //$_SESSION['user_id'] = $userid;
            //$_SESSION['email'] = $email;
             $response->token = $token;
             $response->nickname = $email;
        }
    }
} else {
    $error->code = 400;
    $error->msg = "Email and/or password is empty!";
    http_response_code(400);
    $response->error = $error;
}
echo json_encode($response);

//taken and adapted from https://phppot.com/php/how-to-generate-initial-avatar-image-from-username-using-php-dynamically/
function createAvatarImage($string, $userid) {
    $imageFilePath = "../../img/avatars/" . $userid . ".png";
    //base avatar image that we use to center our text string on top of it.
    $avatar = imagecreatetruecolor(150, 150);
    $bg_color = imagecolorallocate($avatar, mt_rand(0, 150), mt_rand(0, 150), mt_rand(0, 150));
    imagefill($avatar, 0, 0, $bg_color);
    $avatar_text_color = imagecolorallocate($avatar, 255, 255, 255);
    // Load the gd font and write 
    //$font = imageloadfont('Latin2_8x20_LE.gdf');
    imagestring($avatar, 5, 50, 50, $string, $avatar_text_color);
    //putenv('GDFONTPATH=' . realpath('.'));
    // imagettftext($avatar, 10, 0, 10, 10, $avatar_text_color, "RobotoCondensed.ttf", $string);
    imagepng($avatar, $imageFilePath);
    imagedestroy($avatar);
    return $imageFilePath;
}
