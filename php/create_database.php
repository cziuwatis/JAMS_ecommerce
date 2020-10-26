<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>PHP Create Database</title>
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

        /* Create table */
        $query = "CREATE TABLE IF NOT EXISTS categories(
                    category_id INT NOT NULL PRIMARY KEY,
                    name VARCHAR(64) NOT NULL);"
                . "CREATE TABLE IF NOT EXISTS products(
                    product_id INT NOT NULL PRIMARY KEY,
                    name VARCHAR(88) NOT NULL,
                    description TEXT NOT NULL,
                    unit_price FLOAT NOT NULL,
                    image_url VARCHAR(255) NULL,
                    stock INT NOT NULL,
                    category_id INT NOT NULL,
                    FOREIGN KEY (category_id) REFERENCES categories(category_id));"
                . "CREATE TABLE IF NOT EXISTS users(
                    user_id VARCHAR(255) NOT NULL PRIMARY KEY,
                    email VARCHAR(255),
                    password VARCHAR(255),
                    mc_username VARCHAR(16) NULL,
                    mc_uuid VARCHAR(255) NULL);"
                . "CREATE TABLE IF NOT EXISTS orders(
                    order_id BIGINT NOT NULL PRIMARY KEY,
                    user_id VARCHAR(255) NOT NULL,
                    date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
                    date_ordered DATETIME NULL,
                    FOREIGN KEY (user_id) REFERENCES users(user_id));"
                . "CREATE TABLE IF NOT EXISTS order_lines(
                    order_id BIGINT NOT NULL,
                    product_id INT NOT NULL,
                    quantity INT NOT NULL,
                    PRIMARY KEY (order_id, product_id),
                    FOREIGN KEY (order_id) REFERENCES orders(order_id),
                    FOREIGN KEY (product_id) REFERENCES products(product_id));";
                /* Alter table statements for existing tables */
                // . "ALTER TABLE products ADD category_id INT NOT NULL;
                //     ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(category_id);";
        $statement = $dbConnection->prepare($query);
        $statement->execute();

        $statement = $dbConnection->prepare("SHOW TABLES;");
        $statement->execute();

        echo json_encode($statement->fetchAll(PDO::FETCH_OBJ));

        /* Provide feedback to the user */
        echo "<br>Database '$dbName' created.";
        ?>
    </body>
</html>