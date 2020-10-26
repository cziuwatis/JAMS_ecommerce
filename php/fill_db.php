<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Fill Database</title>
    </head>
    <body>

        <?php
        /* Include "configuration.php" file */
        require_once "configuration.php";


        /* Connect to the database */
        $dbConnection = new PDO("mysql:host=$dbHost", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception



        /* Create the database */
        $query = "CREATE DATABASE IF NOT EXISTS $dbName;";
        $statement = $dbConnection->prepare($query);
        $statement->execute();

        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        /* Fill tables */

        $query = "LOAD DATA INFILE 'categories.csv'
                    INTO TABLE categories
                    FIELDS TERMINATED BY ','
                    LINES TERMINATED BY '\n'
                    IGNORE 1 LINES;"
                . "LOAD DATA INFILE 'products.csv'
                    INTO TABLE products
                    FIELDS TERMINATED BY ','
                    LINES TERMINATED BY '\n'
                    IGNORE 4 LINES;";

        $statement = $dbConnection->prepare($query);
        $statement->execute();
        
        echo json_encode($statement->fetchAll(PDO::FETCH_OBJ));

        /* Provide feedback to the user */
        echo "<br>Database '$dbName' created.";
        ?>
    </body>
</html>