<?php
require_once __DIR__ . '/../models/OrderModel.php';

requireRole('Pelanggan');

$currentUserId = (int) $_SESSION['user_id'];
$orderId = (int) ($_GET['id'] ?? $_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    setFlash('danger', 'ID pesanan tidak valid.');
    redirect('index.php?page=menu#packages-section');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'cancel_order') {
        $result = cancelCustomerOrder($orderId, $currentUserId);
        if (!$result['ok']) {
            setFlash('danger', $result['message']);
        } else {
            setFlash('success', 'Pesanan #' . str_pad((string) $orderId, 3, '0', STR_PAD_LEFT) . ' berhasil dibatalkan.');
        }
        redirect('index.php?page=order-detail&id=' . $orderId);
    }

    redirect('index.php?page=order-detail&id=' . $orderId);
}

$order = getOrderWithDetailsById($orderId, $currentUserId);
if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan tersebut.');
    redirect('index.php?page=menu#packages-section');
}

$isReviewed = hasOrderReview($orderId);

render('customer/order_detail', [
    'title' => 'Detail Pesanan #ORD-' . str_pad((string) $order['order_id'], 3, '0', STR_PAD_LEFT) . ' - MealMate',
    'page' => 'order-detail',
    'hideFooter' => true,
    'order' => $order,
    'isReviewed' => $isReviewed,
]);
