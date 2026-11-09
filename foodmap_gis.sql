-- FoodMap GIS - Langkah 1: Database
-- Catatan: koordinat & harga bersifat perkiraan untuk wilayah Kota Bandung.
-- VERIFIKASI tiap koordinat di Google Maps (klik kanan titik -> salin koordinat) sebelum demo.

CREATE DATABASE IF NOT EXISTS foodmap_gis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodmap_gis;

DROP TABLE IF EXISTS menu_makanan;
DROP TABLE IF EXISTS penyedia_makanan;
DROP TABLE IF EXISTS kategori_makanan;

-- Tabel 1: kategori
CREATE TABLE kategori_makanan (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT
) ENGINE=InnoDB;

-- Tabel 2: penyedia (data spasial: latitude, longitude; nonspasial: sisanya)
CREATE TABLE penyedia_makanan (
    id_penyedia INT AUTO_INCREMENT PRIMARY KEY,
    id_kategori INT NOT NULL,
    nama_penyedia VARCHAR(100) NOT NULL,
    alamat TEXT,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    jam_buka TIME,
    jam_tutup TIME,
    no_telepon VARCHAR(20),
    foto VARCHAR(255),
    FOREIGN KEY (id_kategori) REFERENCES kategori_makanan(id_kategori)
) ENGINE=InnoDB;

