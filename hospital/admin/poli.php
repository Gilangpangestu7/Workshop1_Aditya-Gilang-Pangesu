<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Proses tambah poli
if (isset($_POST['tambah_poli'])) {
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    $sql = "INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nama_poli, $keterangan);

    if ($stmt->execute()) {
        echo "<script>alert('Poli berhasil ditambahkan!'); window.location.href='poli.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan poli!');</script>";
    }
}

// Proses edit poli
if (isset($_POST['edit_poli'])) {
    $id = $_POST['id'];
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    $sql = "UPDATE poli SET nama_poli=?, keterangan=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nama_poli, $keterangan, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data poli berhasil diupdate!'); window.location.href='poli.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data poli!');</script>";
    }
}

// Proses hapus poli
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah poli memiliki dokter
    $sql_check = "SELECT COUNT(*) as total FROM dokter WHERE id_poli = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('Poli tidak dapat dihapus karena memiliki dokter yang terdaftar!'); window.location.href='poli.php';</script>";
    } else {
        $sql = "DELETE FROM poli WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Poli berhasil dihapus!'); window.location.href='poli.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus poli!');</script>";
        }
    }
}

// Ambil daftar poli beserta jumlah dokter
$sql_poli = "SELECT p.*, COUNT(d.id) as jumlah_dokter 
             FROM poli p 
             LEFT JOIN dokter d ON p.id = d.id_poli 
             GROUP BY p.id 
             ORDER BY p.nama_poli";
$poli_result = $conn->query($sql_poli);

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Poli</h1>
        <button class="btn btn-dark-primary" data-bs-toggle="modal" data-bs-target="#tambahPoliModal">
            <i class="mdi mdi-plus"></i> Tambah Poli
        </button>
    </div>

    <!-- Tabel Poli -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Poli</th>
                            <th>Keterangan</th>
                            <th>Jumlah Dokter</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($poli = $poli_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($poli['nama_poli'] ?? '') ?></td>
                                <td><?= htmlspecialchars($poli['keterangan'] ?? '') ?></td>
                                <td>
                                    <span class="badge bg-info"><?= $poli['jumlah_dokter'] ?? 0 ?> Dokter</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editPoliModal<?= $poli['id'] ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <a href="?hapus=<?= $poli['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus poli ini?')">
                                        <i class="mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($poli_result->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data poli</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Poli -->
<div class="modal fade" id="tambahPoliModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Poli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Poli</label>
                        <input type="text" class="form-control" name="nama_poli" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_poli" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Poli -->
<?php $poli_result = $conn->query($sql_poli); ?>
<?php while ($poli = $poli_result->fetch_assoc()): ?>
    <div class="modal fade" id="editPoliModal<?= $poli['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Poli</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $poli['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Poli</label>
                            <input type="text" class="form-control" name="nama_poli"
                                value="<?= htmlspecialchars($poli['nama_poli'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3"
                                required><?= htmlspecialchars($poli['keterangan'] ?? '') ?></textarea>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="edit_poli" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php include 'include/footer.php'; ?>