<?php
include 'koneksi.php';
session_start();

// PROTEKSI HALAMAN SISWA
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}
// PENTING: Hapus kode $_SESSION['id_siswa'] = 1; (jika sebelumnya ada)


$id_siswa = $_SESSION['id_siswa'];
$pesan = "";

// Proses Input Absen
if (isset($_POST['absen'])) {
    $status = $_POST['status'];
    $tanggal = date('Y-m-d');
    $jam = date('H:i:s');

    // Cek apakah sudah absen hari ini
    $cek = mysqli_query($koneksi, "SELECT * FROM absensi WHERE id_siswa='$id_siswa' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<p style='color:red;'>Anda sudah melakukan absensi hari ini!</p>";
    } else {
        $query = "INSERT INTO absensi (id_siswa, tanggal, jam_absen, status) VALUES ('$id_siswa', '$tanggal', '$jam', '$status')";
        if (mysqli_query($koneksi, $query)) {
            $pesan = "<p style='color:green;'>Absensi berhasil disimpan pada jam $jam!</p>";
        } else {
            $pesan = "<p style='color:red;'>Gagal menyimpan absensi.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>E-Learning & Absensi Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        nav { margin-bottom: 20px; }
        nav a { margin-right: 15px; text-decoration: none; color: #007BFF; font-weight: bold; }
        .box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
        button { background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Selamat Datang, <?= $_SESSION['nama_siswa']; ?>!
        <!-- Tambahkan kode menu navigasi ini di index.php -->
<div style="background: #fff; padding: 10px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <a href="materi.php" style="margin-right: 15px; text-decoration: none; color: #007BFF; font-weight: bold;">📖 Lihat Materi & Tugas</a> | 
    <a href="lihat_nilai.php" style="margin-left: 15px; text-decoration: none; color: #28a745; font-weight: bold;">📊 Lihat Rekap Nilai Tugas</a>
</div>


        <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')" style="float: right; padding: 8px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold;">Keluar / Logout</a>
    </h2>
    <hr>
    
    <!-- Bagian Menu Navigasi atau Form Absensi Siswa Selanjutnya -->
    
    <nav>
        <a href="index.php">Absensi Online</a> | 
        <a href="materi.php">Pembelajaran Daring</a>
    </nav>

    <div class="box">
        <h3>Form Absensi Online</h3>
        <?= $pesan; ?>
        <form action="" method="POST">
            <label>Pilih Status Kehadiran:</label><br><br>
            <input type="radio" name="status" value="Hadir" checked> Hadir <br>
            <input type="radio" name="status" value="Izin"> Izin <br>
            <input type="radio" name="status" value="Sakit"> Sakit <br><br>
            <!-- Tempat untuk menampilkan jam otomatis (real-time) -->
<div style="background: #eef2f7; padding: 15px; border-radius: 6px; text-align: center; margin: 15px 0; border: 1px solid #dcdfe6;">
    <span style="display: block; font-size: 14px; color: #606266; font-weight: bold;">WAKTU SERVER SEKARANG:</span>
    <span id="jam-otomatis" style="font-size: 28px; font-weight: bold; color: #303133; letter-spacing: 1px;">00:00:00 WIB</span>
</div>

            <button type="submit" name="absen">Kirim Absensi</button>
        </form>
    </div>
</div>

</body>
</html>
