<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid invoice id");
}

$invoice_id = (int)$_GET['id'];

// Fetch invoice & customer details
$invoice = $conn->query("
    SELECT invoices.*, 
           customers.name AS customer_name, 
           customers.phone AS customer_phone, 
           customers.address AS customer_address
    FROM invoices 
    JOIN customers ON customers.id = invoices.customer_id
    WHERE invoices.id = $invoice_id
")->fetch_assoc();

if (!$invoice) {
    die("Invoice not found");
}

// Fetch invoice items
$items = $conn->query("
    SELECT invoice_items.*, items.name AS item_name
    FROM invoice_items
    JOIN items ON items.id = invoice_items.item_id
    WHERE invoice_id = $invoice_id
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $invoice_id ?></title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #c5d1e7ff;
            margin: 0;
        }

        .main {
            margin-left: 220px;
            padding: 65px;
            display: flex;
            justify-content: center;
        }

        .invoice-box {
            width: 700px;
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .invoice-header h2 {
            margin: 0;
            color: #00264d;
        }

        .invoice-info {
            text-align: right;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .bill-box {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            background: #f9f9f9;
        }

        .bill-from,
        .bill-to {
            width: 48%;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: #00264d;
            color: #fff;
            padding: 8px;
            text-align: left;
        }

        table td {
            border-bottom: 1px solid #ccc;
            padding: 8px;
        }

        table tr:nth-child(even) td {
            background: #f9f9f9;
        }

        /* TOTAL BOX */
        .summary-box {
            margin-top: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 12px;
            width: 320px;
            margin-left: auto;
            background: #f9f9f9;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
        }

        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #bbb;
            margin-top: 6px;
            padding-top: 8px;
            color: #00264d;
        }

        .buttons {
            text-align: center;
            margin-top: 15px;
        }

        .buttons button {
            padding: 8px 15px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: #007BFF;
            color: #fff;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }

        @media print {

            .sidebar,
            .topnav,
            .buttons {
                display: none;
            }

            body {
                background: #fff;
            }

            .main {
                margin: 0;
                padding: 0;
            }

            .invoice-box {
                width: 100%;
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

<?php include "../../includes/sidebar.php"; ?>

<div class="main">
    <div class="invoice-box" id="invoice">

        <div class="invoice-header">
            <h2>Invoice #<?= $invoice_id ?></h2>
        </div>

        <div class="invoice-info">
            <strong>Date:</strong> <?= date('d M Y', strtotime($invoice['created_at'])) ?>
        </div>

        <div class="bill-box">
            <div class="bill-from">
                <strong>Bill From:</strong><br>
                Name: NA Inventory<br>
                Address: Phul Gulab Road, Mandian ATD<br>
                Phone: 0123456789
            </div>

            <div class="bill-to">
                <strong>Bill To:</strong><br>
                <?= htmlspecialchars($invoice['customer_name']) ?><br>
                <?= htmlspecialchars($invoice['customer_address']) ?><br>
                <?= htmlspecialchars($invoice['customer_phone']) ?>
            </div>
        </div>

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
            $idx = 1;
            while ($i = $items->fetch_assoc()):
                $grand += $i['total'];
            ?>
                <tr>
                    <td><?= $idx++ ?></td>
                    <td><?= htmlspecialchars($i['item_name']) ?></td>
                    <td><?= (int)$i['quantity'] ?></td>
                    <td><?= number_format($i['price'], 2) ?></td>
                    <td><?= number_format($i['total'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- SUMMARY -->
        <div class="summary-box">
            <div class="summary-row">
                <span>Grand Total</span>
                <span><?= number_format($grand, 2) ?></span>
            </div>
            <div class="summary-row">
                <span>Paid Amount</span>
                <span><?= number_format($invoice['paid'], 2) ?></span>
            </div>
            <div class="summary-row total">
                <span>Balance</span>
                <span><?= number_format($invoice['balance'], 2) ?></span>
            </div>
        </div>

        <div class="buttons">
            <button onclick="window.print()">Print Invoice</button>
            <button onclick="downloadPDF()">Download PDF</button>
        </div>

        <div class="footer">
            Thank you for your business!<br>NA Inventory
        </div>

    </div>
</div>

<script>
function downloadPDF() {
    const { jsPDF } = window.jspdf;
    const invoice = document.getElementById("invoice");
    const buttons = invoice.querySelector('.buttons');

    if (buttons) buttons.style.display = 'none';

    html2canvas(invoice, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'pt', 'a4');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = canvas.height * pdfWidth / canvas.width;

        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save("Invoice_<?= $invoice_id ?>.pdf");

        if (buttons) buttons.style.display = 'block';
    });
}
</script>

</body>
</html>
