<?php
include 'koneksi.php';
session_start();

// Jika sudah login, langsung alihkan ke halaman masing-masing
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'guru') header("Location: guru.php");
    if ($_SESSION['role'] == 'siswa') header("Location: index.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $role = $_POST['role'];
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    if ($role == 'guru') {
        // Cek login Guru berdasarkan NIP
        $query = mysqli_query($koneksi, "SELECT * FROM guru WHERE nip = '$username'");
        $data  = mysqli_fetch_assoc($query);
        
        if ($data && password_verify($password, $data['password'])) {
            $_SESSION['id_guru'] = $data['id_guru'];
            $_SESSION['nama_guru'] = $data['nama_guru'];
            $_SESSION['role'] = 'guru';
            header("Location: guru.php");
            exit;
        } else {
            $error = "NIP atau Password Guru salah!";
        }
    } else if ($role == 'siswa') {
        // Cek login Siswa berdasarkan NIS
        $query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis = '$username'");
        $data  = mysqli_fetch_assoc($query);
        
        if ($data && password_verify($password, $data['password'])) {
            $_SESSION['id_siswa'] = $data['id_siswa'];
            $_SESSION['nama_siswa'] = $data['nama_siswa'];
            $_SESSION['role'] = 'siswa';
            header("Location: index.php");
            exit;
        } else {
            $error = "NIS atau Password Siswa salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login E-Learning Sekolah</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; height: 100vh; justify-content: center; align-items: center; margin: 0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        label { font-weight: bold; display: block; margin-top: 10px; }
        input[type="text"], input[type="password"], select { width: 100%; padding: 10px; margin: 5px 0 15px 0; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; background: #007BFF; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 4px; font-weight: bold; font-size: 16px; }
        button:hover { background: #0056b3; }
        .error { color: red; font-weight: bold; text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login PJJ Mem Heni</h2>
    
    <?php if(!empty($error)): ?>
        <div class="error"><?= $error; ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <label for="role">Masuk Sebagai:</label>
        <select name="role" id="role" required>
            <option value="siswa">Siswa (Gunakan NIS)</option>
            <option value="guru">Guru (Gunakan NIP)</option>
        </select>

        <label for="username">Nomor Induk (NIS / NIP):</label>
        <input type="text" id="username" name="username" placeholder="Masukkan NIS atau NIP" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>

        <button type="submit" name="login">Masuk ke Sistem</button>
    </form>
    <a href="registrasi.php" style="display:block; text-align:center; margin-top:15px; text-decoration:none; color:#28a745;">Belum punya akun siswa? Registrasi di sini</a>
    <!-- Tambahkan link ini di bagian bawah form login.php Anda -->
<div style="text-align: center; margin-top: 15px;">
    <a href="registrasi.php" style="text-decoration:none; color:#28a745; margin-right: 15px;">Registrasi Siswa Baru</a> | 
    <a href="registrasi_guru.php" style="text-decoration:none; color:#007BFF; margin-left: 15px;">Registrasi Guru Baru</a>
</div>


</div>

</body>
</html>
