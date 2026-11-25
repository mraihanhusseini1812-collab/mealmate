<?php
require_once __DIR__ . '/../config/database.php';

function getValidOrderStatuses(): array {
    return ['Belum Dibayar', 'Menunggu Verifikasi', 'Aktif', 'Selesai', 'Dibatalkan', 'Ditolak'];
}

function calculateOrderEndDate(string $startDate, int $durationDays, string $applicableDays): string {
    $current = new DateTimeImmutable($startDate);
    $remaining = max(1, $durationDays);
    $weekdaysOnly = ($applicableDays === 'Senin-Jumat');

    while (true) {
        $dayOfWeek = (int) $current->format('N');
        if (!$weekdaysOnly || $dayOfWeek <= 5) {
            $remaining--;
            if ($remaining <= 0) {
                break;
            }
        }
        $current = $current->modify('+1 day');
    }

    return $current->format('Y-m-d');
}

function buildAdminOrderFilter(string $status = '', string $search = ''): array {
    $conditions = [];
    $params = [];

    if ($status !== '' && $status !== 'Semua' && in_array($status, getValidOrderStatuses(), true)) {
        $conditions[] = "o.status = :status";
        $params['status'] = $status;
    }

    $q = trim($search);
    if ($q !== '') {
        $cleanId = ltrim($q, '#ORDordMm-0');
        $idLike = '%' . ($cleanId !== '' ? $cleanId : $q) . '%';
        $textLike = '%' . $q . '%';
        $conditions[] = "(CAST(o.order_id AS CHAR) LIKE :q_id OR u.name LIKE :q_name OR p.package_name LIKE :q_pkg OR u.phone_number LIKE :q_phone)";
        $params['q_id'] = $idLike;
        $params['q_name'] = $textLike;
        $params['q_pkg'] = $textLike;
        $params['q_phone'] = $textLike;
    }

    if (empty($conditions)) {
        return ['', []];
    }

    return ['WHERE ' . implode(' AND ', $conditions), $params];
}

function countAdminOrders(string $status = '', string $search = ''): int {
    $pdo = getDBConnection();
    [$where, $params] = buildAdminOrderFilter($status, $search);
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         JOIN packages p ON p.package_id = o.package_id
         {$where}"
    );
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getAdminOrders(string $status = '', string $search = '', int $limit = 10, int $offset = 0): array {
    $pdo = getDBConnection();
    [$where, $params] = buildAdminOrderFilter($status, $search);
    $stmt = $pdo->prepare(
        "SELECT o.order_id, o.start_date, o.slot, o.total_price, o.status, o.created_at,
                u.name AS customer_name, u.phone_number AS customer_phone,
                p.package_name, p.duration_days, p.applicable_days,
                pay.payment_id, pay.payment_proof, pay.status AS payment_status
         FROM orders o
         JOIN users u ON u.user_id = o.user_id
         JOIN packages p ON p.package_id = o.package_id
         LEFT JOIN payments pay ON pay.order_id = o.order_id
         {$where}
         ORDER BY o.created_at DESC, o.order_id DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getOrderWithDetailsById(int $orderId, ?int $userId = null): ?array {
    $pdo = getDBConnection();
    $sql = "SELECT o.order_id, o.user_id, o.package_id, o.start_date, o.slot,
                   o.delivery_address, o.notes, o.total_price, o.status, o.created_at,
                   u.name AS customer_name, u.email AS customer_email, u.phone_number AS customer_phone,
                   p.package_name, p.description AS package_description, p.price AS package_price,
                   p.image AS package_image, p.duration_days, p.applicable_days,
                   pay.payment_id, pay.payment_proof, pay.status AS payment_status, pay.rejection_reason
            FROM orders o
            JOIN users u ON u.user_id = o.user_id
            JOIN packages p ON p.package_id = o.package_id
            LEFT JOIN payments pay ON pay.payment_id = (
                SELECT p2.payment_id
                FROM payments p2
                WHERE p2.order_id = o.order_id
                ORDER BY p2.payment_id DESC
                LIMIT 1
            )
            WHERE o.order_id = :id";

    $params = ['id' => $orderId];
    if ($userId !== null) {
        $sql .= " AND o.user_id = :user_id";
        $params['user_id'] = $userId;
    }
    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }

    $row['end_date'] = calculateOrderEndDate(
        $row['start_date'],
        (int) $row['duration_days'],
        $row['applicable_days']
    );

    return $row;
}

function createCustomerOrder(
    int $userId,
    int $packageId,
    string $startDate,
    string $slot,
    string $deliveryAddress,
    ?string $notes,
    int $totalPrice
): int {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO orders (user_id, package_id, start_date, slot, delivery_address, notes, total_price, status)
         VALUES (:user_id, :package_id, :start_date, :slot, :delivery_address, :notes, :total_price, 'Belum Dibayar')"
    );
    $stmt->execute([
        'user_id' => $userId,
        'package_id' => $packageId,
        'start_date' => $startDate,
        'slot' => $slot,
        'delivery_address' => trim($deliveryAddress),
        'notes' => ($notes !== null && trim($notes) !== '') ? trim($notes) : null,
        'total_price' => $totalPrice,
    ]);
    return (int) $pdo->lastInsertId();
}

