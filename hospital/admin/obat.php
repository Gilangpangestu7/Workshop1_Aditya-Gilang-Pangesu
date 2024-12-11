<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Proses tambah obat
if (isset($_POST['tambah_obat'])) {
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    $sql = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nama_obat, $kemasan, $harga);

    if ($stmt->execute()) {
        echo "<script>alert('Obat berhasil ditambahkan!'); window.location.href='obat.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan obat!');</script>";
    }
}

// Proses edit obat
if (isset($_POST['edit_obat'])) {
    $id = $_POST['id'];
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'];
    $harga = $_POST['harga'];

    $sql = "UPDATE obat SET nama_obat=?, kemasan=?, harga=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $nama_obat, $kemasan, $harga, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data obat berhasil diupdate!'); window.location.href='obat.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data obat!');</script>";
    }
}

// Proses hapus obat
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah obat pernah digunakan dalam pemeriksaan
    $sql_check = "SELECT COUNT(*) as total FROM detail_periksa WHERE id_obat = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('Obat tidak dapat dihapus karena sudah pernah digunakan dalam pemeriksaan!'); window.location.href='obat.php';</script>";
    } else {
        $sql = "DELETE FROM obat WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Obat berhasil dihapus!'); window.location.href='obat.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus obat!');</script>";
        }
    }
}

// Ambil daftar obat
$sql_obat = "SELECT * FROM obat ORDER BY nama_obat";
$obat_result = $conn->query($sql_obat);

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Obat</h1>
        <button class="btn btn-dark-primary" data-bs-toggle="modal" data-bs-target="#tambahObatModal">
            <i class="mdi mdi-plus"></i> Tambah Obat
        </button>
    </div>

    <!-- Tabel Obat -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Kemasan</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($obat = $obat_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($obat['nama_obat'] ?? '') ?></td>
                                <td><?= htmlspecialchars($obat['kemasan'] ?? '') ?></td>
                                <td>Rp <?= number_format($obat['harga'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editObatModal<?= $obat['id'] ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <a href="?hapus=<?= $obat['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus obat ini?')">
                                        <i class="mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($obat_result->num_rows === 0): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data obat</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Obat -->
<div class="modal fade" id="tambahObatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Obat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Obat</label>
                        <input type="text" class="form-control" name="nama_obat" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kemasan</label>
                        <input type="text" class="form-control" name="kemasan" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="harga" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_obat" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Obat -->
<?php $obat_result = $conn->query($sql_obat); ?>
<?php while ($obat = $obat_result->fetch_assoc()): ?>
    <div class="modal fade" id="editObatModal<?= $obat['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $obat['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Obat</label>
                            <input type="text" class="form-control" name="nama_obat"
                                value="<?= htmlspecialchars($obat['nama_obat'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kemasan</label>
                            <input type="text" class="form-control" name="kemasan"
                                value="<?= htmlspecialchars($obat['kemasan'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" name="harga"
                                    value="<?= htmlspecialchars($obat['harga'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_obat" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php include 'include/footer.php'; ?>