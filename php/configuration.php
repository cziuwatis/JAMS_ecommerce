<?php

/* * ************************ You need to set the values below to match your project ************************ */

// localhost website and localhost database
$localHostSiteFolderName = "ecommerce_jams";

$localhostDatabaseName = "ecommerce_jams";
$localHostDatabaseHostAddress = "localhost";
$localHostDatabaseUserName = "root";
$localHostDatabasePassword = "";

// remotely hosted website and remotely hosted database       /* you will need to get the server details below from your host provider */
$serverWebsiteName = ""; /* use this address if hosting website on the college students' website server */

$serverDatabaseName = "dbname";
$serverDatabaseHostAddress = "fgfgf.fgfg.fgfg";         /* use this address if hosting database on the college computing department database server */
$serverDatabaseUserName = "dbuser";
$serverDatabasePassword = "ABCD";




$useLocalHost = true;      /* set to false if your database is NOT hosted on localhost */


if ($useLocalHost == true) {
    $siteName = "http://localhost/" . $localHostSiteFolderName;
    $dbName = $localhostDatabaseName;
    $dbHost = $localHostDatabaseHostAddress;
    $dbUsername = $localHostDatabaseUserName;
    $dbPassword = $localHostDatabasePassword;
} else {  // using remote host
    $siteName = $serverWebsiteName;
    $dbName = $serverDatabaseName;
    $dbHost = $serverDatabaseHostAddress;
    $dbUsername = $serverDatabaseUserName;
    $dbPassword = $serverDatabasePassword;
}

$stripePK = "used to be my pk";
$stripeSK = "used to be my stripe secret key";
$management_api_token = "used to be my token";
$auth0_domain = "used to be my domaiun";
$auth0_client_id = "used to be my id";
$auth0_client_secret = "used to be my secret";
$sessionSecret = "used to be my secret";
