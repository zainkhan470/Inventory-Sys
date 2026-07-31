<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$message = "";

// Fetch companies
$companies = $conn->query("SELECT * FROM companies ORDER BY name ASC");

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// Handle Add Item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $company  = $_POST['company'];
    $category = $_POST['category'];
    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $quantity = $_POST['quantity'];
    $expiry   = $_POST['expiry_date'];

    // Check if item already exists (company + category + name)
    $check = $conn->query("
        SELECT * FROM items 
        WHERE company='$company' 
        AND category='$category' 
        AND name='$name'
    ")->fetch_assoc();

    if ($check) {
        // Update quantity
        $newQty = $check['quantity'] + $quantity;

        $conn->query("
            UPDATE items 
            SET quantity='$newQty',
                price='$price',
                expiry_date='$expiry'
            WHERE id={$check['id']}
        ");

        $message = "Item already exists. Quantity updated!";
    } else {
        // Insert new item
        $conn->query("
            INSERT INTO items (company, category, name, price, quantity, expiry_date)
            VALUES ('$company', '$category', '$name', '$price', '$quantity', '$expiry')
        ");

        $message = "Item added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Item</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #c5d1e7ff;
        }

        .main {
            margin-left: 240px;
            padding: 40px;
            margin-top: 50px;
            flex: 1;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: auto;
        }

        .card h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        input,
        select,
        button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <?php include '../../includes/sidebar.php'; ?>

    <div class="main">
        <div class="card">

            <h2>Add New Item</h2>

            <?php if ($message): ?>
                <p class="message"><?= $message ?></p>
            <?php endif; ?>

            <form method="post">

                <!-- COMPANY DROPDOWN -->
                <select name="company" required>
                    <option value="">Select Company</option>
                    <?php while ($comp = $companies->fetch_assoc()): ?>
                        <option value="<?= $comp['name'] ?>">
                            <?= $comp['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <!-- CATEGORY DROPDOWN -->
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?= $cat['name'] ?>">
                            <?= $cat['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <input type="text" name="name" placeholder="Item Name" required>
                <input type="number" name="price" step="0.01" placeholder="Price" required>
                <input type="number" name="quantity" placeholder="Quantity" required>
                <input type="date" name="expiry_date" required>

                <button type="submit">Add Item</button>
            </form>

        </div>
    </div>

</body>

</html>
