# FoodMap GIS

Sistem Informasi Geografis pencarian penyedia makanan berbasis web (restoran, kafe, warung) di Kota Bandung.
Tugas proyek mata kuliah Sistem Informasi Geografis, Universitas Langlangbuana.

## Fitur
- Daftar penyedia makanan yang datanya diambil dari database MySQL (bukan data statis)
- Peta Google Maps (iframe) dengan marker lokasi penyedia yang dipilih
- Pencarian dan filter: nama, kategori, rentang harga
- Detail penyedia: alamat, jam operasional, telepon, foto, menu dan harga
- Lokasi pengguna (geolokasi browser) dan pengurutan dari yang terdekat (jarak garis lurus, rumus Haversine)
- Petunjuk rute melalui Google Maps URLs
- Halaman admin (CRUD) untuk penyedia dan menu, dengan validasi koordinat dan upload foto

## Teknologi
HTML, CSS, JavaScript, PHP (mysqli), MySQL, Google Maps (iframe dan Google Maps URLs).

## Cara menjalankan (Laragon / XAMPP)
1. Salin folder proyek ke `www` (Laragon) atau `htdocs` (XAMPP), misalnya `C:\laragon\www\foodmap`.
2. Nyalakan Apache dan MySQL.
3. Di phpMyAdmin, buat database kosong bernama `foodmap_gis`, pilih database itu, lalu **Import** berkas `foodmap_gis.sql`.
4. Jika perlu, sesuaikan user dan password MySQL di `koneksi.php` (bawaan: `root`, tanpa password).
5. Buka `http://localhost/foodmap/` untuk aplikasi dan `http://localhost/foodmap/admin/` untuk admin.

## Struktur
```
foodmap/
  index.html, foodmap.css, app.js   antarmuka pengguna
  api_penyedia.php, koneksi.php     API data (JSON) dan koneksi database
  admin/                            CRUD penyedia dan menu
  foto/                             foto penyedia
  foodmap_gis.sql                   struktur dan data uji database
```

## Catatan
- Data penyedia dan koordinat adalah data uji; verifikasi koordinat di Google Maps sebelum dipakai.
- Halaman admin belum dilindungi login.
