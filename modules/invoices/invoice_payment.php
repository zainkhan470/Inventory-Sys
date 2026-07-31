<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? 0;

// Fetch invoice + customer
$stmt = $conn->prepare("
    SELECT invoices.id, invoices.total, invoices.paid, invoices.balance,
           customers.name AS customer_name
    FROM invoices
    JOIN customers ON customers.id = invoices.customer_id
    WHERE invoices.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

if (!$invoice) {
    die("Invoice not found");
}

// Fetch invoice items
$itemStmt = $conn->prepare("
    SELECT items.name, invoice_items.quantity, invoice_items.price, invoice_items.total
    FROM invoice_items
    JOIN items ON items.id = invoice_items.item_id
    WHERE invoice_items.invoice_id = ?
");
$itemStmt->bind_param("i", $id);
$itemStmt->execute();
$items = $itemStmt->get_result();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);

    if ($amount <= 0) {
        $error = "Invalid payment amount";
    } elseif ($amount > $invoice['balance']) {
        $error = "Payment exceeds balance";
    } else {
        $newPaid = $invoice['paid'] + $amount;
        $newBalance = $invoice['total'] - $newPaid;

        $update = $conn->prepare("
            UPDATE invoices 
            SET paid = ?, balance = ?
            WHERE id = ?
        ");
        $update->bind_param("ddi", $newPaid, $newBalance, $id);
        $update->execute();

        header("Location: invoices.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Invoice Payment</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(#75abe9ff, #c5d1e7ff);
            margin: 0;
        }

        .main {
            margin-left: 220px;
            margin-top: 60px;
            padding: 20px;
            flex: 1;
        }

        h2.section-title {
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .card {
            max-width: 520px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .info-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 15px;
            background: #f9f9f9;
        }

        .info {
            font-size: 14px;
            margin-bottom: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 8px;
        }

        table th {
            background: #2c3e50;
            color: #fff;
            padding: 6px;
            text-align: left;
        }

        table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }

        .error {
            background: #ffe5e5;
            color: #c0392b;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        button {
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            border: none;
            border-radius: 6px;
            background: #28a745;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #218838;
        }

        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                margin-top: 120px;
            }
        }
    </style>
</head>

<body>

    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">
        <h2 class="section-title">💳 Invoice Payment</h2>

        <div class="card">

            <?php if ($error): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Invoice Info -->
            <div class="info-box">
                <div class="info"><strong>Invoice #:</strong> <?= $invoice['id'] ?></div>
                <div class="info"><strong>Customer:</strong> <?= htmlspecialchars($invoice['customer_name']) ?></div>
                <div class="info"><strong>Total:</strong> <?= number_format($invoice['total'], 2) ?></div>
                <div class="info"><strong>Paid:</strong> <?= number_format($invoice['paid'], 2) ?></div>
                <div class="info"><strong>Balance:</strong> <?= number_format($invoice['balance'], 2) ?></div>
            </div>

            <!-- Items -->
            <div class="info-box">
                <strong>Invoice Items</strong>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php while ($row = $items->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= (int)$row['quantity'] ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= number_format($row['total'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <!-- Payment -->
            <form method="post">
                <input type="number" step="0.01" name="amount" placeholder="Enter payment amount" required>
                <button type="submit">Submit Payment</button>
            </form>

            <a href="invoices.php" class="back-link">← Back to Invoices</a>

        </div>
    </div>

</body>

</html>