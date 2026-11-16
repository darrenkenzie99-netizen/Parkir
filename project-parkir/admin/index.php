<?php

session_start();

require_once "../koneksi.php";

if (!isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

    <div class="navbar">

        <h2>APLIKASI PARKIR</h2>

        <div>
            <?= $_SESSION['nama_lengkap']; ?>
            |
            <a href="../logout.php">Logout</a>
        </div>

    </div>

    <div class="container">

        <div class="card">

            <h1>Dashboard Admin</h1>

            <p>
                Selamat datang,
                <b><?= $_SESSION['nama_lengkap']; ?></b>
            </p>

        </div>


        <div class="menu">

            <div class="menu-card">

                <h3>👤 User</h3>

                <p>
                    Kelola data pengguna aplikasi.
                </p>
                <a href="user.php">Kelola User</a>


            </div>


            <div class="menu-card">

                <h3>💰 Tarif Parkir</h3>

                <p>
                    Kelola tarif kendaraan.
                </p>

                <a href="tarif.php">Kelola Tarif Parkir</a>

            </div>


            <div class="menu-card">

                <h3>🅿️ Area Parkir</h3>

                <p>
                    Kelola area dan kapasitas parkir.
                </p>
                <a href="area.php">Kelola Area Parkir</a>


            </div>


            <div class="menu-card">

                <h3>🚗 Kendaraan</h3>

                <p>
                    Kelola data kendaraan.
                </p>

                <a href="kendaraan.php">Kelola Kendaraan</a>

            </div>


            <div class="menu-card">

                <h3>📋 Log Aktivitas</h3>

                <p>
                    Melihat aktivitas pengguna.
                </p>
                <a href="log.php">Log Aktivitas</a> 

            </div>

        </div>

    </div>

</body>

</html>