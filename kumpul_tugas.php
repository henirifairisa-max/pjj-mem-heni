<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

$id_materi = isset($_GET['id']) ? intval($_GET['id']) : 0;
$pesan = "";

$query_materi = mysqli_query($koneksi, "SELECT judul FROM materi WHERE id_materi = $id_materi");
$materi = mysqli_fetch_assoc($query_materi);

if (!$materi) {
    die("Materi tidak ditemukan.");
}

if (isset($_POST['submit_tugas'])) {
    if (!empty($_FILES['file_jawaban']['name'])) {
        $nama_file   = $_FILES['file_jawaban']['name'];
        $ukuran_file = $_FILES['file_jawaban']['size'];
        $tmp_file    = $_FILES['file_jawaban']['tmp_name'];
        
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diperbolehkan = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'zip');

        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            if ($ukuran_file <= 5242880) { 
                $nama_file_db = 'tugas_' . time() . '_' . $nama_file;
                $folder_tujuan = 'uploads/' . $nama_file_db;

                // Pastikan folder uploads tersedia
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($tmp_file, $folder_tujuan)) {
                    $id_siswa = $_SESSION['id_siswa'];
                    $query_simpan = "INSERT INTO pengumpulan_tugas (id_materi, id_siswa, file_tugas) VALUES ($id_materi, $id_siswa, '$nama_file_db')";
                    
                    if (mysqli_query($koneksi, $query_simpan)) {
                        $pesan = "<p style='color:green; font-weight:bold;'>✓ Tugas berhasil terkirim ke Guru!</p>";
                    } else {
                        $pesan = "<p style='color:red;'>Gagal menyimpan ke database: " . mysqli_error($koneksi) . "</p>";
                    }
                } else {
                    $pesan = "<p style='color:red;'>Gagal mengunggah file ke server. Periksa izin folder uploads Anda.</p>";
                }
            } else {
                $pesan = "<p style='color:red;'>Ukuran file maksimal 5MB.</p>";
            }
        } else {
            $pesan = "<p style='color:red;'>Format tidak didukung! Gunakan PDF/Word/Gambar/ZIP.</p>";
        }
    } else {
        $pesan = "<p style='color:red;'>Silakan pilih file terlebih dahulu.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kumpul Tugas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 500px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        input[type="file"] { width: 100%; padding: 10px; margin: 15px 0; border: 1px solid #ccc; }
        button { background: #007BFF; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
        .back-link { display: inline-block; margin-bottom: 15px; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>
<div class="container">
    <a href="materi.php" class="back-link">← Kembali ke Ruang Materi</a>
    <h2>Pengumpulan Tugas</h2>
    <p><strong>Materi:</strong> <?= htmlspecialchars($materi['judul']); ?></p>
    <hr>
    <?= $pesan; ?>
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Pilih File Jawaban (PDF, Word, Gambar, ZIP - Maks 5MB):</label>
        <input type="file" name="file_jawaban" required>
        <button type="submit" name="submit_tugas">Kirim Jawaban</button>
    </form>
</div>
</body>
</html>
