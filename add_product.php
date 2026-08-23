<?php

include "martyfragrance_db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = $_POST["product_name"];
    $unit_price = $_POST["unit_price"];
    $expiration_date = $_POST["expiration_date"];
    $current_stock = $_POST["current_stock"];
    $reorder_level = $_POST["reorder_level"];
    $status = $_POST["status"];

    $sql = "INSERT INTO products
            (product_name, unit_price, expiration_date, current_stock, reorder_level, status)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sdsiss",
        $product_name,
        $unit_price,
        $expiration_date,
        $current_stock,
        $reorder_level,
        $status
    );

    if ($stmt->execute()) {

        $message = "Product successfully added!";

    } else {

        $message = "Error: " . $stmt->error;

    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Product - Martyr Fragrances</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>Martyr Fragrances</h1>

    <p>Inventory Management System</p>

</header>

<nav>

    <a href="index.php">Dashboard</a>

    <a href="products.php">Products</a>

    <a href="add_product.php">Add Product</a>

</nav>

<main>

    <h2>Add New Product</h2>

    <?php

    if ($message != "") {

        echo "<p>" . $message . "</p>";

    }

    ?>

    <form method="POST">

        <label>Product Name</label><br>

        <input type="text" name="product_name" required>

        <br><br>


        <label>Unit Price</label><br>

        <input type="number" name="unit_price" step="0.01" required>

        <br><br>


        <label>Expiration Date</label><br>

        <input type="date" name="expiration_date">

        <br><br>


        <label>Current Stock</label><br>

        <input type="number" name="current_stock" min="0" required>

        <br><br>


        <label>Reorder Level</label><br>

        <input type="number" name="reorder_level" min="0" required>

        <br><br>


        <label>Status</label><br>

        <select name="status">

            <option value="Active">Active</option>

            <option value="Inactive">Inactive</option>

        </select>

        <br><br>


        <button type="submit">Add Product</button>

    </form>

</main>

</body>

</html>