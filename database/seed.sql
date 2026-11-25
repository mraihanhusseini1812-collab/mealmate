-- =====================================================
-- Data demo aplikasi MealMate (Idempotent / Aman dijalankan berulang kali)
-- =====================================================

-- 1. Akun Pengguna Demo (Admin & Pelanggan)
-- Password Admin (admin@mealmate.id): admin12345
-- Password Pelanggan (angga@mealmate.id, budi@mealmate.id, dewi@mealmate.id): pelanggan123
INSERT INTO users (user_id, name, email, password, phone_number, address, role) VALUES
(1, 'Jasson D.',     'admin@mealmate.id', '$2y$10$hwlA6zJLxZzXY6l4CLcVkeak.YJl4B0pEaA4DzTDydzmP.qoZX2Zu', '081234567890', 'Jl. Ahmad Yani, Batam Center, Kota Batam', 'Admin'),
(2, 'Angga Pratama', 'angga@mealmate.id', '$2y$10$XcxbLtgy69aBH9WhdLBdPeeAnPFITgBj7TVI33E6B5XeWnUdllunu', '081378219821', 'Jl. Melati No 12, RT 03/RW 05, Kel. Sungai Jawi, Kec. Batam Kota, Kota Batam, Kepulauan Riau 29444', 'Pelanggan'),
(3, 'Budi Santoso',  'budi@mealmate.id',  '$2y$10$XcxbLtgy69aBH9WhdLBdPeeAnPFITgBj7TVI33E6B5XeWnUdllunu', '087844210963', 'Jl. Cempaka No. 31, Batam Center, Kota Batam', 'Pelanggan'),
(4, 'Dewi Anggraini','dewi@mealmate.id',  '$2y$10$XcxbLtgy69aBH9WhdLBdPeeAnPFITgBj7TVI33E6B5XeWnUdllunu', '081234567801', 'Jl. Melati No. 12, Sekupang, Kota Batam', 'Pelanggan')
ON DUPLICATE KEY UPDATE
    name         = VALUES(name),
    email        = VALUES(email),
    phone_number = VALUES(phone_number),
    address      = VALUES(address),
    role         = VALUES(role);

-- 2. Katalog Menu (21 menu)
INSERT INTO menus (menu_id, menu_name, description, image) VALUES
(1,  'Nasi Uduk Spesial',   'Nasi uduk gurih santan dengan telur, bawang goreng, kerupuk, dan sambal kacang.', 'nasi-lemak.webp'),
(2,  'Ayam Bakar Madu',     'Ayam bakar berbumbu madu yang manis gurih, disajikan dengan nasi putih dan lalapan.', 'nasi-lemak.webp'),
(3,  'Tumis Buncis Sapi',   'Buncis segar ditumis dengan irisan daging sapi, bumbu bawang, dan sedikit saus tiram.', 'nasi-lemak.webp'),
(4,  'Lontong Sayur',       'Lontong dengan kuah sayur santan, telur, tahu, dan taburan bawang goreng.', 'nasi-lemak.webp'),
(5,  'Ikan Goreng Kuning',  'Ikan goreng berbumbu kunyit yang renyah, dengan nasi hangat dan sambal terasi.', 'nasi-lemak.webp'),
(6,  'Sup Ayam Bening',     'Sup ayam kuah bening dengan wortel, kentang, dan seledri, hangat dan ringan.', 'nasi-lemak.webp'),
(7,  'Nasi Kuning Komplit', 'Nasi kuning harum dengan ayam suwir, telur dadar, tempe orek, dan kerupuk.', 'nasi-lemak.webp'),
(8,  'Rendang Sapi',        'Daging sapi dimasak perlahan dalam bumbu rendang kaya rempah, dengan nasi putih.', 'nasi-lemak.webp'),
(9,  'Capcay Seafood',      'Tumis aneka sayuran dengan udang dan cumi dalam kuah kental yang gurih.', 'nasi-lemak.webp'),
(10, 'Bubur Ayam',          'Bubur nasi lembut dengan ayam suwir, cakwe, kacang kedelai, dan kerupuk.', 'nasi-lemak.webp'),
(11, 'Ayam Geprek',         'Ayam goreng tepung renyah yang digeprek dengan sambal bawang pedas, plus nasi putih.', 'nasi-lemak.webp'),
(12, 'Soto Ayam',           'Soto kuah kuning dengan ayam suwir, soun, telur rebus, dan perasan jeruk nipis.', 'nasi-lemak.webp'),
(13, 'Nasi Goreng Kampung', 'Nasi goreng dengan teri, sayuran, dan telur mata sapi, berbumbu khas rumahan.', 'nasi-lemak.webp'),
(14, 'Ikan Bakar Jimbaran', 'Ikan bakar dengan bumbu Jimbaran segar, disajikan dengan nasi dan plecing kangkung.', 'nasi-lemak.webp'),
(15, 'Fu Yung Hai',         'Telur dadar tebal isi sayuran dan udang dengan saus asam manis.', 'nasi-lemak.webp'),
(16, 'Roti Bakar Telur',    'Roti bakar renyah dengan isian telur dan keju, cocok untuk sarapan akhir pekan.', 'nasi-lemak.webp'),
(17, 'Mie Goreng Spesial',  'Mie goreng dengan telur, sayuran, dan irisan ayam, dibumbui kecap manis.', 'nasi-lemak.webp'),
(18, 'Sate Ayam',           'Sate ayam bakar dengan bumbu kacang, lontong, dan acar timun.', 'nasi-lemak.webp'),
(19, 'Pancake Nutella',     'Pancake lembut dengan olesan Nutella dan potongan pisang.', 'nasi-lemak.webp'),
(20, 'Nasi Liwet',          'Nasi liwet gurih dengan ayam, teri, telur pindang, dan lalapan.', 'nasi-lemak.webp'),
(21, 'Bistik Sapi',         'Daging sapi empuk dengan saus kecap bawang, kentang goreng, dan sayuran rebus.', 'nasi-lemak.webp')
ON DUPLICATE KEY UPDATE
    menu_name   = VALUES(menu_name),
    description = VALUES(description),
    image       = VALUES(image);

