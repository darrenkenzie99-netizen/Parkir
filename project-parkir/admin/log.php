<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

$data = mysqli_query($koneksi, "
    SELECT
        tb_log_aktivitas.*,
        tb_user.nama_lengkap,
        tb_user.username,
        tb_user.role
    FROM tb_log_aktivitas
    LEFT JOIN tb_user
        ON tb_log_aktivitas.id_user = tb_user.id_user
    ORDER BY tb_log_aktivitas.id_log DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Log Aktivitas</title>

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
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background: #198754;
    color: white;
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

<h1>Log Aktivitas</h1>

<div class="card">

<table>

<tr>

<th>No</th>
<th>Nama</th>
<th>Username</th>
<th>Role</th>
<th>Aktivitas</th>
<th>Waktu</th>

</tr>

<?php

$no = 1;

while ($row = mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= htmlspecialchars($row['nama_lengkap'] ?? '-'); ?></td>

<td><?= htmlspecialchars($row['username'] ?? '-'); ?></td>

<td><?= htmlspecialchars($row['role'] ?? '-'); ?></td>

<td><?= htmlspecialchars($row['aktivitas']); ?></td>

<td><?= $row['waktu_aktivitas']; ?></td>

</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>