<?php

include "martyfragrance_db.php";


// Get inventory transaction history
$sql = "SELECT
            inventory_transactions.inventory_transaction_id,
            products.product_name,
            inventory_transactions.transaction_type,
            inventory_transactions.quantity,
            inventory_transactions.transaction_date,
            inventory_transactions.stock_before,
            inventory_transactions.stock_after,
            inventory_transactions.remarks

        FROM inventory_transactions

        INNER JOIN products
        ON inventory_transactions.product_id = products.product_id

        ORDER BY inventory_transactions.transaction_date DESC";


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

    <title>Inventory History - Martyr Fragrances</title>

    <link rel="stylesheet" href="style.css">
<style>

    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        background-color: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .history-table th {
        background-color: #222;
        color: white;
        padding: 14px;
        text-align: left;
    }

    .history-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #ddd;
    }

    .history-table tr:nth-child(even) {
        background-color: #f8f8f8;
    }

    .history-table tr:hover {
        background-color: #eeeeee;
    }

    .transaction-in {
        color: #198754;
        font-weight: bold;
    }

    .transaction-out {
        color: #dc3545;
        font-weight: bold;
    }

    .history-title {
        margin-bottom: 5px;
    }

    .history-description {
        color: #666;
        margin-bottom: 20px;
    }

</style>
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

</nav>


<main>

<h2 class="history-title">
    Inventory Transaction History
</h2>

<p class="history-description">
    View all incoming and outgoing inventory movements.
</p>

<table class="history-table">
        <thead>

            <tr>

                <th>ID</th>

                <th>Product</th>

                <th>Type</th>

                <th>Quantity</th>

                <th>Stock Before</th>

                <th>Stock After</th>

                <th>Date</th>

                <th>Remarks</th>

            </tr>

        </thead>


        <tbody>

            <?php

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>";

                    echo "<td>"
                        . $row["inventory_transaction_id"]
                        . "</td>";

$type = $row["transaction_type"];

if ($type == "IN") {

    echo "<td class='transaction-in'>IN</td>";

} else {

    echo "<td class='transaction-out'>OUT</td>";

}
                    echo "<td>"
                        . htmlspecialchars($row["transaction_type"])
                        . "</td>";

                    echo "<td>"
                        . $row["quantity"]
                        . "</td>";

                    echo "<td>"
                        . $row["stock_before"]
                        . "</td>";

                    echo "<td>"
                        . $row["stock_after"]
                        . "</td>";

                    echo "<td>"
                        . $row["transaction_date"]
                        . "</td>";

                    echo "<td>"
                        . htmlspecialchars($row["remarks"])
                        . "</td>";

                    echo "</tr>";

                }

            } else {

                echo "<tr>";

                echo "<td colspan='8'>";

                echo "No inventory transactions found.";

                echo "</td>";

                echo "</tr>";

            }

            ?>

        </tbody>

    </table>


    <br>

    <a href="index.php">
        <button type="button">
            ← Back to Dashboard
        </button>
    </a>

</main>


</body>

</html>