-- Tabel 3: menu
CREATE TABLE menu_makanan (
    id_menu INT AUTO_INCREMENT PRIMARY KEY,
    id_penyedia INT NOT NULL,
    nama_menu VARCHAR(100) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (id_penyedia) REFERENCES penyedia_makanan(id_penyedia) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO kategori_makanan (nama_kategori, deskripsi) VALUES
('Restoran', 'Tempat makan dengan menu lengkap dan area duduk nyaman'),
('Kafe', 'Tempat nongkrong dengan kopi, minuman, dan camilan'),
('Warung', 'Warung makan sederhana dengan harga terjangkau');

INSERT INTO penyedia_makanan (id_kategori, nama_penyedia, alamat, latitude, longitude, jam_buka, jam_tutup, no_telepon, foto) VALUES
(3,'Batagor Kingsley','Jl. Veteran, Bandung',-6.92110000,107.60750000,'09:00','21:00','022-0000001','batagor_kingsley.jpg'),
(2,'Kopi Toko Djawa','Jl. Braga, Bandung',-6.91750000,107.60910000,'09:00','22:00','022-0000002','kopi_toko_djawa.jpg'),
(1,'Sindang Reret','Jl. Ciumbuleuit, Bandung',-6.87000000,107.60300000,'10:00','21:00','022-0000003','sindang_reret.jpg'),
(3,'Mie Kocok Mang Oman','Jl. Cipaganti, Bandung',-6.89400000,107.59800000,'08:00','20:00','022-0000004','mie_kocok.jpg'),
(2,'Warung Kopi Purnama','Jl. Alkateri, Bandung',-6.92100000,107.60200000,'08:00','21:00','022-0000005','kopi_purnama.jpg'),
(1,'Nasi Timbel Ciwangi','Jl. Dago, Bandung',-6.88500000,107.61300000,'10:00','22:00','022-0000006','timbel_ciwangi.jpg'),
(3,'Warung Nasi Bu Imas','Jl. Riau, Bandung',-6.90800000,107.61700000,'07:00','17:00','022-0000007','bu_imas.jpg'),
(2,'Kafe Dago Atas','Jl. Dago Atas, Bandung',-6.86500000,107.62000000,'10:00','23:00','022-0000008','dago_atas.jpg'),
(1,'RM Sunda Pasteur','Jl. Dr. Djundjunan, Bandung',-6.89700000,107.58700000,'10:00','21:00','022-0000009','sunda_pasteur.jpg'),
(3,'Soto Bandung Mang Uus','Jl. Kebon Kawung, Bandung',-6.91300000,107.59800000,'07:00','15:00','022-0000010','soto_bandung.jpg'),
(2,'Kafe Cihampelas','Jl. Cihampelas, Bandung',-6.89600000,107.60500000,'09:00','22:00','022-0000011','kafe_cihampelas.jpg'),
(1,'Restoran Kebon Jati','Jl. Kebon Jati, Bandung',-6.91000000,107.60300000,'10:00','21:00','022-0000012','kebon_jati.jpg'),
(3,'Warung Lotek Tegalega','Jl. Moh. Toha, Bandung',-6.93500000,107.60000000,'08:00','18:00','022-0000013','lotek.jpg'),
(2,'Kopi Asia Afrika','Jl. Asia Afrika, Bandung',-6.92100000,107.60900000,'08:00','22:00','022-0000014','kopi_aa.jpg'),
(1,'Restoran Padang Simpang Dago','Jl. Ir. H. Juanda, Bandung',-6.89000000,107.61000000,'08:00','22:00','022-0000015','padang_dago.jpg'),
(3,'Bubur Ayam Gatsu','Jl. Gatot Subroto, Bandung',-6.92300000,107.63000000,'05:00','12:00','022-0000016','bubur_gatsu.jpg'),
(2,'Kafe Buah Batu','Jl. Buah Batu, Bandung',-6.94000000,107.63500000,'10:00','23:00','022-0000017','kafe_buahbatu.jpg'),
(1,'Seafood Antapani','Jl. Terusan Jakarta, Bandung',-6.91500000,107.65500000,'16:00','23:00','022-0000018','seafood_antapani.jpg'),
(3,'Warung Gado-Gado Lengkong','Jl. Lengkong Besar, Bandung',-6.92200000,107.61500000,'09:00','19:00','022-0000019','gado_lengkong.jpg'),
(3,'Nasi Goreng Kebon Kelapa','Jl. Kebon Kelapa, Bandung',-6.92000000,107.60400000,'17:00','00:00','022-0000020','nasgor.jpg');

INSERT INTO menu_makanan (id_penyedia, nama_menu, harga) VALUES
(1,'Batagor Kuah',25000),(1,'Siomay',22000),(1,'Es Teh Manis',5000),
(2,'Kopi Susu',22000),(2,'Roti Bakar',18000),(2,'Teh Tarik',20000),
(3,'Nasi Timbel Komplit',45000),(3,'Gurame Bakar',85000),(3,'Es Jeruk',10000),
(4,'Mie Kocok Biasa',25000),(4,'Mie Kocok Spesial',32000),(4,'Es Kelapa Muda',12000),
(5,'Kopi Hitam',12000),(5,'Pisang Goreng',15000),(5,'Mie Instan Telur',16000),
(6,'Nasi Timbel Ayam',38000),(6,'Pepes Ikan',30000),(6,'Lalapan',10000),
(7,'Nasi Sayur Ayam',20000),(7,'Nasi Telur Balado',17000),(7,'Es Teh',4000),
(8,'Latte',28000),(8,'Croissant',25000),(8,'Pasta Carbonara',45000),
(9,'Ayam Goreng Sunda',35000),(9,'Karedok',20000),(9,'Sop Buntut',65000),
(10,'Soto Bandung',28000),(10,'Nasi Putih',5000),(10,'Teh Botol',6000),
(11,'Cappuccino',26000),(11,'Kentang Goreng',20000),(11,'Matcha Latte',27000),
(12,'Nasi Liwet',40000),(12,'Ayam Bakar',38000),(12,'Es Cendol',15000),
(13,'Lotek',15000),(13,'Karedok',15000),(13,'Es Campur',12000),
(14,'Espresso',18000),(14,'Americano',20000),(14,'Cheese Cake',30000),
(15,'Rendang',32000),(15,'Ayam Pop',28000),(15,'Gulai Ikan',30000),
(16,'Bubur Ayam Biasa',15000),(16,'Bubur Ayam Spesial',22000),(16,'Teh Hangat',4000),
(17,'Es Kopi Gula Aren',22000),(17,'Churros',24000),(17,'Nasi Ayam Geprek',28000),
(18,'Udang Saus Padang',75000),(18,'Cumi Goreng Tepung',60000),(18,'Kerang Rebus',45000),
(19,'Gado-Gado',18000),(19,'Ketoprak',17000),(19,'Es Jeruk',8000),
(20,'Nasi Goreng Spesial',25000),(20,'Mie Goreng',22000),(20,'Es Teh Manis',5000);
