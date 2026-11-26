<?php

$faqs = [
    [
        'question' => 'Bagaimana cara memesan di MealMate?',
        'answer' => '
            <p>MealMate menyediakan paket langganan makan harian, bukan pemesanan menu satuan. Caranya:</p>
            <ol>
                <li>Masuk ke akun Anda (atau daftar jika belum punya), lalu buka halaman Menu dan pilih paket yang diinginkan.</li>
                <li>Isi tanggal mulai, waktu antar (Pagi, Siang, atau Malam), dan alamat pengantaran. Catatan tambahan boleh diisi, boleh juga dikosongkan.</li>
                <li>Klik tombol pesan, lalu transfer sesuai total pembayaran dan unggah bukti transfer.</li>
                <li>Setelah pembayaran diperiksa admin, pesanan Anda aktif dan makanan diantar sesuai jadwal.</li>
            </ol>',
    ],
    [
        'question' => 'Bagaimana cara membayar pesanan?',
        'answer' => '
            <p>Pembayaran dilakukan dengan transfer bank. Nomor rekening tujuan dan total yang harus dibayar tampil di halaman pembayaran.</p>
            <p>Setelah transfer, unggah foto atau tangkapan layar bukti transfer (format JPG atau PNG, maksimal 2 MB). Ongkos kirim gratis, jadi total pembayaran sama dengan harga paket.</p>',
    ],
    [
        'question' => 'Bagaimana cara mengatur jadwal pengantaran?',
        'answer' => '
            <p>Jadwal diatur sendiri oleh Anda pada halaman pemesanan (checkout), setelah memilih paket. Ada dua hal yang perlu dipilih:</p>
            <p class="faq-sub">1. Tanggal mulai</p>
            <p>Ini adalah hari pertama Anda menerima makanan.</p>
            <ul>
                <li>Paling cepat <strong>besok</strong>. Pemesanan untuk hari ini tidak bisa dilakukan.</li>
                <li>Untuk paket <strong>Senin-Jumat</strong>, tanggal mulai harus hari kerja. Jika Anda memilih Sabtu atau Minggu, akan muncul peringatan dan pesanan tidak bisa dilanjutkan.</li>
                <li>Untuk paket <strong>Senin-Minggu</strong>, Anda bebas memilih hari apa pun.</li>
            </ul>
            <p class="faq-sub">2. Waktu antar</p>
            <p>Pilih satu waktu pengantaran yang berlaku setiap hari selama masa langganan:</p>
            <ul>
                <li><strong>Pagi:</strong> 06.00-09.00 WIB</li>
                <li><strong>Siang:</strong> 11.00-13.00 WIB</li>
                <li><strong>Malam:</strong> 17.00-20.00 WIB</li>
            </ul>
            <p class="faq-sub">Tanggal selesai</p>
            <p>Tidak perlu dihitung sendiri. Tanggal selesai muncul otomatis di ringkasan pesanan dan langsung berubah saat Anda mengganti tanggal mulai. Perhitungannya mengikuti lama paket, dan untuk paket Senin-Jumat, hari Sabtu dan Minggu tidak dihitung.</p>
            <p><em>Contoh:</em> paket 5 hari (Senin-Jumat) yang dimulai Senin akan selesai pada Jumat di minggu yang sama. Jika dimulai Rabu, akan selesai Selasa di minggu berikutnya.</p>
            <p><strong>Perlu diingat:</strong> tanggal mulai dan waktu antar tidak bisa diubah setelah pesanan dibuat, jadi pastikan pilihan Anda sudah benar sebelum menekan tombol pesan.</p>',
    ],
    [
        'question' => 'Apakah saya bisa mengubah jadwal pesanan?',
        'answer' => '
            <p>Setelah pesanan dibuat, tanggal mulai dan waktu antar tidak bisa diubah. Jika pesanan belum dibayar, Anda bisa membatalkannya lalu membuat pesanan baru dengan jadwal yang diinginkan.</p>',
    ],
    [
        'question' => 'Bagaimana cara membatalkan pesanan?',
        'answer' => '
            <p>Pesanan hanya bisa dibatalkan selama belum dibayar, yaitu sebelum Anda mengirim bukti transfer.</p>
            <p>Buka halaman Pesanan Saya, pilih pesanan yang dimaksud, lalu klik "Batalkan Pesanan". Setelah bukti transfer dikirim, pesanan tidak bisa dibatalkan lagi.</p>',
    ],
    [
        'question' => 'Bagaimana jika bukti pembayaran saya ditolak?',
        'answer' => '
            <p>Admin akan menolak bukti pembayaran yang bermasalah, misalnya foto buram atau nominal tidak sesuai. Alasannya bisa Anda lihat di halaman detail pesanan.</p>
            <p>Pesanan yang ditolak tidak bisa dibayar ulang, jadi silakan buat pesanan baru dan unggah bukti transfer yang benar.</p>',
    ],
    [
        'question' => 'Apa arti status pesanan saya?',
        'answer' => '
            <ul>
                <li><strong>Belum Dibayar:</strong> pesanan sudah dibuat, menunggu pembayaran Anda.</li>
                <li><strong>Menunggu Verifikasi:</strong> bukti transfer sedang diperiksa admin.</li>
                <li><strong>Aktif:</strong> pembayaran diterima, makanan diantar sesuai jadwal.</li>
                <li><strong>Selesai:</strong> masa langganan sudah berakhir.</li>
                <li><strong>Ditolak:</strong> bukti pembayaran tidak diterima.</li>
                <li><strong>Dibatalkan:</strong> pesanan dibatalkan sebelum dibayar.</li>
            </ul>',
    ],
    [
        'question' => 'Bagaimana cara memberi ulasan?',
        'answer' => '
            <p>Ulasan bisa diberikan setelah pesanan berstatus Selesai. Buka detail pesanan, klik "Berikan Ulasan", lalu beri bintang 1-5 untuk menu yang ingin Anda nilai.</p>
            <p>Komentar boleh diisi atau dikosongkan. Setiap menu hanya bisa diulas satu kali per pesanan.</p>',
    ],
];

render('customer/faq', [
    'title' => 'FAQ - MealMate',
    'page' => 'faq',
    'faqs' => $faqs,
]);