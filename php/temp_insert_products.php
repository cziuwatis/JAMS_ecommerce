<?php

/* Include "configuration.php" file */
require_once "configuration.php";

$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

/* Create table */
$query = "INSERT INTO products VALUES "
        . "(18, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(19, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(20, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(21, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(22, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(23, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(24, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(25, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(26, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(27, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(11, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(277, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(51351, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(151, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(111, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(10, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(12, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(13, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(14, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(15, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(16, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10),"
        . "(17, \"KNOCKBACK DIAMOND SWORD\", \"A super awesome sword crafted from the best of the best sourcerer's stones called a diamond. Yes, a diamond is a sourcerer stone. Just for now. Yes, awesome description. Buy it now for 99.59$!!\", 99.59,\"img/product/p3.jpg\", 10)";
$statement = $dbConnection->prepare($query);
$statement->execute();

