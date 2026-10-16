<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cari tahu apakah ada file terlampir untuk dihapus dari folder uploads
$query = mysqli_query($koneksi, "SELECT nama_file FROM materi WHERE id_materi = $id");
$data = mysqli_fetch_assoc($query);

if ($data) {
    if (!empty($data['nama_file']) && file_exists("uploads/" . $data['nama_file'])) {
        unlink("uploads/" . $data['nama_file']); // Hapus file dari server
    }
    
    mysqli_query($koneksi, "DELETE FROM materi WHERE id_materi = $id");
}

header("Location: guru.php");
exit;
