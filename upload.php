<?php
// Helper upload bukti foto pengaduan. Butuh auth.php (fungsi e()).

const BUKTI_MAKS_BYTE = 2097152; // 2 MB

function folder_bukti(): string
{
    return __DIR__ . '/uploads/bukti/';
}

/**
 * Simpan foto bukti dari $_FILES['foto'].
 * Return: nama file baru, atau null (tidak ada file / gagal -> cek $error).
 */
function simpan_bukti(array $file, string &$error): ?string
{
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // foto bersifat opsional
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
        $error = "Ukuran foto maksimal 2 MB.";
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Upload foto gagal (kode " . (int)$file['error'] . "). Coba lagi.";
        return null;
    }
    if ($file['size'] > BUKTI_MAKS_BYTE) {
        $error = "Ukuran foto maksimal 2 MB.";
        return null;
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        $error = "File upload tidak valid.";
        return null;
    }

    // Cek isi file benar-benar gambar (bukan hanya dari ekstensi)
    $info = @getimagesize($file['tmp_name']);
    $tipe = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    if ($info === false || !isset($tipe[$info['mime']])) {
        $error = "Foto harus berformat JPG, PNG, atau WEBP.";
        return null;
    }

    $folder = folder_bukti();
    if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
        $error = "Folder uploads/bukti tidak bisa dibuat.";
        return null;
    }

    // Nama acak: mencegah tabrakan nama dan nama file berbahaya
    $nama = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $tipe[$info['mime']];

    if (!move_uploaded_file($file['tmp_name'], $folder . $nama)) {
        $error = "Foto gagal disimpan di server.";
        return null;
    }
    return $nama;
}

function hapus_bukti(?string $nama): void
{
    if ($nama === null || $nama === '') {
        return;
    }
    $path = folder_bukti() . basename($nama);
    if (is_file($path)) {
        @unlink($path);
    }
}

// Thumbnail yang bisa diklik untuk memperbesar; "-" jika tidak ada foto
function tampil_bukti(?string $nama, string $root = '../'): string
{
    if ($nama === null || $nama === '' || !is_file(folder_bukti() . basename($nama))) {
        return '<span class="kosong-teks">-</span>';
    }
    $url = e($root . 'uploads/bukti/' . basename($nama));
    return '<a href="' . $url . '" target="_blank" title="Klik untuk memperbesar">'
         . '<img src="' . $url . '" alt="Bukti foto" class="thumb"></a>';
}
