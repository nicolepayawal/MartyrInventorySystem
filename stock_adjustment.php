<?php

include "martyfragrance_db.php";


// Process the stock adjustment form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_id = $_POST["product_id"];
    $transaction_type = $_POST["transaction_type"];
    $quantity = $_POST["quantity"];
    $remarks = $_POST["remarks"];


    // Make sure quantity is positive
    if ($quantity <= 0) {

        die("Quantity must be greater than 0.");

    }


    // Get the current stock
    $stock_sql = "SELECT current_stock
                  FROM products
                  WHERE product_id = ?";

    $stock_stmt = $conn->prepare($stock_sql);

    $stock_stmt->bind_param("i", $product_id);

    $stock_stmt->execute();

    $stock_result = $stock_stmt->get_result();


    if ($stock_result->num_rows == 0) {

        die("Product not found.");

    }


    $product = $stock_result->fetch_assoc();

    $stock_before = $product["current_stock"];


    // Calculate the new stock
    if ($transaction_type == "IN") {

        $stock_after = $stock_before + $quantity;

    } elseif ($transaction_type == "OUT") {

        // Prevent negative inventory
        if ($quantity > $stock_before) {

            die("Not enough stock available.");

        }

        $stock_after = $stock_before - $quantity;

    } else {

        die("Invalid transaction type.");

    }


    // Start database transaction
    $conn->begin_transaction();

    try {

        // Update the product stock
        $update_sql = "UPDATE products
                       SET current_stock = ?
                       WHERE product_id = ?";

        $update_stmt = $conn->prepare($update_sql);

        $update_stmt->bind_param(
            "ii",
            $stock_after,
            $product_id
        );

        if (!$update_stmt->execute()) {

            throw new Exception("Failed to update product stock.");

        }


        // Record the inventory transaction
        $transaction_sql = "INSERT INTO inventory_transactions
                            (
                                product_id,
                                transaction_type,
                                quantity,
                                transaction_date,
                                stock_before,
                                stock_after,
                                remarks
                            )
                            VALUES (?, ?, ?, NOW(), ?, ?, ?)";

        $transaction_stmt = $conn->prepare($transaction_sql);

        $transaction_stmt->bind_param(
            "isiiss",
            $product_id,
            $transaction_type,
            $quantity,
            $stock_before,
            $stock_after,
            $remarks
        );

        if (!$transaction_stmt->execute()) {

            throw new Exception("Failed to record inventory transaction.");

        }


        // Save both changes
        $conn->commit();

        header("Location: stock_adjustment.php?success=1");

        exit();


    } catch (Exception $e) {

        // Undo everything if something fails
        $conn->rollback();

        die("Transaction failed: " . $e->getMessage());

    }

}


// Get active products for the dropdown
$sql = "SELECT product_id, product_name, current_stock
        FROM products
        WHERE status = 'Active'
        ORDER BY product_name ASC";

$result = $conn->query($sql);

if (!$result) {

    die("SQL ERROR: " . $conn->error);

}

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Stock Adjustment - Martyr Fragrances</title>

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

    <h2>Stock Adjustment</h2>

    <p>
        Use this page to record incoming or outgoing inventory.
    </p>

    <?php

    if (isset($_GET["success"])) {

        echo "<p>Stock adjustment saved successfully!</p>";

    }

    ?>

    <form method="POST">

        <label for="product_id">
            Product
        </label>

        <br>

        <select name="product_id" id="product_id" required>

            <option value="">
                -- Select Product --
            </option>

            <?php

            while ($row = $result->fetch_assoc()) {

                echo "<option value='" . $row["product_id"] . "'>";

                echo htmlspecialchars($row["product_name"]);

                echo " (Current Stock: " . $row["current_stock"] . ")";

                echo "</option>";

            }

            ?>

        </select>

        <br><br>


        <label for="transaction_type">
            Transaction Type
        </label>

        <br>

        <select
            name="transaction_type"
            id="transaction_type"
            required
        >

            <option value="">
                -- Select Type --
            </option>

            <option value="IN">
                Stock IN
            </option>

            <option value="OUT">
                Stock OUT
            </option>

        </select>

        <br><br>


        <label for="quantity">
            Quantity
        </label>

        <br>

        <input
            type="number"
            name="quantity"
            id="quantity"
            min="1"
            required
        >

        <br><br>


        <label for="remarks">
            Reason / Remarks
        </label>

        <br>

        <textarea
            name="remarks"
            id="remarks"
            rows="4"
            cols="40"
            placeholder="Example: New stock received"
            required
        ></textarea>

        <br><br>


        <button type="submit">
            Save Adjustment
        </button>

    </form>

</main>

</body>

