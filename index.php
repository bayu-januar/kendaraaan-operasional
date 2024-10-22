<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penggunaan Mobil</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            margin: 0 auto;
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        canvas {
            border: 1px solid #000;
            width: 100%;
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mt-4 mb-4 text-center">Form Penggunaan Mobil</h2>
            <form action="save_data.php" method="POST" class="needs-validation" novalidate>
                <div class="form-group mb-3">
                    <label for="nama">Nama Pemakai:</label>
                    <input type="text" id="nama" name="nama" class="form-control" required>
                    <div class="invalid-feedback">
                        Nama harus diisi.
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="jabatan">Jabatan Pemakai:</label>
                    <input type="text" id="jabatan" name="jabatan" class="form-control" required>
                    <div class="invalid-feedback">
                        Jabatan harus diisi.
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="tanggal">Tanggal Pemakaian:</label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" required>
                    <div class="invalid-feedback">
                        Tanggal pemakaian harus diisi.
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="km">KM Pemakaian:</label>
                    <input type="number" id="km" name="km" class="form-control" required>
                    <div class="invalid-feedback">
                        KM pemakaian harus diisi.
                    </div>
                </div>
                <h3 class="mb-3">Tanda Tangan Pemakai</h3>
                <canvas id="signature-pad" class="mb-3"></canvas>
                <button type="button" id="clear" class="btn btn-warning mb-3">Clear Tanda Tangan</button>

                <!-- Field untuk menyimpan data tanda tangan dalam bentuk base64 -->
                <input type="hidden" name="signature" id="signature">
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tambahkan Bootstrap JS dan Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>

    <script>
        // Signature Pad setup
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas);

        // Fungsi untuk menyesuaikan ukuran canvas berdasarkan device pixel ratio
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Tombol Clear untuk membersihkan canvas
        document.getElementById('clear').addEventListener('click', function() {
            signaturePad.clear();
        });

        // Sebelum submit form, simpan data tanda tangan ke dalam hidden input
        document.querySelector('form').addEventListener('submit', function(event) {
            if (signaturePad.isEmpty()) {
                alert('Tanda tangan harus diisi!');
                event.preventDefault();
            } else {
                const dataURL = signaturePad.toDataURL('image/png');
                document.getElementById('signature').value = dataURL;
            }
        });

        // Bootstrap validation
        (function () {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>
