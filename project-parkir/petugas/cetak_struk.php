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


$id_parkir = isset($_GET['id'])
    ? $_GET['id']
    : 0;


$query = mysqli_query($koneksi, "
    SELECT
        t.*,
        k.plat_nomor,
        k.jenis_kendaraan,
        k.warna,
        k.pemilik,
        tr.tarif_per_jam,
        a.nama_area,
        u.nama_lengkap AS nama_petugas

    FROM tb_transaksi t

    INNER JOIN tb_kendaraan k
        ON t.id_kendaraan = k.id_kendaraan

    INNER JOIN tb_tarif tr
        ON t.id_tarif = tr.id_tarif

    INNER JOIN tb_area_parkir a
        ON t.id_area = a.id_area

    INNER JOIN tb_user u
        ON t.id_user = u.id_user

    WHERE t.id_parkir = '$id_parkir'
");


$data = mysqli_fetch_assoc($query);


if (!$data) {
    die("Data transaksi tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Struk Parkir</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 30px;
        }

        .struk {
            width: 400px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
            word-break: break-word;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header h2 {
            margin: 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin: 9px 0;
        }

        .total {
            border-top: 1px dashed #333;
            margin-top: 15px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: bold;
        }

        .tengah {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            background: #198754;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 5px;
            cursor: pointer;
        }

        .kembali {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #333;
        }

        @media print {

            body {
                padding: 0;
                background: white;
            }

            .struk {
                width: 80mm; auto;
                margin: 0;
                box-shadow: none;
            }

            .tengah {
                display: none;
            }

        }
    </style>

</head>

<body>

    <div class="struk">

        <div class="header">

            <h2>STRUK PARKIR</h2>

            <p>
                Aplikasi Parkir
            </p>

        </div>


        <div class="row">

            <span>ID Parkir</span>

            <strong>
                <?= $data['id_parkir']; ?>
            </strong>

        </div>


        <div class="row">

            <span>Plat Nomor</span>

            <strong>
                <?= htmlspecialchars($data['plat_nomor']); ?>
            </strong>

        </div>


        <div class="row">

            <span>Jenis</span>

            <span>
                <?= htmlspecialchars($data['jenis_kendaraan']); ?>
            </span>

        </div>


        <div class="row">

            <span>Warna</span>

            <span>
                <?= htmlspecialchars($data['warna']); ?>
            </span>

        </div>


        <div class="row">

            <span>Pemilik</span>

            <span>
                <?= htmlspecialchars($data['pemilik']); ?>
            </span>

        </div>


        <div class="row">

            <span>Area</span>

            <span>
                <?= htmlspecialchars($data['nama_area']); ?>
            </span>

        </div>


        <div class="row">

            <span>Masuk</span>

            <span>
                <?= $data['waktu_masuk']; ?>
            </span>

        </div>


        <div class="row">

            <span>Keluar</span>

            <span>
                <?= $data['waktu_keluar']; ?>
            </span>

        </div>


        <div class="row">

            <span>Durasi</span>

            <span>
                <?= $data['durasi_jam']; ?> jam
            </span>

        </div>


        <div class="row">

            <span>Tarif/Jam</span>

            <span>
                Rp <?= number_format(
                        $data['tarif_per_jam'],
                        0,
                        ',',
                        '.'
                    ); ?>
            </span>

        </div>


        <div class="total">

            <div class="row">

                <span>TOTAL</span>

                <span>
                    Rp <?= number_format(
                            $data['biaya_total'],
                            0,
                            ',',
                            '.'
                        ); ?>
                </span>

            </div>

        </div>


        <div class="tengah">

            <button
                class="btn"
                onclick="window.print()">

                🖨️ Cetak Struk

            </button>

            <br>

            <a href="transaksi.php"
                class="kembali">

                Kembali ke Transaksi

            </a>

        </div>

    </div>

</body>

</html>