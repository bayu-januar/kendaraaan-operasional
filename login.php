<?php
session_start();
require 'db_connect.php'; // Koneksi database

// Pastikan admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Validasi input bulan dan tahun
$selected_month = isset($_GET['month']) ? filter_var($_GET['month'], FILTER_SANITIZE_NUMBER_INT) : date('m');
$selected_year = isset($_GET['year']) ? filter_var($_GET['year'], FILTER_SANITIZE_NUMBER_INT) : date('Y');

// Query untuk menghitung jumlah peminjaman per bulan di tahun yang dipilih
$rekap_bulanan = [];
for ($month = 1; $month <= 12; $month++) {
    $sql_rekap = "SELECT COUNT(*) as jumlah_peminjaman FROM pencatatan_penggunaan_mobil WHERE MONTH(tanggal_peminjaman) = :month AND YEAR(tanggal_peminjaman) = :year";
    $stmt_rekap = $pdo->prepare($sql_rekap);
    $stmt_rekap->bindParam(':month', $month, PDO::PARAM_INT);
    $stmt_rekap->bindParam(':year', $selected_year, PDO::PARAM_INT);
    $stmt_rekap->execute();
    $rekap_bulanan[$month] = $stmt_rekap->fetch(PDO::FETCH_ASSOC)['jumlah_peminjaman'];
}



// Query untuk mengambil data berdasarkan bulan dan tahun
$sql = "SELECT * FROM pencatatan_penggunaan_mobil WHERE MONTH(tanggal_peminjaman) = :month AND YEAR(tanggal_peminjaman) = :year";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':month', $selected_month, PDO::PARAM_INT);
$stmt->bindParam(':year', $selected_year, PDO::PARAM_INT);
$stmt->execute();
$filtered_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Rekap Penggunaan Mobil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Rekap Penggunaan Mobil</h1>
        <form method="GET" class="mt-4 mb-4 text-center">
            <div class="row justify-content-center">
                <div class="col-md-2">
                    <select name="month" class="form-select" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $selected_month ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-select" required>
                        <?php for ($i = 2020; $i <= date('Y'); $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $i == $selected_year ? 'selected' : ''; ?>>
                                <?php echo $i; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                </div>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Peminjam</th>
                    <th>Jabatan Peminjam</th>
                    <th>Jenis Kendaraan</th>
                    <th>Jenis Peminjaman</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Tanggal Pengembalian</th>
                    <th>KM Awal</th>
                    <th>Lokasi Tujuan</th>
                    <th>Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($filtered_data)): ?>
                    <?php foreach ($filtered_data as $key => $row): ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['nama']); ?></td>
                            <td><?php echo htmlspecialchars($row['jabatan']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_kendaraan']); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis_peminjaman']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_peminjaman']); ?></td>
                            <td><?php echo htmlspecialchars($row['tanggal_pengembalian']); ?></td>
                            <td><?php echo htmlspecialchars($row['km_awal']); ?></td>
                            <td><?php echo htmlspecialchars($row['lokasi_tujuan']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['signature']); ?>" alt="Tanda Tangan" style="width: 100px;"></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="text-center mt-4">
            <a href="generate_pdf.php?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="btn btn-success">Download PDF</a>
        </div>
        
        <div class="container mt-5">
   
    <!-- Form dan tabel data tetap sama seperti sebelumnya -->

    <!-- Grafik Statistik Peminjaman -->
    <h2 class="text-center mt-5">Statistik Peminjaman Kendaraan Tahun <?php echo $selected_year; ?></h2>
    <canvas id="rekapChart" width="400" height="200"></canvas>
</div>

        
    </div>
    
    
    
    
    
    
    
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Data rekap bulanan dari PHP
var rekapBulanan = <?php echo json_encode(array_values($rekap_bulanan)); ?>;

var ctx = document.getElementById('rekapChart').getContext('2d');
var rekapChart = new Chart(ctx, {
    type: 'bar', // Jenis chart: bisa 'line', 'bar', 'pie', dll.
    data: {
        labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
        datasets: [{
            label: 'Jumlah Peminjaman',
            data: rekapBulanan,
            backgroundColor: 'rgba(54, 162, 235, 0.2)', // Warna batang
            borderColor: 'rgba(54, 162, 235, 1)', // Warna garis
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

    
    
    
    
    
    
    
</body>
</html>
