<?php

session_start();

require_once "../koneksi.php";

if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') != 'admin') {
    header("Location: ../index.php");
    exit;
}

$pesan = "";
$error = "";


/* TAMBAH USER */

if (isset($_POST['tambah'])) {

    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $status = $_POST['status_aktif'];

    $query = mysqli_query(
        $koneksi,
        "INSERT INTO tb_user
        (nama_lengkap, username, password, role, status_aktif)
        VALUES
        ('$nama', '$username', '$password', '$role', '$status')"
    );

    if ($query) {
        $pesan = "User berhasil ditambahkan.";
    } else {
        $error = "User gagal ditambahkan.";
    }
}


/* HAPUS USER */

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query(
        $koneksi,
        "DELETE FROM tb_user WHERE id_user='$id'"
    );

    header("Location: user.php");
    exit;
}


/* DATA USER */

$data_user = mysqli_query(
    $koneksi,
    "SELECT * FROM tb_user ORDER BY id_user DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola User</title>

    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>

<div class="navbar">

    <h2>KELOLA USER</h2>

    <a href="index.php">Dashboard</a>

</div>

<div class="container">

    <div class="card">

        <h2>Tambah User</h2>

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

        <form method="POST">

            <div class="form-group">
                <label>Nama Lengkap</label>

                <input
                    type="text"
                    name="nama_lengkap"
                    required
                >
            </div>

            <div class="form-group">
                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    required
                >
            </div>

            <div class="form-group">

                <label>Role</label>

                <select name="role" required>

                    <option value="admin">Admin</option>

                    <option value="petugas">Petugas</option>

                    <option value="owner">Owner</option>

                </select>

            </div>

            <div class="form-group">

                <label>Status Aktif</label>

                <select name="status_aktif">

                    <option value="1">Aktif</option>

                    <option value="0">Tidak Aktif</option>

                </select>

            </div>

            <button
                type="submit"
                name="tambah"
                class="btn btn-success"
            >
                Tambah User
            </button>

        </form>

    </div>


    <div class="card">

        <h2>Data User</h2>

        <table>

            <tr>

                <th>No</th>

                <th>Nama</th>

                <th>Username</th>

                <th>Role</th>

                <th>Status</th>

                <th>Aksi</th>

            </tr>

            <?php

            $no = 1;

            while ($user = mysqli_fetch_assoc($data_user)) {

            ?>

            <tr>

                <td><?= $no++; ?></td>

                <td><?= $user['nama_lengkap']; ?></td>

                <td><?= $user['username']; ?></td>

                <td><?= $user['role']; ?></td>

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
                        href="user.php?hapus=<?= $user['id_user']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Hapus user ini?')"
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