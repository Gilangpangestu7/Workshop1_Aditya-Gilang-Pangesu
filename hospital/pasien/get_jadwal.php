<?php
include '../config/koneksi.php';

if (isset($_POST['id_poli'])) {
    $id_poli = $_POST['id_poli'];

    $sql = "SELECT jp.id, d.nama as nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai 
            FROM jadwal_periksa jp 
            JOIN dokter d ON jp.id_dokter = d.id 
            WHERE d.id_poli = ? AND jp.status = 1
            ORDER BY FIELD(jp.hari, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_poli);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Pilih Jadwal</option>';

    while ($row = $result->fetch_assoc()) {
        // Konversi hari ke bahasa Indonesia
        $hari_inggris = $row['hari'];
        $hari_indo = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        $hari = $hari_indo[$hari_inggris];

        echo '<option value="' . $row['id'] . '">' .
            $row['nama_dokter'] . ' | ' .
            $hari . ' | ' .
            substr($row['jam_mulai'], 0, 5) . ' - ' .
            substr($row['jam_selesai'], 0, 5) .
            '</option>';
    }
}
