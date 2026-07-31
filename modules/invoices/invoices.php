<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$role = $_SESSION['role'] ?? 'salesman';
if ($role !== 'owner') {
    // Salesman cannot access company bills
    header("Location: dashboard.php");
    exit;
}

// Search
$search = $_GET['search'] ?? '';

if ($search) {
    $searchParam = "%$search%";
    $stmt = $conn->prepare("
        SELECT 
            invoices.id,
            invoices.total,
            invoices.paid,
            invoices.balance,
            invoices.created_at,
            customers.name AS customer_name
        FROM invoices
        JOIN customers ON customers.id = invoices.customer_id
        WHERE invoices.id LIKE ? OR customers.name LIKE ?
        ORDER BY invoices.created_at DESC
    ");
    $stmt->bind_param("ss", $searchParam, $searchParam);
} else {
    $stmt = $conn->prepare("
        SELECT 
            invoices.id,
            invoices.total,
            invoices.paid,
            invoices.balance,
            invoices.created_at,
            customers.name AS customer_name
        FROM invoices
        JOIN customers ON customers.id = invoices.customer_id
        ORDER BY invoices.created_at DESC
    ");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Invoices</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #c5d1e7ff;
        }

        /* Main content */
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

        .search-box {
            margin-bottom: 15px;
        }

        .search-box input {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 260px;
        }

        .search-box button {
            padding: 8px 14px;
            border-radius: 6px;
            border: none;
            background: #007BFF;
            color: #fff;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #00264d;
            color: #fff;
            font-weight: 600;
        }

        tr:hover {
            background: #f1f8ff;
        }

        select {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        .status-paid {
            color: green;
            font-weight: 600;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                margin-top: 120px;
                padding: 15px;
            }

            th,
            td {
                font-size: 13px;
                padding: 8px 10px;
            }
        }
    </style>
</head>

<body>

    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">

        <h2 class="section-title">🧾 Invoices</h2>

        <form method="get" class="search-box">
            <input type="text" name="search" placeholder="Search Invoice or Customer"
                value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>#</th>
                <th>Invoice ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Action</th>
            </tr>

            <?php if ($result->num_rows): $i = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= date('d M Y', strtotime($row['created_at'])) ?></td>
                        <td><?= number_format($row['total'], 2) ?></td>
                        <td><?= number_format($row['paid'], 2) ?></td>
                        <td><?= number_format($row['balance'], 2) ?></td>
                        <td>
                            <select onchange="invoiceAction(this, <?= $row['id'] ?>)">
                                <option value="">-- Select --</option>
                                <option value="view">View Invoice</option>

                                <?php if ($row['balance'] > 0): ?>
                                    <option value="pay">Pay Invoice</option>
                                <?php else: ?>
                                    <option disabled class="status-paid">Paid</option>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;color:#777;">
                        No invoices found
                    </td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <script>
        function invoiceAction(select, invoiceId) {
            const action = select.value;

            if (action === 'view') {
                window.location.href = 'invoice.php?id=' + invoiceId;
            }

            if (action === 'pay') {
                window.location.href = 'invoice_payment.php?id=' + invoiceId;
            }

            select.selectedIndex = 0;
        }
    </script>

</body>

</html>