-- 3. Paket Langganan (4 paket)
INSERT INTO packages (package_id, package_name, description, price, image, duration_days, applicable_days) VALUES
(1, 'Paket Hemat',        'Menu seimbang, harga terjangkau untuk kebutuhan harian kerja mahasiswa dan pekerja.', 125000, 'nasi-lemak.webp', 5,  'Senin-Jumat'),
(2, 'Paket Standar',      'Pilihan terbaik untuk kebutuhan makan harian yang lebih lengkap dan praktis.',        150000, 'nasi-lemak.webp', 5,  'Senin-Jumat'),
(3, 'Paket Premium',      'Menu lebih bervariasi dan bergizi untuk energi sepanjang minggu termasuk akhir pekan.', 185000, 'nasi-lemak.webp', 7,  'Senin-Minggu'),
(4, 'Paket Bulanan Hemat','Langganan makan harian selama 20 hari kerja dengan harga lebih hemat.',               450000, 'nasi-lemak.webp', 20, 'Senin-Jumat')
ON DUPLICATE KEY UPDATE
    package_name    = VALUES(package_name),
    description     = VALUES(description),
    price           = VALUES(price),
    image           = VALUES(image),
    duration_days   = VALUES(duration_days),
    applicable_days = VALUES(applicable_days);

-- 4. Jadwal Harian Historis: 19 Oktober - 1 November 2026 (14 hari x 3 slot = 42 baris)
INSERT INTO daily_menus (menu_id, `date`, slot) VALUES
(1,  '2026-10-19', 'Pagi'), (2,  '2026-10-19', 'Siang'), (3,  '2026-10-19', 'Malam'),
(4,  '2026-10-20', 'Pagi'), (5,  '2026-10-20', 'Siang'), (6,  '2026-10-20', 'Malam'),
(7,  '2026-10-21', 'Pagi'), (8,  '2026-10-21', 'Siang'), (9,  '2026-10-21', 'Malam'),
(10, '2026-10-22', 'Pagi'), (11, '2026-10-22', 'Siang'), (12, '2026-10-22', 'Malam'),
(13, '2026-10-23', 'Pagi'), (14, '2026-10-23', 'Siang'), (15, '2026-10-23', 'Malam'),
(16, '2026-10-24', 'Pagi'), (17, '2026-10-24', 'Siang'), (18, '2026-10-24', 'Malam'),
(19, '2026-10-25', 'Pagi'), (20, '2026-10-25', 'Siang'), (21, '2026-10-25', 'Malam'),
(1,  '2026-10-26', 'Pagi'), (2,  '2026-10-26', 'Siang'), (3,  '2026-10-26', 'Malam'),
(4,  '2026-10-27', 'Pagi'), (5,  '2026-10-27', 'Siang'), (6,  '2026-10-27', 'Malam'),
(7,  '2026-10-28', 'Pagi'), (8,  '2026-10-28', 'Siang'), (9,  '2026-10-28', 'Malam'),
(10, '2026-10-29', 'Pagi'), (11, '2026-10-29', 'Siang'), (12, '2026-10-29', 'Malam'),
(13, '2026-10-30', 'Pagi'), (14, '2026-10-30', 'Siang'), (15, '2026-10-30', 'Malam'),
(16, '2026-10-31', 'Pagi'), (17, '2026-10-31', 'Siang'), (18, '2026-10-31', 'Malam'),
(19, '2026-11-01', 'Pagi'), (20, '2026-11-01', 'Siang'), (21, '2026-11-01', 'Malam')
ON DUPLICATE KEY UPDATE
    menu_id = VALUES(menu_id);

