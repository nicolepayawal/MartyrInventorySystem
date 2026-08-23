<?php

include "martyfragrance_db.php";

// Get total number of products
$product_query = "SELECT COUNT(*) AS total_products FROM products";
$product_result = $conn->query($product_query);
$product_data = $product_result->fetch_assoc();

$total_products = $product_data["total_products"];


// Get total stock
$stock_query = "SELECT SUM(current_stock) AS total_stock FROM products";
$stock_result = $conn->query($stock_query);
$stock_data = $stock_result->fetch_assoc();

$total_stock = $stock_data["total_stock"];


// Get low-stock products
$low_stock_query = "
    SELECT COUNT(*) AS low_stock
    FROM products
    WHERE current_stock <= reorder_level
";

$low_stock_result = $conn->query($low_stock_query);
$low_stock_data = $low_stock_result->fetch_assoc();

$low_stock = $low_stock_data["low_stock"];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Martyr Fragrances - Dashboard</title>

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

    <a href="stock_adjustment.php">Stock Adjustment</a>

    <a href="inventory_history.php">Inventory History</a>

</nav>



    <main>

        <h2>Dashboard</h2>


        <div class="dashboard-cards">


            <div class="card">

                <h3>Total Products</h3>

                <p>
                    <?php echo $total_products; ?>
                </p>

            </div>


            <div class="card">

                <h3>Total Stock</h3>

                <p>
                    <?php echo $total_stock; ?>
                </p>

            </div>


            <div class="card">

                <h3>Low Stock</h3>

                <p>
                    <?php echo $low_stock; ?>
                </p>

            </div>


        </div>


        <section>

            <h2>Inventory Overview</h2>

            <p>
                Welcome to the Martyr Fragrances Inventory Management System.
            </p>

            <p>
                Use the navigation menu to manage products, sales, inventory,
                and reports.
            </p>

        </section>

    </main>

</body>

</html>