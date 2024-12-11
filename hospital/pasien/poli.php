<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['pasien_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pasien
$pasien_id = $_SESSION['pasien_id'];
$sql_pasien = "SELECT * FROM pasien WHERE id = ?";
$stmt_pasien = $conn->prepare($sql_pasien);
$stmt_pasien->bind_param("i", $pasien_id);
$stmt_pasien->execute();
$pasien = $stmt_pasien->get_result()->fetch_assoc();

// Ambil daftar poli
$sql_poli = "SELECT * FROM poli";
$poli_result = $conn->query($sql_poli);

// Proses pendaftaran poli
if (isset($_POST['daftar_poli'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $keluhan = $_POST['keluhan'];

    // Hitung nomor antrian
    $sql_antrian = "SELECT COUNT(*) as total FROM daftar_poli WHERE id_jadwal = ?";
    $stmt_antrian = $conn->prepare($sql_antrian);
    $stmt_antrian->bind_param("i", $id_jadwal);
    $stmt_antrian->execute();
    $no_antrian = $stmt_antrian->get_result()->fetch_assoc()['total'] + 1;

    // Insert pendaftaran
    $sql_daftar = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) VALUES (?, ?, ?, ?)";
    $stmt_daftar = $conn->prepare($sql_daftar);
    $stmt_daftar->bind_param("iisi", $pasien_id, $id_jadwal, $keluhan, $no_antrian);

    if ($stmt_daftar->execute()) {
        echo "<script>
    alert('Berhasil mendaftar ke poli.');
    window.location.href = 'riwayat.php';
</script>";
    } else {
        echo "<script>
    alert('Gagal mendaftar ke poli.');
</script>";
    }
}

include 'include/header.php'; ?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3">
        <h2>Pendaftaran Poli</h2>
    </div>

    <!-- Form Pendaftaran Poli -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Pendaftaran Poli</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">No. Rekam Medis</label>
                    <input type="text" class="form-control" value="<?= $pasien['no_rm'] ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Poli</label>
                    <select class="form-select" name="id_poli" id="id_poli" required>
                        <option value="">Pilih Poli</option>
                        <?php while ($poli = $poli_result->fetch_assoc()): ?>
                            <option value="<?= $poli['id'] ?>"><?= $poli['nama_poli'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Jadwal</label>
                    <select class="form-select" name="id_jadwal" id="jadwal" required>
                        <option value="">Pilih Poli Terlebih Dahulu</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor Antrian Anda</label>
                    <div class="form-control bg-light">
                        <span id="no_antrian">-</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keluhan</label>
                    <textarea class="form-control" name="keluhan" rows="3" required></textarea>
                </div>
                <button type="submit" name="daftar_poli" class="btn btn-teal">Daftar</button>
            </form>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $('#id_poli').change(function() {
        var id_poli = $(this).val();
        if (id_poli) {
            $.ajax({
                url: 'get_jadwal.php',
                type: 'POST',
                data: {
                    id_poli: id_poli
                },
                success: function(response) {
                    $('#jadwal').html(response);
                    // Reset nomor antrian saat poli berubah
                    $('#no_antrian').text('-');
                }
            });
        } else {
            $('#jadwal').html('<option value="">Pilih Poli Terlebih Dahulu</option>');
            $('#no_antrian').text('-');
        }
    });

    // Tambahkan event listener untuk perubahan jadwal
    $('#jadwal').change(function() {
        var id_jadwal = $(this).val();
        if (id_jadwal) {
            $.ajax({
                url: 'get_antrian.php',
                type: 'POST',
                data: {
                    id_jadwal: id_jadwal
                },
                success: function(response) {
                    $('#no_antrian').text(response);
                }
            });
        } else {
            $('#no_antrian').text('-');
        }
    });
</script>

<?php include 'include/footer.php'; ?>