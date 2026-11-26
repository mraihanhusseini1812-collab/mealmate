<?php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die("Akses ditolak! Setup database hanya boleh dijalankan melalui Command Line/Terminal.");
}

date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/../app/config/database.php';

$config = getDBConfig();

try {
    $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']);
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $dbName = $config['dbname'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");

    echo "Database `{$dbName}` berhasil disiapkan.\n";

    $sqlFilePath = __DIR__ . '/schema.sql';
    if (file_exists($sqlFilePath)) {
        $sql = file_get_contents($sqlFilePath);
        $pdo->exec($sql);

        $pdo->exec(
            "ALTER TABLE orders MODIFY COLUMN status
             ENUM('Belum Dibayar', 'Menunggu Verifikasi', 'Aktif', 'Selesai', 'Dibatalkan', 'Ditolak')
             NOT NULL DEFAULT 'Belum Dibayar'"
        );

        // cegah ulasan ganda (order_id, menu_id)
        $stmtIdx = $pdo->prepare(
            "SELECT COUNT(*) FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'reviews' AND INDEX_NAME = 'uniq_order_menu'"
        );
        $stmtIdx->execute(['db' => $dbName]);
        if ((int) $stmtIdx->fetchColumn() === 0) {
            // sisakan review_id terkecil pada pasangan yang terlanjur ganda
            $pdo->exec(
                "DELETE r1 FROM reviews r1
                 JOIN reviews r2
                   ON r1.order_id = r2.order_id
                  AND r1.menu_id = r2.menu_id
                  AND r1.review_id > r2.review_id"
            );
            $pdo->exec("ALTER TABLE reviews ADD UNIQUE KEY uniq_order_menu (order_id, menu_id)");
            echo "unique key reviews(order_id, menu_id) ditambahkan.\n";
        }
        echo "Seluruh tabel berhasil dibuat/diperbarui dari file schema.sql.\n";
    } else {
        die("Gagal: File schema.sql tidak ditemukan di " . $sqlFilePath . "\n");
    }

    // Hapus data demo jika dijalankan dengan flag --reset
    if (in_array('--reset', $argv ?? [], true)) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE reviews");
        $pdo->exec("TRUNCATE TABLE payments");
        $pdo->exec("TRUNCATE TABLE orders");
        $pdo->exec("TRUNCATE TABLE daily_menus");
        $pdo->exec("TRUNCATE TABLE packages");
        $pdo->exec("TRUNCATE TABLE menus");
        $pdo->exec("TRUNCATE TABLE users");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        echo "Data lama berhasil di-reset.\n";
    }

    // Data demo dijalankan dengan flag --demo
    if (in_array('--demo', $argv ?? [], true)) {
        $seedFilePath = __DIR__ . '/seed.sql';

        if (file_exists($seedFilePath)) {
            $pdo->exec(file_get_contents($seedFilePath));

            $startOfWeek = new DateTime('monday this week');
            $slots = ['Pagi', 'Siang', 'Malam'];
            $stmtSchedule = $pdo->prepare(
                "INSERT INTO daily_menus (menu_id, `date`, slot)
                 VALUES (:menu_id, :date, :slot)
                 ON DUPLICATE KEY UPDATE menu_id = VALUES(menu_id)"
            );
            $menuIndex = 1;
            for ($dayOffset = 0; $dayOffset < 14; $dayOffset++) {
                $currDate = (clone $startOfWeek)->modify("+{$dayOffset} days")->format('Y-m-d');
                foreach ($slots as $slot) {
                    $stmtSchedule->execute([
                        'menu_id' => $menuIndex,
                        'date' => $currDate,
                        'slot' => $slot,
                    ]);
                    $menuIndex = ($menuIndex % 21) + 1;
                }
            }

            echo "Data demo berhasil dimasukkan dari seed.sql (termasuk jadwal menu minggu berjalan).\n";
        } else {
            die("Gagal: File seed.sql tidak ditemukan di " . $seedFilePath . "\n");
        }
    } else {
        echo "Data demo dilewati (jalankan dengan --demo untuk mengisi data demo).\n";
    }

    echo "Setup database selesai.\n";
} catch (PDOException $e) {
    http_response_code(500);
    die("Gagal melakukan setup database: " . $e->getMessage() . "\n");
}