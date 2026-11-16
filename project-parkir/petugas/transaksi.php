<?php
session_start();
require_once "../koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['role'] != 'petugas') {
    header("Location: ../index.php");
    exit;
}


/* =====================================================
   KENDARAAN MASUK
   ===================================================== */

if (isset($_POST['kendaraan_masuk'])) {

    $plat_nomor = $_POST['plat_nomor'];
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $warna = $_POST['warna'];
    $pemilik = $_POST['pemilik'];
    $id_area = $_POST['id_area'];
    $waktu_masuk = $_POST['waktu_masuk'];

    // Ubah format datetime-local menjadi format DATETIME MySQL
    $waktu_masuk = str_replace('T', ' ', $waktu_masuk);

    $id_user = $_SESSION['id_user'];


    // Cek apakah plat sedang parkir
    $cek = mysqli_query($koneksi, "
        SELECT *
        FROM tb_kendaraan k
        INNER JOIN tb_transaksi t
            ON k.id_kendaraan = t.id_kendaraan
        WHERE k.plat_nomor = '$plat_nomor'
        AND t.status = 'masuk'
    ");


    if (mysqli_num_rows($cek) > 0) {

        $pesan = "Kendaraan dengan plat tersebut masih berada di area parkir.";
    } else {

        // Simpan kendaraan
        mysqli_query($koneksi, "
            INSERT INTO tb_kendaraan
            (
                plat_nomor,
                jenis_kendaraan,
                warna,
                pemilik,
                id_user
            )
            VALUES
            (
                '$plat_nomor',
                '$jenis_kendaraan',
                '$warna',
                '$pemilik',
                '$id_user'
            )
        ");


        $id_kendaraan = mysqli_insert_id($koneksi);


        // Cari tarif
        $jenis_tarif = strtolower($jenis_kendaraan);

        if (
            $jenis_tarif != 'motor' &&
            $jenis_tarif != 'mobil'
        ) {
            $jenis_tarif = 'lainnya';
        }


        $tarif = mysqli_query($koneksi, "
            SELECT *
            FROM tb_tarif
            WHERE jenis_kendaraan = '$jenis_tarif'
            LIMIT 1
        ");


        $data_tarif = mysqli_fetch_assoc($tarif);


        if (!$data_tarif) {

            $pesan = "Tarif untuk kendaraan tersebut belum tersedia.";
        } else {

            $id_tarif = $data_tarif['id_tarif'];


            // Simpan transaksi
            mysqli_query($koneksi, "
                INSERT INTO tb_transaksi
                (
                    id_kendaraan,
                    waktu_masuk,
                    waktu_keluar,
                    id_tarif,
                    durasi_jam,
                    biaya_total,
                    status,
                    id_user,
                    id_area
                )
                VALUES
                (
                    '$id_kendaraan',
                    '$waktu_masuk',
                    NULL,
                    '$id_tarif',
                    0,
                    0,
                    'masuk',
                    '$id_user',
                    '$id_area'
                )
            ");


            // Tambah jumlah terisi
            mysqli_query($koneksi, "
                UPDATE tb_area_parkir
                SET terisi = terisi + 1
                WHERE id_area = '$id_area'
            ");


            // Log aktivitas
            mysqli_query($koneksi, "
                INSERT INTO tb_log_aktivitas
                (
                    id_user,
                    aktivitas,
                    waktu_aktivitas
                )
                VALUES
                (
                    '$id_user',
                    'Mencatat kendaraan masuk',
                    NOW()
                )
            ");


            $pesan = "Kendaraan berhasil dicatat masuk.";
        }
    }
}


/* =====================================================
   KENDARAAN KELUAR
   ===================================================== */

if (isset($_POST['kendaraan_keluar'])) {

    $id_parkir = $_POST['id_parkir'];
    $waktu_keluar_input = $_POST['waktu_keluar'];

    // Ubah format datetime-local menjadi format DATETIME MySQL
    $waktu_keluar_input = str_replace('T', ' ', $waktu_keluar_input);

    $id_user = $_SESSION['id_user'];


    // Ambil transaksi
    $cek = mysqli_query($koneksi, "
        SELECT
            t.*,
            k.plat_nomor,
            k.jenis_kendaraan,
            tr.tarif_per_jam
        FROM tb_transaksi t

        INNER JOIN tb_kendaraan k
            ON t.id_kendaraan = k.id_kendaraan

        INNER JOIN tb_tarif tr
            ON t.id_tarif = tr.id_tarif

        WHERE t.id_parkir = '$id_parkir'
        AND t.status = 'masuk'
    ");


    $transaksi = mysqli_fetch_assoc($cek);


    if (!$transaksi) {

        $pesan = "Transaksi tidak ditemukan.";
    } else {

        $waktu_masuk = strtotime($transaksi['waktu_masuk']);
        $waktu_keluar = strtotime($waktu_keluar_input);

        // Pastikan waktu keluar tidak lebih awal dari waktu masuk
        if ($waktu_keluar < $waktu_masuk) {
            $pesan = "Waktu keluar tidak boleh lebih awal dari waktu masuk.";
        } else {

            // Hitung selisih waktu dalam jam
            $selisih = $waktu_keluar - $waktu_masuk;

            $durasi_jam = ceil($selisih / 3600);


        // Minimal 1 jam
        if ($durasi_jam < 1) {
            $durasi_jam = 1;
        }


        // Hitung biaya
        $biaya_total =
            $durasi_jam * $transaksi['tarif_per_jam'];


        // Update transaksi
        mysqli_query($koneksi, "
            UPDATE tb_transaksi
            SET
                waktu_keluar = '$waktu_keluar_input',
                durasi_jam = '$durasi_jam',
                biaya_total = '$biaya_total',
                status = 'keluar'
            WHERE id_parkir = '$id_parkir'
        ");


        // Kurangi jumlah kendaraan di area
        mysqli_query($koneksi, "
            UPDATE tb_area_parkir
            SET terisi =
                CASE
                    WHEN terisi > 0 THEN terisi - 1
                    ELSE 0
                END
            WHERE id_area = '{$transaksi['id_area']}'
        ");


            // Log
            mysqli_query($koneksi, "
                INSERT INTO tb_log_aktivitas
                (
                    id_user,
                    aktivitas,
                    waktu_aktivitas
                )
                VALUES
                (
                    '$id_user',
                    'Mencatat kendaraan keluar',
                    NOW()
                )
            ");


            // Langsung menuju cetak struk
            header(
                "Location: cetak_struk.php?id=$id_parkir"
            );

            exit;
        }
    }
}


/* =====================================================
   DATA AREA
   ===================================================== */

$area = mysqli_query($koneksi, "
    SELECT *
    FROM tb_area_parkir
    WHERE kapasitas > terisi
    ORDER BY id_area ASC
");


/* =====================================================
   TRANSAKSI KENDARAAN MASIH PARKIR
   ===================================================== */

$transaksi_masuk = mysqli_query($koneksi, "
    SELECT
        t.*,
        k.plat_nomor,
        k.jenis_kendaraan,
        k.warna,
        k.pemilik,
        a.nama_area
    FROM tb_transaksi t

    INNER JOIN tb_kendaraan k
        ON t.id_kendaraan = k.id_kendaraan

    INNER JOIN tb_area_parkir a
        ON t.id_area = a.id_area

    WHERE t.status = 'masuk'

    ORDER BY t.id_parkir DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Transaksi Parkir</title>

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
            max-width: 1250px;
            margin: auto;
        }

        .kembali {
            display: inline-block;
            margin-bottom: 20px;
            color: #333;
            text-decoration: none;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            margin: 7px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background: #198754;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #157347;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
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

        .keluar {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .alert {
            background: #d1e7dd;
            color: #0f5132;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        @media (max-width: 700px) {

            body {
                padding: 15px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

        }
    </style>

</head>

<body>

    <div class="container">

        <a href="index.php" class="kembali">
            ← Kembali ke Dashboard
        </a>

        <h1>Transaksi Parkir</h1>


        <?php if (isset($pesan)) { ?>

            <div class="alert">
                <?= htmlspecialchars($pesan); ?>
            </div>

        <?php } ?>


        <!-- =========================================
     KENDARAAN MASUK
     ========================================= -->

        <div class="card">

            <h2>🚗 Kendaraan Masuk</h2>

            <form method="POST">

                <div class="form-grid">

                    <div>

                        <label>Plat Nomor</label>

                        <input
                            type="text"
                            name="plat_nomor"
                            placeholder="Contoh: N 1234 AB"
                            required>

                    </div>


                    <div>

                        <label>Jenis Kendaraan</label>

                        <select name="jenis_kendaraan" required>

                            <option value="">
                                -- Pilih Jenis --
                            </option>

                            <option value="motor">
                                Motor
                            </option>

                            <option value="mobil">
                                Mobil
                            </option>

                            <option value="lainnya">
                                Lainnya
                            </option>

                        </select>

                    </div>


                    <div>

                        <label>Warna</label>

                        <input
                            type="text"
                            name="warna"
                            required>

                    </div>


                    <div>

                        <label>Pemilik</label>

                        <input
                            type="text"
                            name="pemilik"
                            required>

                    </div>


                    <div>

                        <label>Area Parkir</label>

                        <select name="id_area" required>

                            <option value="">
                                -- Pilih Area --
                            </option>

                            <?php while ($a = mysqli_fetch_assoc($area)) { ?>

                                <option value="<?= $a['id_area']; ?>">

                                    <?= htmlspecialchars($a['nama_area']); ?>

                                    -
                                    Sisa <?= $a['kapasitas'] - $a['terisi']; ?>

                                </option>

                            <?php } ?>

                        </select>

                    </div>


                    <div>

                        <label>Waktu Masuk</label>

                        <input
                            type="datetime-local"
                            name="waktu_masuk"
                            value="<?= date('Y-m-d\\TH:i'); ?>"
                            required>

                    </div>

                </div>


                <button
                    type="submit"
                    name="kendaraan_masuk">

                    Simpan Kendaraan Masuk

                </button>

            </form>

        </div>


        <!-- =========================================
     KENDARAAN MASIH PARKIR
     ========================================= -->

        <div class="card">

            <h2>🅿️ Kendaraan Masih Parkir</h2>

            <div class="table-wrapper">

                <table>

                    <tr>

                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Warna</th>
                        <th>Pemilik</th>
                        <th>Area</th>
                        <th>Waktu Masuk</th>
                        <th>Aksi</th>

                    </tr>


                    <?php

                    $no = 1;

                    if (mysqli_num_rows($transaksi_masuk) > 0) {

                        while ($row = mysqli_fetch_assoc($transaksi_masuk)) {

                    ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['plat_nomor']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['jenis_kendaraan']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['warna']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['pemilik']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_area']); ?>
                                </td>

                                <td>
                                    <?= $row['waktu_masuk']; ?>
                                </td>

                                <td>

                                    <form method="POST">

                                        <input
                                            type="hidden"
                                            name="id_parkir"
                                            value="<?= $row['id_parkir']; ?>">

                                        <input
                                            type="datetime-local"
                                            name="waktu_keluar"
                                            value="<?= date('Y-m-d\\TH:i'); ?>"
                                            required
                                            style="margin: 0 0 8px; width: 190px;">

                                        <button
                                            type="submit"
                                            name="kendaraan_keluar"
                                            class="keluar"
                                            onclick="return confirm('Kendaraan ini keluar dari parkir?')">

                                            Kendaraan Keluar

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php

                        }
                    } else {

                        ?>

                        <tr>

                            <td colspan="8"
                                style="text-align:center;">

                                Tidak ada kendaraan yang sedang parkir.

                            </td>

                        </tr>

                    <?php } ?>

                </table>

            </div>

        </div>


    </div>

</body>

</html>