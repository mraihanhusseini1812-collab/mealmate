<?php
require_once __DIR__ . '/../models/PackageModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

requireRole('Pelanggan');

$packageId = (int) ($_GET['package_id'] ?? $_POST['package_id'] ?? 0);
$package = $packageId > 0 ? getPackageById($packageId) : null;

if (!$package) {
    setFlash('danger', 'Paket langganan yang dipilih tidak ditemukan.');
    redirect('index.php?page=menu#packages-section');
}

$currentUserId = (int) $_SESSION['user_id'];
$userRecord = findUserById($currentUserId);

$minDateObj = new DateTimeImmutable('tomorrow');
if ($package['applicable_days'] === 'Senin-Jumat') {
    while ((int) $minDateObj->format('N') > 5) {
        $minDateObj = $minDateObj->modify('+1 day');
    }
}
$minStartDate = $minDateObj->format('Y-m-d');
$validSlots = ['Pagi', 'Siang', 'Malam'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = trim((string) ($_POST['start_date'] ?? ''));
    $slot = trim((string) ($_POST['slot'] ?? ''));
    $deliveryAddress = trim((string) ($_POST['delivery_address'] ?? ''));
    $notes = trim((string) ($_POST['notes'] ?? ''));

    $errors = [];
    $old = [
        'start_date' => $startDate,
        'slot' => $slot,
        'delivery_address' => $deliveryAddress,
        'notes' => $notes,
    ];

    $parsedDate = DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
    $isDateValid = $parsedDate && $parsedDate->format('Y-m-d') === $startDate;

    if ($startDate === '') {
        $errors['start_date'] = 'Tanggal mulai langganan wajib diisi.';
    } elseif (!$isDateValid) {
        $errors['start_date'] = 'Format tanggal mulai tidak valid.';
    } elseif ($startDate < date('Y-m-d', strtotime('+1 day'))) {
        $errors['start_date'] = 'Tanggal mulai minimal adalah besok (' . date('d/m/Y', strtotime('+1 day')) . ').';
    } elseif ($package['applicable_days'] === 'Senin-Jumat' && (int) $parsedDate->format('N') > 5) {
        $errors['start_date'] = 'Paket Senin - Jumat tidak dapat dimulai pada hari Sabtu atau Minggu.';
    }

    if ($slot === '' || !in_array($slot, $validSlots, true)) {
        $errors['slot'] = 'Pilih slot waktu pengantaran yang valid (Pagi, Siang, atau Malam).';
    }

    if ($deliveryAddress === '') {
        $errors['delivery_address'] = 'Alamat pengantaran wajib diisi.';
    } elseif (mb_strlen($deliveryAddress) > 500) {
        $errors['delivery_address'] = 'Alamat pengantaran maksimal 500 karakter.';
    }

    if (mb_strlen($notes) > 500) {
        $errors['notes'] = 'Catatan tambahan maksimal 500 karakter.';
    }

    if (!empty($errors)) {
        redirectWithErrors(
            'index.php?page=checkout&package_id=' . (int) $package['package_id'],
            $errors,
            $old,
            'Mohon periksa kembali data pengantaran pesanan Anda.'
        );
    }

    try {
        $newOrderId = createCustomerOrder(
            $currentUserId,
            (int) $package['package_id'],
            $startDate,
            $slot,
            $deliveryAddress,
            $notes !== '' ? $notes : null,
            (int) $package['price']
        );
        setFlash('success', 'Pesanan berhasil dibuat. Silakan periksa rincian pesanan dan lanjutkan ke pembayaran.');
        redirect('index.php?page=order-detail&id=' . $newOrderId);
    } catch (Throwable $e) {
        redirectWithErrors(
            'index.php?page=checkout&package_id=' . (int) $package['package_id'],
            [],
            $old,
            'Gagal menyimpan pesanan karena gangguan sistem. Silakan coba lagi.'
        );
    }
}

$errors = getValidationErrors();
$old = getOldInput();

$selectedStartDate = $old['start_date'] ?? $minStartDate;
$selectedSlot = $old['slot'] ?? 'Pagi';
$selectedAddress = $old['delivery_address'] ?? (string) ($userRecord['address'] ?? '');
$selectedNotes = $old['notes'] ?? '';

$parsedSelectedDate = DateTimeImmutable::createFromFormat('Y-m-d', $selectedStartDate);
$previewStartDate = ($parsedSelectedDate && $parsedSelectedDate->format('Y-m-d') === $selectedStartDate)
    ? $selectedStartDate
    : $minStartDate;

$estimatedEndDate = calculateOrderEndDate(
    $previewStartDate,
    (int) $package['duration_days'],
    $package['applicable_days']
);

render('customer/checkout', [
    'title' => 'Checkout Langganan - MealMate',
    'page' => 'checkout',
    'hideFooter' => true,
    'package' => $package,
    'minStartDate' => $minStartDate,
    'selectedStartDate' => $selectedStartDate,
    'selectedSlot' => $selectedSlot,
    'selectedAddress' => $selectedAddress,
    'selectedNotes' => $selectedNotes,
    'estimatedEndDate' => $estimatedEndDate,
    'errors' => $errors,
]);