function cancelCustomerOrder(int $orderId, int $userId): array {
    $order = getOrderWithDetailsById($orderId, $userId);
    if (!$order) {
        return ['ok' => false, 'message' => 'Pesanan tidak ditemukan.'];
    }

    if ($order['status'] !== 'Belum Dibayar' || !empty($order['payment_id'])) {
        return [
            'ok' => false,
            'message' => 'Pesanan yang sudah mengirim bukti pembayaran, sedang diproses, atau selesai tidak dapat dibatalkan.',
        ];
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare(
        "UPDATE orders
         SET status = 'Dibatalkan'
         WHERE order_id = :order_id AND user_id = :user_id AND status = 'Belum Dibayar'"
    );
    $stmt->execute([
        'order_id' => $orderId,
        'user_id' => $userId,
    ]);

    if ($stmt->rowCount() === 0) {
        return ['ok' => false, 'message' => 'Gagal membatalkan pesanan.'];
    }

    return ['ok' => true];
}

function buildCustomerOrderFilter(int $userId, string $search = ''): array {
    $where = 'WHERE o.user_id = :user_id';
    $params = ['user_id' => $userId];

    $q = trim($search);
    if ($q !== '') {
        // "#ORD-001" / "ORD-001" / "001" -> "1"
        $clean = preg_replace('/^#?(?:ORD)?-?/i', '', $q);
        $idTerm = ($clean !== '' && ctype_digit($clean)) ? ltrim($clean, '0') : '';

        if ($idTerm !== '') {
            $where .= ' AND (CAST(o.order_id AS CHAR) LIKE :q_id OR p.package_name LIKE :q_pkg)';
            $params['q_id'] = '%' . $idTerm . '%';
        } else {
            $where .= ' AND p.package_name LIKE :q_pkg';
        }
        $params['q_pkg'] = '%' . $q . '%';
    }

    return [$where, $params];
}

function countCustomerOrders(int $userId, string $search = ''): int {
    $pdo = getDBConnection();
    [$where, $params] = buildCustomerOrderFilter($userId, $search);
    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM orders o
         JOIN packages p ON p.package_id = o.package_id
         {$where}"
    );
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

function getCustomerOrders(int $userId, string $search = '', int $limit = 5, int $offset = 0): array {
    $pdo = getDBConnection();
    [$where, $params] = buildCustomerOrderFilter($userId, $search);
    $stmt = $pdo->prepare(
        "SELECT o.order_id, o.start_date, o.slot, o.status, o.created_at,
                p.package_name, p.image AS package_image, p.duration_days, p.applicable_days
         FROM orders o
         JOIN packages p ON p.package_id = o.package_id
         {$where}
         ORDER BY o.created_at DESC, o.order_id DESC
         LIMIT :limit OFFSET :offset"
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue(":{$key}", $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    foreach ($rows as &$row) {
        $row['end_date'] = calculateOrderEndDate(
            $row['start_date'],
            (int) $row['duration_days'],
            $row['applicable_days']
        );
    }
    unset($row);

    return $rows;
}

function createOrderPayment(int $orderId, int $userId, string $proofFilename): array {
    $pdo = getDBConnection();

    try {
        $pdo->beginTransaction();

        // kunci pesanan agar klik ganda / dua tab engga membuat dua payment
        $stmtLock = $pdo->prepare(
            "SELECT status FROM orders WHERE order_id = :oid AND user_id = :uid FOR UPDATE"
        );
        $stmtLock->execute(['oid' => $orderId, 'uid' => $userId]);
        $status = $stmtLock->fetchColumn();

        if ($status === false) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Pesanan tidak ditemukan.'];
        }

        if ($status !== 'Belum Dibayar') {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Pesanan ini tidak dapat dibayar. Status pesanan telah berubah.'];
        }

        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE order_id = :oid");
        $stmtCheck->execute(['oid' => $orderId]);
        if ((int) $stmtCheck->fetchColumn() > 0) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Bukti pembayaran untuk pesanan ini sudah dikirim.'];
        }

        $stmtPay = $pdo->prepare("INSERT INTO payments (order_id, payment_proof) VALUES (:oid, :proof)");
        $stmtPay->execute(['oid' => $orderId, 'proof' => $proofFilename]);

        $stmtOrd = $pdo->prepare(
            "UPDATE orders SET status = 'Menunggu Verifikasi'
             WHERE order_id = :oid AND user_id = :uid AND status = 'Belum Dibayar'"
        );
        $stmtOrd->execute(['oid' => $orderId, 'uid' => $userId]);

        if ($stmtOrd->rowCount() !== 1) {
            $pdo->rollBack();
            return ['ok' => false, 'message' => 'Gagal memperbarui status pesanan.'];
        }

        $pdo->commit();
        return ['ok' => true];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log($e->getMessage());
        return ['ok' => false, 'message' => 'Gagal menyimpan bukti pembayaran. Silakan coba lagi.'];
    }
}

function verifyOrderPayment(int $orderId, string $decision, ?string $rejectionReason = null): array {
    $order = getOrderWithDetailsById($orderId);
    if (!$order) {
        return ['ok' => false, 'message' => 'Pesanan tidak ditemukan.'];
    }

    if ($order['status'] !== 'Menunggu Verifikasi' || empty($order['payment_id'])) {
        return ['ok' => false, 'message' => 'Pesanan ini tidak sedang menunggu verifikasi bukti pembayaran.'];
    }

    if (!in_array($decision, ['approve', 'reject'], true)) {
        return ['ok' => false, 'message' => 'Keputusan verifikasi tidak valid.'];
    }

    $cleanReason = trim((string) $rejectionReason);
    if ($decision === 'reject' && $cleanReason === '') {
        return ['ok' => false, 'field' => 'rejection_reason', 'message' => 'Alasan penolakan wajib diisi.'];
    }

    $pdo = getDBConnection();
    $pdo->beginTransaction();

    try {
        if ($decision === 'approve') {
            $stmtPay = $pdo->prepare("UPDATE payments SET status = 'Valid', rejection_reason = NULL WHERE payment_id = :pid");
            $stmtPay->execute(['pid' => (int) $order['payment_id']]);

            $stmtOrd = $pdo->prepare("UPDATE orders SET status = 'Aktif' WHERE order_id = :oid");
            $stmtOrd->execute(['oid' => $orderId]);
        } else {
            $stmtPay = $pdo->prepare("UPDATE payments SET status = 'Tidak Valid', rejection_reason = :reason WHERE payment_id = :pid");
            $stmtPay->execute(['reason' => $cleanReason, 'pid' => (int) $order['payment_id']]);

            $stmtOrd = $pdo->prepare("UPDATE orders SET status = 'Ditolak' WHERE order_id = :oid");
            $stmtOrd->execute(['oid' => $orderId]);
        }

        $pdo->commit();
        return ['ok' => true];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['ok' => false, 'message' => 'Gagal menyimpan hasil verifikasi pembayaran.'];
    }
}

function updateOrderStatusByAdmin(int $orderId, string $newStatus): array {
    $order = getOrderWithDetailsById($orderId);
    if (!$order) {
        return ['ok' => false, 'message' => 'Pesanan tidak ditemukan.'];
    }

    $allowedTransitions = [
        'Aktif' => ['Selesai'],
    ];

    $currentStatus = $order['status'];
    if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [], true)) {
        return [
            'ok' => false,
            'message' => "Perubahan status ditolak: pesanan berstatus '{$currentStatus}' tidak dapat diubah langsung ke '{$newStatus}'.",
        ];
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $orderId]);

    return ['ok' => true];
}
