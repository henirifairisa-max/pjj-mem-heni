<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

$query_absen = mysqli_query($koneksi, "
    SELECT absensi.*, siswa.nama_siswa, siswa.nis 
    FROM absensi 
    JOIN siswa ON absensi.id_siswa = siswa.id_siswa 
    ORDER BY absensi.tanggal DESC, absensi.jam_absen DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rekap Absensi Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h2 { margin: 0; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #000; text-align: left; }
        th, td { padding: 10px; font-size: 14px; }
        th { background-color: #f2f2f2; }
        /* Otomatis cetak saat halaman dimuat */
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="header">
    <h2>LAPORAN REKAP ABSENSI ONLINE SISWA
 <a href="cetak_absen.php" target="_blank" style="float: right; padding: 5px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Cetak Rekap (PDF)</a>
    </h2>
    <p>E-Learning Sekolah - Dicetak oleh: <?= $_SESSION['nama_guru']; ?></p>
    <hr style="border: 1px solid #000;">
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama Siswa</th>
            <th>Tanggal</th>
            <th>Jam Absen</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($query_absen)) {
            echo "<tr>
                <td>".$no++."</td>
                <td>".htmlspecialchars($row['nis'])."</td>
                <td>".htmlspecialchars($row['nama_siswa'])."</td>
                <td>".date('d-m-Y', strtotime($row['tanggal']))."</td>
                <td>".$row['jam_absen']." WIB</td>
                <td>".$row['status']."</td>
            </tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
