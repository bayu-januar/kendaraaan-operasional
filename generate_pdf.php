<?php
session_start();
require('assets/lib/fpdf.php');
require('db_connect.php'); // Hubungkan dengan database

// Cek apakah user admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validasi input bulan dan tahun dari query string
$selected_month = isset($_GET['month']) && is_numeric($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : date('Y');

// Ambil data dari database berdasarkan bulan dan tahun
$sql = "SELECT nama, jabatan, tanggal, km, signature 
        FROM pencatatan_penggunaan_mobil 
        WHERE MONTH(tanggal) = :selected_month AND YEAR(tanggal) = :selected_year";
$stmt = $pdo->prepare($sql); // Perbaikan disini
$stmt->bindParam(':selected_month', $selected_month, PDO::PARAM_INT);
$stmt->bindParam(':selected_year', $selected_year, PDO::PARAM_INT);
$stmt->execute();
$filtered_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Membuat PDF dengan FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 10, 'Rekap Penggunaan Mobil', 0, 1, 'C');
$pdf->Cell(190, 10, 'Bulan: ' . date('F', mktime(0, 0, 0, $selected_month, 1)) . ' Tahun: ' . $selected_year, 0, 1, 'C');
$pdf->Ln(10);

// Membuat header tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(40, 10, 'Nama', 1);
$pdf->Cell(40, 10, 'Jabatan', 1);
$pdf->Cell(30, 10, 'Tanggal', 1);
$pdf->Cell(20, 10, 'KM', 1);
$pdf->Cell(50, 10, 'Tanda Tangan', 1);
$pdf->Ln();

// Mengisi tabel dengan data yang difilter dari database
$pdf->SetFont('Arial', '', 10);
if (!empty($filtered_data)) {
    foreach ($filtered_data as $key => $fields) {
        $pdf->Cell(10, 10, $key + 1, 1);
        $pdf->Cell(40, 10, $fields['nama'], 1); // Nama pemakai
        $pdf->Cell(40, 10, $fields['jabatan'], 1); // Jabatan pemakai
        $pdf->Cell(30, 10, $fields['tanggal'], 1); // Tanggal pemakaian
        $pdf->Cell(20, 10, $fields['km'], 1); // KM pemakaian

        // Cek apakah tanda tangan tersedia dan tampilkan "Tersedia" atau "Tidak ada"
        if (!empty(trim($fields['signature']))) {
            $pdf->Cell(50, 10, 'Tersedia', 1); // Jika tanda tangan ada
        } else {
            $pdf->Cell(50, 10, 'Tidak ada', 1); // Jika tidak ada tanda tangan
        }
        $pdf->Ln();
    }
} else {
    // Jika tidak ada data yang sesuai
    $pdf->Cell(190, 10, 'Tidak ada data.', 1, 1, 'C');
}

// Output file PDF untuk di-download
$pdf->Output();
?>
