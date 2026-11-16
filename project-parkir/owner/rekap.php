<?php
session_start();
require_once "../koneksi.php";

// Cek login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

// Cek role
if ($_SESSION['role'] != 'owner') {
    header("Location: ../index.php");
    exit;
}


// Ambil tanggal dari form
$tanggal_mulai = isset($_GET['tanggal_mulai'])
    ? $_GET['tanggal_mulai']
    : '';

$tanggal_akhir = isset($_GET['tanggal_akhir'])
    ? $_GET['tanggal_akhir']
    : '';


// Query dasar
$query = "
    SELECT
        tb_transaksi.id_parkir,
        tb_kendaraan.plat_nomor,
        tb_kendaraan.jenis_kendaraan,
        tb_transaksi.waktu_masuk,
        tb_transaksi.waktu_keluar,
        tb_transaksi.durasi_jam,
        tb_transaksi.biaya_total,
        tb_transaksi.status,
        tb_user.nama_lengkap AS nama_petugas,
        tb_area_parkir.nama_area
    FROM tb_transaksi

    LEFT JOIN tb_kendaraan
        ON tb_transaksi.id_kendaraan =
           tb_kendaraan.id_kendaraan

    LEFT JOIN tb_user
        ON tb_transaksi.id_user =
           tb_user.id_user

    LEFT JOIN tb_area_parkir
        ON tb_transaksi.id_area =
           tb_area_parkir.id_area
";


// Kalau tanggal dipilih
if ($tanggal_mulai != '' && $tanggal_akhir != '') {

    $query .= "
        WHERE DATE(tb_transaksi.waktu_masuk)
        BETWEEN '$tanggal_mulai'
        AND '$tanggal_akhir'
    ";

}


$query .= "
    ORDER BY tb_transaksi.id_parkir DESC
";


$data = mysqli_query($koneksi, $query);


// Hitung total transaksi dan pendapatan
$total_transaksi = 0;
$total_pendapatan = 0;

if ($data) {

    while ($row = mysqli_fetch_assoc($data)) {

        $total_transaksi++;

        $total_pendapatan += (float) $row['biaya_total'];

    }

    // Jalankan query lagi karena pointer data sudah selesai
    $data = mysqli_query($koneksi, $query);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Rekap Transaksi</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .container {
            max-width: 1300px;
            margin: auto;
        }

        .kembali {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }

        h1 {
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .filter {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 7px;
            font-weight: bold;
        }

        .form-group input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            border: none;
            background: #198754;
            color: white;
            padding: 11px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #157347;
        }

        .btn-reset {
            background: #6c757d;
            color: white;
            padding: 11px 18px;
            border-radius: 5px;
            text-decoration: none;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .summary-box h3 {
            margin-top: 0;
            color: #666;
            font-size: 15px;
        }

        .summary-box .angka {
            font-size: 25px;
            font-weight: bold;
            color: #198754;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #198754;
            color: white;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-masuk {
            color: #198754;
            font-weight: bold;
        }

        .status-keluar {
            color: #dc3545;
            font-weight: bold;
        }

        @media (max-width: 700px) {

            body {
                padding: 15px;
            }

            .summary {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<div class="container">


    <a href="../index.php" class="kembali">
        ←    Kembali ke Dashboard
    </a>


    <h1>Rekap Transaksi Parkir</h1>


    <!-- FILTER TANGGAL -->

    <div class="card">

        <h2>Filter Berdasarkan Waktu</h2>

        <form method="GET">

            <div class="filter">


                <div class="form-group">

                    <label>
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        name="tanggal_mulai"
                        value="<?= htmlspecialchars($tanggal_mulai); ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Tanggal Akhir
                    </label>

                    <input
                        type="date"
                        name="tanggal_akhir"
                        value="<?= htmlspecialchars($tanggal_akhir); ?>"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn">

                    Tampilkan

                </button>


                <a
                    href="rekap.php"
                    class="btn-reset">

                    Reset

                </a>


            </div>

        </form>

    </div>


    <!-- RINGKASAN -->

    <div class="summary">


        <div class="summary-box">

            <h3>Total Transaksi</h3>

            <div class="angka">
                <?= $total_transaksi; ?>
            </div>

        </div>


        <div class="summary-box">

            <h3>Total Pendapatan</h3>

            <div class="angka">

                Rp
                <?= number_format(
                    $total_pendapatan,
                    0,
                    ',',
                    '.'
                ); ?>

            </div>

        </div>


    </div>


    <!-- TABEL -->

    <div class="card">

        <h2>Data Transaksi</h2>


        <div class="table-wrapper">

            <table>


                <tr>

                    <th>No</th>

                    <th>Plat Nomor</th>

                    <th>Jenis Kendaraan</th>

                    <th>Area</th>

                    <th>Waktu Masuk</th>

                    <th>Waktu Keluar</th>

                    <th>Durasi</th>

                    <th>Biaya</th>

                    <th>Status</th>

                    <th>Petugas</th>

                </tr>


                <?php

                $no = 1;

                if ($data && mysqli_num_rows($data) > 0) {

                    while ($row = mysqli_fetch_assoc($data)) {

                ?>


                <tr>


                    <td>
                        <?= $no++; ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['plat_nomor'] ?? '-'
                        ); ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['jenis_kendaraan'] ?? '-'
                        ); ?>
                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['nama_area'] ?? '-'
                        ); ?>
                    </td>


                    <td>
                        <?= $row['waktu_masuk'] ?? '-'; ?>
                    </td>


                    <td>
                        <?= $row['waktu_keluar'] ?? '-'; ?>
                    </td>


                    <td>

                        <?= $row['durasi_jam'] ?? 0; ?>

                        jam

                    </td>


                    <td>

                        Rp
                        <?= number_format(
                            $row['biaya_total'] ?? 0,
                            0,
                            ',',
                            '.'
                        ); ?>

                    </td>


                    <td>

                        <?php if ($row['status'] == 'masuk') { ?>

                            <span class="status-masuk">
                                Masuk
                            </span>

                        <?php } else { ?>

                            <span class="status-keluar">
                                Keluar
                            </span>

                        <?php } ?>

                    </td>


                    <td>
                        <?= htmlspecialchars(
                            $row['nama_petugas'] ?? '-'
                        ); ?>
                    </td>


                </tr>


                <?php

                    }

                } else {

                ?>


                <tr>

                    <td
                        colspan="10"
                        style="text-align:center;">

                        Belum ada data transaksi.

                    </td>

                </tr>


                <?php } ?>


            </table>

        </div>

    </div>


</div>


</body>

</html>