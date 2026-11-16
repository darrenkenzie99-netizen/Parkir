CREATE DATABASE parkir;
USE parkir;

CREATE TABLE tb_user (
    id_user INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(50),
    username VARCHAR(50),
    password VARCHAR(100),
    role ENUM('admin', 'petugas', 'owner', ''),
    status_aktif TINYINT(1)
) ENGINE=InnoDB;

CREATE TABLE tb_tarif (
    id_tarif INT(11) AUTO_INCREMENT PRIMARY KEY,
    jenis_kendaraan ENUM('motor', 'mobil', 'lainnya', ''),
    tarif_per_jam DECIMAL(10,0)
) ENGINE=InnoDB;

CREATE TABLE tb_area_parkir (
    id_area INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama_area VARCHAR(50),
    kapasitas INT(5),
    terisi INT(5)
) ENGINE=InnoDB;

CREATE TABLE tb_kendaraan (
    id_kendaraan INT(11) AUTO_INCREMENT PRIMARY KEY,
    plat_nomor VARCHAR(15),
    jenis_kendaraan VARCHAR(20),
    warna VARCHAR(20),
    pemilik VARCHAR(100),
    id_user INT(11),

    FOREIGN KEY (id_user)
        REFERENCES tb_user(id_user)
) ENGINE=InnoDB;

CREATE TABLE tb_transaksi (
    id_parkir INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_kendaraan INT(11),
    waktu_masuk DATETIME,
    waktu_keluar DATETIME,
    id_tarif INT(11),
    durasi_jam INT(5),
    biaya_total DECIMAL(10,0),
    status ENUM('masuk', 'keluar', ''),
    id_user INT(11),
    id_area INT(11),

    FOREIGN KEY (id_kendaraan)
        REFERENCES tb_kendaraan(id_kendaraan),

    FOREIGN KEY (id_tarif)
        REFERENCES tb_tarif(id_tarif),

    FOREIGN KEY (id_user)
        REFERENCES tb_user(id_user),

    FOREIGN KEY (id_area)
        REFERENCES tb_area_parkir(id_area)
) ENGINE=InnoDB;

CREATE TABLE tb_log_aktivitas (
    id_log INT(11) AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11),
    aktivitas VARCHAR(100),
    waktu_aktivitas DATETIME,

    FOREIGN KEY (id_user)
        REFERENCES tb_user(id_user)
) ENGINE=InnoDB;