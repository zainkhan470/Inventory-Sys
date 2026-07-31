<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM company_items WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: company_items.php");
exit;
