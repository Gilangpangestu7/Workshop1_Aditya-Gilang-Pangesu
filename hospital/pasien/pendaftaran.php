<?php
session_start();
include '../config/koneksi.php';

// Fungsi untuk generate nomor rekam medis baru
function generateNoRM($conn)
{
    $tahun = date("Y"); // Tahun saat ini
    $bulan = date("m"); // Bulan saat ini

    // Ambil jumlah pasien dari ID pasien yang paling terakhir
    $query = $conn->query("SELECT COUNT(*) AS total FROM pasien");
    $urutan = 1; // Default urutan jika belum ada pasien

    if ($query && $row = $query->fetch_assoc()) {
        $urutan += $row['total']; // Hitung urutan berdasarkan jumlah total pasien
    }

    // Format No RM sesuai kebutuhan
    $no_rm = sprintf("%s%s-%03d", $tahun, $bulan, $urutan);

    return $no_rm;
}


// Proses permintaan nomor RM secara asinkron
if (isset($_GET['action']) && $_GET['action'] === 'generateNoRM') {
    echo generateNoRM($conn);
    exit;
}

// Proses pendaftaran pasien baru
if (isset($_POST['submit'])) {
    $nama = $conn->real_escape_string(trim($_POST['nama']));
    $alamat = $conn->real_escape_string(trim($_POST['alamat']));
    $no_ktp = $conn->real_escape_string(trim($_POST['no_ktp']));
    $no_hp = $conn->real_escape_string(trim($_POST['no_hp']));
    $no_rm = generateNoRM($conn);  // Generate nomor rekam medis otomatis

    // Cek apakah pasien dengan no_ktp sudah ada
    $cek = $conn->query("SELECT id FROM pasien WHERE no_ktp = '$no_ktp'");
    if ($cek && $cek->num_rows > 0) {
        echo "<script>alert('Pasien sudah terdaftar!');</script>";
    } else {
        // Masukkan data pasien ke database
        $stmt = $conn->prepare("INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);
        if ($stmt->execute()) {
            // Ambil ID pasien yang baru saja ditambahkan
            $pasien_id = $conn->insert_id;
            $_SESSION['pasien_id'] = $pasien_id;
            $_SESSION['no_rm'] = $no_rm;
            echo "<script>alert('Pasien berhasil didaftarkan dengan No RM: $no_rm'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar pasien.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pasien Baru</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
body {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}

.row {
    height: 100vh;
}

.left-section {
    background-color: #0B3C8F;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center; /* Vertikal center */
    align-items: center; /* Horizontal center */
    padding: 20px;
    text-align: center; /* Center the text inside */
}

.left-section h1 {
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 20px;
}

.left-section p {
    font-size: 1.2rem;
    white-space: nowrap;
    overflow: hidden;
    width: 0;
    display: inline-block; /* Ensure it respects width animation */
    animation: typing 5s steps(40) 1s forwards, blink 0.75s step-end infinite;
}

@keyframes typing {
    from {
        width: 0;
    }
    to {
        width: 100%;
    }
}

@keyframes blink {
    50% {
        border-color: transparent;
    }
}

.text-center {
    font-weight: 600;
}

.right-section {
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px;
}

.form-container {
    width: 100%;
    max-width: 500px;
    padding: 40px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.form-label {
    font-weight: light;
}

.form-control {
    border-radius: 10px;
    padding: 10px;
}

.btn-submit {
    background-color: #0072ff;
    color: white;
    font-weight: bold;
    padding: 10px;
    border-radius: 10px;
}

.btn-submit:hover {
    background-color: #005bb5;
}

    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 left-section">
                <h1>Registrasi Pasien</h1>
                <p>Daftarkan data pasien baru dengan mudah dan cepat.</p>
            </div>
            <div class="col-md-6 right-section">
                <div class="form-container">
                    <h2 class="text-center">Formulir Registrasi</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama:</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat:</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_ktp" class="form-label">No KTP:</label>
                            <input type="text" name="no_ktp" id="no_ktp" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label">No HP:</label>
                            <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                        </div>

                        <input type="hidden" id="no_rm" class="form-control" readonly>

                        <button type="submit" name="submit" class="btn btn-submit w-100">Daftarkan Pasien</button>
                    </form>

                    <p class="text-center mt-3">
                        <a href="login.php">Sudah terdaftar? Klik di sini untuk login.</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("?action=generateNoRM")
                .then(response => response.text())
                .then(data => {
                    document.getElementById("no_rm").value = data;
                })
                .catch(error => console.error("Error generating No RM:", error));
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>