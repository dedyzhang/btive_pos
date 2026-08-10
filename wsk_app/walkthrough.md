# Walkthrough: Fitur Penggajian Staf & Cetak Slip Gaji Bulanan Selesai Diimplementasikan!

Sistem pengelolaan gaji karyawan (payroll) berbasis kehadiran dan rekap absensi bulanan telah selesai dikembangkan, diuji, dan diintegrasikan secara penuh ke dalam sistem POS Kasir.

---

## Perubahan yang Dilakukan

1. **Skema Database (`migrations`)**:
   - Menambahkan kolom `daily_salary` pada tabel `users` untuk menyimpan nominal gaji harian staf.
   - Membuat tabel `salary_adjustments` untuk mengelola bonus/insentif khusus dan potongan/denda gaji karyawan pada tanggal tertentu.

2. **Model & Relasi (`app/Models`)**:
   - Membuat model `SalaryAdjustment` lengkap dengan relasi `belongsTo` ke `User`.
   - Menambahkan relasi `hasMany` (`salaryAdjustments()`) pada model `User`.

3. **Controller & Routing (`PayrollController.php` & `routes/web.php`)**:
   - Membuat `PayrollController` untuk menangani kalkulasi gaji otomatis, pemutakhiran nominal gaji harian, pencatatan bonus/potongan, serta penyajian halaman cetak slip gaji bulanan.
   - Mendaftarkan rute pengelolaan penggajian di bawah perlindungan hak akses (`permission:manage_attendance`).

4. **Tampilan Antarmuka Dasbor Penggajian (`payroll/index.blade.php`)**:
   - Menyediakan dasbor penggajian yang modern di mana manajer dapat memfilter data per bulan.
   - Menampilkan ringkasan ringkas berupa: jumlah kehadiran (hari kerja) yang bersumber langsung dari absensi bulanan, total gaji pokok, total bonus, total potongan, dan gaji bersih yang akan diterima karyawan.
   - Mengintegrasikan modal interaktif untuk memperbarui nominal gaji harian secara instan, serta mengelola daftar bonus/potongan di bulan terkait.

5. **Kalender Absensi Interaktif (`payroll/index.blade.php`)**:
   - Ditambahkan tombol **Kalender** baru pada kartu staf di dasbor.
   - Mengintegrasikan modal kalender bulanan interaktif yang menampilkan status kehadiran staf hari demi hari:
     - **Hijau**: Hadir (Tepat waktu) dengan rincian jam Clock In/Out.
     - **Kuning**: Hadir (Terlambat) dengan rincian jam Clock In/Out.
     - **Merah**: Tidak Absen (Alpa).
     - **Abu-abu**: Hari di masa depan (belum dinilai).
   - Dilengkapi dengan fitur tooltip interaktif saat kursor diarahkan ke sel tanggal kalender.

6. **Integrasi Arus Kas & Status Pembayaran Gaji (`payroll/index.blade.php`)**:
   - Menambahkan visual status **"LUNAS DIBAYAR"** (hijau) atau **"BELUM DIBAYAR"** (oranye) pada kartu karyawan.
   - Menyediakan tombol CTA **"Bayar Gaji Sekarang"** yang akan memicu modal konfirmasi pembayaran gaji.
   - Admin dapat memilih **Sumber Kas Pembayaran** (akun kas riil dari modul Arus Kas) untuk mendebet pengeluaran gaji.
   - Ketika dikonfirmasi, sistem otomatis membuat transaksi pengeluaran kas (`expense`) dengan kategori otomatis **"Gaji Karyawan"**.
   - Menyediakan tombol **"Batal Pembayaran Gaji"** untuk membatalkan pembayaran dan menghapus transaksi dari jurnal arus kas secara instan.

7. **Slip Gaji Siap Cetak (`payroll/print.blade.php`)**:
   - Mendesain slip gaji berformat kertas A4 portrait yang bersih dan profesional.
   - Menyertakan logo dan identitas restoran, ringkasan kehadiran karyawan, rincian komponen gaji pokok, rincian bonus dan potongan ber-tanggal, total gaji bersih diterima, tabel riwayat kehadiran harian lengkap dengan status ketepatan waktu, serta kolom tanda tangan penerima & manajemen.
   - Otomatis memicu dialog print browser (`window.print()`).

