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

// Query untuk mengambil data berdasarkan bulan dan tahun
$sql = "SELECT * FROM pencatatan_penggunaan_mobil WHERE MONTH(tanggal) = :month AND YEAR(tanggal) = :year";
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
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Tanggal</th>
                    <th>KM</th>
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
                            <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                            <td><?php echo htmlspecialchars($row['km']); ?></td>
                            <td><img src="<?php echo htmlspecialchars($row['signature']); ?>" alt="Tanda Tangan" style="width: 100px;"></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="text-center mt-4">
            <a href="generate_pdf.php?month=<?php echo $selected_month; ?>&year=<?php echo $selected_year; ?>" class="btn btn-success">Download PDF</a>
        </div>
    </div>
</body>
</html>
