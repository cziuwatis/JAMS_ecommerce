<?php

require_once "../../php/configuration.php";

require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

use Auth0\SDK\Auth0;

$auth0 = new Auth0([
    'domain' => $auth0_domain,
    'client_id' => $auth0_client_id,
    'client_secret' => $auth0_client_secret,
    'redirect_uri' => $siteName . '/index.php',
    'persist_id_token' => true,
    'persist_access_token' => true,
    'persist_refresh_token' => true,
        ]);
$auth0->logout();
$return_to = $siteName . '/index.php';
$logout_url = sprintf('http://%s/v2/logout?client_id=%s&returnTo=%s', $auth0_domain, $auth0_client_id, $return_to);
header('Location: ' . $logout_url);
