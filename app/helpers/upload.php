<?php

function validateAndUploadImage(
    array $file,
    string $targetDir,
    string $prefix = 'img_',
    int $maxBytes = 2097152,
    array $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png']
): array {
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [
            'ok' => false,
            'filename' => null,
            'error' => 'Silakan pilih berkas terlebih dahulu.',
        ];
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > $maxBytes) {
        $maxMb = round($maxBytes / 1048576, 1);
        return [
            'ok' => false,
            'filename' => null,
            'error' => "Ukuran berkas melebihi batas maksimal ({$maxMb} MB).",
        ];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [
            'ok' => false,
            'filename' => null,
            'error' => 'Terjadi kesalahan saat mengunggah berkas. Silakan coba lagi.',
        ];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!isset($allowedMimes[$mimeType])) {
        $formats = strtoupper(implode('/', array_values($allowedMimes)));
        $maxMb = round($maxBytes / 1048576, 1);
        return [
            'ok' => false,
            'filename' => null,
            'error' => "Format berkas tidak sesuai ketentuan (wajib {$formats}, maksimal {$maxMb} MB).",
        ];
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $ext = $allowedMimes[$mimeType];
    $fileName = $prefix . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $destPath = rtrim($targetDir, '/') . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return [
            'ok' => false,
            'filename' => null,
            'error' => 'Gagal menyimpan berkas ke server. Silakan coba lagi.',
        ];
    }

    return [
        'ok' => true,
        'filename' => $fileName,
        'error' => null,
    ];
}