-- 5. Pesanan Demo (Mencakup seluruh siklus status)
INSERT INTO orders (order_id, user_id, package_id, start_date, slot, delivery_address, notes, total_price, status, created_at) VALUES
(1, 2, 1, '2026-10-19', 'Siang', 'Jl. Melati No 12, RT 03/RW 05, Kel. Sungai Jawi, Kec. Batam Kota, Kota Batam', 'Jangan terlalu pedas', 125000, 'Selesai',             '2026-10-18 09:15:00'),
(2, 3, 3, '2026-10-26', 'Malam', 'Jl. Cempaka No. 31, Batam Center, Kota Batam',                                 'Titip di pos satpam',  185000, 'Selesai',             '2026-10-25 14:20:00'),
(3, 2, 2, '2026-11-09', 'Siang', 'Jl. Melati No 12, RT 03/RW 05, Kel. Sungai Jawi, Kec. Batam Kota, Kota Batam', NULL,                   150000, 'Aktif',               '2026-11-08 10:30:00'),
(4, 4, 1, '2026-11-16', 'Pagi',  'Jl. Melati No. 12, Sekupang, Kota Batam',                                      'Tanpa seledri',        125000, 'Menunggu Verifikasi', '2026-11-14 08:45:00'),
(5, 2, 3, '2026-11-17', 'Siang', 'Jl. Melati No 12, RT 03/RW 05, Kel. Sungai Jawi, Kec. Batam Kota, Kota Batam', NULL,                   185000, 'Belum Dibayar',       '2026-11-14 11:00:00'),
(6, 3, 2, '2026-10-19', 'Pagi',  'Jl. Cempaka No. 31, Batam Center, Kota Batam',                                 NULL,                   150000, 'Selesai',             '2026-10-17 16:00:00'),
(7, 2, 1, '2026-10-26', 'Pagi',  'Jl. Melati No 12, RT 03/RW 05, Kel. Sungai Jawi, Kec. Batam Kota, Kota Batam', 'Tanpa sambal',        125000, 'Selesai',             '2026-10-24 08:30:00')
ON DUPLICATE KEY UPDATE
    status      = VALUES(status),
    total_price = VALUES(total_price);

-- 6. Pembayaran Demo
INSERT INTO payments (payment_id, order_id, payment_proof, status, rejection_reason) VALUES
(1, 1, 'demo-proof-1.jpg', 'Valid', NULL),
(2, 2, 'demo-proof-2.jpg', 'Valid', NULL),
(3, 3, 'demo-proof-3.jpg', 'Valid', NULL),
(4, 4, 'demo-proof-4.jpg', NULL,    NULL),
(5, 6, 'demo-proof-6.jpg', 'Valid', NULL),
(6, 7, 'demo-proof-7.jpg', 'Valid', NULL)
ON DUPLICATE KEY UPDATE
    status           = VALUES(status),
    rejection_reason = VALUES(rejection_reason);

-- 7. Ulasan Demo (menu harus termasuk menu yang diterima pada pesanan terkait)
INSERT INTO reviews (review_id, order_id, menu_id, rating, comment, created_at) VALUES
(1, 1, 2,  5, 'Ayam bakar madunya empuk banget, bumbunya meresap sampai ke dalam. Pengiriman juga tepat waktu!', '2026-10-24 13:10:00'),
(2, 1, 5,  4, 'Ikan goreng kuningnya renyah dan sambal terasinya mantap, porsi pas untuk makan siang.',          '2026-10-24 13:12:00'),
(3, 2, 3,  5, 'Tumis buncis sapinya segar dan rasanya seperti masakan rumah sendiri. Recommended!',              '2026-11-02 19:30:00'),
(4, 2, 21, 5, 'Bistik sapinya empuk, saus kecap bawangnya pas dan kentang gorengnya renyah.',                   '2026-11-02 19:32:00'),
(5, 7, 1,  4, 'Nasi uduknya gurih dan sambal kacangnya pas untuk sarapan.',                                     '2026-11-02 08:10:00'),
(6, 7, 4,  2, 'Kuah lontong sayurnya agak hambar.',                                                             '2026-11-02 08:12:00'),
(7, 7, 10, 3, NULL,                                                                                              '2026-11-02 08:13:00')
ON DUPLICATE KEY UPDATE
    order_id = VALUES(order_id),
    menu_id  = VALUES(menu_id),
    rating   = VALUES(rating),
    comment  = VALUES(comment);