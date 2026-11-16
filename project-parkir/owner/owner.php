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
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard Owner</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .navbar {
            background: #198754;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .logout {
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 9px 15px;
            border-radius: 5px;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .welcome h1 {
            margin-top: 0;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin-top: 0;
        }

        .card p {
            color: #666;
            line-height: 1.5;
        }

        .btn {
            display: inline-block;
            background: #198754;
            color: white;
            text-decoration: none;
            padding: 11px 18px;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn:hover {
            background: #157347;
        }

    </style>

</head>

<body>

    <div class="navbar">

        <h2>Dashboard Owner</h2>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </div>


    <div class="container">

        <div class="welcome">

            <h1>
                Selamat Datang,
                <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
            </h1>

            <p>
                Anda login sebagai <b>Owner</b>.
            </p>

        </div>


        <div class="menu">

            <div class="card">

                <h3>📊 Rekap Transaksi</h3>

                <p>
                    Melihat rekap transaksi parkir
                    berdasarkan waktu yang dipilih.
                </p>

                <a href="rekap.php"
                   target="_blank"
                   class="btn">

                    Lihat Rekap

                </a>

            </div>


            <div class="card">

                <h3>🚗 Informasi Transaksi</h3>

                <p>
                    Data transaksi menampilkan kendaraan,
                    waktu parkir, durasi, dan biaya.
                </p>

            </div>

        </div>

    </div>

</body>

</html>