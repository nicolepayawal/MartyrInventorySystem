<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "martyfragrance_db.php";

$sql = "SELECT * FROM products ORDER BY product_id ASC";
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

    <title>Martyr Fragrances - Products</title>
    <a href="index.php">
    <button type="button">← Back to Dashboard</button>
</a>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #222;
            color: white;
        }

        tr:hover {
            background-color: #f2f2f2;
        }

    </style>

</head>

<body>

    <h1>Martyr Fragrances</h1>

    <p class="subtitle">
        Product Inventory
    </p>

    <table>

        <thead>

            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Expiration Date</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

            <?php

            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>";

                    echo "<td>" . $row["product_id"] . "</td>";

                    echo "<td>" . $row["product_name"] . "</td>";

                    echo "<td>₱" . number_format($row["unit_price"], 2) . "</td>";

                    echo "<td>" . $row["expiration_date"] . "</td>";

                    $stock = $row["current_stock"];
                    $reorder = $row["reorder_level"];

                    if ($stock == 0) {
                        $stock_display = "❌ Out of Stock";
                    } elseif ($stock <= $reorder) {
                        $stock_display = "⚠️ Low Stock (" . $stock . ")";
                    } else {
                        $stock_display = "✅ " . $stock;
                    }

                    echo "<td>" . $stock_display . "</td>";

                    echo "<td>" . $row["reorder_level"] . "</td>";

                    echo "<td>" . $row["status"] . "</td>";

                    echo "<td>";

                    echo "<a href='edit_product.php?id=" . $row["product_id"] . "'>Edit</a> ";

                    echo "<a href='delete_product.php?id=" . $row["product_id"] . "'
                        onclick=\"return confirm('Are you sure you want to deactivate this product?');\">
                        Delete
                        </a>";

                    echo "</td>"; 

                    echo "</tr>";
                }

            } else {

                echo "<tr>";

                echo "<td colspan='7'>No products found.</td>";

                echo "</tr>";
            }

            ?>

        </tbody>

    </table>

</body>

</html>