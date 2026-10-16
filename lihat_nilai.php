<?php
include 'koneksi.php';
session_start();

// PROTEKSI HALAMAN SISWA - Hanya bisa diakses jika sudah login sebagai siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: login.php");
    exit;
}

$id_siswa_aktif = $_SESSION['id_siswa'];

// AMBIL DATA TUGAS YANG SUDAH DIKUMPULKAN OLEH SISWA BESERTA NILAINYA
$query_nilai = mysqli_query($koneksi, "
    SELECT pt.*, m.judul 
    FROM pengumpulan_tugas pt
    JOIN materi m ON pt.id_materi = m.id_materi
    WHERE pt.id_siswa = $id_siswa_aktif
    ORDER BY pt.waktu_kumpul DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai Tugas - E-Learning</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 850px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        .nav-links { margin-bottom: 20px; display: flex; gap: 15px; }
        .nav-links a { text-decoration: none; color: #007BFF; font-weight: bold; }
        .nav-links a:hover { text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; text-align: left; }
        th, td { padding: 12px; }
        th { background-color: #f2f2f2; }
        .nilai-tinggi { color: #28a745; font-weight: bold; font-size: 16px; }
        .nilai-belum { color: #e6a23c; font-style: italic; font-size: 14px; }
        .catatan { font-size: 13px; color: #666; margin-top: 4px; display: block; }
    </style>
</head>
<body>

<div class="container">
    <!-- Navigasi Internal Siswa -->
    <div class="nav-links">
        <a href="index.php">← Halaman Utama Absensi</a> | 
        <a href="materi.php">Ruang Belajar Materi</a>
    </div>

    <h2>Rekap Nilai Tugas Anda</h2>
    <p>Nama Siswa: <b><?= htmlspecialchars($_SESSION['nama_siswa']); ?></b></p>
    <hr>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Judul Tugas / Materi</th>
                <th>Waktu Mengumpulkan</th>
                <th style="width: 250px;">Hasil Nilai & Catatan Guru</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            if (mysqli_num_rows($query_nilai) > 0) {
                while ($row = mysqli_fetch_assoc($query_nilai)) {
                    $waktu_kumpul = date('d-m-Y H:i', strtotime($row['waktu_kumpul']));
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['judul']); ?></td>
                        <td><?= $waktu_kumpul; ?> WIB</td>
                        <td>
                            <?php if ($row['nilai'] !== null): ?>
                                <span class="nilai-tinggi">Skor: <?= $row['nilai']; ?></span>
                                <?php if (!empty($row['catatan'])): ?>
                                    <span class="catatan">💬 "<?= htmlspecialchars($row['catatan']); ?>"</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="nilai-belum">⏳ Belum diperiksa guru</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                } 
            } else {
                echo "<tr><td colspan='4' style='text-align:center; color:#777;'>Anda belum pernah mengumpulkan tugas apa pun.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
