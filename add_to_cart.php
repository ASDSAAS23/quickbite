<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

require_login();

if (!isset($_GET['id'])) {
    header("Location: " . qb_url('menu.php'));
    exit();
}

$userId = (int) $_SESSION['user_id'];
$itemId = (int) $_GET['id'];

$checkSql = "SELECT id, quantity FROM cart WHERE user_id = ? AND menu_item_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ii", $userId, $itemId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows > 0) {
    $cartItem = $result->fetch_assoc();
    $newQty = (int) $cartItem['quantity'] + 1;

    $updateSql = "UPDATE cart SET quantity = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ii", $newQty, $cartItem['id']);
    $updateStmt->execute();
} else {
    $insertSql = "INSERT INTO cart (user_id, menu_item_id, quantity) VALUES (?, ?, 1)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("ii", $userId, $itemId);
    $insertStmt->execute();
}

$back = $_SERVER['HTTP_REFERER'] ?? qb_url('menu.php');
header("Location: " . $back);
exit();
