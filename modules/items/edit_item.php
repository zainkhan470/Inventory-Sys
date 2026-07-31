<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Validate ID
if (!isset($_GET['id'])) {
    header("Location: itemslist.php");
    exit;
}

$id = (int)$_GET['id'];

// Fetch item
$item = $conn->query("SELECT * FROM items WHERE id='$id'")->fetch_assoc();
if (!$item) {
    header("Location: itemslist.php");
    exit;
}

$message = "";

// Fetch companies
$companies = $conn->query("SELECT * FROM companies ORDER BY name ASC");

// Fetch categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $company  = $_POST['company'];
    $category = $_POST['category'];
    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $quantity = $_POST['quantity'];
    $expiry   = $_POST['expiry_date'];

    // Check duplicate item (excluding current item)
    $check = $conn->query("
        SELECT * FROM items 
        WHERE company='$company' 
        AND category='$category' 
        AND name='$name' 
        AND id != $id
    ")->fetch_assoc();

    if ($check) {
        $message = "Error: Another item with same company, category and name already exists!";
    } else {

        $conn->query("
            UPDATE items 
            SET company='$company',
                category='$category',
                name='$name',
                price='$price',
                quantity='$quantity',
                expiry_date='$expiry'
            WHERE id='$id'
        ");

        $message = "Item updated successfully!";

        // Refresh item
        $item = $conn->query("SELECT * FROM items WHERE id='$id'")->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Item</title>

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

        input, select, button {
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

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<?php include '../../includes/sidebar.php'; ?>

<div class="main">
    <div class="card">

        <h2>Edit Item</h2>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'Error') !== false ? 'error' : 'message' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <form method="post">

            <!-- COMPANY DROPDOWN -->
            <select name="company" required>
                <option value="">Select Company</option>
                <?php while ($comp = $companies->fetch_assoc()): ?>
                    <option value="<?= $comp['name'] ?>"
                        <?= $item['company'] == $comp['name'] ? 'selected' : '' ?>>
                        <?= $comp['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- CATEGORY DROPDOWN -->
            <select name="category" required>
                <option value="">Select Category</option>
                <?php while ($cat = $categories->fetch_assoc()): ?>
                    <option value="<?= $cat['name'] ?>"
                        <?= $item['category'] == $cat['name'] ? 'selected' : '' ?>>
                        <?= $cat['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" placeholder="Item Name" required>
            <input type="number" name="price" step="0.01" value="<?= $item['price'] ?>" required>
            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" required>
            <input type="date" name="expiry_date" value="<?= $item['expiry_date'] ?>" required>

            <button type="submit">Update Item</button>
        </form>

    </div>
</div>

</body>
</html>
