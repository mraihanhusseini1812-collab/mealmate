<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../helpers/upload.php';

requireRole('Pelanggan');

$currentUserId = (int) $_SESSION['user_id'];

$rawOrderId = trim((string) ($_GET['order_id'] ?? ($_POST['order_id'] ?? '0')));
$validOrderId = filter_var($rawOrderId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
$orderId = $validOrderId !== false ? $validOrderId : 0;

if ($orderId <= 0) {
    setFlash('danger', 'ID pesanan tidak valid.');
    redirect('index.php?page=menu#packages-section');
}

$order = getOrderWithDetailsById($orderId, $currentUserId);
if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan tersebut.');
    redirect('index.php?page=menu#packages-section');
}

$detailUrl = 'index.php?page=order-detail&id=' . $orderId;

if ($order['status'] !== 'Belum Dibayar' || !empty($order['payment_id'])) {
    setFlash('warning', 'Pesanan ini tidak dapat dibayar. Bukti pembayaran sudah dikirim atau status pesanan telah berubah.');
    redirect($detailUrl);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentUrl = 'index.php?page=payment&order_id=' . $orderId;

    if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        redirectWithErrors($paymentUrl, [
            'payment_proof' => 'Ukuran berkas melebihi batas unggahan server (maksimal 2 MB).',
        ]);
    }

    $upload = validateAndUploadImage(
        $_FILES['payment_proof'] ?? ['error' => UPLOAD_ERR_NO_FILE],
        __DIR__ . '/../../public/assets/img',
        'pay_'
    );

    if (!$upload['ok']) {
        redirectWithErrors($paymentUrl, ['payment_proof' => $upload['error']]);
    }

    $result = createOrderPayment($orderId, $currentUserId, $upload['filename']);

    if (!$result['ok']) {
        // buang berkas yang sudah terunggah ketika transaksi gagal 
        @unlink(__DIR__ . '/../../public/assets/img/' . $upload['filename']);
        setFlash('danger', $result['message']);
        redirect($detailUrl);
    }

    setFlash('success', 'Bukti pembayaran berhasil dikirim. Pesanan Anda sedang menunggu verifikasi admin.');
    redirect($detailUrl);
}

// rekening tujuan
$bankAccount = [
    'bank' => 'BCA',
    'account_number' => '1234 5678 9012',
    'account_name' => 'Meal Mate',
    'branch' => 'Batam',
    'logo' => 'bca.webp', // logo dari asset, fallback teks jika tidak ada
];

$orderCode = '#ORD-' . str_pad((string) $order['order_id'], 3, '0', STR_PAD_LEFT);
$accountDigits = preg_replace('/\D/', '', $bankAccount['account_number']);

$bankLogoUrl = (!empty($bankAccount['logo']) && is_file(__DIR__ . '/../../public/assets/img/' . $bankAccount['logo']))
    ? './assets/img/' . $bankAccount['logo']
    : null;

$maxUploadBytes = 2097152; // 2 MB
$maxUploadMb = round($maxUploadBytes / 1048576, 1);

render('customer/payment', [
    'title' => 'Pembayaran Pesanan - MealMate',
    'page' => 'payment',
    'hideFooter' => true,
    'order' => $order,
    'orderCode' => $orderCode,
    'bankAccount' => $bankAccount,
    'accountDigits' => $accountDigits,
    'bankLogoUrl' => $bankLogoUrl,
    'maxUploadBytes' => $maxUploadBytes,
    'maxUploadMb' => $maxUploadMb,
    'errors' => getValidationErrors(),
]);
