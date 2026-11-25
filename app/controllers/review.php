<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/ReviewModel.php';

requireRole('Pelanggan');

$userId = (int) $_SESSION['user_id'];

$rawOrderId = trim((string) ($_GET['order_id'] ?? ($_POST['order_id'] ?? '0')));
$validOrderId = filter_var($rawOrderId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]);
$orderId = $validOrderId !== false ? $validOrderId : 0;

if ($orderId <= 0) {
    setFlash('danger', 'ID pesanan tidak valid.');
    redirect('index.php?page=orders');
}

$order = getOrderWithDetailsById($orderId, $userId);
if (!$order) {
    setFlash('danger', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan tersebut.');
    redirect('index.php?page=orders');
}

$detailUrl = 'index.php?page=order-detail&id=' . $orderId;
$reviewUrl = 'index.php?page=review&order_id=' . $orderId;

if ($order['status'] !== 'Selesai') {
    setFlash('warning', 'Ulasan hanya dapat diberikan untuk pesanan yang berstatus Selesai.');
    redirect($detailUrl);
}

$menus = getReviewableMenus($order);
$pendingMenus = array_values(array_filter($menus, static fn (array $m): bool => $m['review_id'] === null));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($pendingMenus)) {
        setFlash('warning', 'Semua menu pada pesanan ini sudah Anda ulas.');
        redirect($detailUrl);
    }

    $rawRatings = is_array($_POST['rating'] ?? null) ? $_POST['rating'] : [];
    $rawComments = is_array($_POST['comment'] ?? null) ? $_POST['comment'] : [];
    $errors = [];
    $items = [];
    $old = ['rating' => [], 'comment' => []];

    // cuman menu turunan pesanan yang belom diulas yang diproses
    foreach ($pendingMenus as $menu) {
        $menuId = (int) $menu['menu_id'];
        $ratingRaw = $rawRatings[$menuId] ?? '';
        $ratingRaw = is_string($ratingRaw) ? trim($ratingRaw) : '';
        $comment = $rawComments[$menuId] ?? '';
        $comment = is_string($comment) ? trim($comment) : '';

        $old['comment'][$menuId] = $comment;

        if ($ratingRaw === '') {
            if ($comment !== '') {
                $errors['menu_' . $menuId] = 'Pilih rating bintang terlebih dahulu.';
            }
            continue;
        }

        $rating = filter_var($ratingRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        if ($rating === false) {
            $errors['menu_' . $menuId] = 'Rating tidak valid (1-5).';
            continue;
        }
        $old['rating'][$menuId] = $rating;

        if (mb_strlen($comment) > REVIEW_COMMENT_MAX_LENGTH) {
            $errors['menu_' . $menuId] = 'Komentar maksimal ' . REVIEW_COMMENT_MAX_LENGTH . ' karakter.';
            continue;
        }

        $items[$menuId] = ['rating' => $rating, 'comment' => $comment];
    }

    if (empty($items) && empty($errors)) {
        $errors['general'] = 'Beri rating bintang pada minimal satu menu sebelum mengirim ulasan.';
    }

    if (!empty($errors)) {
        redirectWithErrors($reviewUrl, $errors, $old);
    }

    $result = createReviews($orderId, $items);
    if (!$result['ok']) {
        redirectWithErrors($reviewUrl, ['general' => $result['message']], $old);
    }

    setFlash('success', count($items) . ' ulasan berhasil dikirim. Terima kasih atas masukan Anda!');
    redirect($detailUrl);
}

$errors = getValidationErrors();
$old = getOldInput();
$imgDir = __DIR__ . '/../../public/assets/img/';

$reviewMenus = [];
foreach ($menus as $menu) {
    $menuId = (int) $menu['menu_id'];
    $reviewed = $menu['review_id'] !== null;
    $rating = $reviewed ? (int) $menu['rating'] : (int) ($old['rating'][$menuId] ?? 0);

    $reviewMenus[] = [
        'menu_id' => $menuId,
        'name' => $menu['menu_name'],
        'image_url' => (!empty($menu['image']) && is_file($imgDir . $menu['image']))
            ? './assets/img/' . $menu['image']
            : null,
        'meta' => $order['slot'] . ' · ' . formatDateIndo($menu['first_date'], true),
        'reviewed' => $reviewed,
        'rating' => $rating,
        'stars_html' => renderStars((float) $rating),
        'comment' => $reviewed ? (string) ($menu['comment'] ?? '') : (string) ($old['comment'][$menuId] ?? ''),
        'comment_length' => mb_strlen($reviewed ? (string) ($menu['comment'] ?? '') : (string) ($old['comment'][$menuId] ?? '')),
        'error' => $errors['menu_' . $menuId] ?? null,
    ];
}

$readOnly = (count($pendingMenus) === 0 && !empty($reviewMenus));

render('customer/review', [
    'title' => $readOnly ? 'Ulasan Saya - MealMate' : 'Beri Ulasan - MealMate',
    'page' => 'review',
    'hideFooter' => true,
    'detailUrl' => $detailUrl,
    'formAction' => $reviewUrl,
    'orderId' => $orderId,
    'reviewMenus' => $reviewMenus,
    'pendingCount' => count($pendingMenus),
    'maxComment' => REVIEW_COMMENT_MAX_LENGTH,
    'generalError' => $errors['general'] ?? null,
    'readOnly' => $readOnly,
    'heroTitle' => $readOnly ? 'Ulasan Anda' : 'Bagaimana makanan Anda?',
    'heroSub' => $readOnly
        ? 'Terima kasih atas masukan Anda untuk pesanan ini!'
        : 'Pendapat Anda sangat berarti bagi kami. Bantu kami terus memberikan layanan terbaik!',
]);