<?php
require 'db_connect.php'; // Hubungkan dengan database

$response_message = '';
$response_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitasi input
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jabatan = htmlspecialchars(trim($_POST['jabatan']));
    $jenis_kendaraan = htmlspecialchars(trim($_POST['jenis_kendaraan']));
    $jenis_peminjaman = htmlspecialchars(trim($_POST['jenis_peminjaman']));
    $tanggal_peminjaman = htmlspecialchars(trim($_POST['tanggal_peminjaman']));
    $tanggal_pengembalian = htmlspecialchars(trim($_POST['tanggal_pengembalian']));
    $km_awal = filter_var($_POST['km_awal'], FILTER_VALIDATE_INT);
    $lokasi_tujuan = htmlspecialchars(trim($_POST['lokasi_tujuan']));
    $signature = $_POST['signature'];

    // Validasi data
    if (
        empty($nama) || empty($jabatan) || empty($jenis_kendaraan) || 
        empty($jenis_peminjaman) || empty($tanggal_peminjaman) || 
        empty($tanggal_pengembalian) || !$km_awal || empty($lokasi_tujuan) || 
        empty($signature)
    ) {
        $response_message = 'Data tidak valid. Silakan periksa kembali inputan Anda.';
        $response_type = 'danger';
    } else {
        // Simpan data ke database menggunakan prepared statement
        $sql = "INSERT INTO pencatatan_penggunaan_mobil (nama, jabatan, jenis_kendaraan, jenis_peminjaman, tanggal_peminjaman, tanggal_pengembalian, km_awal, lokasi_tujuan, signature) 
                VALUES (:nama, :jabatan, :jenis_kendaraan, :jenis_peminjaman, :tanggal_peminjaman, :tanggal_pengembalian, :km_awal, :lokasi_tujuan, :signature)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':jabatan', $jabatan);
        $stmt->bindParam(':jenis_kendaraan', $jenis_kendaraan);
        $stmt->bindParam(':jenis_peminjaman', $jenis_peminjaman);
        $stmt->bindParam(':tanggal_peminjaman', $tanggal_peminjaman);
        $stmt->bindParam(':tanggal_pengembalian', $tanggal_pengembalian);
        $stmt->bindParam(':km_awal', $km_awal, PDO::PARAM_INT);
        $stmt->bindParam(':lokasi_tujuan', $lokasi_tujuan);
        $stmt->bindParam(':signature', $signature);

        if ($stmt->execute()) {
            $response_message = "Data berhasil disimpan!";
            $response_type = 'success';
        } else {
            $response_message = "Terjadi kesalahan saat menyimpan data.";
            $response_type = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Save Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-card {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .response-card.success { 
            background-color: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb;
        }
        .response-card.danger { 
            background-color: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($response_message)): ?>
            <div class="response-card <?php echo $response_type; ?>">
                <h4 class="mb-3"><?php echo $response_type === 'success' ? 'Berhasil' : 'Gagal'; ?></h4>
                <p><?php echo htmlspecialchars($response_message); ?></p>
            </div>
        <?php endif; ?>

        <!-- Form atau Konten lain bisa ditambahkan di sini -->
        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-primary">Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>
