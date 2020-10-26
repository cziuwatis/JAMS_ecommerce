<?php

require_once "../../php/configuration.php";

require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

use Auth0\SDK\Auth0;

$auth0 = new Auth0([
    'domain' => $auth0_domain,
    'client_id' => $auth0_client_id,
    'client_secret' => $auth0_client_secret,
    'redirect_uri' => $siteName . '/php/logged_in.php',
    'scope' => 'openid profile email',
        ]);

$auth0->login();

