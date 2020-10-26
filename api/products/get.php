<?php
    require_once "../../php/configuration.php";

    $response = new stdClass();

    if (isset($_GET['product'])) {
        $product_id = filter_input(INPUT_GET, "product", FILTER_SANITIZE_NUMBER_INT);
        
        /* Connect to the database */
        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        /* Perform Query */
        $query = "SELECT p.product_id, p.stock, p.name AS product_title, p.description, p.image_url, p.unit_price, c.name AS category_name FROM products p, categories c WHERE p.category_id = c.category_id AND p.product_id = :product_id";
        $statement = $dbConnection->prepare($query);
        $statement->bindParam(":product_id", $product_id, PDO::PARAM_INT);
        $statement->execute();

        /* echo "<br>---------DEBUG--------<br>";
        echo $statement->rowCount();
        echo "<br>-------END DEBUG------<br><br><br>"; */

        if ($statement->rowCount() == 1) {
            $result = $statement->fetch(PDO::FETCH_OBJ);

            $response->apiVersion = "1.0";
            $response->data = $result;
        } else {
            // Product not found
            $error = new stdClass();
            $error->code = 404;
            $error->msg = "Product not found, check product id and try again.";

            $response->apiVersion = "1.0";
            $response->error = $error;

            http_response_code(404);
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

    echo json_encode($response);
?>