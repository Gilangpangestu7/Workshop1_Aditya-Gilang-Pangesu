<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['dokter_id'])) {
    header('Location: dokter_login.php');
    exit;
}

$dokter_id = $_SESSION['dokter_id'];

// Ambil semua jadwal dokter
$sql_jadwal = "SELECT * FROM jadwal_periksa WHERE id_dokter = ? ORDER BY hari";
$stmt_jadwal = $conn->prepare($sql_jadwal);
$stmt_jadwal->bind_param("i", $dokter_id);
$stmt_jadwal->execute();
$jadwal_result = $stmt_jadwal->get_result();

// Proses tambah jadwal
if (isset($_POST['tambah_jadwal'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Cek jadwal yang bertabrakan
    $sql_cek = "SELECT * FROM jadwal_periksa 
                WHERE id_dokter = ? 
                AND hari = ? 
                AND ((jam_mulai BETWEEN ? AND ?) 
                OR (jam_selesai BETWEEN ? AND ?))";
    $stmt_cek = $conn->prepare($sql_cek);
    $stmt_cek->bind_param("isssss", $dokter_id, $hari, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();

    if ($result_cek->num_rows > 0) {
        echo "<script>alert('Jadwal bertabrakan dengan jadwal yang sudah ada!');</script>";
    } else {
        $sql_tambah = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)";
        $stmt_tambah = $conn->prepare($sql_tambah);
        $stmt_tambah->bind_param("isss", $dokter_id, $hari, $jam_mulai, $jam_selesai);

        if ($stmt_tambah->execute()) {
            echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location.href='jadwal_periksa.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan jadwal!');</script>";
        }
    }
}
// Proses edit jadwal
if (isset($_POST['edit_jadwal'])) {
    $jadwal_id = $_POST['jadwal_id'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    // Cek jadwal yang bertabrakan
    $sql_cek = "SELECT * FROM jadwal_periksa 
                WHERE id_dokter = ? 
                AND hari = ? 
                AND ((jam_mulai BETWEEN ? AND ?) 
                OR (jam_selesai BETWEEN ? AND ?)) 
                AND id != ?";
    $stmt_cek = $conn->prepare($sql_cek);
    $stmt_cek->bind_param("isssssi", $dokter_id, $hari, $jam_mulai, $jam_selesai, $jam_mulai, $jam_selesai, $jadwal_id);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();

    if ($result_cek->num_rows > 0) {
        echo "<script>alert('Jadwal bertabrakan dengan jadwal yang sudah ada!');</script>";
    } else {
        $sql_edit = "UPDATE jadwal_periksa SET hari = ?, jam_mulai = ?, jam_selesai = ? WHERE id = ? AND id_dokter = ?";
        $stmt_edit = $conn->prepare($sql_edit);
        $stmt_edit->bind_param("ssssi", $hari, $jam_mulai, $jam_selesai, $jadwal_id, $dokter_id);

        if ($stmt_edit->execute()) {
            echo "<script>alert('Jadwal berhasil diubah!'); window.location.href='jadwal_periksa.php';</script>";
        } else {
            echo "<script>alert('Gagal mengubah jadwal!');</script>";
        }
    }
}

// Proses penghapusan data
if (isset($_POST['delete']) && isset($_POST['jadwal_id'])) {
    $jadwal_id = $_POST['jadwal_id'];

    // Query untuk menghapus data berdasarkan ID jadwal
    $query_delete = "DELETE FROM jadwal_periksa WHERE id = ?";
    $stmt = $conn->prepare($query_delete);
    $stmt->bind_param("i", $jadwal_id); // "i" berarti integer (untuk ID)

    if ($stmt->execute()) {
        echo "<script>alert('Data jadwal berhasil dihapus'); window.location.href='jadwal_periksa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data jadwal');</script>";
    }
}

// Proses aktivasi jadwal
if (isset($_POST['aktivasi_jadwal'])) {
    $jadwal_id = $_POST['jadwal_id'];
    $status = $_POST['status'];

    // Nonaktifkan semua jadwal terlebih dahulu
    $sql_nonaktif = "UPDATE jadwal_periksa SET status = 0 WHERE id_dokter = ?";
    $stmt_nonaktif = $conn->prepare($sql_nonaktif);
    $stmt_nonaktif->bind_param("i", $dokter_id);
    $stmt_nonaktif->execute();

    // Aktifkan jadwal yang dipilih
    $sql_aktif = "UPDATE jadwal_periksa SET status = 1 WHERE id = ? AND id_dokter = ?";
    $stmt_aktif = $conn->prepare($sql_aktif);
    $stmt_aktif->bind_param("ii", $jadwal_id, $dokter_id);

    if ($stmt_aktif->execute()) {
        echo "<script>alert('Status jadwal berhasil diubah!'); window.location.href='jadwal_periksa.php';</script>";
    } else {
        echo "<script>alert('Gagal mengubah status jadwal!');</script>";
    }
}

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Jadwal Periksa</h1>
        <button class="btn btn-cyan" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
            <i class="mdi mdi-plus"></i> Tambah Jadwal
        </button>
    </div>

    <!-- Jadwal Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Jadwal</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($jadwal = $jadwal_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $jadwal['hari'] ?></td>
                                <td><?= substr($jadwal['jam_mulai'], 0, 5) ?></td>
                                <td><?= substr($jadwal['jam_selesai'], 0, 5) ?></td>
                                <td>
                                    <?php if ($jadwal['status']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$jadwal['status']): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit" name="aktivasi_jadwal" class="btn btn-sm btn-success">
                                                <i class="mdi mdi-check"></i> Aktifkan
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <?php if ($jadwal['status'] && date('l') !== $jadwal['hari']): ?>
                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editJadwalModal<?= $jadwal['id'] ?>">
                                            <i class="mdi mdi-pencil"></i> Edit
                                        </button>
                                    <?php endif; ?>
                                    <form action="" method="POST" style="display:inline;">
                                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($jadwal_result->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada jadwal yang ditambahkan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Jadwal -->
<div class="modal fade" id="tambahJadwalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jadwal Periksa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Hari</label>
                        <select name="hari" class="form-select" required>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_jadwal" class="btn btn-cyan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<?php $jadwal_result->data_seek(0); ?>
<?php while ($jadwal = $jadwal_result->fetch_assoc()): ?>
    <div class="modal fade" id="editJadwalModal<?= $jadwal['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Jadwal Periksa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Hari</label>
                            <select name="hari" class="form-select" required>
                                <option value="Senin" <?= $jadwal['hari'] === 'Senin' ? 'selected' : '' ?>>Senin</option>
                                <option value="Selasa" <?= $jadwal['hari'] === 'Selasa' ? 'selected' : '' ?>>Selasa</option>
                                <option value="Rabu" <?= $jadwal['hari'] === 'Rabu' ? 'selected' : '' ?>>Rabu</option>
                                <option value="Kamis" <?= $jadwal['hari'] === 'Kamis' ? 'selected' : '' ?>>Kamis</option>
                                <option value="Jumat" <?= $jadwal['hari'] === 'Jumat' ? 'selected' : '' ?>>Jumat</option>
                                <option value="Sabtu" <?= $jadwal['hari'] === 'Sabtu' ? 'selected' : '' ?>>Sabtu</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="<?= $jadwal['jam_mulai'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="<?= $jadwal['jam_selesai'] ?>" required>
                        </div>
                        <input type="hidden" name="jadwal_id" value="<?= $jadwal['id'] ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_jadwal" class="btn btn-cyan">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php include 'include/footer.php'; ?>