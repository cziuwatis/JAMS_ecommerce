<?php

require '../../vendor/autoload.php';

// /* Connect to the database */
// $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
// $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

// /* Perform Query */
// $query = "INSERT INTO messages VALUES (:message)";
// $statement = $dbConnection->prepare($query);
// $message = "Start";
// $statement->bindParam(":message", $message, PDO::PARAM_STR);
// $statement->execute();



// Set your secret key: remember to change this to your live secret key in production
// See your keys here: https://dashboard.stripe.com/account/apikeys
\Stripe\Stripe::setApiKey($stripeSK);

// You can find your endpoint's secret in your webhook settings
$endpoint_secret = 'whsec_p2xb6rs94M0fkVwtWbWoOfzF9M4sfc19';

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$event = null;

try {
    
  $event = \Stripe\Webhook::constructEvent(
    $payload, $sig_header, $endpoint_secret
  );
} catch(\UnexpectedValueException $e) {
  // Invalid payload
  http_response_code(400);
  echo "Invalid payload.";
  exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
  // Invalid signature
  http_response_code(400);
  echo "Invalid signature.";
  exit();
}



// Handle the checkout.session.completed event
if ($event->type == 'checkout.session.completed') {
  $session = $event->data->object;

  // Fulfill the purchase...
  handle_checkout_session($session);
}

function handle_checkout_session($session) {

    require_once "../../php/configuration.php";

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
                        if($newstock < 0) {
                            $newstock = 0;
                        }
                    }
                    $query = "UPDATE products SET stock = :stock WHERE product_id = :product_id";
                    $statement = $dbConnection->prepare($query);
                    $statement->bindParam(":product_id", $result->product_id, PDO::PARAM_INT);
                    $statement->bindParam(":stock", $newstock, PDO::PARAM_INT);
                    $statement->execute();
                } else {
                    //Invalid item
                    http_response_code(404);
                    echo "Invalid item";
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
                echo "Invalid order id";
                exit();
            }
        } else {
            //No items in order
            http_response_code(400);
            echo "No items in order";
            exit();
        }
    } else {
        //Invalid order
        http_response_code(404);
        echo "Invalid order";
        exit();
    }
    http_response_code(200);
    exit();
}


http_response_code(500);

?>