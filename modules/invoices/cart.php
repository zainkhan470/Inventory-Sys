<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// ensure cart session exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// select customer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $_SESSION['selected_customer'] = (int)$_POST['customer_id'];
}

// Fetch customers and items
$customers = $conn->query("SELECT * FROM customers ORDER BY name ASC");
$items = $conn->query("SELECT * FROM items ORDER BY name ASC");

// Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $qty = (int)($_POST['quantity'] ?? 0);

    if ($item_id <= 0 || $qty <= 0) {
        $_SESSION['notice'] = "Please choose a valid item and quantity.";
        header("Location: cart.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT id, name, price, quantity FROM items WHERE id=?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($qty > $item['quantity']) {
        $_SESSION['notice'] = "Requested quantity exceeds stock.";
        header("Location: cart.php");
        exit;
    }

    $_SESSION['cart'][] = [
        'item_id' => $item['id'],
        'name' => $item['name'],
        'price' => (float)$item['price'],
        'quantity' => $qty,
        'row_total' => $item['price'] * $qty
    ];

    header("Location: cart.php");
    exit;
}

// Checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $_SESSION['notice'] = "Cart is empty.";
        header("Location: cart.php");
        exit;
    }

    $customer_id = $_SESSION['selected_customer'] ?? 0;

    if ($customer_id <= 0) {
        $_SESSION['notice'] = "Select customer before checkout.";
        header("Location: cart.php");
        exit;
    }

    $conn->begin_transaction();
    try {
        // CALCULATE GRAND TOTAL
        $grand = 0;
        foreach ($_SESSION['cart'] as $c) {
            $grand += $c['row_total'];
        }

        // GET PAID
        $paid = (float)($_POST['paid_amount'] ?? 0);

        if ($paid < 0 || $paid > $grand) {
            throw new Exception("Invalid paid amount.");
        }

        $balance = $grand - $paid;

        // INSERT INVOICE (DISCOUNT REMOVED)
        $stmt = $conn->prepare("
            INSERT INTO invoices (customer_id, total, paid, balance)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iddd", $customer_id, $grand, $paid, $balance);
        $stmt->execute();
        $invoice_id = $stmt->insert_id;
        $stmt->close();

        // INSERT INVOICE ITEMS
        $insert = $conn->prepare("
            INSERT INTO invoice_items (invoice_id, item_id, quantity, price, total)
            VALUES (?, ?, ?, ?, ?)
        ");

        // DEDUCT STOCK
        $stock = $conn->prepare("
            UPDATE items 
            SET quantity = quantity - ?
            WHERE id = ? AND quantity >= ?
        ");

        foreach ($_SESSION['cart'] as $c) {
            $stock->bind_param("iii", $c['quantity'], $c['item_id'], $c['quantity']);
            $stock->execute();

            $insert->bind_param(
                "iiidd",
                $invoice_id,
                $c['item_id'],
                $c['quantity'],
                $c['price'],
                $c['row_total']
            );
            $insert->execute();
        }

        $conn->commit();

        // CLEAR CART
        $_SESSION['cart'] = [];
        unset($_SESSION['selected_customer']);

        header("Location: invoice.php?id=$invoice_id");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['notice'] = "Error: " . $e->getMessage();
        header("Location: cart.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Invoice</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(135deg, #c3dafe, #e0e7ff);
        }

        .main {
            margin-left: 240px;
            padding: 40px;
            flex: 1;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            max-width: 1100px;
            margin: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        h2,
        h3 {
            margin-bottom: 15px;
            color: #1f2937;
        }

        .notice {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 2fr 3fr 1fr 1.5fr;
            gap: 12px;
            align-items: end;
            margin-bottom: 20px;
        }

        .form-row label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            float: right;
        }

        select,
        input {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            outline: none;
        }

        select:focus,
        input:focus {
            border-color: #6366f1;
        }

        button {
            padding: 10px;
            border-radius: 8px;
            background: #2579f7ff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }

        button:hover {
            background: #0051caff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table th,
        table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            background: #f9fafb;
            text-align: left;
        }

        .payment-box {
            margin-bottom: 5px;
            display: flex;
        }

        .payment-box input {
            max-width: 200px;
        }
    </style>
</head>

<body>

    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">
        <div class="card">

            <h2>Create Invoice</h2>

            <?php if (!empty($_SESSION['notice'])): ?>
                <p class="notice"><?= $_SESSION['notice'];
                                    unset($_SESSION['notice']); ?></p>
            <?php endif; ?>

            <!-- Customer + Item + Quantity -->
            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label>Customer</label>
                        <select name="customer_id" onchange="this.form.submit()" required>
                            <option value="">-- Choose Customer --</option>
                            <?php
                            $sel = $_SESSION['selected_customer'] ?? 0;
                            $customers = $conn->query("SELECT * FROM customers ORDER BY name ASC");
                            while ($c = $customers->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>" <?= ($sel == $c['id']) ? "selected" : "" ?>>
                                    <?= $c['name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Item</label>
                        <select name="item_id" required>
                            <option value="">-- Select Item --</option>
                            <?php
                            $items = $conn->query("SELECT * FROM items ORDER BY name ASC");
                            while ($i = $items->fetch_assoc()):
                            ?>
                                <option value="<?= $i['id'] ?>">
                                    <?= $i['name'] ?> (<?= $i['quantity'] ?> in stock)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button name="add_to_cart">Add to Cart</button>
                    </div>
                </div>
            </form>

            <h3>Cart Items</h3>
            <table>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>

                <?php
                $grand = 0;
                if (!empty($_SESSION['cart'])):
                    foreach ($_SESSION['cart'] as $i => $row):
                        $grand += $row['row_total'];
                ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= number_format($row['row_total'], 2) ?></td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="5" align="center">Cart is empty</td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <th colspan="4" align="right">Grand Total</th>
                    <th><?= number_format($grand, 2) ?></th>
                </tr>
            </table>

            <h3>Payment</h3>
            <form method="post">
                <div class="payment-box">
                    <div class="form-group" style="float: right;">
                        <label>Paid Amount</label>
                        <input type="number" step="0.01" min="0" max="<?= $grand ?>" name="paid_amount" value="0" required>
                    </div>
                </div>

                <button name="checkout">Generate Invoice & Deduct Stock</button>
            </form>

        </div>
    </div>

</body>

</html>