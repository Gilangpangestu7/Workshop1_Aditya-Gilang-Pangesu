<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['dokter_id'])) {
    header('Location: dokter_login.php');
    exit;
}

$dokter_id = $_SESSION['dokter_id'];

// Ambil daftar semua pasien
$sql_pasien = "SELECT dp.*, p.nama as nama_pasien, p.no_rm, jp.hari, jp.jam_mulai, jp.jam_selesai,
               MAX(pr.id) as id_periksa, MAX(pr.tgl_periksa) as tgl_periksa, 
               MAX(pr.catatan) as catatan_periksa,
               GROUP_CONCAT(DISTINCT o.id) as obat_ids,
               GROUP_CONCAT(DISTINCT o.nama_obat) as nama_obat
               FROM daftar_poli dp 
               JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
               JOIN pasien p ON dp.id_pasien = p.id
               LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
               LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
               LEFT JOIN obat o ON dpr.id_obat = o.id
               WHERE jp.id_dokter = ?
               GROUP BY dp.id, dp.id_pasien, dp.id_jadwal, dp.keluhan, dp.no_antrian,
                        p.nama, p.no_rm, jp.hari, jp.jam_mulai, jp.jam_selesai
               ORDER BY MAX(pr.tgl_periksa) DESC, dp.no_antrian ASC";

$stmt_pasien = $conn->prepare($sql_pasien);
$stmt_pasien->bind_param("i", $dokter_id);
$stmt_pasien->execute();
$daftar_pasien = $stmt_pasien->get_result();

// Ambil daftar obat
$sql_obat = "SELECT * FROM obat ORDER BY nama_obat";
$obat_result = $conn->query($sql_obat);

// Proses pemeriksaan atau update
if (isset($_POST['periksa']) || isset($_POST['update'])) {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $catatan = $_POST['catatan'];
    $obat = isset($_POST['obat']) ? $_POST['obat'] : [];

    // Hitung total biaya obat
    $total_biaya_obat = 0;
    if (!empty($obat)) {
        $obat_ids = implode(',', array_map('intval', $obat));
        $sql_harga = "SELECT SUM(harga) as total FROM obat WHERE id IN ($obat_ids)";
        $result_harga = $conn->query($sql_harga);
        $total_biaya_obat = $result_harga->fetch_assoc()['total'];
    }

    $biaya_periksa = 150000 + $total_biaya_obat;

    $conn->begin_transaction();

    try {
        if (isset($_POST['periksa'])) {
            // Insert baru
            $sql_periksa = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) 
                           VALUES (?, CURRENT_DATE(), ?, ?)";
            $stmt_periksa = $conn->prepare($sql_periksa);
            $stmt_periksa->bind_param("isi", $id_daftar_poli, $catatan, $biaya_periksa);
            $stmt_periksa->execute();
            $id_periksa = $conn->insert_id;
        } else {
            // Update
            $id_periksa = $_POST['id_periksa'];
            $sql_periksa = "UPDATE periksa SET catatan = ?, biaya_periksa = ? WHERE id = ?";
            $stmt_periksa = $conn->prepare($sql_periksa);
            $stmt_periksa->bind_param("sii", $catatan, $biaya_periksa, $id_periksa);
            $stmt_periksa->execute();

            // Hapus detail obat lama
            $conn->query("DELETE FROM detail_periksa WHERE id_periksa = $id_periksa");
        }

        // Insert detail obat
        if (!empty($obat)) {
            $sql_detail = "INSERT INTO detail_periksa (id_periksa, id_obat) VALUES (?, ?)";
            $stmt_detail = $conn->prepare($sql_detail);

            foreach ($obat as $id_obat) {
                $stmt_detail->bind_param("ii", $id_periksa, $id_obat);
                $stmt_detail->execute();
            }
        }

        $conn->commit();
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='memeriksa_pasien.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Gagal menyimpan data: " . $e->getMessage() . "');</script>";
    }
}

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Pemeriksaan Pasien</h1>
    </div>

    <!-- Daftar Pasien -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Pasien</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Tanggal Periksa</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pasien = $daftar_pasien->fetch_assoc()): ?>
                            <tr>
                                <td><?= $pasien['no_antrian'] ?></td>
                                <td><?= $pasien['tgl_periksa'] ?? 'Belum diperiksa' ?></td>
                                <td><?= htmlspecialchars($pasien['no_rm']) ?></td>
                                <td><?= htmlspecialchars($pasien['nama_pasien']) ?></td>
                                <td><?= htmlspecialchars($pasien['keluhan']) ?></td>
                                <td>
                                    <?php if ($pasien['id_periksa']): ?>
                                        <span class="badge bg-success">Sudah Diperiksa</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Belum Diperiksa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm <?= $pasien['id_periksa'] ? 'btn-warning' : 'btn-cyan' ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#periksaModal<?= $pasien['id'] ?>">
                                        <i class="mdi mdi-stethoscope"></i>
                                        <?= $pasien['id_periksa'] ? 'Edit' : 'Periksa' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Periksa/Edit -->
