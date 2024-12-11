<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Proses tambah pasien
if (isset($_POST['tambah_pasien'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    // Generate nomor rekam medis (yyyymm-nomor_urut)
    $tahun_bulan = date('Ym');
    $sql_check = "SELECT MAX(SUBSTRING_INDEX(no_rm, '-', -1)) as last_number FROM pasien WHERE no_rm LIKE '$tahun_bulan-%'";
    $result_check = $conn->query($sql_check);
    $row = $result_check->fetch_assoc();
    $last_number = $row['last_number'] ? $row['last_number'] : 0;
    $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
    $no_rm = $tahun_bulan . '-' . $new_number;

    $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);

    if ($stmt->execute()) {
        echo "<script>alert('Pasien berhasil ditambahkan!'); window.location.href='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pasien!');</script>";
    }
}

// Proses edit pasien
if (isset($_POST['edit_pasien'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];

    $sql = "UPDATE pasien SET nama=?, alamat=?, no_ktp=?, no_hp=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nama, $alamat, $no_ktp, $no_hp, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Data pasien berhasil diupdate!'); window.location.href='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data pasien!');</script>";
    }
}

// Proses hapus pasien
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    // Cek apakah pasien memiliki riwayat daftar poli
    $sql_check = "SELECT COUNT(*) as total FROM daftar_poli WHERE id_pasien = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row = $result_check->fetch_assoc();

    if ($row['total'] > 0) {
        echo "<script>alert('Pasien tidak dapat dihapus karena memiliki riwayat kunjungan!'); window.location.href='pasien.php';</script>";
    } else {
        $sql = "DELETE FROM pasien WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<script>alert('Pasien berhasil dihapus!'); window.location.href='pasien.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus pasien!');</script>";
        }
    }
}

// Ambil daftar pasien
$sql_pasien = "SELECT * FROM pasien ORDER BY id DESC";
$pasien_result = $conn->query($sql_pasien);

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Data Pasien</h1>
        <button class="btn btn-dark-primary" data-bs-toggle="modal" data-bs-target="#tambahPasienModal">
            <i class="mdi mdi-plus"></i> Tambah Pasien
        </button>
    </div>

    <!-- Tabel Pasien -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. RM</th>
                            <th>Nama</th>
                            <th>No. KTP</th>
                            <th>No. HP</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($pasien = $pasien_result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($pasien['no_rm']) ?></td>
                                <td><?= htmlspecialchars($pasien['nama']) ?></td>
                                <td><?= htmlspecialchars($pasien['no_ktp']) ?></td>
                                <td><?= htmlspecialchars($pasien['no_hp']) ?></td>
                                <td><?= htmlspecialchars($pasien['alamat']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                        data-bs-target="#editPasienModal<?= $pasien['id'] ?>">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <a href="?hapus=<?= $pasien['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pasien ini?')">
                                        <i class="mdi mdi-delete"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($pasien_result->num_rows === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pasien</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah Pasien -->
<div class="modal fade" id="tambahPasienModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pasien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pasien</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. KTP</label>
                        <input type="text" class="form-control" name="no_ktp" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_pasien" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pasien -->
<?php $pasien_result = $conn->query($sql_pasien); ?>
<?php while ($pasien = $pasien_result->fetch_assoc()): ?>
    <div class="modal fade" id="editPasienModal<?= $pasien['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" value="<?= $pasien['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" name="nama" value="<?= $pasien['nama'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input type="text" class="form-control" name="no_ktp" value="<?= $pasien['no_ktp'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" class="form-control" name="no_hp" value="<?= $pasien['no_hp'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required><?= $pasien['alamat'] ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_pasien" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php include 'include/footer.php'; ?>