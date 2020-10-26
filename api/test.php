<?php

require '../vendor/autoload.php';

$session = new stdClass();
$session->client_reference_id = "8205402466418688";

handle_checkout_session($session);

function handle_checkout_session($session) {

    require_once "../php/configuration.php";

    $order_id = intval($session->client_reference_id);

    /* Connect to the database */
    $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

    /* Perform Query */
    $query = "SELECT order_id FROM orders WHERE order_id = :order_id";
    $statement = $dbConnection->prepare($query);
    $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
    $statement->execute();

    if($statement->rowCount() > 0) {
        $query = "SELECT product_id, quantity FROM order_lines WHERE order_id = :order_id";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $statement->execute();
        if($statement->rowCount() > 0) {
            $results = $statement->fetchAll(PDO::FETCH_OBJ);
            foreach ($results as $result) {
                $quantity = $result->quantity;
                $query = "SELECT product_id, stock FROM products WHERE product_id = :product_id";
                $statement = $dbConnection->prepare($query);

                $product_id = $result->product_id;

                $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                $statement->execute();

                if($statement->rowCount() > 0) {
                    $result = $statement->fetch(PDO::FETCH_OBJ);
                    $newstock = $result->stock;
                    if($result->stock != -1) {
                        $newstock = $newstock - $quantity;
                    }
                    $query = "UPDATE products SET stock = :stock WHERE product_id = :product_id";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":product_id", $result->product_id, PDO::PARAM_INT);
                    $statement->bindParam(":stock", $newstock, PDO::PARAM_INT);
                    $statement->execute();
                } else {
                    //Invalid item
                    http_response_code(404);
                    exit();
                }
            }
            $query = "UPDATE orders SET date_ordered = CURRENT_TIMESTAMP WHERE order_id = :order_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
            $statement->execute();

            $query = "SELECT user_id FROM orders WHERE order_id = :order_id";
            $statement = $dbConnection->prepare($query);
            $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
            $statement->execute();
            if($statement->rowCount() > 0) {
                $result = $statement->fetch(PDO::FETCH_OBJ);
                $user_id = $result->user_id;

                $snowflake = new \Godruoyi\Snowflake\Snowflake;
                $snowflake->setStartTimeStamp(strtotime('2019-11-11')*1000);

                $order_id = $snowflake->id();

                $query = "INSERT INTO orders(order_id, user_id) VALUES (:order_id, :user_id)";
                $statement = $dbConnection->prepare($query);
                $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
                $statement->execute();

                http_response_code(201);
                exit();
            } else {
                //Invalid order id?
                http_response_code(404);
                exit();
            }
        } else {
            //No items in order
            http_response_code(400);
            exit();
        }
    } else {
        //Invalid order
        http_response_code(404);
        exit();
    }
    http_response_code(200);
    exit();
}

?>