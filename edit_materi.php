<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = mysqli_query($koneksi, "SELECT * FROM materi WHERE id_materi = $id");
$materi = mysqli_fetch_assoc($query);

if (!$materi) {
    die("Materi tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['update_materi'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_sumber = mysqli_real_escape_string($koneksi, $_POST['link_sumber']);
    $nama_file_db = $materi['nama_file'];

    if (!empty($_FILES['berkas']['name'])) {
        $nama_file   = $_FILES['berkas']['name'];
        $ukuran_file = $_FILES['berkas']['size'];
        $tmp_file    = $_FILES['berkas']['tmp_name'];
        
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diperbolehkan = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');

        if (in_array($ekstensi, $ekstensi_diperbolehkan) && $ukuran_file <= 5242880) {
            // Hapus file lama jika ada
            if (!empty($materi['nama_file']) && file_exists("uploads/" . $materi['nama_file'])) {
                unlink("uploads/" . $materi['nama_file']);
            }
            $nama_file_db = time() . '_' . $nama_file;
            move_uploaded_file($tmp_file, 'uploads/' . $nama_file_db);
        } else {
            $pesan = "<p style='color:red;'>Gagal upload file (Cek format/ukuran maks 5MB).</p>";
        }
    }

    if (empty($pesan)) {
        $query_update = "UPDATE materi SET judul='$judul', deskripsi='$deskripsi', link_sumber='$link_sumber', nama_file='$nama_file_db' WHERE id_materi=$id";
        if (mysqli_query($koneksi, $query_update)) {
            header("Location: guru.php");
            exit;
        } else {
            $pesan = "<p style='color:red;'>Gagal update data.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Materi</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        input[type="text"], input[type="file"], textarea { width: 100%; padding: 8px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; }
        button { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Materi Pembelajaran</h2>
    <a href="guru.php">← Kembali</a>
    <hr>
    <?= $pesan; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Judul Materi:</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($materi['judul']); ?>" required>

        <label>Deskripsi:</label>
        <textarea name="deskripsi" required><?= htmlspecialchars($materi['deskripsi']); ?></textarea>

        <label>Link Eksternal:</label>
        <input type="text" name="link_sumber" value="<?= htmlspecialchars($materi['link_sumber']); ?>">

        <label>Ganti Lampiran (Kosongkan jika tidak ingin mengubah):</label>
        <input type="file" name="berkas">
        
        <button type="submit" name="update_materi">Simpan Perubahan</button>
    </form>
</div>
</body>
</html>
