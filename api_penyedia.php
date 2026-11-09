<?php
// Mengambil data penyedia makanan + menu dari MySQL, dikirim sebagai JSON
require "koneksi.php";
header("Content-Type: application/json; charset=utf-8");

$sql = "SELECT p.id_penyedia, p.nama_penyedia, k.nama_kategori AS kategori,
               p.alamat, p.latitude, p.longitude, p.jam_buka, p.jam_tutup,
               p.no_telepon, p.foto
        FROM penyedia_makanan p
        JOIN kategori_makanan k ON k.id_kategori = p.id_kategori
        ORDER BY p.id_penyedia";
$hasil = $conn->query($sql);

$data = [];
while ($r = $hasil->fetch_assoc()) {
    $r["id_penyedia"] = (int)$r["id_penyedia"];
    $r["latitude"]    = (float)$r["latitude"];
    $r["longitude"]   = (float)$r["longitude"];
    $r["jam_buka"]    = substr($r["jam_buka"], 0, 5);
    $r["jam_tutup"]   = substr($r["jam_tutup"], 0, 5);
    $r["menu"]        = [];
    $data[$r["id_penyedia"]] = $r;
}

$menu = $conn->query("SELECT id_penyedia, nama_menu, harga FROM menu_makanan ORDER BY id_menu");
while ($m = $menu->fetch_assoc()) {
    $id = (int)$m["id_penyedia"];
    if (isset($data[$id])) {
        $data[$id]["menu"][] = ["nama_menu" => $m["nama_menu"], "harga" => (float)$m["harga"]];
    }
}

echo json_encode(array_values($data), JSON_UNESCAPED_UNICODE);
