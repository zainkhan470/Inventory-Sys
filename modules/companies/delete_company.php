<?php
session_start();
include "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("DELETE FROM companies WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: companies.php");
exit;