<?php $daftar_pasien->data_seek(0); ?>
<?php while ($pasien = $daftar_pasien->fetch_assoc()): ?>
    <div class="modal fade" id="periksaModal<?= $pasien['id'] ?>" tabindex="-1" aria-labelledby="periksaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periksaModalLabel">Pemeriksaan Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <input type="hidden" name="id_daftar_poli" value="<?= $pasien['id'] ?>">
                        <input type="hidden" name="id_periksa" value="<?= $pasien['id_periksa'] ?? '' ?>">

                        <div class="mb-3">
                            <label class="form-label">No. Rekam Medis</label>
                            <input type="text" class="form-control" value="<?= $pasien['no_rm'] ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Pasien</label>
                            <input type="text" class="form-control" value="<?= $pasien['nama_pasien'] ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea class="form-control" name="keluhan" rows="3" readonly><?= $pasien['keluhan'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="3"><?= $pasien['catatan_periksa'] ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Obat</label>
                            <div class="row">
                                <?php
                                $obat_result->data_seek(0); // Reset pointer hasil query obat
                                $selected_obat = !empty($pasien['obat_ids']) ? explode(',', $pasien['obat_ids']) : [];
                                while ($obat = $obat_result->fetch_assoc()):
                                ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input class="form-check-input obat-checkbox" type="checkbox"
                                                name="obat[]"
                                                id="obat<?= $pasien['id'] ?>_<?= $obat['id'] ?>"
                                                value="<?= $obat['id'] ?>"
                                                data-harga="<?= $obat['harga'] ?>"
                                                <?= in_array($obat['id'], $selected_obat) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="obat<?= $pasien['id'] ?>_<?= $obat['id'] ?>">
                                                <?= $obat['nama_obat'] ?>
                                                <br>
                                                <small>Rp <?= number_format($obat['harga'], 0, ',', '.') ?></small>
                                            </label>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Obat</label>
                            <div class="form-control bg-light">
                                Rp <span id="totalObat<?= $pasien['id'] ?>">0</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Biaya Periksa</label>
                            <div class="form-control bg-light">
                                Rp 150.000
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Total Biaya</label>
                            <div class="form-control bg-light">
                                Rp <span id="totalPembayaran<?= $pasien['id'] ?>">150.000</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <?php if (!$pasien['id_periksa']): ?>
                            <button type="submit" name="periksa" value="1" class="btn btn-teal">Simpan Pemeriksaan</button>
                        <?php else: ?>
                            <button type="submit" name="update" value="1" class="btn btn-warning">Update Pemeriksaan</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function hitungTotal(id_pasien) {
        const biayaPeriksa = 150000;
        let totalObat = 0;

        document.querySelectorAll(`#periksaModal${id_pasien} .obat-checkbox:checked`).forEach(function(checkbox) {
            totalObat += parseInt(checkbox.dataset.harga);
        });

        document.getElementById(`totalObat${id_pasien}`).textContent = formatRupiah(totalObat);
        document.getElementById(`totalPembayaran${id_pasien}`).textContent = formatRupiah(biayaPeriksa + totalObat);
    }

    // Inisialisasi semua modal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Event listener untuk semua checkbox obat
        document.querySelectorAll('.obat-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                // Dapatkan ID modal dari parent
                const modalId = this.closest('.modal').getAttribute('id');
                const pasienId = modalId.replace('periksaModal', '');
                hitungTotal(pasienId);
            });
        });

        // Hitung total awal untuk setiap modal
        document.querySelectorAll('.modal').forEach(function(modal) {
            const pasienId = modal.id.replace('periksaModal', '');
            hitungTotal(pasienId);
        });
    });
</script>

<?php include 'include/footer.php'; ?>