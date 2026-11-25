<?php
require_once __DIR__ . '/../config/database.php';

const REVIEW_COMMENT_MAX_LENGTH = 500;

function getReviewableMenus(array $order): array {
    $pdo = getDBConnection();
    $weekdayFilter = ($order['applicable_days'] === 'Senin-Jumat')
        ? 'AND WEEKDAY(dm.`date`) <= 4'
        : '';

    $stmt = $pdo->prepare(
        "SELECT m.menu_id, m.menu_name, m.description, m.image,
                MIN(dm.`date`) AS first_date,
                r.review_id, r.rating, r.comment
         FROM daily_menus dm
         JOIN menus m ON m.menu_id = dm.menu_id
         LEFT JOIN reviews r ON r.order_id = :order_id AND r.menu_id = m.menu_id
         WHERE dm.slot = :slot
           AND dm.`date` BETWEEN :start_date AND :end_date
           {$weekdayFilter}
         GROUP BY m.menu_id, m.menu_name, m.description, m.image,
                  r.review_id, r.rating, r.comment
         ORDER BY first_date ASC, m.menu_id ASC"
    );
    $stmt->execute([
        'order_id' => (int) $order['order_id'],
        'slot' => $order['slot'],
        'start_date' => $order['start_date'],
        'end_date' => $order['end_date'],
    ]);

    return $stmt->fetchAll();
}

function hasPendingReviews(array $order): bool {
    foreach (getReviewableMenus($order) as $menu) {
        if ($menu['review_id'] === null) {
            return true;
        }
    }
    return false;
}

function createReviews(int $orderId, array $items): array {
    $pdo = getDBConnection();

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare(
            "INSERT INTO reviews (order_id, menu_id, rating, comment)
             VALUES (:order_id, :menu_id, :rating, :comment)"
        );
        foreach ($items as $menuId => $item) {
            $stmt->execute([
                'order_id' => $orderId,
                'menu_id' => (int) $menuId,
                'rating' => (int) $item['rating'],
                'comment' => ($item['comment'] !== null && $item['comment'] !== '') ? $item['comment'] : null,
            ]);
        }
        $pdo->commit();
        return ['ok' => true];
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ($e->getCode() === '23000') {
            return ['ok' => false, 'message' => 'Sebagian menu sudah pernah Anda ulas. Silakan muat ulang halaman.'];
        }
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'Ulasan gagal disimpan. Silakan coba lagi.'];
    }
}