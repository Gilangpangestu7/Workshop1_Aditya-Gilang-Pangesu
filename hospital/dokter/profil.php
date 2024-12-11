<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['dokter_id'])) {
    header('Location: dokter_login.php');
    exit;
}

$dokter_id = $_SESSION['dokter_id'];

// Ambil data dokter
$sql_dokter = "SELECT d.*, p.nama_poli, p.id as id_poli 
               FROM dokter d
               JOIN poli p ON d.id_poli = p.id
               WHERE d.id = ?";
$stmt_dokter = $conn->prepare($sql_dokter);
$stmt_dokter->bind_param("i", $dokter_id);
$stmt_dokter->execute();
$dokter = $stmt_dokter->get_result()->fetch_assoc();

// Ambil daftar poli untuk dropdown
$sql_poli = "SELECT * FROM poli ORDER BY nama_poli";
$poli_result = $conn->query($sql_poli);

// Proses update profile
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    $sql_update = "UPDATE dokter SET nama = ?, alamat = ?, no_hp = ?, id_poli = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssii", $nama, $alamat, $no_hp, $id_poli, $dokter_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Profil berhasil diupdate!'); window.location.href='profil.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate profil!');</script>";
    }
}

// Proses update password
if (isset($_POST['update_password'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Verifikasi password lama
    $sql_check = "SELECT password FROM dokter WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $dokter_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result()->fetch_assoc();

    if ($password_lama == $result['password']) {
        if ($password_baru === $konfirmasi_password) {
            $sql_password = "UPDATE dokter SET password = ? WHERE id = ?";
            $stmt_password = $conn->prepare($sql_password);
            $stmt_password->bind_param("si", $password_baru, $dokter_id);

            if ($stmt_password->execute()) {
                echo "<script>alert('Password berhasil diupdate!'); window.location.href='profil.php';</script>";
            } else {
                echo "<script>alert('Gagal mengupdate password!');</script>";
            }
        } else {
            echo "<script>alert('Konfirmasi password tidak sesuai!');</script>";
        }
    } else {
        echo "<script>alert('Password lama tidak sesuai!');</script>";
    }
}

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Profil Dokter</h1>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Profil</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama"
                                value="<?= htmlspecialchars($dokter['nama']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3"
                                required><?= htmlspecialchars($dokter['alamat']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="no_hp"
                                value="<?= htmlspecialchars($dokter['no_hp']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poli</label>
                            <select class="form-select" name="id_poli" required>
                                <?php while ($poli = $poli_result->fetch_assoc()): ?>
                                    <option value="<?= $poli['id'] ?>"
                                        <?= $poli['id'] == $dokter['id_poli'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($poli['nama_poli']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-cyan">
                            Update Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Update and Stats -->
        <div class="col-md-4">
            <!-- Change Password Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" name="password_lama" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" name="password_baru" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="konfirmasi_password" required>
                        </div>

                        <button type="submit" name="update_password" class="btn btn-warning w-100">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik</h5>
                </div>
                <div class="card-body">
                    <?php
                    // Total pasien yang sudah diperiksa
                    $sql_total_pasien = "SELECT COUNT(DISTINCT dp.id_pasien) as total 
                                       FROM periksa pr
                                       JOIN daftar_poli dp ON pr.id_daftar_poli = dp.id
                                       JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                                       WHERE jp.id_dokter = ?";
                    $stmt_total = $conn->prepare($sql_total_pasien);
                    $stmt_total->bind_param("i", $dokter_id);
                    $stmt_total->execute();
                    $total_pasien = $stmt_total->get_result()->fetch_assoc()['total'];

                    // Total pemeriksaan bulan ini
                    $sql_bulan_ini = "SELECT COUNT(*) as total 
                                    FROM periksa pr
                                    JOIN daftar_poli dp ON pr.id_daftar_poli = dp.id
                                    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                                    WHERE jp.id_dokter = ? 
                                    AND MONTH(pr.tgl_periksa) = MONTH(CURRENT_DATE())
                                    AND YEAR(pr.tgl_periksa) = YEAR(CURRENT_DATE())";
                    $stmt_bulan = $conn->prepare($sql_bulan_ini);
                    $stmt_bulan->bind_param("i", $dokter_id);
                    $stmt_bulan->execute();
                    $pemeriksaan_bulan = $stmt_bulan->get_result()->fetch_assoc()['total'];
                    ?>

                    <div class="mb-3">
                        <label class="form-label text-muted">Total Pasien</label>
                        <div class="h3"><?= number_format($total_pasien) ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Pemeriksaan Bulan Ini</label>
                        <div class="h3"><?= number_format($pemeriksaan_bulan) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'include/footer.php'; ?>