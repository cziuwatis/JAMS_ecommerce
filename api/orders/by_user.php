<?php
    require_once "../../php/configuration.php";

    $response = new stdClass();

    if (isset($_GET['user'])) {
        $user_id = filter_input(INPUT_GET, "user", FILTER_SANITIZE_NUMBER_INT);
        
        /* Connect to the database */
        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        /* Perform Query */
        $query = "SELECT order_id, user_id, date_created, date_ordered FROM orders WHERE user_id = :user_id AND date_ordered != NULL";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $statement->execute();

        /* echo "<br>---------DEBUG--------<br>";
        echo $statement->rowCount();
        echo "<br>-------END DEBUG------<br><br><br>"; */

        if ($statement->rowCount() > 0) {
            $result = $statement->fetchAll(PDO::FETCH_OBJ);

            $response->apiVersion = "1.0";
            $response->data = $result;
        } else {
            // No orders found
            $error = new stdClass();
            $error->code = 404;
            $error->msg = "No orders found.";

            $response->apiVersion = "1.0";
            $response->error = $error;

            http_response_code(404);
        }

        
    } else {
        // user id not in url
        $error = new stdClass();
        $error->code = 400;
        $error->msg = "Malformed URL, please check url parameters and try again.";

        $response->apiVersion = "1.0";
        $response->error = $error;

        http_response_code(400);
    }

    echo json_encode($response);
?>