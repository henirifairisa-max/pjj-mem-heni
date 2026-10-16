<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: login.php");
    exit;
}

$pesan = "";

// 1. PROSES INPUT MATERI OLEH GURU
if (isset($_POST['tambah_materi'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_sumber = mysqli_real_escape_string($koneksi, $_POST['link_sumber']);
    $nama_file_db = null;

    if (!empty($_FILES['berkas']['name'])) {
        $nama_file   = $_FILES['berkas']['name'];
        $ukuran_file = $_FILES['berkas']['size'];
        $tmp_file    = $_FILES['berkas']['tmp_name'];
        
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_diperbolehkan = array('pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png');

        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            if ($ukuran_file <= 5242880) {
                $nama_file_db = time() . '_' . $nama_file;
                move_uploaded_file($tmp_file, 'uploads/' . $nama_file_db);
            } else {
                $pesan = "<p style='color:red;'>Gagal: Ukuran file maksimal 5MB.</p>";
            }
        } else {
            $pesan = "<p style='color:red;'>Gagal: Format berkas lampiran tidak didukung.</p>";
        }
    }

    if (empty($pesan)) {
        if (!empty($judul) && !empty($deskripsi)) {
            $query = "INSERT INTO materi (judul, deskripsi, link_sumber, nama_file) VALUES ('$judul', '$deskripsi', '$link_sumber', '$nama_file_db')";
            if (mysqli_query($koneksi, $query)) {
                $pesan = "<p style='color:green; font-weight:bold;'>Materi sukses diterbitkan!</p>";
            } else {
                $pesan = "<p style='color:red;'>Gagal: " . mysqli_error($koneksi) . "</p>";
            }
        }
    }
}

// 2. PROSES INPUT & UPDATE NILAI SISWA OLEH GURU
if (isset($_POST['simpan_nilai'])) {
    $id_pengumpulan = intval($_POST['id_pengumpulan']);
    $nilai = intval($_POST['nilai']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);

    $query_nilai = "UPDATE pengumpulan_tugas SET nilai = $nilai, catatan = '$catatan' WHERE id_pengumpulan = $id_pengumpulan";
    if (mysqli_query($koneksi, $query_nilai)) {
        $pesan = "<p style='color:green; font-weight:bold;'>✓ Nilai & catatan berhasil diperbarui!</p>";
    } else {
        $pesan = "<p style='color:red;'>Gagal menyimpan nilai: " . mysqli_error($koneksi) . "</p>";
    }
}

// AMBIL DATA REKAP ABSENSI
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
    <title>Dashboard Guru - E-Learning</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .container { max-width: 1000px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin: auto; }
        .box { border: 1px solid #ccc; padding: 20px; margin-bottom: 25px; border-radius: 5px; background: #fafafa; }
        input[type="text"], input[type="file"], textarea { width: 100%; padding: 8px; margin: 8px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; }
        button { background: #007BFF; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table, th, td { border: 1px solid #ddd; text-align: left; }
        th, td { padding: 12px; }
        th { background-color: #f2f2f2; }
        .status-hadir { color: green; font-weight: bold; }
        .status-izin { color: orange; font-weight: bold; }
        .status-sakit { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h2>
        Dashboard Guru: <?= htmlspecialchars($_SESSION['nama_guru'] ?? 'Guru'); ?>
        <a href="logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?')" style="float: right; padding: 8px 15px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: bold;">Keluar / Logout</a>
    </h2>
    <hr>
    
    <?= $pesan; ?>

    <!-- 1. FORM INPUT MATERI -->
    <div class="box">
        <h3> Buat Materi Pembelajaran & Upload File</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <label>Judul Materi:</label>
            <input type="text" name="judul" placeholder="Contoh: Tugas Mandiri - Matematika" required>

            <label>Deskripsi / Instruksi Tugas:</label>
            <textarea name="deskripsi" placeholder="Tuliskan petunjuk pengerjaan di sini..." required></textarea>

            <label>Link Eksternal (Opsional):</label>
            <input type="text" name="link_sumber" placeholder="Contoh: https://youtube.com...">

            <label>Lampiran Dokumen (PDF/Word/Gambar):</label>
            <input type="file" name="berkas">

            <button type="submit" name="tambah_materi">Terbitkan Materi & File</button>
        </form>
    </div>

    <!-- 2. KELOLA MATERI -->
    <div class="box">
        <h3>Kelola Materi Pembelajaran</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Judul Materi</th>
                    <th style="width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_list_materi = mysqli_query($koneksi, "SELECT id_materi, judul FROM materi ORDER BY id_materi DESC");
                $no_mat = 1;
                if ($query_list_materi && mysqli_num_rows($query_list_materi) > 0) {
                    while ($m = mysqli_fetch_assoc($query_list_materi)) {
                        echo "<tr>
                                <td>".$no_mat++."</td>
                                <td>".htmlspecialchars($m['judul'] ?? '')."</td>
                                <td>
                                    <a href='edit_materi.php?id=".$m['id_materi']."' style='color:#007BFF; font-weight:bold; text-decoration:none; margin-right:15px;'>Edit</a>
                                    <a href='hapus_materi.php?id=".$m['id_materi']."' onclick='return confirm(\"Yakin hapus?\")' style='color:#dc3545; font-weight:bold; text-decoration:none;'>Hapus</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' style='text-align:center;'>Belum ada materi.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- 3. ABSENSI SISWA -->
    <div class="box">
        <h3> Rekap Absensi Online Siswa
            <a href="cetak_absen.php" target="_blank" style="float: right; padding: 5px 15px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; font-weight: normal;">Cetak Rekap (PDF)</a>
        </h3>
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
                if ($query_absen && mysqli_num_rows($query_absen) > 0) {
                    while ($row = mysqli_fetch_assoc($query_absen)) {
                        $status_class = '';
                        if (($row['status'] ?? '') == 'Hadir') $status_class = 'status-hadir';
                        elseif (($row['status'] ?? '') == 'Izin') $status_class = 'status-izin';
                        elseif (($row['status'] ?? '') == 'Sakit') $status_class = 'status-sakit';
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nis'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($row['nama_siswa'] ?? ''); ?></td>
                            <td><?= isset($row['tanggal']) ? date('d-m-Y', strtotime($row['tanggal'])) : '-'; ?></td>
                            <td><?= htmlspecialchars($row['jam_absen'] ?? '-'); ?> WIB</td>
                            <td class="<?= $status_class; ?>"><?= htmlspecialchars($row['status'] ?? '-'); ?></td>
                        </tr>
                    <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data absensi.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

        <!-- 4. TUGAS MASUK & INPUT NILAI -->
    <div class="box">
        <h3> Dokumen Tugas Masuk Dari Siswa</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Siswa</th>
                    <th>Judul Materi</th>
                    <th>Waktu Kumpul</th>
                    <th>File Tugas</th>
                    <th style="width: 320px;">Input Nilai & Catatan Guru</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $query_tugas_masuk = mysqli_query($koneksi, "
                    SELECT pt.*, s.nama_siswa, m.judul 
                    FROM pengumpulan_tugas pt
                    LEFT JOIN siswa s ON pt.id_siswa = s.id_siswa
                    LEFT JOIN materi m ON pt.id_materi = m.id_materi
                    ORDER BY pt.waktu_kumpul DESC
                ");
                
                $no_tugas = 1;
                if ($query_tugas_masuk && mysqli_num_rows($query_tugas_masuk) > 0) {
                    while ($tugas = mysqli_fetch_assoc($query_tugas_masuk)) {
                        $nama_siswa = !empty($tugas['nama_siswa']) ? htmlspecialchars($tugas['nama_siswa']) : 'Siswa Tidak Diketahui';
                        $judul_materi = !empty($tugas['judul']) ? htmlspecialchars($tugas['judul']) : 'Materi Tidak Diketahui';
                        $waktu_kumpul = isset($tugas['waktu_kumpul']) ? date('d-m-Y H:i', strtotime($tugas['waktu_kumpul'])) : '-';
                        $nilai_tampil = $tugas['nilai'] !== null ? $tugas['nilai'] : '';
                        $catatan_tampil = htmlspecialchars($tugas['catatan'] ?? '');
                    ?>
                        <tr>
                            <td><?= $no_tugas++; ?></td>
                            <td><?= $nama_siswa; ?></td>
                            <td><?= $judul_materi; ?></td>
                            <td><?= $waktu_kumpul; ?> WIB</td>
                            <td>
                                <a href="uploads/<?= $tugas['file_tugas']; ?>" download style="color: #28a745; font-weight: bold; text-decoration: none;">Download</a>
                            </td>
                            <td>
                                <form action="" method="POST" style="margin: 0; display: flex; gap: 5px; align-items: center;">
                                    <input type="hidden" name="id_pengumpulan" value="<?= $tugas['id_pengumpulan']; ?>">
                                    <input type="number" name="nilai" placeholder="Nilai" min="0" max="100" style="width: 65px; padding: 5px;" value="<?= $nilai_tampil; ?>" required>
                                    <input type="text" name="catatan" placeholder="Catatan" style="width: 140px; padding: 5px; margin: 0;" value="<?= $catatan_tampil; ?>">
                                    <button type="submit" name="simpan_nilai" style="padding: 6px 10px; background: #28a745; font-size: 12px; border:none; color:white; border-radius:3px; cursor:pointer; font-weight:bold;">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Belum ada siswa yang mengumpulkan tugas.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
