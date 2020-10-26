<?php

require_once "../../php/configuration.php";
session_start();
require '../../vendor/autoload.php';
\Firebase\JWT\JWT::$leeway = 60;

session_destroy();
header('Location:' . $siteName);

