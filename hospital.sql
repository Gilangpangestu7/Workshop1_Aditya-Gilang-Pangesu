/*
 Navicat Premium Data Transfer

 Source Server         : PhpMyAdmin
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : hospital

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 09/12/2024 00:13:00
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- ----------------------------
-- Table structure for daftar_poli
-- ----------------------------
DROP TABLE IF EXISTS `daftar_poli`;
CREATE TABLE `daftar_poli`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pasien` int NOT NULL,
  `id_jadwal` int NOT NULL,
  `keluhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_antrian` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_pasien`(`id_pasien` ASC) USING BTREE,
  INDEX `id_jadwal`(`id_jadwal` ASC) USING BTREE,
  CONSTRAINT `daftar_poli_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `daftar_poli_ibfk_2` FOREIGN KEY (`id_jadwal`) REFERENCES `jadwal_periksa` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of daftar_poli
-- ----------------------------
INSERT INTO `daftar_poli` VALUES (1, 1, 1, 'Demam dan pusing', 1);
INSERT INTO `daftar_poli` VALUES (2, 2, 3, 'Sakit gigi', 1);
INSERT INTO `daftar_poli` VALUES (3, 3, 5, 'Mata berair', 1);
INSERT INTO `daftar_poli` VALUES (4, 4, 6, 'Batuk pilek', 1);
INSERT INTO `daftar_poli` VALUES (29, 19, 1, 'sakit parah', 2);

-- ----------------------------
-- Table structure for detail_periksa
-- ----------------------------
DROP TABLE IF EXISTS `detail_periksa`;
CREATE TABLE `detail_periksa`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_periksa` int NOT NULL,
  `id_obat` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_periksa`(`id_periksa` ASC) USING BTREE,
  INDEX `id_obat`(`id_obat` ASC) USING BTREE,
  CONSTRAINT `detail_periksa_ibfk_1` FOREIGN KEY (`id_periksa`) REFERENCES `periksa` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `detail_periksa_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detail_periksa
-- ----------------------------
INSERT INTO `detail_periksa` VALUES (30, 1, 1);
INSERT INTO `detail_periksa` VALUES (31, 1, 4);
INSERT INTO `detail_periksa` VALUES (32, 2, 2);
INSERT INTO `detail_periksa` VALUES (33, 3, 4);
INSERT INTO `detail_periksa` VALUES (34, 4, 2);
INSERT INTO `detail_periksa` VALUES (35, 4, 4);
INSERT INTO `detail_periksa` VALUES (36, 36, 2);
INSERT INTO `detail_periksa` VALUES (37, 36, 1);

-- ----------------------------
-- Table structure for dokter
-- ----------------------------
DROP TABLE IF EXISTS `dokter`;
CREATE TABLE `dokter`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `no_hp` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_poli` int NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_poli`(`id_poli` ASC) USING BTREE,
  CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of dokter
-- ----------------------------
INSERT INTO `dokter` VALUES (1, 'Dr. Andika Pratama', 'Jl. Mangga No. 10', '08123456789', 1, 'dokter123');
INSERT INTO `dokter` VALUES (2, 'Dr. Sinta Dewi', 'Jl. Apel No. 15', '08234567890', 2, 'dokter123');
INSERT INTO `dokter` VALUES (3, 'Dr. Budi Santoso', 'Jl. Jeruk No. 20', '08345678901', 3, 'dokter123');
INSERT INTO `dokter` VALUES (4, 'Dr. Maya Putri', 'Jl. Anggur No. 25', '08456789012', 4, 'dokter123');

-- ----------------------------
-- Table structure for jadwal_periksa
-- ----------------------------
DROP TABLE IF EXISTS `jadwal_periksa`;
CREATE TABLE `jadwal_periksa`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_dokter` int NOT NULL,
  `hari` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `status` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_dokter`(`id_dokter` ASC) USING BTREE,
  CONSTRAINT `jadwal_periksa_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jadwal_periksa
-- ----------------------------
INSERT INTO `jadwal_periksa` VALUES (1, 1, 'Senin', '08:00:00', '16:00:00', 0);
INSERT INTO `jadwal_periksa` VALUES (2, 1, 'Rabu', '08:00:00', '16:00:00', 1);
INSERT INTO `jadwal_periksa` VALUES (3, 2, 'Selasa', '09:00:00', '17:00:00', 1);
INSERT INTO `jadwal_periksa` VALUES (4, 2, 'Kamis', '09:00:00', '17:00:00', 0);
INSERT INTO `jadwal_periksa` VALUES (5, 3, 'Rabu', '10:00:00', '18:00:00', 1);
INSERT INTO `jadwal_periksa` VALUES (6, 4, 'Jumat', '08:00:00', '16:00:00', 1);
INSERT INTO `jadwal_periksa` VALUES (24, 1, 'Selasa', '18:24:00', '19:20:00', 0);

-- ----------------------------
-- Table structure for obat
-- ----------------------------
DROP TABLE IF EXISTS `obat`;
CREATE TABLE `obat`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kemasan` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `harga` int UNSIGNED NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of obat
-- ----------------------------
INSERT INTO `obat` VALUES (1, 'Paracetamol', 'Strip 10 Tablet', 12000);
INSERT INTO `obat` VALUES (2, 'Amoxicillin', 'Strip 8 Kapsul', 25000);
INSERT INTO `obat` VALUES (3, 'Omeprazole', 'Strip 6 Tablet', 35000);
INSERT INTO `obat` VALUES (4, 'Vitamin C', 'Strip 12 Tablet', 15000);
INSERT INTO `obat` VALUES (5, 'Aspirin', 'Strip 10 Tablet', 12000);
INSERT INTO `obat` VALUES (16, 'Ibuprofen', 'Strip 10 Tablet', 18000);

-- ----------------------------
-- Table structure for pasien
-- ----------------------------
DROP TABLE IF EXISTS `pasien`;
CREATE TABLE `pasien`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_ktp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_rm` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of pasien
-- ----------------------------
INSERT INTO `pasien` VALUES (1, 'Rudi Hermawan', 'Jl. Mawar No. 1', '3374123456789001', '081234567890', '202401-001');
INSERT INTO `pasien` VALUES (2, 'Siti Aminah', 'Jl. Melati No. 2', '3374123456789002', '082345678901', '202401-002');
INSERT INTO `pasien` VALUES (3, 'Joko Widodo', 'Jl. Kenanga No. 3', '3374123456789003', '083456789012', '202401-003');
INSERT INTO `pasien` VALUES (4, 'Ani Susilowati', 'Jl. Dahlia No. 4', '3374123456789004', '084567890123', '202401-004');
INSERT INTO `pasien` VALUES (19, 'Aditya Gilang Pangestu', 'Brebes', '12333', '085329727224', '202412-005');
INSERT INTO `pasien` VALUES (20, 'Explicabo Consequat', 'Sed molestiae incidi', '12345678', '08918819819', '202412-006');

-- ----------------------------
-- Table structure for periksa
-- ----------------------------
DROP TABLE IF EXISTS `periksa`;
CREATE TABLE `periksa`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_daftar_poli` int NOT NULL,
  `tgl_periksa` date NOT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `biaya_periksa` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_daftar_poli`(`id_daftar_poli` ASC) USING BTREE,
  CONSTRAINT `periksa_ibfk_1` FOREIGN KEY (`id_daftar_poli`) REFERENCES `daftar_poli` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of periksa
-- ----------------------------
INSERT INTO `periksa` VALUES (1, 1, '2024-01-08', 'Flu dan kecapekan', 165000);
INSERT INTO `periksa` VALUES (2, 2, '2024-01-09', 'Gigi berlubang', 175000);
INSERT INTO `periksa` VALUES (3, 3, '2024-01-10', 'Iritasi mata', 185000);
INSERT INTO `periksa` VALUES (4, 4, '2024-01-12', 'ISPA', 170000);
INSERT INTO `periksa` VALUES (36, 29, '2024-12-08', 'istirahat yaa', 187000);

-- ----------------------------
-- Table structure for poli
-- ----------------------------
DROP TABLE IF EXISTS `poli`;
CREATE TABLE `poli`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_poli` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of poli
-- ----------------------------
INSERT INTO `poli` VALUES (1, 'Poli Umum', 'Menangani masalah kesehatan umum');
INSERT INTO `poli` VALUES (2, 'Poli Gigi', 'Menangani masalah kesehatan gigi dan mulut');
INSERT INTO `poli` VALUES (3, 'Poli Mata', 'Menangani masalah kesehatan mata');
INSERT INTO `poli` VALUES (4, 'Poli Anak', 'Menangani masalah kesehatan anak');

SET FOREIGN_KEY_CHECKS = 1;
