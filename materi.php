<?php
include 'koneksi.php';
session_start();

// Ambil semua daftar materi dari database
$query_materi = mysqli_query($koneksi, "SELECT * FROM materi ORDER BY id_materi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ruang Belajar Siswa - E-Learning</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        .materi-card { border-left: 5px solid #007BFF; background: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .materi-card h3 { margin-top: 0; color: #333; }
        .btn-link { display: inline-block; padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 10px; }
        .btn-link:hover { background: #218838; }
        .btn-download { display: inline-block; padding: 6px 12px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn-download:hover { background: #c82333; }
        .nav-link { margin-bottom: 20px; display: inline-block; text-decoration: none; color: #007BFF; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="nav-link">← Kembali ke Halaman Utama Absensi</a>
    <h2> Ruang Materi & Tugas Pembelajaran</h2>
    <hr><br>

    <?php 
    if (mysqli_num_rows($query_materi) > 0) {
        while ($materi = mysqli_fetch_assoc($query_materi)) {
        ?>
            <div class="materi-card">
                <h3><?= htmlspecialchars($materi['judul']); ?></h3>
                <p><?= nl2br(htmlspecialchars($materi['deskripsi'])); ?></p>
                
                <!-- Tampilkan Tombol Link Eksternal jika ada -->
                <?php if (!empty($materi['link_sumber'])): ?>
                    <a href="<?= htmlspecialchars($materi['link_sumber']); ?>" target="_blank" class="btn-link"> Buka Link Tambahan</a>
                <?php endif; ?>

                <!-- Tampilkan Tombol Download Berkas jika ada file terunggah -->
                
<?php if (!empty($materi['nama_file'])): ?>
    <a href="uploads/<?= $materi['nama_file']; ?>" download class="btn-download"> Download Lampiran Tugas</a>
    <!-- TOMBOL BARU UNTUK KUMPUL TUGAS -->
    <a href="kumpul_tugas.php?id=<?= $materi['id_materi']; ?>" style="display: inline-block; padding: 6px 12px; background: #007BFF; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;"> Kumpul Tugas Di Sini</a>
<?php endif; ?>

            </div>
        <?php 
        } 
    } else {
        echo "<p style='text-align:center; color:#777;'>Belum ada materi pembelajaran yang diterbitkan oleh guru.</p>";
    }
    ?>
</div>

</body>
</html>
