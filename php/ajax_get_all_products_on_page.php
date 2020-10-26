<?php

$response = new stdClass();
$default_page = 0;
$default_pageLimit = 12; //items per page

$query = "SELECT product_id, name, unit_price, image_url, stock FROM products WHERE 1=1 ";
$where_statements = "";

$page = filter_input(INPUT_GET, "pagenumber", FILTER_SANITIZE_NUMBER_INT);
if ($page < 0) {
    $page = $default_page;
}
if (empty($page)) {
    $page = $default_page;
}
$pageLimit = filter_input(INPUT_GET, "pagelimit", FILTER_SANITIZE_NUMBER_INT);
if ($pageLimit != $default_pageLimit && $pageLimit != 27 && $pageLimit != 57) {
    $pageLimit = $default_pageLimit;
}
if (empty($pageLimit)) {
    $pageLimit = $default_pageLimit;
}
$name = trim(filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING));
if ($name != null and $name != False) {
    $where_statements .= "AND name LIKE :name ";
}
//Filter float requires the flag to allow fraction so the sanitizer doesn't remove the . in numbers
$minprice = filter_input(INPUT_GET, "minprice", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if ($minprice != null and $minprice != False) {
    $where_statements .= "AND unit_price > :minprice ";
}
$maxprice = filter_input(INPUT_GET, "maxprice", FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
if ($maxprice != null and $maxprice != False) {
    $where_statements .= "AND unit_price < :maxprice ";
}
$category = filter_input(INPUT_GET, "category_id", FILTER_SANITIZE_NUMBER_INT);
if (!empty($category)) {
    $where_statements .= "AND category_id = :category ";
}
$query .= $where_statements;
$sorting = trim(filter_input(INPUT_GET, "sorting", FILTER_SANITIZE_STRING));
strtolower($sorting);
if (!empty($sorting)) {
    if ($sorting == "priceasc") {
        $query .= "ORDER BY unit_price ASC ";
    } else if ($sorting == "pricedesc") {
        $query .= "ORDER BY unit_price DESC ";
    }
}

//pagestart is exclusive
$pageStart = $page * $pageLimit;
require_once "configuration.php";
$dbConnection = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query .= " LIMIT :pageStart,:pageLimit";
$statement = $dbConnection->prepare($query);
if ($name != null and $name != False) {
    $name = '%' . $name . '%';
    $statement->bindParam(":name", $name, PDO::PARAM_STR);
}
if ($minprice != null and $minprice != False) {
    $statement->bindParam(":minprice", strval($minprice), PDO::PARAM_STR);
}
if ($maxprice != null and $maxprice != False) {
    $statement->bindParam(":maxprice", strval($maxprice), PDO::PARAM_STR);
}
if (!empty($category)) {
    $statement->bindParam(":category", $category, PDO::PARAM_INT);
}

$statement->bindParam(":pageStart", intval($pageStart), PDO::PARAM_INT);
$statement->bindParam(":pageLimit", intval($pageLimit), PDO::PARAM_INT);
$statement->execute();

$response->apiVersion = "1.0";
if ($statement->rowCount() > 0) {
    $result = $statement->fetchAll(PDO::FETCH_OBJ);

    foreach ($result as $row) {
        $response->data->products[] = $row;
    }
    //    $product_response->data->products = $result;

    $query = "SELECT COUNT(*) AS count FROM products WHERE 1=1 " . $where_statements;
    $statement = $dbConnection->prepare($query);
    if ($name != null and $name != False) {
        $name = '%' . $name . '%';
        $statement->bindParam(":name", $name, PDO::PARAM_STR);
    }
    if ($minprice != null and $minprice != False) {
        $statement->bindParam(":minprice", strval($minprice), PDO::PARAM_STR);
    }
    if ($maxprice != null and $maxprice != False) {
        $statement->bindParam(":maxprice", strval($maxprice), PDO::PARAM_STR);
    }
    if (!empty($category)) {
        $statement->bindParam(":category", $category, PDO::PARAM_INT);
    }
    $statement->execute();

    $response->data->prod_count = $statement->fetch(PDO::FETCH_OBJ);
} else {
    $response->data->products[] = $statement->fetch(PDO::FETCH_OBJ);
    $error = new stdClass();
    $error->code = 404;
    $error->msg = "No products found.";
    $response->error = $error;
}
echo json_encode($response);
?>