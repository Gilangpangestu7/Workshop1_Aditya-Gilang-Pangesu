<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Temu Janji Pasien - Dokter</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
   html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    font-family: 'Poppins', sans-serif;
}

/* Kontainer utama dengan flexbox */
.container-fluid {
    height: 100vh;
    display: flex;
    flex-wrap: nowrap;
    margin: 0;
    padding: 0;
}

/* Sisi kiri - Biru */
.left-section {
    background-color: #0B3C8F;
    color: white;
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 40px;
    min-height: 100vh;
    position: relative;
    overflow: hidden;
}

/* Sisi kanan - Putih */
.right-section {
    background-color: white;
    width: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 20px;
    min-height: 100vh;
}

/* Animasi typing yang berulang terus */
.typing-text {
    display: inline-block;
    font-size: 1.5rem;
    font-weight: normal; /* Menghapus bold */
    white-space: nowrap;
    overflow: hidden;
    border-right: 4px solid #fff; /* Meniru efek kursor */
    animation: typing 3.5s steps(30) infinite, blink 0.75s step-end infinite;
}

/* Animasi typing */
@keyframes typing {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

/* Efek kedip untuk kursor */
@keyframes blink {
    50% {
        border-color: transparent;
    }
}

/* Gaya untuk form login */
.login-form {
    width: 100%;
    max-width: 400px;
    background-color: #ffffff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    padding: 20px;
    opacity: 0;
    transform: translateX(100px);
}

/* Card styling dengan shadow */
.login-option {
    margin-bottom: 20px;
    text-align: center;
    padding: 20px;
    background: white; /* Card putih */
    border-radius: 15px;
    width: 80%;
    max-width: 390px;
    opacity: 0;
    animation: fadeInUp 1.2s ease-out forwards;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Shadow effect lebih jelas */
}

.login-option h5 {
    font-size: 1.5rem;
    color: #333;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
}

.login-option p {
    font-size: 1rem;
    color: #555;
    margin-bottom: 15px;
    position: relative;
    z-index: 1;
}

.btn {
    padding: 10px;
    font-size: 0.9rem;
    width: 70%;
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
    position: relative;
    z-index: 1;
}

.btn:hover {
    transform: translateY(-3px);
}

.btn-pasien {
    background-color: #28a745;
    color: white;
}

.btn-pasien:hover {
    background-color: #218838;
}

.btn-dokter {
    background-color: #0B3C8F;
    color: white;
}

.btn-dokter:hover {
    background-color: #0056b3;
}

/* Responsif */
@media (max-width: 768px) {
    .container-fluid {
        flex-wrap: wrap;
        height: auto; /* Menghindari tinggi kontainer terlalu besar */
    }

    .left-section, .right-section {
        width: 100%;
        min-height: 50vh; /* Memberi ruang lebih agar konten lebih proporsional */
    }

    /* Teks lebih kecil di mobile */
    .typing-text {
        font-size: 1.2rem; /* Ukuran font lebih kecil */
    }

    .login-option {
        width: 100%;
        max-width: none;
        padding: 15px; /* Mengurangi padding agar lebih efisien */
    }

    /* Tombol lebar di mobile */
    .btn {
        width: 100%;
        padding: 15px;
        font-size: 1rem; /* Ukuran font lebih besar agar tombol mudah diklik */
    }

    .btn-pasien, .btn-dokter {
        font-size: 1rem; /* Font tombol lebih besar untuk mobile */
    }
}

/* Animasi fadeInUp */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>

</head>
<body>
    <div class="container-fluid">
        <div class="left-section">
            <div class="text-container">
                <h1>Sistem Temu Janji Pasien - Dokter</h1>
                <p class="typing-text">Memberikan layanan terbaik untuk kesehatan Anda</p>
            </div>
        </div>
        <div class="right-section">
            <div class="login-option">
                <h5>Registrasi Sebagai Pasien</h5>
                <p>Apabila Anda adalah seorang Pasien, silahkan Registrasi terlebih dahulu untuk melakukan pendaftaran sebagai Pasien!</p>
                <a href="pasien/pendaftaran.php" class="btn btn-pasien">
                    Registrasi
                </a>
            </div>
            <div class="login-option">
                <h5>Login Sebagai Dokter</h5>
                <p>Apabila Anda adalah seorang Dokter, silahkan Login terlebih dahulu untuk memulai melayani Pasien!</p>
                <a href="dokter/login.php" class="btn btn-dokter">
                    Login Dokter
                </a>
            </div>
            <div class="login-option">
                <h5>Login Sebagai Admin</h5>
                <p>Apabila Anda adalah seorang Admin, silahkan Login terlebih dahulu untuk mengelola sistem!</p>
                <a href="admin/login.php" class="btn btn-dokter">
                    Login Admin
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
