# Panduan Penggunaan Sistem SAARe (Berdasarkan Role & Fitur)

## 1) Ringkasan Akses Role

### Guest (belum login)
- Akses halaman publik: landing page, artikel, jurnal, info risiko.
- Bisa:
  - Daftar akun di `/register`
  - Login di `/login`

### Siswa
- Setelah login dan **tervalidasi**, diarahkan ke `/education`.
- Fitur utama:
  - Pretest / Posttest (sesuai pengaturan sekolah)
  - Hasil tes
  - Video edukasi
  - Forum sekolah
  - FAQ
  - Unduh sertifikat (jika lulus)

### Guru
- Setelah login dan tervalidasi, diarahkan ke `/dashboard/guru`.
- Fitur utama:
  - Dashboard ringkasan siswa sekolah
  - Validasi siswa di `/guru/validasi`
  - Pantau progress pretest/posttest siswa
  - Respon forum sekolah
  - Lihat video edukasi

### Admin
- Masuk ke panel admin Filament di `/admin`.
- Fitur utama:
  - Validasi peserta
  - Kelola sekolah (termasuk toggle Pretest/Posttest per sekolah)
  - Kelola video, bank soal, FAQ, postingan, jurnal, slider
  - Pantau hasil tes & export

---

## 2) Alur Umum Akun

1. User daftar akun (role: `siswa` atau `guru`).
2. Status awal akun: menunggu validasi.
3. Saat status masih pending, user masuk ke halaman `validation-pending`.
4. Setelah divalidasi:
   - Admin -> panel `/admin`
   - Guru -> `/dashboard/guru`
   - Siswa -> `/education`

---

## 3) Panduan Role Siswa

## 3.1 Halaman Utama Siswa (`/education`)
- **Hasil Tes Anda**
  - Menampilkan skor pretest/posttest, progress bar, dan perubahan skor.
- **Akses Tes**
  - Tombol `Mulai Pretest` dan `Mulai Posttest` muncul sesuai toggle sekolah.
  - Jika tes sudah pernah dikerjakan, klik tombol akan menampilkan notifikasi.
- **Video Edukasi**
  - Terkunci jika pretest belum dikerjakan.
  - Setelah pretest selesai, video bisa dibuka.
- **Forum Sekolah**
  - Siswa bisa membuat pertanyaan.
  - Siswa bisa menambahkan jawaban lanjutan pada thread miliknya.
- **FAQ**
  - Berisi pertanyaan dan jawaban umum.

## 3.2 Alur Pretest
1. Klik `Mulai Pretest` (jika tombol tersedia).
2. Kerjakan:
   - Soal Pretest
   - Pertanyaan Sikap
   - Pertanyaan Pengetahuan
3. Setelah hasil tampil, klik `Selesai Pretest`.
4. Sistem kembali ke `/education`.

## 3.3 Alur Posttest
1. Klik `Mulai Posttest` (jika tombol tersedia).
2. Kerjakan:
   - Soal Posttest
   - Pertanyaan Sikap Akhir
   - Pertanyaan Pengetahuan Akhir
3. Setelah selesai, sistem kembali ke `/education`.

## 3.4 Sertifikat
- Sertifikat bisa diunduh jika memenuhi syarat kelulusan posttest.
- Batas lulus saat ini: minimal **8 jawaban benar** pada posttest.

---

## 4) Panduan Role Guru

## 4.1 Dashboard Guru (`/dashboard/guru`)
- Menampilkan:
  - Total siswa
  - Jumlah siswa tervalidasi
  - Rata-rata skor pretest/posttest
  - Aktivitas forum
  - Progress 10 siswa terbaru

## 4.2 Validasi Siswa (`/guru/validasi`)
- Guru hanya memvalidasi siswa dari sekolahnya sendiri.
- Fitur:
  - Cari siswa (nama/NISN/username)
  - Filter kelas
  - Pilih banyak siswa
  - Aksi massal: Setujui/Tolak
  - Aksi per siswa: ACC/Tolak

## 4.3 Forum Sekolah
- Guru dapat membalas pertanyaan.
- Guru dapat menandai solusi dan menutup diskusi.

---

## 5) Panduan Role Admin (Panel `/admin`)

## 5.1 Validasi Peserta
- Menu: **Validasi Peserta**
- Bisa approve/reject akun pending (single maupun bulk).

## 5.2 Pengaturan Tombol Pretest/Posttest per Sekolah
- Menu: **Sekolah**
- Pada tabel sekolah, gunakan toggle:
  - `Pretest` ON/OFF
  - `Posttest` ON/OFF
- Efek:
  - OFF: tombol disembunyikan di siswa dan akses URL tes diblokir.
  - ON: tombol tampil dan tes bisa diakses (sesuai step).

## 5.3 Manajemen Video
- Menu: **Video**
- Fitur:
  - Upload video
  - Preview
  - Set video aktif
  - Hapus video

## 5.4 Manajemen Soal
- Menu terkait:
  - Pertanyaan (pre/post)
  - Pertanyaan Sikap
  - Pertanyaan Pengetahuan
  - Jawaban/Opsi

## 5.5 Hasil Tes & Export
- Menu: **Hasil Tes**
- Bisa filter berdasarkan:
  - Jenis tes (pre/post)
  - Sekolah
- Bisa export hasil ke Excel.

## 5.6 Konten Umum
- Admin juga dapat kelola:
  - FAQ
  - Postingan
  - Jurnal
  - Slider landing
  - Tanya jawab siswa (resource forum)

---

## 6) Troubleshooting Cepat

## 6.1 Siswa tidak bisa masuk tes
- Cek apakah tombol tes untuk sekolahnya sudah ON di menu Sekolah (admin).
- Cek apakah user sudah tervalidasi.

## 6.2 Video tidak bisa dibuka
- Pastikan siswa sudah menyelesaikan pretest terlebih dahulu.

## 6.3 Tombol tes ada tapi tidak jalan
- Jika muncul alert “sudah mengerjakan”, berarti tes tersebut sudah pernah dikerjakan.

## 6.4 Sertifikat tidak muncul
- Pastikan siswa sudah menyelesaikan posttest dan mencapai batas minimal kelulusan.

---

## 7) Rekomendasi SOP Operasional

1. Admin validasi akun setiap hari.
2. Admin aktifkan `Pretest` per sekolah sesuai jadwal.
3. Setelah periode pretest selesai, aktifkan `Posttest`.
4. Guru memantau progress dan merespon forum secara berkala.
5. Admin export hasil tes untuk pelaporan periodik.

