<?php
require 'db_connect.php'; // Hubungkan dengan database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jabatan = htmlspecialchars(trim($_POST['jabatan']));
    $tanggal = htmlspecialchars(trim($_POST['tanggal']));
    $km = filter_var($_POST['km'], FILTER_VALIDATE_INT);
    $signature = $_POST['signature'];

    // Validasi data
    if (empty($nama) || empty($jabatan) || empty($tanggal) || !$km || empty($signature)) {
        die('Data tidak valid.');
    }

    // Simpan data ke database menggunakan prepared statement
    $sql = "INSERT INTO pencatatan_penggunaan_mobil (nama, jabatan, tanggal, km, signature) 
            VALUES (:nama, :jabatan, :tanggal, :km, :signature)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':jabatan', $jabatan);
    $stmt->bindParam(':tanggal', $tanggal);
    $stmt->bindParam(':km', $km, PDO::PARAM_INT);
    $stmt->bindParam(':signature', $signature);

    if ($stmt->execute()) {
        echo "Data berhasil disimpan!";
    } else {
        echo "Terjadi kesalahan saat menyimpan data.";
    }
}
?>
