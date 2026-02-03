-- pesantren_db.gedung definition

CREATE TABLE `gedung` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_gedung` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_gedung` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_gedung` enum('asrama_putra','asrama_putri','kelas','serbaguna','masjid','kantor','perpustakaan','lab','dapur','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_lantai` int NOT NULL DEFAULT '1',
  `kapasitas_total` int DEFAULT NULL COMMENT 'Kapasitas total orang',
  `alamat_lokasi` text COLLATE utf8mb4_unicode_ci,
  `tahun_dibangun` year DEFAULT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `fasilitas` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array fasilitas',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gedung_kode_gedung_unique` (`kode_gedung`),
  KEY `gedung_kode_gedung_index` (`kode_gedung`),
  KEY `gedung_jenis_gedung_index` (`jenis_gedung`),
  KEY `gedung_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.jenis_pembayaran definition

CREATE TABLE `jenis_pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('bulanan','tahunan','pendaftaran','kegiatan','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bulanan',
  `nominal` decimal(15,2) NOT NULL COMMENT 'Nominal default',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `jenis_pembayaran_kode_unique` (`kode`),
  KEY `jenis_pembayaran_kode_index` (`kode`),
  KEY `jenis_pembayaran_kategori_index` (`kategori`),
  KEY `jenis_pembayaran_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.kategori_inventaris definition

CREATE TABLE `kategori_inventaris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategori_inventaris_kode_unique` (`kode`),
  KEY `kategori_inventaris_kode_index` (`kode`),
  KEY `kategori_inventaris_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.komponen_nilai definition

CREATE TABLE `komponen_nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: UTS, UAS, Tugas, Quiz, Hafalan',
  `bobot` int NOT NULL COMMENT 'Persentase bobot nilai (0-100)',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `komponen_nilai_kode_unique` (`kode`),
  KEY `komponen_nilai_kode_index` (`kode`),
  KEY `komponen_nilai_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.mata_pelajaran definition

CREATE TABLE `mata_pelajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_mapel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_mapel` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('agama','umum','keterampilan','ekstrakurikuler') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agama',
  `bobot_sks` int NOT NULL DEFAULT '2' COMMENT 'Bobot SKS/jam per minggu',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_pelajaran_kode_mapel_unique` (`kode_mapel`),
  KEY `mata_pelajaran_kode_mapel_index` (`kode_mapel`),
  KEY `mata_pelajaran_kategori_index` (`kategori`),
  KEY `mata_pelajaran_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.password_reset_tokens definition

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.roles definition

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nama_unique` (`nama`),
  UNIQUE KEY `roles_kode_unique` (`kode`),
  KEY `roles_kode_index` (`kode`),
  KEY `roles_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.sessions definition

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.tahun_ajaran definition

CREATE TABLE `tahun_ajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: 2024/2025',
  `tahun_mulai` year NOT NULL,
  `tahun_selesai` year NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hanya 1 tahun ajaran yang aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan: Setiap tahun ajaran memiliki 2 semester di tabel semester',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tahun_ajaran_nama_unique` (`nama`),
  KEY `tahun_ajaran_tahun_mulai_index` (`tahun_mulai`),
  KEY `tahun_ajaran_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.users definition

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','tidak_aktif','banned') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_username_index` (`username`),
  KEY `users_status_index` (`status`),
  KEY `users_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.inventaris definition

CREATE TABLE `inventaris` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kategori_inventaris_id` bigint unsigned NOT NULL,
  `gedung_id` bigint unsigned DEFAULT NULL,
  `kode_inventaris` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_model` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `satuan` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unit' COMMENT 'unit, buah, set, dll',
  `kondisi` enum('baik','rusak_ringan','rusak_berat','hilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `tanggal_perolehan` date NOT NULL,
  `harga_perolehan` decimal(15,2) DEFAULT NULL,
  `nilai_penyusutan` decimal(15,2) DEFAULT NULL,
  `sumber_dana` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'APBN, Donasi, dll',
  `lokasi` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lokasi penyimpanan detail',
  `spesifikasi` text COLLATE utf8mb4_unicode_ci,
  `nomor_seri` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_maintenance_terakhir` date DEFAULT NULL,
  `penanggung_jawab` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventaris_kode_inventaris_unique` (`kode_inventaris`),
  KEY `inventaris_kode_inventaris_index` (`kode_inventaris`),
  KEY `inventaris_kategori_inventaris_id_index` (`kategori_inventaris_id`),
  KEY `inventaris_gedung_id_index` (`gedung_id`),
  KEY `inventaris_kondisi_index` (`kondisi`),
  KEY `inventaris_is_active_index` (`is_active`),
  KEY `inventaris_tanggal_perolehan_index` (`tanggal_perolehan`),
  CONSTRAINT `inventaris_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedung` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventaris_kategori_inventaris_id_foreign` FOREIGN KEY (`kategori_inventaris_id`) REFERENCES `kategori_inventaris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.kamar definition

CREATE TABLE `kamar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `gedung_id` bigint unsigned NOT NULL,
  `nomor_kamar` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_kamar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lantai` int NOT NULL DEFAULT '1',
  `kapasitas` int NOT NULL COMMENT 'Kapasitas maksimal penghuni',
  `luas` decimal(8,2) DEFAULT NULL COMMENT 'Luas dalam m2',
  `fasilitas` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array fasilitas',
  `kondisi` enum('baik','rusak_ringan','rusak_berat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kamar_gedung_id_nomor_kamar_unique` (`gedung_id`,`nomor_kamar`),
  KEY `kamar_gedung_id_index` (`gedung_id`),
  KEY `kamar_lantai_index` (`lantai`),
  KEY `kamar_is_active_index` (`is_active`),
  CONSTRAINT `kamar_gedung_id_foreign` FOREIGN KEY (`gedung_id`) REFERENCES `gedung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.pengajar definition

CREATE TABLE `pengajar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor Induk Pengajar',
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_lengkap` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pendidikan_terakhir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jurusan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `universitas` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_lulus` year DEFAULT NULL,
  `keahlian` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array keahlian',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_bergabung` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status_kepegawaian` enum('tetap','tidak_tetap','honorer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tidak_tetap',
  `status` enum('aktif','non_aktif','pensiun') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengajar_nip_unique` (`nip`),
  UNIQUE KEY `pengajar_nik_unique` (`nik`),
  KEY `pengajar_user_id_foreign` (`user_id`),
  KEY `pengajar_nip_index` (`nip`),
  KEY `pengajar_jenis_kelamin_index` (`jenis_kelamin`),
  KEY `pengajar_status_kepegawaian_index` (`status_kepegawaian`),
  KEY `pengajar_status_index` (`status`),
  KEY `pengajar_tanggal_bergabung_index` (`tanggal_bergabung`),
  CONSTRAINT `pengajar_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.role_user definition

CREATE TABLE `role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `role_user_user_id_index` (`user_id`),
  KEY `role_user_role_id_index` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.santri definition

CREATE TABLE `santri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor Induk Santri',
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor Induk Siswa Nasional',
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_panggilan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis_kelamin` enum('laki-laki','perempuan') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NIK KTP',
  `alamat_lengkap` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `provinsi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kabupaten` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kecamatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelurahan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anak_ke` int DEFAULT NULL,
  `jumlah_saudara` int DEFAULT NULL,
  `golongan_darah` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `riwayat_penyakit` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('aktif','lulus','pindah','keluar','cuti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `santri_nis_unique` (`nis`),
  UNIQUE KEY `santri_nisn_unique` (`nisn`),
  UNIQUE KEY `santri_nik_unique` (`nik`),
  KEY `santri_user_id_foreign` (`user_id`),
  KEY `santri_nis_index` (`nis`),
  KEY `santri_nisn_index` (`nisn`),
  KEY `santri_jenis_kelamin_index` (`jenis_kelamin`),
  KEY `santri_status_index` (`status`),
  KEY `santri_tanggal_masuk_index` (`tanggal_masuk`),
  KEY `santri_created_at_index` (`created_at`),
  CONSTRAINT `santri_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.semester definition

CREATE TABLE `semester` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `jenis_semester` enum('ganjil','genap') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: Semester Ganjil 2024/2025',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Hanya 1 semester yang aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `semester_tahun_ajaran_id_jenis_semester_unique` (`tahun_ajaran_id`,`jenis_semester`),
  KEY `semester_tahun_ajaran_id_index` (`tahun_ajaran_id`),
  KEY `semester_jenis_semester_index` (`jenis_semester`),
  KEY `semester_is_active_index` (`is_active`),
  CONSTRAINT `semester_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.wali_santri definition

CREATE TABLE `wali_santri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint unsigned NOT NULL,
  `jenis_wali` enum('ayah','ibu','wali') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ayah/ibu/wali',
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `pendidikan_terakhir` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penghasilan` decimal(15,2) DEFAULT NULL COMMENT 'Penghasilan per bulan',
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `status` enum('hidup','meninggal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hidup',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wali_santri_santri_id_index` (`santri_id`),
  KEY `wali_santri_jenis_wali_index` (`jenis_wali`),
  KEY `wali_santri_telepon_index` (`telepon`),
  CONSTRAINT `wali_santri_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.kelas definition

CREATE TABLE `kelas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tahun_ajaran_id` bigint unsigned NOT NULL,
  `wali_kelas_id` bigint unsigned DEFAULT NULL,
  `nama_kelas` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: 1A, 2B, Tahfidz 1',
  `tingkat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Contoh: 1, 2, 3, Ibtidaiyah, Tsanawiyah',
  `kapasitas` int NOT NULL DEFAULT '30',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_tahun_ajaran_id_index` (`tahun_ajaran_id`),
  KEY `kelas_wali_kelas_id_index` (`wali_kelas_id`),
  KEY `kelas_tingkat_index` (`tingkat`),
  KEY `kelas_is_active_index` (`is_active`),
  CONSTRAINT `kelas_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_wali_kelas_id_foreign` FOREIGN KEY (`wali_kelas_id`) REFERENCES `pengajar` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.kelas_santri definition

CREATE TABLE `kelas_santri` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kelas_id` bigint unsigned NOT NULL,
  `santri_id` bigint unsigned NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('aktif','lulus','pindah','keluar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_santri_kelas_id_index` (`kelas_id`),
  KEY `kelas_santri_santri_id_index` (`santri_id`),
  KEY `kelas_santri_status_index` (`status`),
  CONSTRAINT `kelas_santri_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_santri_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.pembayaran definition

CREATE TABLE `pembayaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_pembayaran` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nomor transaksi unik',
  `santri_id` bigint unsigned NOT NULL,
  `jenis_pembayaran_id` bigint unsigned NOT NULL,
  `tahun_ajaran_id` bigint unsigned DEFAULT NULL,
  `tanggal_pembayaran` date NOT NULL,
  `bulan` int DEFAULT NULL COMMENT 'Bulan pembayaran untuk tipe bulanan (1-12)',
  `tahun` year DEFAULT NULL COMMENT 'Tahun pembayaran untuk tipe bulanan',
  `nominal` decimal(15,2) NOT NULL COMMENT 'Nominal yang dibayar',
  `potongan` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Diskon/potongan',
  `denda` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Denda keterlambatan',
  `total_bayar` decimal(15,2) NOT NULL COMMENT 'Total yang harus dibayar',
  `metode_pembayaran` enum('tunai','transfer','qris','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tunai',
  `nomor_referensi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor referensi transfer/bukti',
  `status` enum('lunas','belum_lunas','cicilan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lunas',
  `petugas_id` bigint unsigned DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembayaran_kode_pembayaran_unique` (`kode_pembayaran`),
  KEY `pembayaran_petugas_id_foreign` (`petugas_id`),
  KEY `pembayaran_kode_pembayaran_index` (`kode_pembayaran`),
  KEY `pembayaran_santri_id_index` (`santri_id`),
  KEY `pembayaran_jenis_pembayaran_id_index` (`jenis_pembayaran_id`),
  KEY `pembayaran_tahun_ajaran_id_index` (`tahun_ajaran_id`),
  KEY `pembayaran_tanggal_pembayaran_index` (`tanggal_pembayaran`),
  KEY `pembayaran_status_index` (`status`),
  KEY `pembayaran_santri_id_bulan_tahun_index` (`santri_id`,`bulan`,`tahun`),
  CONSTRAINT `pembayaran_jenis_pembayaran_id_foreign` FOREIGN KEY (`jenis_pembayaran_id`) REFERENCES `jenis_pembayaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_petugas_id_foreign` FOREIGN KEY (`petugas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pembayaran_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayaran_tahun_ajaran_id_foreign` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.pengampu definition

CREATE TABLE `pengampu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengajar_id` bigint unsigned NOT NULL,
  `mata_pelajaran_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','selesai','diganti') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_pengampu_per_semester` (`pengajar_id`,`mata_pelajaran_id`,`kelas_id`,`semester_id`),
  KEY `pengampu_pengajar_id_index` (`pengajar_id`),
  KEY `pengampu_mata_pelajaran_id_index` (`mata_pelajaran_id`),
  KEY `pengampu_kelas_id_index` (`kelas_id`),
  KEY `pengampu_semester_id_index` (`semester_id`),
  KEY `pengampu_status_index` (`status`),
  KEY `pengampu_pengajar_id_semester_id_index` (`pengajar_id`,`semester_id`),
  KEY `pengampu_kelas_id_semester_id_index` (`kelas_id`,`semester_id`),
  KEY `pengampu_mata_pelajaran_id_semester_id_index` (`mata_pelajaran_id`,`semester_id`),
  CONSTRAINT `pengampu_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengampu_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengampu_pengajar_id_foreign` FOREIGN KEY (`pengajar_id`) REFERENCES `pengajar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pengampu_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.penghuni_kamar definition

CREATE TABLE `penghuni_kamar` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint unsigned NOT NULL,
  `kamar_id` bigint unsigned NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `status` enum('aktif','keluar','pindah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penghuni_kamar_santri_id_index` (`santri_id`),
  KEY `penghuni_kamar_kamar_id_index` (`kamar_id`),
  KEY `penghuni_kamar_status_index` (`status`),
  KEY `penghuni_kamar_tanggal_masuk_index` (`tanggal_masuk`),
  CONSTRAINT `penghuni_kamar_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `penghuni_kamar_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.perizinan definition

CREATE TABLE `perizinan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nomor_izin` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `santri_id` bigint unsigned NOT NULL,
  `jenis_izin` enum('pulang','kunjungan','sakit','keluar_sementara') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pulang',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `waktu_keluar` time DEFAULT NULL,
  `waktu_kembali` time DEFAULT NULL,
  `keperluan` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Alasan/keperluan izin',
  `tujuan` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Alamat tujuan',
  `penjemput_nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penjemput_hubungan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hubungan dengan santri',
  `penjemput_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `penjemput_identitas` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'No KTP/SIM',
  `status` enum('diajukan','disetujui','ditolak','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'diajukan',
  `disetujui_oleh` bigint unsigned DEFAULT NULL,
  `waktu_persetujuan` timestamp NULL DEFAULT NULL,
  `catatan_persetujuan` text COLLATE utf8mb4_unicode_ci,
  `waktu_kembali_aktual` timestamp NULL DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `perizinan_nomor_izin_unique` (`nomor_izin`),
  KEY `perizinan_disetujui_oleh_foreign` (`disetujui_oleh`),
  KEY `perizinan_nomor_izin_index` (`nomor_izin`),
  KEY `perizinan_santri_id_index` (`santri_id`),
  KEY `perizinan_jenis_izin_index` (`jenis_izin`),
  KEY `perizinan_status_index` (`status`),
  KEY `perizinan_tanggal_mulai_index` (`tanggal_mulai`),
  KEY `perizinan_santri_id_status_index` (`santri_id`,`status`),
  CONSTRAINT `perizinan_disetujui_oleh_foreign` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `perizinan_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.rapor definition

CREATE TABLE `rapor` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint unsigned NOT NULL,
  `pengampu_id` bigint unsigned NOT NULL,
  `nilai_akhir` decimal(5,2) NOT NULL COMMENT 'Nilai akhir hasil perhitungan dari semua komponen',
  `nilai_huruf` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A, B+, B, C+, C, D, E',
  `nilai_angka` decimal(3,2) DEFAULT NULL COMMENT 'Nilai 4.0 scale',
  `predikat` enum('sangat_baik','baik','cukup','kurang') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ranking_kelas` int DEFAULT NULL COMMENT 'Ranking di mata pelajaran ini dalam kelas',
  `catatan_pengajar` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari pengajar mata pelajaran',
  `catatan_wali_kelas` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari wali kelas',
  `is_lulus` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Apakah lulus mata pelajaran ini',
  `is_finalized` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Apakah rapor sudah final/tidak bisa diubah',
  `finalized_by` bigint unsigned DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_rapor_per_pengampu` (`santri_id`,`pengampu_id`),
  KEY `rapor_finalized_by_foreign` (`finalized_by`),
  KEY `rapor_santri_id_index` (`santri_id`),
  KEY `rapor_pengampu_id_index` (`pengampu_id`),
  KEY `rapor_is_finalized_index` (`is_finalized`),
  KEY `rapor_santri_id_pengampu_id_index` (`santri_id`,`pengampu_id`),
  CONSTRAINT `rapor_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rapor_pengampu_id_foreign` FOREIGN KEY (`pengampu_id`) REFERENCES `pengampu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rapor_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.rapor_summary definition

CREATE TABLE `rapor_summary` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint unsigned NOT NULL,
  `kelas_id` bigint unsigned NOT NULL,
  `semester_id` bigint unsigned NOT NULL,
  `rata_rata` decimal(5,2) NOT NULL COMMENT 'Rata-rata nilai keseluruhan',
  `total_mapel` int NOT NULL COMMENT 'Total mata pelajaran yang diikuti',
  `total_mapel_lulus` int NOT NULL COMMENT 'Total mata pelajaran yang lulus',
  `ranking_kelas` int DEFAULT NULL COMMENT 'Ranking di kelas',
  `total_siswa_kelas` int DEFAULT NULL COMMENT 'Total siswa di kelas',
  `total_kehadiran` int NOT NULL DEFAULT '0' COMMENT 'Total hari masuk',
  `total_sakit` int NOT NULL DEFAULT '0',
  `total_izin` int NOT NULL DEFAULT '0',
  `total_alpa` int NOT NULL DEFAULT '0',
  `catatan_wali_kelas` text COLLATE utf8mb4_unicode_ci,
  `catatan_kepala_sekolah` text COLLATE utf8mb4_unicode_ci,
  `saran` text COLLATE utf8mb4_unicode_ci,
  `prestasi` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array prestasi yang diraih',
  `pelanggaran` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON array pelanggaran',
  `keputusan` enum('naik_kelas','tinggal_kelas','lulus') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_finalized` tinyint(1) NOT NULL DEFAULT '0',
  `finalized_by` bigint unsigned DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_summary_per_semester` (`santri_id`,`semester_id`),
  KEY `rapor_summary_finalized_by_foreign` (`finalized_by`),
  KEY `rapor_summary_santri_id_index` (`santri_id`),
  KEY `rapor_summary_kelas_id_index` (`kelas_id`),
  KEY `rapor_summary_semester_id_index` (`semester_id`),
  KEY `rapor_summary_is_finalized_index` (`is_finalized`),
  KEY `rapor_summary_ranking_kelas_index` (`ranking_kelas`),
  CONSTRAINT `rapor_summary_finalized_by_foreign` FOREIGN KEY (`finalized_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rapor_summary_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rapor_summary_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rapor_summary_semester_id_foreign` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.jadwal_pelajaran definition

CREATE TABLE `jadwal_pelajaran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pengampu_id` bigint unsigned NOT NULL,
  `hari` enum('senin','selasa','rabu','kamis','jumat','sabtu','minggu') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwal_pelajaran_pengampu_id_index` (`pengampu_id`),
  KEY `jadwal_pelajaran_hari_index` (`hari`),
  KEY `jadwal_pelajaran_is_active_index` (`is_active`),
  KEY `jadwal_pelajaran_pengampu_id_hari_index` (`pengampu_id`,`hari`),
  CONSTRAINT `jadwal_pelajaran_pengampu_id_foreign` FOREIGN KEY (`pengampu_id`) REFERENCES `pengampu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.kehadiran definition

CREATE TABLE `kehadiran` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `santri_id` bigint unsigned NOT NULL,
  `pengampu_id` bigint unsigned DEFAULT NULL,
  `jadwal_pelajaran_id` bigint unsigned DEFAULT NULL,
  `jenis_kehadiran` enum('pelajaran','sholat','kegiatan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pelajaran',
  `status_kehadiran` enum('hadir','sakit','izin','alpa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hadir',
  `waktu_absen` time DEFAULT NULL,
  `keterangan_kegiatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Untuk jenis kehadiran selain pelajaran',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kehadiran_santri_id_index` (`santri_id`),
  KEY `kehadiran_pengampu_id_index` (`pengampu_id`),
  KEY `kehadiran_jadwal_pelajaran_id_index` (`jadwal_pelajaran_id`),
  KEY `kehadiran_tanggal_index` (`tanggal`),
  KEY `kehadiran_jenis_kehadiran_index` (`jenis_kehadiran`),
  KEY `kehadiran_status_kehadiran_index` (`status_kehadiran`),
  KEY `kehadiran_santri_id_tanggal_index` (`santri_id`,`tanggal`),
  KEY `kehadiran_pengampu_id_tanggal_index` (`pengampu_id`,`tanggal`),
  CONSTRAINT `kehadiran_jadwal_pelajaran_id_foreign` FOREIGN KEY (`jadwal_pelajaran_id`) REFERENCES `jadwal_pelajaran` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kehadiran_pengampu_id_foreign` FOREIGN KEY (`pengampu_id`) REFERENCES `pengampu` (`id`) ON DELETE SET NULL,
  CONSTRAINT `kehadiran_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- pesantren_db.nilai definition

CREATE TABLE `nilai` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `santri_id` bigint unsigned NOT NULL,
  `pengampu_id` bigint unsigned NOT NULL,
  `komponen_nilai_id` bigint unsigned NOT NULL,
  `nilai` decimal(5,2) NOT NULL COMMENT 'Nilai 0-100',
  `catatan` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan dari pengajar',
  `tanggal_input` date NOT NULL COMMENT 'Tanggal nilai diinput',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nilai_per_komponen` (`santri_id`,`pengampu_id`,`komponen_nilai_id`),
  KEY `nilai_santri_id_index` (`santri_id`),
  KEY `nilai_pengampu_id_index` (`pengampu_id`),
  KEY `nilai_komponen_nilai_id_index` (`komponen_nilai_id`),
  KEY `nilai_tanggal_input_index` (`tanggal_input`),
  KEY `nilai_santri_id_pengampu_id_index` (`santri_id`,`pengampu_id`),
  KEY `nilai_pengampu_id_komponen_nilai_id_index` (`pengampu_id`,`komponen_nilai_id`),
  CONSTRAINT `nilai_komponen_nilai_id_foreign` FOREIGN KEY (`komponen_nilai_id`) REFERENCES `komponen_nilai` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_pengampu_id_foreign` FOREIGN KEY (`pengampu_id`) REFERENCES `pengampu` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilai_santri_id_foreign` FOREIGN KEY (`santri_id`) REFERENCES `santri` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;