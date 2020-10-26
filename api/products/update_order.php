<?php

require_once "../../php/configuration.php";

require '../../vendor/autoload.php';


\Firebase\JWT\JWT::$leeway = 60;
$snowflake = new \Godruoyi\Snowflake\Snowflake;
$snowflake->setStartTimeStamp(strtotime('2019-11-11') * 1000);

session_start();
$userInfo = array();
if (isset($_SESSION['sesToken'])) {
    $token = \Firebase\JWT\JWT::decode($_SESSION['sesToken'], $sessionSecret, array('HS256'));
    $userInfo = ['sub' => $token->userId];
}

$response = new stdClass();

if ($userInfo) {
    if (isset($_GET['product']) && isset($_GET['quantity'])) {
        $product_id = filter_input(INPUT_GET, "product", FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_input(INPUT_GET, "quantity", FILTER_SANITIZE_NUMBER_INT);
        /* Connect to the database */
        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        /* Perform Query */
        $query = "SELECT name, stock FROM products WHERE product_id = :product_id";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
        $statement->execute();

        if ($statement->rowCount() > 0) {
            $result = $statement->fetch(PDO::FETCH_OBJ);
            $product_name = $result->name;
            $stock = $result->stock;
            if ($quantity >= 0 && (($result->stock == -1 && $quantity < 999) || $quantity <= $stock)) {
                $user_id = $userInfo['sub'];


                /* Perform Query */
                $query = "SELECT order_id FROM orders WHERE user_id = :user_id AND date_ordered IS NULL";
                $statement = $dbConnection->prepare($query);
                $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    $result = $statement->fetch(PDO::FETCH_OBJ);

                    $query = "SELECT order_id, quantity FROM order_lines WHERE order_id = :order_id AND product_id = :product_id";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
                    $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                    $statement->execute();

                    if ($statement->rowCount() > 0) {
                        /* Item already in basket */
                        $result = $statement->fetch(PDO::FETCH_OBJ);

                        if ($quantity < 1) {
                            //item being removed since quantity 0 or less
                            $query = "DELETE FROM order_lines WHERE order_id = :order_id AND product_id = :product_id";
                            $statement = $dbConnection->prepare($query);
                            $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
                            $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                            $statement->execute();

                            if ($statement->rowCount() > 0) {
                                $data = new stdClass();
                                $data->product_id = $product_id;
                                $data->name = $product_name;

                                $response->apiVersion = "1.0";
                                $response->data = $data;
                            } else {
                                //Couldn't remove item
                                $error = new stdClass();
                                $error->code = 500;
                                $error->msg = "Item failed to remove";

                                $response->apiVersion = "1.0";
                                $response->error = $error;

                                http_response_code(500);
                            }
                        } else if ($quantity > $stock && $stock != -1) {
                            //Quantity too large
                            $error = new stdClass();
                            $error->code = 400;
                            $error->msg = $product_name . " specified quantity is greater than current available stock.";

                            $response->apiVersion = "1.0";
                            $response->error = $error;

                            http_response_code(400);
                        } else {
                            //quantity ok
                            $query = "UPDATE order_lines SET quantity = :quantity WHERE order_id = :order_id AND product_id = :product_id";
                            $statement = $dbConnection->prepare($query);
                            $statement->bindParam(":quantity", $quantity, PDO::PARAM_INT);
                            $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
                            $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                            $statement->execute();

                            if ($statement->rowCount() > 0) {
                                //successfully updated
                                $data = new stdClass();
                                $data->product_id = $product_id;
                                $data->quantity = $quantity;
                                $data->name = $product_name;

                                $response->apiVersion = "1.0";
                                $response->data = $data;
                            } else {
                                //Couldn't modify quantity
                                $error = new stdClass();
                                $error->code = 500;
                                $error->msg = "Couldn't modify quantity for product " . $product_name;

                                $response->apiVersion = "1.0";
                                $response->error = $error;

                                http_response_code(500);
                            }
                        }
                    } else {
                        /* New item in basket */
                        $query = "INSERT INTO order_lines(order_id,product_id,quantity) VALUES (:order_id, :product_id, :quantity);";
                        $statement = $dbConnection->prepare($query);
                        $statement->bindParam(":order_id", $result->order_id, PDO::PARAM_INT);
                        $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                        $statement->bindParam(":quantity", $quantity, PDO::PARAM_INT);
                        $statement->execute();

                        if ($statement->rowCount() > 0) {
                            $data = new stdClass();
                            $data->product_id = $product_id;
                            $data->quantity = $quantity;
                            $data->name = $product_name;

                            $response->apiVersion = "1.0";
                            $response->data = $data;
                        } else {
                            //Couldnt add product to basket
                            $error = new stdClass();
                            $error->code = 500;
                            $error->msg = "Unable to add the product to basket";

                            $response->apiVersion = "1.0";
                            $response->error = $error;

                            http_response_code(500);
                        }
                    }
                } else {
                    // No basket found
                    $order_id = $snowflake->id();
                    $query = "INSERT INTO orders(order_id, user_id) VALUES (:order_id, :user_id);";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                    $statement->bindParam(":user_id", $user_id, PDO::PARAM_STR);
                    $statement->execute();

                    /* Perform Query */
                    $query = "INSERT INTO order_lines(order_id,product_id,quantity) VALUES (:order_id, :product_id, :quantity);";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":order_id", $order_id, PDO::PARAM_INT);
                    $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
                    $statement->bindParam(":quantity", $quantity, PDO::PARAM_INT);
                    $statement->execute();

                    if ($statement->rowCount() > 0) {
                        $data = new stdClass();
                        $data->status = "OK";
                        $data->product_id = $product_id;
                        $data->quantity = $quantity;

                        $response->apiVersion = "1.0";
                        $response->data = $data;
                    } else {
                        //Couldnt add product to basket
                        $error = new stdClass();
                        $error->code = 500;
                        $error->msg = "Unable to add the product to basket";

                        $response->apiVersion = "1.0";
                        $response->error = $error;

                        http_response_code(500);
                    }
                }
            } else {
                // Invalid quantity e.g a float not an int?
                $error = new stdClass();
                $error->code = 400;
                $error->msg = "Invalid product quantity specified for " . $product_name;

                $response->apiVersion = "1.0";
                $response->error = $error;

                http_response_code(400);
            }
        } else {
            
        }
    } else {
        // Product id not in url
        $error = new stdClass();
        $error->code = 400;
        $error->msg = "Malformed URL, please check url parameters and try again.";

        $response->apiVersion = "1.0";
        $response->error = $error;

        http_response_code(400);
    }
} else { // Not logged in
    $error = new stdClass();
    $error->code = 403;
    $error->msg = "Please log in.";

    $response->apiVersion = "1.0";
    $response->error = $error;

    http_response_code(403);
}
echo json_encode($response);
?>