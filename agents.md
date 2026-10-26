1. IDENTITAS DAN TUJUAN PROYEK

Nama aplikasi: KonterKu

Deskripsi: Aplikasi berbasis web untuk mengelola keuangan dan persediaan barang pada usaha konter.

Teknologi yang digunakan:

Laravel 13 sebagai framework backend.

PHP yang kompatibel dengan Laravel 13.

MySQL sebagai database.

Laragon sebagai lingkungan pengembangan lokal di Windows.

Laravel Blade sebagai template frontend.

Tailwind CSS sebagai framework CSS.

Vite sebagai alat pengelolaan aset frontend.

JavaScript untuk interaksi frontend.

Git untuk pengelolaan versi kode.

GitHub sebagai repository kode sumber.

PHPUnit atau Pest untuk pengujian backend.

Playwright untuk pengujian browser jika diperlukan.

Fitur utama:

Dashboard.

Pemasukan.

Pengeluaran.

Produk & Stok.

Laporan.

Pengaturan.

Tujuan utama adalah menghasilkan aplikasi yang aman, stabil, mudah digunakan, mudah dikembangkan, serta memiliki pencatatan keuangan dan stok yang akurat.

2. ATURAN WAJIB AI AGENT

AI Agent wajib mematuhi aturan berikut selama pengembangan.

Kerjakan proyek secara bertahap dari awal sampai selesai.

Jangan membuat seluruh aplikasi sekaligus dalam satu perubahan besar.

Periksa file dan konfigurasi yang sudah ada sebelum melakukan perubahan.

Jangan menghapus atau menimpa file tanpa memeriksa isinya.

Jangan mengubah proyek lain yang berada di dalam Laragon.

Jangan menghapus database atau data pengguna tanpa persetujuan.

Jangan menjalankan perintah yang berpotensi menghapus data tanpa menjelaskan dampaknya terlebih dahulu.

Gunakan standar pengembangan Laravel yang baik.

Pastikan validasi dilakukan di backend, bukan hanya frontend.

Jaga konsistensi transaksi keuangan dan pergerakan stok.

Jangan menyimpan password, token, atau kredensial rahasia di dalam source code.

Jangan mengarang hasil pengujian, commit, atau push.

Jangan menyatakan fitur selesai jika belum diverifikasi.

Jika menemukan error, cari penyebabnya sebelum melakukan perubahan.

Jika ada aturan bisnis yang belum jelas dan dapat memengaruhi uang atau stok, tanyakan kepada pengguna sebelum mengambil keputusan.

3. TAHAP 1 — PEMERIKSAAN LINGKUNGAN

Sebelum membuat aplikasi, periksa lingkungan pengembangan.

Periksa:
Versi PHP.
Versi Composer.
Versi Node.js.
Versi npm.

Versi Git.

Lokasi instalasi Laragon.

Lokasi folder www yang sebenarnya.

Ketersediaan layanan MySQL.

Kompatibilitas lingkungan dengan Laravel 13.

Apakah folder konterku sudah tersedia.

Apakah folder tujuan sudah memiliki proyek atau repository Git.

Jalankan pemeriksaan melalui terminal dan gunakan hasil sebenarnya.

Jangan menganggap lokasi Laragon selalu C:\laragon\www. Pastikan lokasi instalasinya terlebih dahulu.

Jika ada komponen yang belum tersedia atau tidak kompatibel, laporkan masalah dan berikan solusi yang aman.

Jangan langsung memperbarui atau memasang perangkat lunak sistem tanpa menjelaskan perubahan yang diperlukan.

Setelah pemeriksaan selesai, laporkan hasilnya dan tunggu persetujuan sebelum melanjutkan ke tahap berikutnya.

4. TAHAP 2 — PEMBUATAN FOLDER DAN INSTALASI LARAVEL

Buat folder proyek bernama konterku pada direktori Laragon yang telah diverifikasi.

Contoh lokasi apabila Laragon menggunakan direktori standar:

C:\laragon\www\konterku

Aturan:

Periksa apakah folder tujuan sudah ada.

Jangan menimpa proyek yang sudah tersedia.

Jika folder sudah ada, periksa isinya sebelum mengambil tindakan.

Instal Laravel 13 menggunakan Composer dan versi dependensi yang kompatibel.

Pastikan Laravel berhasil diinstal.

Periksa versi Laravel menggunakan perintah Artisan.

Pastikan dependensi Composer terpasang dengan benar.

Pastikan aplikasi dapat dijalankan.

Catat perintah instalasi di README.md.

Jangan melanjutkan apabila instalasi Laravel mengalami error yang belum diselesaikan.

Setelah berhasil, periksa perubahan dan buat commit Git untuk tahap ini apabila semua pemeriksaan yang relevan lulus.