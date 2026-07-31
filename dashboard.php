<?php
session_start();
include "config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// ----------------------
// Fetch Data From Items Table
// ----------------------

// Total items
$items_count_result = $conn->query("SELECT COUNT(*) as total FROM items");
$items_count = $items_count_result->fetch_assoc()['total'] ?? 0;

// Total distinct categories
$categories_count_result = $conn->query("SELECT COUNT(DISTINCT category) as total FROM items");
$categories_count = $categories_count_result->fetch_assoc()['total'] ?? 0;

// Total quantity
$total_quantity_result = $conn->query("SELECT SUM(quantity) as total FROM items");
$total_quantity = $total_quantity_result->fetch_assoc()['total'] ?? 0;

// Total companies
$total_companies_result = $conn->query("SELECT COUNT(*) as total FROM companies");
$total_companies = $total_companies_result->fetch_assoc()['total'] ?? 0;

// Low stock items (quantity <= 10)
$low_stock_query = $conn->query("SELECT name, quantity FROM items WHERE quantity <= 10 ORDER BY quantity ASC");
$low_stock_items = [];
if ($low_stock_query && $low_stock_query->num_rows > 0) {
    while ($row = $low_stock_query->fetch_assoc()) {
        $low_stock_items[] = $row;
    }
}

// Recent items (latest 5) - check if created_at column exists, fallback to id
$recent_items_query = $conn->query("SELECT name, quantity FROM items ORDER BY id DESC LIMIT 5");
$recent_items = [];
if ($recent_items_query && $recent_items_query->num_rows > 0) {
    while ($row = $recent_items_query->fetch_assoc()) {
        $recent_items[] = $row;
    }
}

// Fetch all companies for company items section
$companies_result = $conn->query("SELECT * FROM companies ORDER BY name ASC");

// ----------------------
// Monthly Sales from Invoices
// ----------------------

// Get current month and year
$currentMonth = date('m');
$currentYear  = date('Y');

// Fetch invoices for current month
$sales_result = $conn->query("
    SELECT id, created_at, total
    FROM invoices
    WHERE MONTH(created_at) = $currentMonth AND YEAR(created_at) = $currentYear
    ORDER BY created_at ASC
");

// Calculate grand total
$grand_total = 0;
$monthly_sales = [];
if ($sales_result && $sales_result->num_rows > 0) {
    while ($row = $sales_result->fetch_assoc()) {
        $grand_total += floatval($row['total']);
        $monthly_sales[] = $row;
    }
} else {
    // If query failed or no results, set empty array
    $monthly_sales = [];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Inventory Dashboard</title>
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

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .card-box {
            background: #031f3bff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .card-box h2 {
            font-size: 32px;
            color: #007BFF;
            margin-bottom: 8px;
        }

        .card-box p {
            font-size: 15px;
            color: #ffffff;
        }

        .stock-card {
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            gap: 30px;
            margin-top: 60px;
        }

        .stock-heading {
            display: flex;
            flex-direction: column;
            flex: 1;
            margin-bottom: -10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
            margin-top: 0px;
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

        .low {
            color: red;
            font-weight: bold;
        }

        /* Scrollable table container */
        .scrollable-table {
            max-height: 300px;
            /* fixed height */
            overflow-y: auto;
            /* vertical scrollbar */
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
        }

        .scrollable-table table {
            width: 100%;
            table-layout: fixed;
        }

        @media(max-width:768px) {
            .main {
                margin-left: 0;
                margin-top: 120px;
                padding: 15px;
            }

            .cards {
                grid-template-columns: 1fr;
                gap: 15px;
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
    <?php include 'includes/sidebar.php'; ?>

    <div class="main">
        <h2 class="section-title">📊 Dashboard Overview</h2>

        <!-- Summary Cards -->
        <div class="cards">
            <div class="card-box">
                <h2><?= $items_count ?></h2>
                <p>Total Items</p>
            </div>
            <div class="card-box">
                <h2><?= $categories_count ?></h2>
                <p>Total Categories</p>
            </div>
            <div class="card-box">
                <h2><?= $total_quantity ?></h2>
                <p>Total Quantity</p>
            </div>
            <div class="card-box">
                <h2><?= $total_companies ?></h2>
                <p>Total Companies</p>
            </div>
        </div>

        <!-- Low Stock & Recent Items -->
        <div class="stock-card">
            <div class="stock-heading">
                <h2 class="section-title">Low Stock Items</h2>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Stock</th>
                    </tr>
                    <?php if (!empty($low_stock_items)): ?>
                        <?php foreach ($low_stock_items as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td class="low"><?= $row['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align:center;color:#777;">No low stock items</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="stock-heading">
                <h2 class="section-title">Recent Items</h2>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Qty</th>
                    </tr>
                    <?php if (!empty($recent_items)): ?>
                        <?php foreach ($recent_items as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['quantity'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" style="text-align:center;color:#777;">No recent items</td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Monthly Sales (Scrollable Only) -->
        <div class="stock-card" style="margin-top: 40px;">
            <div class="stock-heading" style="flex:1;">
                <h2 class="section-title">💰 Monthly Sales (<?= date('F Y') ?>)</h2>
                <div class="scrollable-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Date</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthly_sales)): ?>
                                <?php foreach ($monthly_sales as $sale): ?>
                                    <tr>
                                        <td><?= $sale['id'] ?></td>
                                        <td><?= date('d-m-Y', strtotime($sale['created_at'])) ?></td>
                                        <td>$<?= number_format($sale['total'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <th colspan="2" style="text-align:right;">Grand Total</th>
                                    <th>$<?= number_format($grand_total, 2) ?></th>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center;color:#777;">No sales for this month</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>

</html>