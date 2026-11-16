<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// TAMBAH DATA
if (isset($_POST['tambah'])) {

    $nama_area = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];
    $terisi = $_POST['terisi'];

    mysqli_query($koneksi, "INSERT INTO tb_area_parkir 
        (nama_area, kapasitas, terisi)
        VALUES ('$nama_area', '$kapasitas', '$terisi')");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Menambahkan area parkir',
            NOW()
        )");

    header("Location: area.php");
    exit;
}

// HAPUS DATA
if (isset($_GET['hapus'])) {

    $id_area = $_GET['hapus'];

    mysqli_query($koneksi, "DELETE FROM tb_area_parkir
        WHERE id_area='$id_area'");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Menghapus area parkir',
            NOW()
        )");

    header("Location: area.php");
    exit;
}

// EDIT DATA
if (isset($_POST['edit'])) {

    $id_area = $_POST['id_area'];
    $nama_area = $_POST['nama_area'];
    $kapasitas = $_POST['kapasitas'];
    $terisi = $_POST['terisi'];

    mysqli_query($koneksi, "UPDATE tb_area_parkir SET
        nama_area='$nama_area',
        kapasitas='$kapasitas',
        terisi='$terisi'
        WHERE id_area='$id_area'");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Mengubah area parkir',
            NOW()
        )");

    header("Location: area.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM tb_area_parkir ORDER BY id_area DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Area Parkir</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        h1 {
            margin-bottom: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        button {
            background: #198754;
            color: white;
            border: none;
            padding: 10px 18px;
            cursor: pointer;
            border-radius: 5px;
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

        .hapus {
            color: white;
            background: #dc3545;
            padding: 7px 10px;
            text-decoration: none;
            border-radius: 4px;
        }

        .edit {
            color: white;
            background: #ffc107;
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

    <a href="index.php" class="kembali">← Kembali ke Dashboard</a>

    <h1>Kelola Area Parkir</h1>

    <div class="card">
        <h2>Tambah Area</h2>

        <form method="POST">

            <label>Nama Area</label>
            <input type="text" name="nama_area" required>

            <label>Kapasitas</label>
            <input type="number" name="kapasitas" required>

            <label>Terisi</label>
            <input type="number" name="terisi" value="0" required>

            <button type="submit" name="tambah">
                Simpan Data
            </button>

        </form>
    </div>

    <div class="card">

        <h2>Data Area Parkir</h2>

        <table>

            <tr>
                <th>No</th>
                <th>Nama Area</th>
                <th>Kapasitas</th>
                <th>Terisi</th>
                <th>Sisa</th>
                <th>Aksi</th>
            </tr>

            <?php
            $no = 1;

            while ($row = mysqli_fetch_assoc($data)) {
            ?>

            <tr>

                <td><?= $no++; ?></td>

                <td><?= htmlspecialchars($row['nama_area']); ?></td>

                <td><?= $row['kapasitas']; ?></td>

                <td><?= $row['terisi']; ?></td>

                <td>
                    <?= $row['kapasitas'] - $row['terisi']; ?>
                </td>

                <td>

                    <a class="hapus"
                       href="area.php?hapus=<?= $row['id_area']; ?>"
                       onclick="return confirm('Hapus data ini?')">
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