<?php
session_start();
include "../../config/database.php";

// Protect page and role check
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

$company_id = $_GET['company_id'] ?? null;

// Fetch bills with optional company filter
$sql = "SELECT company_bills.*, companies.name as company_name 
        FROM company_bills 
        JOIN companies ON companies.id=company_bills.company_id";

if ($company_id) $sql .= " WHERE company_id=" . intval($company_id);

$sql .= " ORDER BY company_bills.id DESC";
$result = $conn->query($sql);

// Fetch all companies for filter dropdown
$companies = $conn->query("SELECT * FROM companies ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Company Bills</title>
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

        .btn {
            display: inline-block;
            background: #007BFF;
            color: #fff;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .btn:hover {
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

        a.action-btn {
            color: #007BFF;
            text-decoration: none;
            margin-right: 10px;
            font-weight: 500;
        }

        a.action-btn:hover {
            text-decoration: underline;
        }

        select {
            padding: 6px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include "../../includes/sidebar.php"; ?>

    <div class="main">
        <h2 class="section-title">💰 Company Bills</h2>
        <a href="add_company_bill.php" class="btn">+ Add Bill</a>

        <form method="get">
            <select name="company_id" onchange="this.form.submit()">
                <option value="">-- Filter by Company --</option>
                <?php while ($c = $companies->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>" <?= ($company_id == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>

        <table>
            <tr>
                <th>ID</th>
                <th>Company</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>

            <?php if ($result->num_rows > 0): while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['company_name']) ?></td>
                        <td><?= number_format($row['total'], 2) ?></td>
                        <td><?= number_format($row['paid'], 2) ?></td>
                        <td><?= number_format($row['balance'], 2) ?></td>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <a href="edit_company_bill.php?id=<?= $row['id'] ?>" class="action-btn">Edit</a>
                            <a href="delete_company_bill.php?id=<?= $row['id'] ?>" class="action-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;color:#777;">No bills found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>

</html>