<?php
session_start();
include "../../config/database.php";

// Protect page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Get customer ID
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: customers.php");
    exit;
}

// Delete customer
$stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: customers.php");
exit;
