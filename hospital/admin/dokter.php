<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Ambil data poli untuk dropdown
$sql_poli = "SELECT * FROM poli ORDER BY nama_poli";
$poli_result = $conn->query($sql_poli);

// Proses tambah dokter
if (isset($_POST['tambah_dokter'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];
    $password = $_POST['password'];

    $sql = "INSERT INTO dokter (nama, alamat, no_hp, id_poli, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $nama, $alamat, $no_hp, $id_poli, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Dokter berhasil ditambahkan!'); window.location.href='dokter.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan dokter!');</script>";
    }
}

// Proses edit dokter
if (isset($_POST['edit_dokter'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    $sql = "UPDATE dokter SET nama=?, alamat=?, no_hp=?, id_poli=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $nama, $alamat, $no_hp, $id_poli, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data dokter berhasil diupdate!'); window.location.href='dokter.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data dokter!');</script>";
    }
}

// Proses hapus dokter
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    $sql = "DELETE FROM dokter WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Dokter berhasil dihapus!'); window.location.href='dokter.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus dokter!');</script>";
    }
}

// Ambil daftar dokter
$sql_dokter = "SELECT d.*, p.nama_poli 
               FROM dokter d 
               JOIN poli p ON d.id_poli = p.id 
               ORDER BY d.nama";
$dokter_result = $conn->query($sql_dokter);

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Dokter</h1>
        <button class="btn btn-dark-primary" data-bs-toggle="modal" data-bs-target="#tambahDokterModal">
            <i class="mdi mdi-plus"></i> Tambah Dokter
        </button>
    </div>

    <!-- Tabel Dokter -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th>Poli</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($dokter = $dokter_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($dokter['nama']) ?></td>
                                <td><?= htmlspecialchars($dokter['alamat']) ?></td>
                                <td><?= htmlspecialchars($dokter['no_hp']) ?></td>
                                <td><?= htmlspecialchars($dokter['nama_poli']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editDokterModal<?= $dokter['id'] ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <a href="?hapus=<?= $dokter['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus dokter ini?')">
                                        <i class="mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($dokter_result->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data dokter</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Dokter -->
<div class="modal fade" id="tambahDokterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Dokter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Dokter</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Poli</label>
                        <select class="form-select" name="id_poli" required>
                            <option value="">Pilih Poli</option>
                            <?php
                            $poli_result->data_seek(0);
                            while ($poli = $poli_result->fetch_assoc()):
                            ?>
                                <option value="<?= $poli['id'] ?>">
                                    <?= htmlspecialchars($poli['nama_poli']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_dokter" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Dokter -->
<?php $dokter_result->data_seek(0); ?>
<?php while ($dokter = $dokter_result->fetch_assoc()): ?>
    <div class="modal fade" id="editDokterModal<?= $dokter['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Dokter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $dokter['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Dokter</label>
                            <input type="text" class="form-control" name="nama" value="<?= $dokter['nama'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= $dokter['alamat'] ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= $dokter['no_hp'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Poli</label>
                            <select class="form-select" name="id_poli" required>
                                <option value="">Pilih Poli</option>
                                <?php
                                $poli_result->data_seek(0);
                                while ($poli = $poli_result->fetch_assoc()):
                                ?>
                                    <option value="<?= $poli['id'] ?>" <?= $dokter['id_poli'] == $poli['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($poli['nama_poli']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_dokter" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>


<?php include 'include/footer.php'; ?>