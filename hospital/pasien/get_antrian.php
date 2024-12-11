<?php
include '../config/koneksi.php';

if (isset($_POST['id_jadwal'])) {
    $id_jadwal = $_POST['id_jadwal'];

    // Hitung nomor antrian
    $sql = "SELECT COUNT(*) as total FROM daftar_poli WHERE id_jadwal = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_jadwal);
    $stmt->execute();
    $result = $stmt->get_result();
    $no_antrian = $result->fetch_assoc()['total'] + 1;

    echo $no_antrian;
}
