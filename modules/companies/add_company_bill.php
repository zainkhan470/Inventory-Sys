<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$companies = $conn->query("SELECT * FROM companies ORDER BY name ASC");

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_id = (int)$_POST['company_id'];
    $total = floatval($_POST['total']);
    $paid = floatval($_POST['paid']);
    $balance = $total - $paid;

    if ($company_id <= 0 || $total <= 0 || $paid < 0 || $paid > $total) {
        $error = "Please enter valid data.";
    } else {
        $stmt = $conn->prepare("INSERT INTO company_bills (company_id, total, paid, balance) VALUES (?,?,?,?)");
        $stmt->bind_param("iddd", $company_id, $total, $paid, $balance);
        $stmt->execute();
        header("Location: company_bills.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Company Bill</title>
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

        .card {
            max-width: 500px;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        input,
        select,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #007BFF;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 15px;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: #c0392b;
            background: #ffe5e5;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include "../../includes/sidebar.php"; ?>
    <div class="main">
        <div class="card">
            <h2>Add Company Bill</h2>

            <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <form method="post">
                <label>Company:</label>
                <select name="company_id" required>
                    <option value="">-- Select Company --</option>
                    <?php while ($c = $companies->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label>Total Amount:</label>
                <input type="number" step="0.01" name="total" required>

                <label>Paid Amount:</label>
                <input type="number" step="0.01" name="paid" required>

                <button type="submit">Add Bill</button>
            </form>
        </div>
    </div>
</body>

</html>