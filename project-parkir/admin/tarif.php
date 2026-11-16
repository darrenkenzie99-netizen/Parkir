<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// TAMBAH
if (isset($_POST['tambah'])) {

    $jenis = $_POST['jenis_kendaraan'];
    $tarif = $_POST['tarif_per_jam'];

    mysqli_query($koneksi, "INSERT INTO tb_tarif
        (jenis_kendaraan, tarif_per_jam)
        VALUES ('$jenis', '$tarif')");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Menambahkan tarif parkir',
            NOW()
        )");

    header("Location: tarif.php");
    exit;
}

// HAPUS
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($koneksi, "DELETE FROM tb_tarif
        WHERE id_tarif='$id'");

    mysqli_query($koneksi, "INSERT INTO tb_log_aktivitas
        (id_user, aktivitas, waktu_aktivitas)
        VALUES (
            '{$_SESSION['id_user']}',
            'Menghapus tarif parkir',
            NOW()
        )");

    header("Location: tarif.php");
    exit;
}

$data = mysqli_query($koneksi, "SELECT * FROM tb_tarif ORDER BY id_tarif DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Kelola Tarif Parkir</title>

    <style>

        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
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
            cursor: pointer;
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

    <h1>Kelola Tarif Parkir</h1>

    <div class="card">

        <h2>Tambah Tarif</h2>

        <form method="POST">

            <label>Jenis Kendaraan</label>

            <select name="jenis_kendaraan" required>

                <option value="">-- Pilih --</option>

                <option value="motor">Motor</option>

                <option value="mobil">Mobil</option>

                <option value="lainnya">Lainnya</option>

            </select>

            <label>Tarif Per Jam</label>

            <input
                type="number"
                name="tarif_per_jam"
                required
            >

            <button type="submit" name="tambah">
                Simpan Data
            </button>

        </form>

    </div>

    <div class="card">

        <h2>Data Tarif Parkir</h2>

        <table>

            <tr>
                <th>No</th>
                <th>Jenis Kendaraan</th>
                <th>Tarif Per Jam</th>
                <th>Aksi</th>
            </tr>

            <?php
            $no = 1;

            while ($row = mysqli_fetch_assoc($data)) {
            ?>

            <tr>

                <td><?= $no++; ?></td>

                <td>
                    <?= htmlspecialchars($row['jenis_kendaraan']); ?>
                </td>

                <td>
                    Rp <?= number_format($row['tarif_per_jam'], 0, ',', '.'); ?>
                </td>

                <td>

                    <a
                        class="hapus"
                        href="tarif.php?hapus=<?= $row['id_tarif']; ?>"
                        onclick="return confirm('Hapus tarif ini?')"
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