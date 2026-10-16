<?php
include 'koneksi.php';
$pesan = "";

if (isset($_POST['register'])) {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_siswa']);
    $password = $_POST['password'];

    if (!empty($nis) && !empty($nama) && !empty($password)) {
        // Cek apakah NIS sudah terdaftar sebelumnya
        $cek_nis = mysqli_query($koneksi, "SELECT nis FROM siswa WHERE nis = '$nis'");
        if (mysqli_num_rows($cek_nis) > 0) {
            $pesan = "<p style='color:red;'>Gagal: NIS sudah terdaftar di sistem!</p>";
        } else {
            // Enkripsi password menggunakan Bcrypt
            $password_aman = password_hash($password, PASSWORD_BCRYPT);
            
            $query_reg = "INSERT INTO siswa (nis, nama_siswa, password) VALUES ('$nis', '$nama', '$password_aman')";
            if (mysqli_query($koneksi, $query_reg)) {
                $pesan = "<p style='color:green; font-weight:bold;'>Registrasi Berhasil! Silakan <a href='login.php'>Login disini</a></p>";
            } else {
                $pesan = "<p style='color:red;'>Terjadi kesalahan data.</p>";
            }
        }
    } else {
        $pesan = "<p style='color:red;'>Semua data wajib diisi!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Akun Siswa Baru</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; height: 100vh; justify-content: center; align-items: center; margin: 0; }
        .reg-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #28a745; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 16px; }
        button:hover { background: #218838; }
        .link-login { text-align: center; margin-top: 15px; display: block; text-decoration: none; color: #007BFF; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2>Daftar Akun Siswa</h2>
    <?= $pesan; ?>
    <form action="" method="POST">
        <label for="nis">Nomor Induk Siswa (NIS):</label>
        <input type="text" id="nis" name="nis" placeholder="Contoh: 1002" required>

        <label for="nama_siswa">Nama Lengkap Siswa:</label>
        <input type="text" id="nama_siswa" name="nama_siswa" placeholder="Masukkan Nama Lengkap" required>

        <label for="password">Buat Password:</label>
        <input type="password" id="password" name="password" placeholder="Minimal 6 Karakter" required>

        <button type="submit" name="register">Daftar Sekarang</button>
    </form>
    <a href="login.php" class="link-login">Sudah punya akun? Login disini</a>
</div>

</body>
</html>
