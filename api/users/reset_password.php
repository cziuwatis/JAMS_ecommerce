<?php

require_once "../../php/configuration.php";

session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

$response = new stdClass();
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId, 'nickname' => $token->nickname, 'email' => $token->email];
}
if ($userInfo) {
    $payloadArray = array();
    $payloadArray['userId'] = $userInfo['sub'];
    $payloadArray['exp'] = time() + ( 5 * 60);
    $payloadArray['reset'] = 1;
    $resetToken = \Firebase\JWT\JWT::encode($payloadArray, $sessionSecret);
    $msg = "Hello,"
            . "\nThere has been a request to reset a password for your account registered on this email."
            . "\nTo reset it, click the following link: " . $siteName . "/reset_password.php?reset=" . \Firebase\JWT\JWT::urlsafeB64Encode($resetToken)
            . "\nThe session token will expire 5 minutes after pressing the reset password button\nThanks,\nJAMS store";
    //$msg = wordwrap($msg, 70);
    mail("d00218937@student.dkit.ie", "Password Reset Verification", $msg);
    $response->msg = "A reset email has been sent to your email, please check it to reset.";
    //echo $msg;
    // mail($userInfo['email'], "Password Reset Verification", $msg);
} else {
    $error = new stdClass();
    $error->code = 401;
    $error->msg = "User is not logged in, please login.";
    $response->error = $error;
}
echo json_encode($response);
