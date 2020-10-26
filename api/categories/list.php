<?php

require_once "../../php/configuration.php";


$query = "SELECT category_id, name FROM categories;";

/* Connect to the database */
$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);   // set the PDO error mode to exception

/* Perform Query */

$statement = $dbConnection->prepare($query);
$statement->execute();

/* echo "<br>---------DEBUG--------<br>";
  echo $statement->rowCount();
  echo "<br>-------END DEBUG------<br><br><br>"; */
$response = new stdClass();
$response->apiVersion = "1.0";
if ($statement->rowCount() > 0) {
    $results = $statement->fetchAll(PDO::FETCH_OBJ);
    foreach ($results as $row) {
        $response->data->categories[] = $row;
    }
//    $response->data->categories = $results;

    //get the count of products for each category, including categories which have 0 products
    $query = "SELECT COUNT(p.product_id) AS product_count FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id;";
    $statement = $dbConnection->prepare($query);
    $statement->execute();

    if ($statement->rowCount() > 0) {
        $results = $statement->fetchAll(PDO::FETCH_OBJ);
        $response->data->categories_numbers = $results;
    } else {
        $error = new stdClass();
        $error->code = 404;
        $error->msg = "Something went wrong? Oops.";
        $response->error = $error;
        http_response_code(404);
    }
} else {
    // Categories not found
    $error = new stdClass();
    $error->code = 404;
    $error->msg = "No categories found.";

    $response->error = $error;

    http_response_code(404);
}

echo json_encode($response);
?>