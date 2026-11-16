<?php

session_start();
include 'koneksi.php';

$error = "";
$pesan = "";


/* =====================================================
   LOGIN
===================================================== */

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "
        SELECT * FROM tb_user
        WHERE username='$username'
        AND password='$password'
        AND status_aktif=1
    ");

    if (mysqli_num_rows($query) > 0) {

        $user = mysqli_fetch_assoc($query);

        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: index.php");
        exit;
    } else {

        $error = "Username atau password salah!";
    }
}


/* =====================================================
   LOGOUT
===================================================== */

if (isset($_GET['logout'])) {

    session_destroy();

    header("Location: index.php");
    exit;
}


/* =====================================================
   CEK LOGIN
===================================================== */

$sudah_login = isset($_SESSION['id_user']);


/* =====================================================
   CRUD USER - KHUSUS ADMIN
===================================================== */

if ($sudah_login && $_SESSION['role'] == 'admin') {


    /* ==============================
       TAMBAH USER
    ============================== */

    if (isset($_POST['tambah_user'])) {

        $nama = mysqli_real_escape_string(
            $koneksi,
            $_POST['nama_lengkap']
        );

        $username = mysqli_real_escape_string(
            $koneksi,
            $_POST['username']
        );

        $password = mysqli_real_escape_string(
            $koneksi,
            $_POST['password']
        );

        $role = mysqli_real_escape_string(
            $koneksi,
            $_POST['role']
        );

        $status = $_POST['status_aktif'];

        $query = mysqli_query($koneksi, "
            INSERT INTO tb_user
            (
                nama_lengkap,
                username,
                password,
                role,
                status_aktif
            )
            VALUES
            (
                '$nama',
                '$username',
                '$password',
                '$role',
                '$status'
            )
        ");

        if ($query) {

            $pesan = "User berhasil ditambahkan.";
        } else {

            $error = "User gagal ditambahkan.";
        }
    }


    /* ==============================
       HAPUS USER
    ============================== */

    if (isset($_GET['hapus_user'])) {

        $id = $_GET['hapus_user'];

        // Supaya admin yang sedang login tidak terhapus
        if ($id != $_SESSION['id_user']) {

            mysqli_query(
                $koneksi,
                "DELETE FROM tb_user WHERE id_user='$id'"
            );

            $pesan = "User berhasil dihapus.";
        } else {

            $error = "User yang sedang login tidak dapat dihapus.";
        }
    }


    /* ==============================
       EDIT USER
    ============================== */

    if (isset($_POST['edit_user'])) {

        $id = $_POST['id_user'];

        $nama = mysqli_real_escape_string(
            $koneksi,
            $_POST['nama_lengkap']
        );

        $username = mysqli_real_escape_string(
            $koneksi,
            $_POST['username']
        );

        $role = mysqli_real_escape_string(
            $koneksi,
            $_POST['role']
        );

        $status = $_POST['status_aktif'];

        $query = mysqli_query($koneksi, "
            UPDATE tb_user
            SET
                nama_lengkap='$nama',
                username='$username',
                role='$role',
                status_aktif='$status'
            WHERE id_user='$id'
        ");

        if ($query) {

            $pesan = "User berhasil diubah.";
        } else {

            $error = "User gagal diubah.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Aplikasi Parkir</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f6f9;
        }

        /* =========================
           LOGIN
        ========================= */

        .login-container {
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            width: 380px;

            background: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow: 0 5px 20px rgba(0, 0, 0, .1);
        }

        .login-box h1 {
            text-align: center;
            margin-bottom: 10px;
        }

        .login-box p {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 11px;

            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            cursor: pointer;
        }

        .btn-login {
            width: 100%;

            padding: 12px;

            background: #222;
            color: white;

            border: none;
            border-radius: 6px;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            background: #222;
            color: white;

            padding: 18px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .logout {
            background: #d33;

            padding: 8px 14px;

            border-radius: 5px;
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            width: 90%;
            max-width: 1100px;

            margin: 30px auto;
        }

        .welcome {
            background: white;

            padding: 25px;

            border-radius: 10px;

            margin-bottom: 25px;
        }

        /* =========================
           CARD
        ========================= */

        .cards {
            display: grid;

            grid-template-columns:
                repeat(auto-fit, minmax(200px, 1fr));

            gap: 20px;
        }

        .card {
            background: white;

            padding: 25px;

            border-radius: 10px;
        }

        .card a {
            color: #222;
            text-decoration: none;
        }

        /* =========================
           BUTTON
        ========================= */

        .btn {
            display: inline-block;

            padding: 9px 14px;

            border: none;

            border-radius: 5px;

            text-decoration: none;

            cursor: pointer;
        }

        .btn-tambah {
            background: #198754;
            color: white;
        }

        .btn-edit {
            background: #ffc107;
            color: black;
        }

        .btn-hapus {
            background: #dc3545;
            color: white;
        }

        .btn-kembali {
            background: #6c757d;
            color: white;
        }

        /* =========================
           TABLE
        ========================= */

        table {
            width: 100%;

            border-collapse: collapse;

            background: white;

            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;

            border-bottom: 1px solid #ddd;

            text-align: left;
        }

        th {
            background: #222;
            color: white;
        }

        /* =========================
           FORM BOX
        ========================= */

        .form-box {
            background: white;

            padding: 25px;

            border-radius: 10px;

            max-width: 600px;
        }

        /* =========================
           PESAN
        ========================= */

        .success {
            background: #d1e7dd;
            color: #0f5132;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;
        }

        .error {
            background: #f8d7da;
            color: #842029;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;
        }
    </style>

</head>

<body>


    <?php

    /* =====================================================
   JIKA BELUM LOGIN
===================================================== */

    if (!$sudah_login) {

    ?>

        <div class="login-container">

            <div class="login-box">

                <h1>APLIKASI PARKIR</h1>

                <p>Silakan login untuk masuk</p>

                <?php if ($error != "") { ?>

                    <div class="error">
                        <?= $error; ?>
                    </div>

                <?php } ?>


                <form method="POST">

                    <div class="form-group">

                        <label>Username</label>

                        <input
                            type="text"
                            name="username"
                            required>

                    </div>


                    <div class="form-group">

                        <label>Password</label>

                        <input
                            type="password"
                            name="password"
                            required>

                    </div>


                    <button
                        type="submit"
                        name="login"
                        class="btn-login">
                        LOGIN
                    </button>

                </form>

            </div>

        </div>


    <?php

    }


    /* =====================================================
   SUDAH LOGIN
===================================================== */ else {

    ?>

        <div class="navbar">

            <div>
                <b>APLIKASI PARKIR</b>
            </div>

            <div>

                <?= $_SESSION['nama_lengkap']; ?>

                &nbsp;&nbsp;

                <a
                    class="logout"
                    href="index.php?logout=1">
                    Logout
                </a>

            </div>

        </div>


        <div class="container">

            <?php


            /* =====================================================
   HALAMAN ADMIN
===================================================== */

            if ($_SESSION['role'] == 'admin') {


                /* ==============================
       HALAMAN USER
    ============================== */

                if (isset($_GET['page']) && $_GET['page'] == 'user') {

            ?>

                    <h1>Data User</h1>

                    <br>

                    <a
                        href="index.php?page=tambah_user"
                        class="btn btn-tambah">
                        + Tambah User
                    </a>

                    <a
                        href="index.php"
                        class="btn btn-kembali">
                        Kembali
                    </a>


                    <?php if ($pesan != "") { ?>

                        <div class="success">
                            <?= $pesan; ?>
                        </div>

                    <?php } ?>


                    <?php if ($error != "") { ?>

                        <div class="error">
                            <?= $error; ?>
                        </div>

                    <?php } ?>


                    <table>

                        <tr>

                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>

                        </tr>


                        <?php

                        $no = 1;

                        $data_user = mysqli_query(
                            $koneksi,
                            "SELECT * FROM tb_user ORDER BY id_user DESC"
                        );

                        while ($user = mysqli_fetch_assoc($data_user)) {

                        ?>

                            <tr>

                                <td><?= $no++; ?></td>

                                <td>
                                    <?= $user['nama_lengkap']; ?>
                                </td>

                                <td>
                                    <?= $user['username']; ?>
                                </td>

                                <td>
                                    <?= $user['role']; ?>
                                </td>

                                <td>

                                    <?php

                                    if ($user['status_aktif'] == 1) {
                                        echo "Aktif";
                                    } else {
                                        echo "Tidak Aktif";
                                    }

                                    ?>
                                </td>

                                <td>

                                    <a
                                        href="index.php?page=edit_user&id=<?= $user['id_user']; ?>"
                                        class="btn btn-edit">
                                        Edit
                                    </a>


                                    <a
                                        href="index.php?page=user&hapus_user=<?= $user['id_user']; ?>"
                                        class="btn btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        Hapus
                                    </a>

                                </td>

                            </tr>

                        <?php

                        }

                        ?>

                    </table>


                <?php


                    /* ==============================
       TAMBAH USER
    ============================== */

                } elseif (
                    isset($_GET['page'])
                    && $_GET['page'] == 'tambah_user'
                ) {

                ?>

                    <h1>Tambah User</h1>

                    <br>

                    <div class="form-box">

                        <form method="POST">

                            <div class="form-group">

                                <label>Nama Lengkap</label>

                                <input
                                    type="text"
                                    name="nama_lengkap"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>Username</label>

                                <input
                                    type="text"
                                    name="username"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>Password</label>

                                <input
                                    type="password"
                                    name="password"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>Role</label>

                                <select name="role" required>

                                    <option value="">
                                        -- Pilih Role --
                                    </option>

                                    <option value="admin">
                                        Admin
                                    </option>

                                    <option value="petugas">
                                        Petugas
                                    </option>

                                    <option value="owner">
                                        Owner
                                    </option>

                                </select>

                            </div>


                            <div class="form-group">

                                <label>Status</label>

                                <select name="status_aktif">

                                    <option value="1">
                                        Aktif
                                    </option>

                                    <option value="0">
                                        Tidak Aktif
                                    </option>

                                </select>

                            </div>


                            <button
                                type="submit"
                                name="tambah_user"
                                class="btn btn-tambah">
                                Simpan
                            </button>


                            <a
                                href="index.php?page=user"
                                class="btn btn-kembali">
                                Kembali
                            </a>

                        </form>

                    </div>


                <?php


                    /* ==============================
       EDIT USER
    ============================== */

                } elseif (
                    isset($_GET['page'])
                    && $_GET['page'] == 'edit_user'
                ) {

                    $id = $_GET['id'];

                    $query_user = mysqli_query(
                        $koneksi,
                        "SELECT * FROM tb_user WHERE id_user='$id'"
                    );

                    $user = mysqli_fetch_assoc($query_user);

                ?>

                    <h1>Edit User</h1>

                    <br>

                    <div class="form-box">

                        <form method="POST">

                            <input
                                type="hidden"
                                name="id_user"
                                value="<?= $user['id_user']; ?>">


                            <div class="form-group">

                                <label>Nama Lengkap</label>

                                <input
                                    type="text"
                                    name="nama_lengkap"
                                    value="<?= $user['nama_lengkap']; ?>"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>Username</label>

                                <input
                                    type="text"
                                    name="username"
                                    value="<?= $user['username']; ?>"
                                    required>

                            </div>


                            <div class="form-group">

                                <label>Role</label>

                                <select name="role">

                                    <option
                                        value="admin"
                                        <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>
                                        Admin
                                    </option>

                                    <option
                                        value="petugas"
                                        <?= $user['role'] == 'petugas' ? 'selected' : ''; ?>>
                                        Petugas
                                    </option>

                                    <option
                                        value="owner"
                                        <?= $user['role'] == 'owner' ? 'selected' : ''; ?>>
                                        Owner
                                    </option>

                                </select>

                            </div>


                            <div class="form-group">

                                <label>Status</label>

                                <select name="status_aktif">

                                    <option
                                        value="1"
                                        <?= $user['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option
                                        value="0"
                                        <?= $user['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>

                            </div>


                            <button
                                type="submit"
                                name="edit_user"
                                class="btn btn-edit">
                                Simpan Perubahan
                            </button>


                            <a
                                href="index.php?page=user"
                                class="btn btn-kembali">
                                Kembali
                            </a>

                        </form>

                    </div>


                <?php


                    /* ==============================
       DASHBOARD ADMIN
    ============================== */

                } else {

                ?>

                    <div class="welcome">

                        <h1>Dashboard Admin</h1>

                        <br>

                        <p>
                            Selamat datang,
                            <b><?= $_SESSION['nama_lengkap']; ?></b>
                        </p>

                    </div>


                    <div class="cards">

                        <div class="card">

                            <h3>👤 User</h3>

                            <br>

                            <a href="index.php?page=user">
                                Kelola Data User →
                            </a>

                        </div>


                        <div class="card">

                            <h3>💰 Tarif Parkir</h3>

                            <br>

                            <a href="admin/tarif.php">
                                Kelola Tarif Parkir →
                            </a>

                        </div>


                        <div class="card">

                            <h3>🅿️ Area Parkir</h3>

                            <br>

                            <a href="admin/area.php">
                                Kelola Area Parkir →
                            </a>

                        </div>


                        <div class="card">

                            <h3>🚗 Kendaraan</h3>

                            <br>

                            <a href="admin/kendaraan.php">
                                Kelola Kendaraan →
                            </a>

                        </div>


                        <div class="card">

                            <h3>📋 Log Aktivitas</h3>

                            <br>

                            <a href="admin/log.php">
                                Lihat Log Aktivitas →
                            </a>

                        </div>

                    </div>

                <?php

                }
            }


            /* =====================================================
   DASHBOARD PETUGAS
===================================================== */ elseif ($_SESSION['role'] == 'petugas') {

                ?>

                <div class="welcome">

                    <h1>Dashboard Petugas</h1>

                    <br>

                    <p>
                        Selamat datang,
                        <b><?= $_SESSION['nama_lengkap']; ?></b>
                    </p>

                </div>


                <div class="cards">

                    <div class="card">

                        <h3>🚗 Transaksi</h3>

                        <br>

                        <a href="petugas/index.php">
                            Kelola Transaksi →
                        </a>

                    </div>


                    <div class="card">

                        <h3>🧾 Cetak Struk</h3>

                        <br>

                        <a href="petugas/index.php">
                            Cetak Struk →
                        </a>

                    </div>

                </div>

            <?php

            }


            /* =====================================================
   DASHBOARD OWNER
===================================================== */ elseif ($_SESSION['role'] == 'owner') {

            ?>

                <div class="welcome">

                    <h1>Dashboard Owner</h1>

                    <br>

                    <p>
                        Selamat datang,
                        <b><?= $_SESSION['nama_lengkap']; ?></b>
                    </p>

                </div>


                <div class="cards">

                    <div class="card">

                        <h3>📊 Rekap Transaksi</h3>

                        <br>

                        <a href="owner/rekap.php">
                            Lihat Rekap Transaksi →
                        </a>

                    </div>

                </div>

            <?php

            }

            ?>

        </div>

    <?php

    }

    ?>

</body>

</html>