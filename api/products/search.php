<?php
    require_once "../../php/configuration.php";

    $response = new stdClass();

    if (isset($_GET['name']) || isset($_GET['minprice']) || isset($_GET['maxprice']) || isset($_GET['category'])) {

        $query = "SELECT product_id, name, description, image_url, unit_price, category_id FROM products WHERE 1=1 ";

        $name = ltrim(rtrim(filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING)));
        if($name != null and $name != False) {
            $query .= "AND name LIKE :name ";
        }

        $minprice = filter_input(INPUT_GET, "minprice", FILTER_SANITIZE_NUMBER_FLOAT);
        if($minprice != null and $minprice != False) {
            $query .= "AND unit_price > :minprice ";
        }

        $maxprice = filter_input(INPUT_GET, "maxprice", FILTER_SANITIZE_NUMBER_FLOAT);
        if($maxprice != null and $maxprice != False) {
            $query .= "AND unit_price < :maxprice ";
        }
        
        $category = filter_input(INPUT_GET, "category", FILTER_SANITIZE_NUMBER_INT);
        if($category != null and $category != False) {
            $query .= "AND category_id = :category_id ";
        }

        /* Connect to the database */
        $dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

        /* Perform Query */
        
        $statement = $dbConnection->prepare($query);
        if($name != null and $name != False) {
            $name = '%'.$name.'%';
            $statement->bindParam(":name", $name, PDO::PARAM_STR);
        }
        if($minprice != null and $minprice != False) {
            $statement->bindParam(":minprice", strval($minprice), PDO::PARAM_STR);
        }
        if($maxprice != null and $maxprice != False) {
            $statement->bindParam(":maxprice", strval($maxprice), PDO::PARAM_STR);
        }
        $statement->execute();

        /* echo "<br>---------DEBUG--------<br>";
        echo $statement->rowCount();
        echo "<br>-------END DEBUG------<br><br><br>"; */

        if ($statement->rowCount() > 0) {
            $results = $statement->fetchAll(PDO::FETCH_OBJ);

            $response->apiVersion = "1.0";
            $response->data = $results;
        } else {
            // Product not found
            $error = new stdClass();
            $error->code = 404;
            $error->msg = "No products found, check search terms and try again.";

            $response->apiVersion = "1.0";
            $response->error = $error;

            http_response_code(404);
        }

        
    } else {
        // Not enough data for search
        $error = new stdClass();
        $error->code = 400;
        $error->msg = "Malformed URL, please check url parameters and try again.";

        $response->apiVersion = "1.0";
        $response->error = $error;

        http_response_code(400);
    }

    echo json_encode($response);
?>