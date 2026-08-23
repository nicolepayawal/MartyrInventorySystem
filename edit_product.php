<?php

include "martyfragrance_db.php";


// Check if product ID was provided
if (!isset($_GET["id"])) {

    die("Product ID is missing.");

}

$product_id = $_GET["id"];


// Get the existing product
$sql = "SELECT * FROM products WHERE product_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $product_id);

$stmt->execute();

$result = $stmt->get_result();


// Check if product exists
if ($result->num_rows == 0) {

    die("Product not found.");

}

$product = $result->fetch_assoc();


// If form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = $_POST["product_name"];
    $unit_price = $_POST["unit_price"];
    $expiration_date = $_POST["expiration_date"];
    $current_stock = $_POST["current_stock"];
    $reorder_level = $_POST["reorder_level"];
    $status = $_POST["status"];

    $old_stock = $product["current_stock"];

    // Update product
    $update_sql = "UPDATE products
                   SET product_name = ?,
                       unit_price = ?,
                       expiration_date = ?,
                       current_stock = ?,
                       reorder_level = ?,
                       status = ?
                   WHERE product_id = ?";


    $update_stmt = $conn->prepare($update_sql);

    $update_stmt->bind_param(
        "sdsissi",
        $product_name,
        $unit_price,
        $expiration_date,
        $current_stock,
        $reorder_level,
        $status,
        $product_id
    );

if ($update_stmt->execute()) {

    // Check if the stock actually changed
    if ($current_stock != $old_stock) {

        // Determine transaction type
        if ($current_stock > $old_stock) {
            $transaction_type = "IN";
            $quantity = $current_stock - $old_stock;
        } else {
            $transaction_type = "OUT";
            $quantity = $old_stock - $current_stock;
        }

        // Record the inventory transaction
        $transaction_sql = "INSERT INTO inventory_transactions
                            (product_id, transaction_type, quantity,
                             transaction_date, stock_before, stock_after, remarks)
                            VALUES (?, ?, ?, NOW(), ?, ?, ?)";

        $transaction_stmt = $conn->prepare($transaction_sql);

        $remarks = "Stock changed through product edit.";

        $transaction_stmt->bind_param(
            "isiiss",
            $product_id,
            $transaction_type,
            $quantity,
            $old_stock,
            $current_stock,
            $remarks
        );

        if (!$transaction_stmt->execute()) {

            echo "Product updated, but inventory transaction failed: "
                 . $transaction_stmt->error;

            exit();

        }

    }

    header("Location: products.php");

    exit();

} else {

    echo "Error updating product: " . $update_stmt->error;

}

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Product</title>

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

    <h2>Edit Product</h2>


    <form method="POST">


        <label>Product Name</label>

        <br>

        <input
            type="text"
            name="product_name"
            value="<?php echo htmlspecialchars($product["product_name"]); ?>"
            required
        >

        <br><br>


        <label>Unit Price</label>

        <br>

        <input
            type="number"
            name="unit_price"
            step="0.01"
            value="<?php echo $product["unit_price"]; ?>"
            required
        >

        <br><br>


        <label>Expiration Date</label>

        <br>

        <input
            type="date"
            name="expiration_date"
            value="<?php echo $product["expiration_date"]; ?>"
        >

        <br><br>


        <label>Current Stock</label>

        <br>

        <input
            type="number"
            name="current_stock"
            min="0"
            value="<?php echo $product["current_stock"]; ?>"
            required
        >

        <br><br>


        <label>Reorder Level</label>

        <br>

        <input
            type="number"
            name="reorder_level"
            min="0"
            value="<?php echo $product["reorder_level"]; ?>"
            required
        >

        <br><br>


        <label>Status</label>

        <br>

        <select name="status">

            <option value="Active"
                <?php
                if ($product["status"] == "Active") {
                    echo "selected";
                }
                ?>>
                Active
            </option>

            <option value="Inactive"
                <?php
                if ($product["status"] == "Inactive") {
                    echo "selected";
                }
                ?>>
                Inactive
            </option>

        </select>

        <br><br>


        <button type="submit">
            Save Changes
        </button>


    </form>

</main>


</body>

</html>