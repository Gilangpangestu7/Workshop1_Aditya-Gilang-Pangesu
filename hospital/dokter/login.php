<?php
include '../config/koneksi.php';

// Proses login
if (isset($_POST['login'])) {
    $no_hp = $_POST['no_hp'];
    $password = $_POST['password']; // Sesuaikan dengan metode autentikasi yang diinginkan

    // Cek apakah no_hp dan password valid
    $result = $conn->query("SELECT * FROM dokter WHERE no_hp = '$no_hp' AND password = '$password'"); // Password harus di-hash
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_start();
        $_SESSION['dokter_id'] = $row['id'];
        $_SESSION['dokter_name'] = $row['nama'];
        header('Location: dashboard.php');
        exit;
    } else {
        echo "<script>alert('Login gagal. Periksa nomor HP dan password Anda.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dokter</title>
    <style>
        /* Reset basic styling */
        body, h2, label, input, button {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }

        /* Left section with blue background */
        .left-section {
            background-color: #0B3C8F;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50%;
            height: 100vh;
        }

        .left-section h1 {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
        }

        /* Right section with form */
        .right-section {
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50%;
            height: 100vh;
            padding: 40px;
        }

        .login-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: scale(1.05);
        }

        h2 {
            font-size: 26px;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="password"] {
            width: 92%;
            padding: 15px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #777;
        }

        .footer a {
            text-decoration: none;
            color: #007bff;
        }

        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <!-- Left Section: Blue Background -->
    <div class="left-section">
        <h1>Selamat Datang di Login Dokter</h1>
    </div>

    <!-- Right Section: White Background with Login Form -->
    <div class="right-section">
        <div class="login-container">
            <h2>Login Dokter</h2>
            <form method="POST">
                <label for="no_hp">No HP:</label>
                <input type="text" name="no_hp" id="no_hp" required placeholder="Masukkan No HP">
                
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required placeholder="Masukkan Password">
                
                <button type="submit" name="login">Login</button>
            </form>
            <div class="footer">
                <p>&copy; 2024 Aditya Gilang Pangestu</p>
            </div>
        </div>
    </div>

</body>
</html>
