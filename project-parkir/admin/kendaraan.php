<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// TAMBAH
if (isset($_POST['tambah'])) {

    $plat = $_POST['plat_nomor'];
    $jenis = $_POST['jenis_kendaraan'];
    $warna = $_POST['warna'];
    $pemilik = $_POST['pemilik'];

    $id_user = $_SESSION['id_user'];

    mysqli_query($koneksi, "INSERT INTO tb_kendaraan
        (plat_nomor, jenis_kendaraan, warna, pemilik, id_user)
        VALUES
        ('$plat', '$jenis', '$warna', '$pemilik', '$id_user')");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '$id_user',
            'Menambahkan kendaraan',
            NOW()
        )");

    header("Location: kendaraan.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($koneksi, "DELETE FROM tb_kendaraan
        WHERE id_kendaraan='$id'");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Menghapus kendaraan',
            NOW()
        )");

    header("Location: kendaraan.php");
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT 
        tb_kendaraan.*,
        tb_user.nama_lengkap
    FROM tb_kendaraan
    LEFT JOIN tb_user
        ON tb_kendaraan.id_user = tb_user.id_user
    ORDER BY id_kendaraan DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Kelola Kendaraan</title>

<style>

body {
    font-family: Arial;
    background: #f4f6f9;
    margin: 0;
    padding: 30px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.card {
    background: white;
    padding: 25px;
    margin-bottom: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

input, select {
    width: 100%;
    padding: 10px;
    margin: 8px 0 15px;
    box-sizing: border-box;
}

button {
    background: #198754;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
}

th {
    background: #198754;
    color: white;
    text-align: left;
}

.hapus {
    background: #dc3545;
    color: white;
    padding: 7px 10px;
    text-decoration: none;
    border-radius: 4px;
}

.kembali {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    color: #333;
}

</style>

</head>

<body>

<div class="container">

<a href="index.php" class="kembali">
    ← Kembali ke Dashboard
</a>

<h1>Kelola Kendaraan</h1>

<div class="card">

<h2>Tambah Kendaraan</h2>

<form method="POST">

<label>Plat Nomor</label>

<input
    type="text"
    name="plat_nomor"
    required
>

<label>Jenis Kendaraan</label>

<input
    type="text"
    name="jenis_kendaraan"
    placeholder="Contoh: Motor"
    required
>

<label>Warna</label>

<input
    type="text"
    name="warna"
    required
>

<label>Pemilik</label>

<input
    type="text"
    name="pemilik"
    required
>

<button type="submit" name="tambah">
    Simpan Data
</button>

</form>

</div>

<div class="card">

<h2>Data Kendaraan</h2>

<table>

<tr>

<th>No</th>
<th>Plat Nomor</th>
<th>Jenis</th>
<th>Warna</th>
<th>Pemilik</th>
<th>User</th>
<th>Aksi</th>

</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['plat_nomor']); ?></td>

<td><?= htmlspecialchars($row['jenis_kendaraan']); ?></td>

<td><?= htmlspecialchars($row['warna']); ?></td>

<td><?= htmlspecialchars($row['pemilik']); ?></td>

<td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>

<td>

<a
    class="hapus"
    href="kendaraan.php?hapus=<?= $row['id_kendaraan']; ?>"
    onclick="return confirm('Hapus kendaraan ini?')"
>
    Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>
</html>