<?php
include 'koneksi.php';
$pesan = "";

if (isset($_POST['register_guru'])) {
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_guru']);
    $password = $_POST['password'];

    if (!empty($nip) && !empty($nama) && !empty($password)) {
        // Cek apakah NIP sudah terdaftar sebelumnya agar tidak ganda
        $cek_nip = mysqli_query($koneksi, "SELECT nip FROM guru WHERE nip = '$nip'");
        if (mysqli_num_rows($cek_nip) > 0) {
            $pesan = "<p style='color:red;'>Gagal: NIP Guru sudah terdaftar di sistem!</p>";
        } else {
            // Proses enkripsi/hashing password secara otomatis
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Simpan data guru baru ke database
            $query_reg = "INSERT INTO guru (nip, nama_guru, password) VALUES ('$nip', '$nama', '$password_hash')";
            if (mysqli_query($koneksi, $query_reg)) {
                $pesan = "<p style='color:green; font-weight:bold;'>Registrasi Guru Berhasil! Silakan <a href='login.php'>Login disini</a></p>";
            } else {
                $pesan = "<p style='color:red;'>Terjadi kesalahan saat menyimpan data.</p>";
            }
        }
    } else {
        $pesan = "<p style='color:red;'>Semua kolom wajib diisi!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun Guru Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; height: 100vh; justify-content: center; align-items: center; margin: 0; }
        .reg-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #007BFF; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 16px; }
        button:hover { background: #0056b3; }
        .link-login { text-align: center; margin-top: 15px; display: block; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2>Daftar Akun Guru</h2>
    <?= $pesan; ?>
    <form action="" method="POST">
        <label for="nip">Nomor Induk Pegawai (NIP):</label>
        <input type="text" id="nip" name="nip" placeholder="Contoh: 19880101" required>

        <label for="nama_guru">Nama Lengkap Guru beserta Gelar:</label>
        <input type="text" id="nama_guru" name="nama_guru" placeholder="Contoh: Ahmad, S.Pd." required>

        <label for="password">Buat Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password Baru" required>

        <button type="submit" name="register_guru">Daftar Akun Guru</button>
    </form>
    <a href="login.php" class="link-login">Sudah punya akun? Login disini</a>
</div>

</body>
</html>
