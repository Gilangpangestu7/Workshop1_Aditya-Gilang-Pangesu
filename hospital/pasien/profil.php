<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['pasien_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pasien
$sql = "SELECT * FROM pasien WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['pasien_id']);
$stmt->execute();
$pasien = $stmt->get_result()->fetch_assoc();

// Update profil
if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    $sql_update = "UPDATE pasien SET nama = ?, alamat = ?, no_hp = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nama, $alamat, $no_hp, $_SESSION['pasien_id']);

    if ($stmt_update->execute()) {
        echo "<script>alert('Profil berhasil diupdate.'); window.location.href='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate profil.');</script>";
    }
}

include 'include/header.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h2>Profil Pasien</h2>
    </div>

    <!-- Profile Card -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">No. Rekam Medis</label>
                            <input type="text" class="form-control" value="<?= $pasien['no_rm'] ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input type="text" class="form-control" value="<?= $pasien['no_ktp'] ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($pasien['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= htmlspecialchars($pasien['alamat']) ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= htmlspecialchars($pasien['no_hp']) ?>" required>
                        </div>
                        <button type="submit" name="update" class="btn btn-teal">Update Profil</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik Kunjungan</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Hitung total kunjungan
                    $sql_total = "SELECT COUNT(*) as total FROM daftar_poli WHERE id_pasien = ?";
                    $stmt_total = $conn->prepare($sql_total);
                    $stmt_total->bind_param("i", $_SESSION['pasien_id']);
                    $stmt_total->execute();
                    $total_kunjungan = $stmt_total->get_result()->fetch_assoc()['total'];

                    // Hitung kunjungan bulan ini
                    $sql_bulan = "SELECT COUNT(*) as total FROM daftar_poli dp 
                                            JOIN periksa p ON dp.id = p.id_daftar_poli 
                                            WHERE dp.id_pasien = ? AND MONTH(p.tgl_periksa) = MONTH(CURRENT_DATE())";
                    $stmt_bulan = $conn->prepare($sql_bulan);
                    $stmt_bulan->bind_param("i", $_SESSION['pasien_id']);
                    $stmt_bulan->execute();
                    $kunjungan_bulan = $stmt_bulan->get_result()->fetch_assoc()['total'];
                    ?>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Kunjungan
                            <span class="badge bg-primary rounded-pill"><?= $total_kunjungan ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kunjungan Bulan Ini
                            <span class="badge bg-info rounded-pill"><?= $kunjungan_bulan ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'include/footer.php'; ?>