# Sistem Informasi Pesantren

Sistem Informasi Pesantren adalah aplikasi berbasis web yang dibangun menggunakan **Laravel (Monolith) + Blade + MySQL** untuk membantu pengelolaan data santri, akademik, kehadiran, pembayaran, dan administrasi pesantren secara terintegrasi.

Aplikasi ini menyediakan **dashboard personal santri** dan **dashboard statistik manajemen** untuk mendukung monitoring dan pengambilan keputusan.

---

## Role

<img width="517" height="214" alt="Screenshot 2026-02-03 at 09 17 53" src="https://github.com/user-attachments/assets/5c45ed21-e351-47fd-a120-b95ec65cd7e3" />

---

## 📌 Demo

Video demo sistem dapat dilihat di:

https://www.awesomescreenshot.com/video/49016773?key=4144f6b0e1477398e689fb666e630acf

Screenshots:

<img width="3420" height="1778" alt="screencapture-localhost-8080-login-2026-02-04-07_30_02" src="https://github.com/user-attachments/assets/f5e56e05-7db0-409d-ba08-e922f76ddc00" />
<img width="3420" height="1778" alt="screencapture-localhost-8080-dashboard-2026-02-04-07_27_17" src="https://github.com/user-attachments/assets/825b9470-a08c-41b5-a8e2-68083cfdddaa" />
<img width="3420" height="1942" alt="screencapture-localhost-8080-santri-dashboard-2026-02-04-07_27_04" src="https://github.com/user-attachments/assets/bedbca98-05bd-4cf9-8d74-4096ea1058fb" />
<img width="881" height="880" alt="Screenshot 2026-02-04 at 07 29 00 Wali" src="https://github.com/user-attachments/assets/3b6c5469-98c5-4ad2-8c87-eeb9a9fa1c81" />

<img width="3420" height="1778" alt="screencapture-localhost-8080-users-2026-02-04-15_06_33" src="https://github.com/user-attachments/assets/1d22cb0f-5dc9-4106-af70-aa089e696414" />
<img width="3420" height="2034" alt="screencapture-localhost-8080-users-2026-02-04-15_06_50" src="https://github.com/user-attachments/assets/4ab19252-4378-421c-a789-dbdfcb1d4c7f" />


<img width="1354" height="758" alt="Screenshot 2026-02-03 at 09 33 46" src="https://github.com/user-attachments/assets/5d0a9ec9-e7e1-4e05-a9e1-a451359712f4" />
<img width="1351" height="693" alt="Screenshot 2026-02-03 at 09 34 07" src="https://github.com/user-attachments/assets/d81f4cc5-1b17-434f-aaf5-3484a17d8d6c" />
<img width="1690" height="877" alt="Screenshot 2026-02-03 at 16 10 39" src="https://github.com/user-attachments/assets/b7a42916-0a19-4e4d-a527-6189c1cc75f4" />
<img width="1379" height="823" alt="Screenshot 2026-02-03 at 16 13 48" src="https://github.com/user-attachments/assets/5da57c4f-ee2d-4f25-946b-9fbd7b593cf8" />

---

## ✨ Fitur Utama

### 1. Dashboard Santri (Personal Dashboard)

Setiap santri memiliki halaman dashboard pribadi yang menampilkan informasi utama, seperti:

- Identitas Santri (Nama, NIS, NISN, Jenis Kelamin)
- Status Keaktifan
- Profil Ringkas
- Navigasi Modul:
  - Profil
  - Kelas
  - Kamar
  - Kehadiran
  - Nilai
  - Rapor
  - Pembayaran
  - Perizinan

Dashboard ini membantu santri dan wali untuk memantau perkembangan akademik dan administrasi secara mandiri.

---

### 2. Dashboard Statistik (Manajemen)

Dashboard Statistik digunakan oleh admin/pengelola pesantren untuk melihat kondisi keseluruhan sistem melalui data visual dan ringkasan.

Menu utama statistik meliputi:

- Statistik Umum
- Kehadiran Santri
- Akademik
- Pembayaran
- Distribusi Status
- Kamar
- Santri Baru

Fitur ini mendukung monitoring operasional pesantren secara real-time dan berbasis data.

---

### 3. Manajemen Data Inti

Sistem mendukung pengelolaan data utama pesantren, termasuk:

- Santri
- Pengajar
- Kelas
- Kamar & Penghuni
- Gedung
- Pelajaran
- Tahun Ajaran
- Kehadiran
- Nilai & Rapor
- Pembayaran (Syahriyah)
- Perizinan
- Inventaris

Semua data terintegrasi dalam satu sistem terpusat.

---

## 🏗️ Arsitektur Sistem

- Backend: Laravel (Monolith)
- Frontend: Blade Template
- Database: MySQL
- Chart & Visualisasi: Chart.js (CDN)
- Authentication: Laravel Auth
- Authorization: Role-based Access (Admin, Pengajar, Santri, etc)

Arsitektur monolith dipilih untuk kemudahan deployment, maintenance, dan pengembangan bertahap.

---

## 📊 Konsep Dashboard

### Dashboard Santri
Digunakan untuk:
- Monitoring pribadi
- Transparansi nilai & kehadiran
- Akses informasi administrasi

### Dashboard Statistik
Digunakan untuk:
- Evaluasi kinerja akademik
- Monitoring pembayaran
- Analisis distribusi santri
- Pengambilan keputusan manajemen

---

## 🎯 Target Pengguna

- Admin Pesantren
- Pengelola Akademik
- Pengajar
- Santri
- Wali Santri

Setiap peran memiliki hak akses sesuai kebutuhan.

---

## 🚀 Tujuan Sistem

Sistem ini dikembangkan untuk:

- Meningkatkan efisiensi administrasi pesantren
- Mengurangi pencatatan manual
- Meningkatkan transparansi data
- Mendukung digitalisasi pesantren
- Mempermudah monitoring santri

---

## 🛠️ Status Pengembangan

**FOR DEMO PURPOSE**

**NEED FURTHER IMPLEMENTATIONS**

---

## Kontak

marjuqi[dot]rahmat[at]gmail[dot]com

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan internal pesantren.

Hak cipta dilindungi.
