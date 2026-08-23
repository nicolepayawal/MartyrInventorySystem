<?php

include "martyfragrance_db.php";


// Check if product ID exists
if (!isset($_GET["id"])) {

    die("Product ID is missing.");

}

$product_id = $_GET["id"];


// Change product status to Inactive
$sql = "UPDATE products
        SET status = 'Inactive'
        WHERE product_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $product_id);


if ($stmt->execute()) {

    header("Location: products.php");

    exit();

} else {

    echo "Error: " . $stmt->error;

}

?>