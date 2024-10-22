<?php
session_start();
require 'db_connect.php'; // Hubungkan dengan file koneksi database

// Cek apakah user sudah login, jika ya arahkan ke dashboard
if (isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fungsi sanitasi input
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// Ketika form login dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = sanitizeInput($_POST['password']);

    // Validasi input tidak kosong
    if (empty($username) || empty($password)) {
        $error_message = 'Username atau password tidak boleh kosong.';
    } else {
        // Query database menggunakan prepared statements
        $sql = "SELECT id, username, password FROM akun_login WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika user ditemukan dan password benar
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID untuk mencegah session fixation
            session_regenerate_id();
            $_SESSION['admin'] = $user['username']; // Set session admin
            header("Location: admin.php");
            exit();
        } else {
            $error_message = 'Username atau password salah.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-container {
            background-color: #ffffff;
            padding: 30px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        .login-container button {
            background-color: #28a745;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 4px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login Admin</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>

            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>