8. **Sidebar Menu (`sidebar.blade.php`)**:
   - Menambahkan menu **"Penggajian"** berikon dompet/uang bill (`fa-money-bill-wave`) di bawah menu Rekap Absensi untuk mempermudah akses admin.

---

## Hasil Pengujian & Verifikasi

### Pengujian Otomatis
Semua pengujian fitur penggajian staf di `Tests\Feature\PayrollTest.php` telah terlewati dengan sukses (`PASS`):
```powershell
php artisan test --filter=PayrollTest
```
*Hasil:*
- `✓ admin can access payroll dashboard` (Dasbor penggajian termuat dengan benar)
- `✓ admin can update daily salary` (Gaji harian diperbarui di DB)
- `✓ admin can store and delete salary adjustment` (Pencatatan bonus/potongan berhasil)
- `✓ admin can view payslip print page` (Tampilan slip cetak ter-render dengan kalkulasi yang akurat)
- `✓ admin can retrieve calendar data` (Endpoint AJAX kalender mengembalikan data JSON absensi yang tepat)
- `✓ admin can pay employee salary` (Pembayaran gaji berhasil terintegrasi membuat transaksi di Arus Kas)
- `✓ admin can cancel salary payment` (Pembatalan gaji berhasil menghapus transaksi di Arus Kas)

---

## Panduan Penggunaan & Uji Manual

1. **Membuka Halaman Penggajian**:
   - Masuk sebagai Admin, pilih menu **Penggajian** di sidebar sebelah kiri.
   - Anda akan melihat daftar karyawan beserta ringkasan gaji mereka untuk bulan ini.

2. **Mengatur Gaji Harian Karyawan**:
   - Klik tombol **Set Gaji** pada kartu salah satu staf (misal: Kasir).
   - Masukkan nominal gaji per hari (contoh: `120000`), lalu klik **Simpan Konfigurasi**. Nominal gaji harian akan diperbarui.

3. **Mengelola Bonus & Potongan**:
   - Klik tombol **Penyesuaian** pada staf terkait.
   - Di modal yang terbuka, Anda dapat melihat daftar bonus/potongan bulan ini.
   - Masukkan tanggal, pilih tipe (Bonus/Potongan), isi nominal (misal: `50000`), tulis alasan (contoh: *"Bonus Lembur Akhir Pekan"*), dan klik **Simpan Penyesuaian**. Gaji bersih pada kartu staf akan langsung terhitung secara otomatis.

4. **Melihat Kalender Kehadiran Bulanan**:
   - Klik tombol **Kalender** pada kartu staf.
   - Modal kalender bulanan yang elegan akan muncul dan memuat data absensi secara asinkron (AJAX).
   - Anda dapat dengan mudah membedakan hari di mana staf **Hadir** (hijau), **Terlambat** (kuning), atau **Alpa/Tidak Absen** (merah).
   - Arahkan kursor (*hover*) pada tanggal hijau/kuning untuk melihat detail jam Clock-In dan Clock-Out staf.

5. **Membayarkan Gaji & Integrasi Arus Kas**:
   - Klik tombol **Bayar Gaji Sekarang** pada kartu staf yang berstatus **"BELUM DIBAYAR"**.
   - Pilih sumber kas pembayaran (misal: *KAS KECIL TOKO*).
   - Keterangan transaksi akan terisi otomatis (misal: *"Pembayaran Gaji Fellian"*).
   - Klik **Bayar & Catat ke Arus Kas**. Status kartu akan berubah menjadi hijau **"LUNAS DIBAYAR via KAS KECIL TOKO"** dan transaksi pengeluaran otomatis tercatat di menu **Arus Kas** Anda!
   - Untuk membatalkan, klik **Batal Pembayaran Gaji** dan konfirmasi. Status akan kembali ke belum dibayar dan catatan arus kas otomatis dihapus.

6. **Mencetak Slip Gaji Bulanan**:
   - Klik tombol **Cetak Slip** pada kartu staf.
   - Halaman slip gaji resmi yang rapi akan terbuka di tab baru dan langsung memicu kotak cetak printer browser. Anda bisa menyimpannya langsung sebagai PDF atau mencetaknya ke kertas A4.

