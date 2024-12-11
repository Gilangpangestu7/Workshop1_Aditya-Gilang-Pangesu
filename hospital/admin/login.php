<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Jangan lupa gunakan hashing lebih aman di produksi

    $sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Reset styling untuk menghindari margin dan padding default */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: #444;
        }

        /* Kontainer untuk layout login */
        .login-container {
            display: flex;
            height: 100vh;
        }

        /* Sisi kiri dengan animasi masuk dari kiri */
        .left-side {
            background-color: #0B3C8F;
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 2rem;
            text-align: center;
            font-weight: bold;
            animation: slideInLeft 1s ease-out;
        }

        /* Sisi kanan dengan form login */
        .right-side {
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
        }

        /* Card login dengan animasi masuk dari bawah */
        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            animation: slideUp 1s ease-out;
        }

        .login-card:hover {
            transform: scale(1.05);
        }

        /* Gaya header (judul) */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
        }

        /* Gaya label form */
        .form-label {
           
            color: #555;
        }

        /* Gaya input form */
        .form-control {
            border-radius: 10px;
            padding: 15px;
            border: 1px solid #0072ff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #00c6ff;
            box-shadow: 0 0 8px rgba(0, 194, 255, 0.5);
        }

        /* Gaya tombol login */
        .btn-login {
            background-color: #0072ff;
            color: white;
            font-weight: bold;
            padding: 15px;
            border-radius: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #00c6ff;
            cursor: pointer;
            transform: translateY(-5px); /* Efek tombol terangkat */
            transition: transform 0.2s ease-out;
        }

        /* Teks di bawah form (daftar sekarang) */
        .text-center {
            margin-top: 20px;
        }

        .text-center a {
            color: #0072ff;
            text-decoration: none;
            font-weight: bold;
        }

        .text-center a:hover {
            text-decoration: underline;
        }

        /* Animasi masuk dari kiri untuk .left-side */
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Animasi masuk dari bawah untuk .login-card */
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        /* Footer halaman */
.footer {
    text-align: center;
    margin-top: 25px;
    font-size: 14px;
    color: #777;
}

.footer a {
    text-decoration: none;
    color: #045FCFFF;
}

.footer a:hover {
    text-decoration: underline;
}
    </style>
</head>
<body>
<div class="login-container">
    <div class="left-side">
        <div>Selamat Datang di Login Admin</div>
    </div>
    <div class="right-side">
        <div class="login-card">
            <h2>Login Admin</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="footer">
                    <p>&copy; 2024 Aditya Gilang Pangestu</p>
                </div>
            </form>
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
            <?php endif; ?>
        </div>
        
    </div>
</div>
</body>
</html>
