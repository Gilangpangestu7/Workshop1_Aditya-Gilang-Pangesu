<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['dokter_id'])) {
    header('Location: dokter_login.php');
    exit;
}

$dokter_id = $_SESSION['dokter_id'];

// Ambil riwayat pemeriksaan pasien
$sql_riwayat = "SELECT p.nama AS nama_pasien, p.no_rm, 
                dp.keluhan, dp.no_antrian,
                pr.id AS id_periksa, pr.tgl_periksa, pr.catatan, pr.biaya_periksa,
                GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS obat,
                GROUP_CONCAT(o.harga SEPARATOR ', ') AS harga_obat
                FROM periksa pr
                JOIN daftar_poli dp ON pr.id_daftar_poli = dp.id
                JOIN pasien p ON dp.id_pasien = p.id
                JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
                LEFT JOIN obat o ON dpr.id_obat = o.id
                WHERE jp.id_dokter = ?
                GROUP BY pr.id
                ORDER BY pr.tgl_periksa DESC, dp.no_antrian ASC";

$stmt_riwayat = $conn->prepare($sql_riwayat);
$stmt_riwayat->bind_param("i", $dokter_id);
$stmt_riwayat->execute();
$riwayat = $stmt_riwayat->get_result();

// Fillter data berdasarkan tanggal
if (isset($_GET['dari']) && isset($_GET['sampai'])) {
    $dari = $_GET['dari'];
    $sampai = $_GET['sampai'];

    $sql_riwayat = "SELECT p.nama AS nama_pasien, p.no_rm, 
                    dp.keluhan, dp.no_antrian,
                    pr.id AS id_periksa, pr.tgl_periksa, pr.catatan, pr.biaya_periksa,
                    GROUP_CONCAT(o.nama_obat SEPARATOR ', ') AS obat,
                    GROUP_CONCAT(o.harga SEPARATOR ', ') AS harga_obat
                    FROM periksa pr
                    JOIN daftar_poli dp ON pr.id_daftar_poli = dp.id
                    JOIN pasien p ON dp.id_pasien = p.id
                    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                    LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa
                    LEFT JOIN obat o ON dpr.id_obat = o.id
                    WHERE jp.id_dokter = ? AND pr.tgl_periksa BETWEEN ? AND ?
                    GROUP BY pr.id
                    ORDER BY pr.tgl_periksa DESC, dp.no_antrian ASC";

    $stmt_riwayat = $conn->prepare($sql_riwayat);
    $stmt_riwayat->bind_param("iss", $dokter_id, $dari, $sampai);
    $stmt_riwayat->execute();
    $riwayat = $stmt_riwayat->get_result();
}

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Riwayat Pemeriksaan Pasien</h1>
    </div>

    <!-- Filter Tanggal -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="dari"
                        value="<?= isset($_GET['dari']) ? $_GET['dari'] : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="sampai"
                        value="<?= isset($_GET['sampai']) ? $_GET['sampai'] : '' ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex justify-content-start gap-3">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <a href="riwayat_pasien.php" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Riwayat Pemeriksaan</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Catatan</th>
                            <th>Obat</th>
                            <th>Biaya</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $riwayat->fetch_assoc()):
                            // Hitung total harga obat
                            $total_obat = 0;
                            if ($row['harga_obat']) {
                                $harga_obat_arr = explode(',', $row['harga_obat']);
                                $total_obat = array_sum($harga_obat_arr);
                            }
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('d/m/Y', strtotime($row['tgl_periksa'])) ?></td>
                                <td><?= htmlspecialchars($row['no_rm']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pasien']) ?></td>
                                <td><?= htmlspecialchars($row['keluhan']) ?></td>
                                <td><?= htmlspecialchars($row['catatan']) ?></td>
                                <td>
                                    <?php if ($row['obat']): ?>
                                        <?= htmlspecialchars($row['obat']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada obat</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>Jasa: Rp <?= number_format(150000, 0, ',', '.') ?></div>
                                    <div>Obat: Rp <?= number_format($total_obat, 0, ',', '.') ?></div>
                                    <div class="fw-bold">Total: Rp <?= number_format($row['biaya_periksa'], 0, ',', '.') ?></div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                        data-bs-target="#detailModal<?= $row['id_periksa'] ?>">
                                        <i class="mdi mdi-eye"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <?php if ($riwayat->num_rows === 0): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada data riwayat pemeriksaan</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Detail untuk setiap riwayat -->
<?php
$riwayat->data_seek(0);
while ($row = $riwayat->fetch_assoc()):
?>
    <div class="modal fade" id="detailModal<?= $row['id_periksa'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pemeriksaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Periksa</label>
                            <input type="text" class="form-control"
                                value="<?= date('d/m/Y', strtotime($row['tgl_periksa'])) ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Rekam Medis</label>
                            <input type="text" class="form-control" value="<?= $row['no_rm'] ?>" readonly>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Pasien</label>
                        <input type="text" class="form-control" value="<?= $row['nama_pasien'] ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keluhan</label>
                        <textarea class="form-control" readonly rows="2"><?= $row['keluhan'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan Pemeriksaan</label>
                        <textarea class="form-control" readonly rows="3"><?= $row['catatan'] ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Obat yang Diberikan</label>
                        <input type="text" class="form-control" value="<?= $row['obat'] ?: 'Tidak ada obat' ?>" readonly>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <td>Biaya Pemeriksaan</td>
                                <td class="text-end">Rp <?= number_format(150000, 0, ',', '.') ?></td>
                            </tr>
                            <tr>
                                <td>Total Biaya Obat</td>
                                <td class="text-end">Rp <?= number_format($row['biaya_periksa'] - 150000, 0, ',', '.') ?></td>
                            </tr>
                            <tr class="table-primary">
                                <th>Total Pembayaran</th>
                                <th class="text-end">Rp <?= number_format($row['biaya_periksa'], 0, ',', '.') ?></th>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<?php include 'include/footer.php'; ?>