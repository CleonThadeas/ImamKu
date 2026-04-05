# Product Specification Document (PRD) - ImamKu

**Project Name:** ImamKu - Ramadan Schedule & Management System  
**Version:** 1.0.0  
**Target Platform:** Web Application (Responsive)  
**Tech Stack:** Laravel 11.x, PHP 8.3, MySQL, Blade Templating, Vanilla JS  

---

## 1. Executive Summary
**ImamKu** adalah sistem manajemen operasional dan penjadwalan komprehensif, didesain spesifik untuk memfasilitasi rotasi kepemimpinan sholat (Imam) pada masjid-masjid selama periode *Season* (seperti Bulan Suci Ramadan). Sistem ini menyoroti kemudahan penugasan otomatis, validasi kehadiran waktu nyata, pengelolaan rotasi secara mandiri antar-imam, serta transparansi rekonsiliasi pembayaran insentif (*Fee*).

---

## 2. Core User Personas
Terdapat tiga aktor logis utama pada sistem:
1. **Administrator (Takmir/Pengurus Masjid):**
   Memegang kendali atas manajemen data master, konfigurasi musim (Season), rotasi jadwal, persetujuan absensi, hingga ekspor laporan pembayaran.
2. **Imam (Pengguna Prioritas):**
   Menerima jadwal, melakukan absensi (Check-in), meminta pertukaran jadwal (Swap), dan memonitor pendapatan tunjangan mereka.
3. **Automated Scheduler (System Job):**
   Bertindak statis dibalik layar untuk memvalidasi absen otomatis, membuang _swap_ kedaluwarsa, serta mengirim bel / *reminder* WhatsApp/Email sebelum jadwal sholat masuk.

---

## 3. Product Features & Modules

### A. Manajemen Season (Musim) & Tipe Sholat
- **Season Aktif:** Jadwal hanya bisa diproses apabila ada satu "Season" yang direpresentasikan aktif (misal: "Ramadan 1447H").
- **Tipe Sholat Fleksibel:** Pengurus dapat membuat tipe-tipe sholat (Tarawih, Isya, Subuh) dikombinasikan dengan API auto-sinkronisasi waktu sholat dari Kemenag (Aladhan API).

### B. Assignment & Penjadwalan Cerdas
- **Auto-Generate:** Sistem dapat men-_generate_ rangka kosong selama durasi hari penuh *season* berlangsung.
- **Assignment Imam:** Admin memilih dari daftar Imam aktif untuk dijadwalkan mengisi kolom *slot* yang tersedia.
- **Rule Engine:** Mencegah terjadinya bentrok (*overbooking*), contohnya: mencegah satu imam mengisi dua sholat pada waktu yang berdekatan ekstrem.

### C. Sistem Absensi Terintegrasi
- **Batas Toleransi (Time-Window):** Absen hanya terbuka secara ketat **30 menit sebelum** waktu azan dan **30 menit setelahnya**. Setelah di luar jam itu, opsi absen terputus.
- **Security Check (Face Proof):** Mewajibkan *upload* foto *real-time* dengan latar masjid (buktifikasi keberadaan).
- **Auto-Approval Protocol:** Admin dapat mengaktifkan modul Otonom, di mana 30 menit pasca-waktu sholat berakhir, sistem (melalui *Scheduler*) akan melakukan validasi "Selesai" ke semua absen secara instan membebaskan jam kerja manusia.

### D. Pasar Pertukaran Jadwal (Swap System)
- **Initiative Request:** Imam yang berhalangan dapat menekan tombol "Swap" di dasbor (maksimal H-2 jam sebelum azan).
- **Barter Shift Exchange:** Pertukaran bersifat *blind-request* (ditawarkan layaknya order terbuka). Imam B yang menyanggupi **WAJIB** memberikan jadwal gantinya sendiri kepada Imam A sebagai tumbal.
- **Auto-Expiry:** Jika sebuah tawaran tidak ada yang mengambil hingga mendekati waktu azan, permintaan akan dikunci mati dan dilabeli `expired` secara otomatis oleh *Scheduler*.
- **Admin Oversight:** Admin disediakan halaman `/admin/swaps` untuk memonitor lalu lintas mutasi jadwal pertukaran agar pengawasan tetap terpusat.

### E. Modul Keuangan & Insentif (Fee System)
- **Dual Calculus Engine:** Tersedia konfigurasi harga insentif:
  - `Per Schedule`: Dinilai dari jenis sholat (misal: Tarawih Rp150.000, Subuh Rp100.000).
  - `Flat Per Day`: Hitung pukul rata per hari kedatangan, berapa kalipun imam itu memimpin.
- **Kondisional Tuntas:** Dana/Fee otomatis dikalkulasikan sebagai uang hak milik HANYA jika status absensinya telah sah tervalidasi (`APPROVED`).
- **Dashboard Transparansi Pendapatan:** Dasbor pribadi Imam menampilkan total estimasi Rupiah dikumpulkan beserta perincian angka pecahan untuk setiap ibadah sholat yang mereka jalani.

### F. Command Center & Notifikasi
- **Notifikasi Omnichannel:** Konfigurasi notifikasi mencakup bel UI Dalam Laman (Database), Notifikasi Email, serta WhatsApp.
- **Reminders:** Mengirim peringatan dinamis ke gawai imam misal 60 menit dan 30 menit sebelum bertugas.
- **Pesan Siaran (Broadcast):** Broadcast global dari Admin ke faksi Imam untuk instruksi cepat / *briefing* mendadak. 

### G. Ekspor Laporan Excel/CSV (Export Hub)
- **No-Memory Constraint Streaming:** Modul _fputcsv streaming_ memastikan rekap ribuan jadwal Ramadan dapat di-unduh instan secepat kilat tanpa menyiksa memori server.
- Termasuk kapabilitas unduh data Master Imam, Rotasi Jadwal, dan Laporan Invoice / Slip Keuangan secara komprehensif.

---

## 4. Estetika dan Desain Antarmuka (UI/UX)
Sistem ini menggunakan gaya _glassmorphism_ dan pewarnaan modern menggunakan CSS murni level tinggi (Aesthetic Gradient & Micro-animations). Seluruh elemen teks konvensional menggunakan SVG Vektor (Lucide/Heroicons) berskala tinggi untuk memastikan pengalaman _enterprise_ web berkelas, dilengkapi _Real-time Digital Clock Server-Synced_ (WIB) pada kedua belah dashboard.

---

## 5. Deployment & Technical Infrastructure
| Layer | Specification |
|-------|--------------|
| Server/Framework | Laravel 11.x |
| Database | MySQL Schema (Strict Typed Relations) |
| Async Workers | Laravel Scheduled Task (`schedule:work`), Queue Workers (segera) |
| UI | Blade Templating with AlpineJS/Vanilla JS & Native Flexbox/CSS Variables |
| Third Party APIs | Aladhan (Prayer Times JSON) |

**Dokumen Spesifikasi ini merepresentasikan status _Current Production Line_ yang berjalan sempurna pada ImamKu saat ini.**
