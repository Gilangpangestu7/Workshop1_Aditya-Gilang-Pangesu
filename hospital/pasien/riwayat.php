<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['pasien_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil riwayat pendaftaran
$sql_history = "SELECT dp.id, poli.nama_poli, j.hari, j.jam_mulai, j.jam_selesai, dp.no_antrian, dp.keluhan , d.nama AS nama_dokter , p.tgl_periksa, p.catatan, p.biaya_periksa
                FROM daftar_poli dp
                JOIN jadwal_periksa j ON dp.id_jadwal = j.id
                JOIN dokter d ON j.id_dokter = d.id
                JOIN poli ON d.id_poli = poli.id
                JOIN periksa p ON dp.id = p.id_daftar_poli
                WHERE dp.id_pasien = ? 
                ORDER BY dp.id DESC";

$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $_SESSION['pasien_id']);
$stmt_history->execute();
$history_result = $stmt_history->get_result();
$history_result_copy = $history_result;

include 'include/header.php'; ?>
<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h2>Riwayat Pendaftaran</h2>
    </div>

    <!-- Riwayat Pendaftaran -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Hari</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>No Antrian</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($history = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $history['nama_poli']; ?></td>
                                <td><?= $history['nama_dokter']; ?></td>
                                <td><?= $history['hari']; ?></td>
                                <td><?= $history['jam_mulai']; ?></td>
                                <td><?= $history['jam_selesai']; ?></td>
                                <td><?= $history['no_antrian']; ?></td>
                                <td>
                                    <?php if ($history['tgl_periksa'] == null) : ?>
                                        <span class="badge bg-danger">Belum Periksa</span>
                                    <?php else : ?>
                                        <span class="badge bg-success">Sudah Periksa</span> - <?= date('d-m-Y', strtotime($history['tgl_periksa'])); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#detailRiwayat<?= $history['id']; ?>">
                                        Detail
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

<?php $history_result->data_seek(0); ?>
<?php while ($history = $history_result->fetch_assoc()): ?>
    <!-- Modal Detail Riwayat -->
    <div class="modal fade" id="detailRiwayat<?= $history['id']; ?>" tabindex="-1" aria-labelledby="detailRiwayatLabel" aria-hidden="true">
        <div class="modal-dialog modal-">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailRiwayatLabel">Detail Riwayat Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_poli" class="form-label">Poli</label>
                        <input type="text" class="form-control" id="nama_poli" value="<?= $history['nama_poli']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="nama_dokter" class="form-label">Dokter</label>
                        <input type="text" class="form-control" id="nama_dokter" value="<?= $history['nama_dokter']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="hari" class="form-label">Hari</label>
                        <input type="text" class="form-control" id="hari" value="<?= $history['hari']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="jam_mulai" class="form-label">Jam Mulai</label>
                        <input type="text" class="form-control" id="jam_mulai" value="<?= $history['jam_mulai']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label">Jam Selesai</label>
                        <input type="text" class="form-control" id="jam_selesai" value="<?= $history['jam_selesai']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="no_antrian" class="form-label">No Antrian</label>
                        <input type="text" class="form-control" id="no_antrian" value="<?= $history['no_antrian']; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="keluhan" class="form-label">Keluhan</label>
                        <textarea class="form-control" id="keluhan" rows="3" readonly><?= $history['keluhan']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" class="form-control" id="status" value="<?= ($history['tgl_periksa'] == null) ? 'Belum Periksa' : 'Sudah Periksa'; ?>" readonly>
                    </div>
                    <?php if ($history['tgl_periksa'] != null) : ?>
                        <div class="mb-3">
                            <label for="tgl_periksa" class="form-label">Tanggal Periksa</label>
                            <input type="text" class="form-control" id="tgl_periksa" value="<?= date('d-m-Y', strtotime($history['tgl_periksa'])); ?>" readonly>
                        </div>
                        <?php $sql_obat = "SELECT o.nama_obat AS obat FROM periksa pr LEFT JOIN detail_periksa dpr ON pr.id = dpr.id_periksa LEFT JOIN obat o ON dpr.id_obat = o.id WHERE pr.id_daftar_poli = ?"; ?>
                        <?php $stmt_obat = $conn->prepare($sql_obat); ?>
                        <?php $stmt_obat->bind_param("i", $history['id']); ?>
                        <?php $stmt_obat->execute(); ?>
                        <?php $obat_result = $stmt_obat->get_result(); ?>
                        <?php $obat = $obat_result->fetch_assoc(); ?>
                        <div class="mb-3">
                            <label for="obat" class="form-label">Obat</label>
                            <textarea class="form-control" id="obat" rows="3" readonly><?= $obat['obat']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="diagnosa" class="form-label">Catatan</label>
                            <textarea class="form-control" id="diagnosa" rows="3" readonly><?= $history['catatan']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Total Biaya</label>
                            <input type="text" class="form-control" id="nominal" value="<?= $history['biaya_periksa']; ?>" readonly>
                        </div>
                        

                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php endwhile; ?>




<?php include 'include/footer.php'; ?>