<?php
require "../koneksi.php";
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$p = ["nama_penyedia" => "", "id_kategori" => "", "alamat" => "", "latitude" => "", "longitude" => "",
      "jam_buka" => "08:00", "jam_tutup" => "21:00", "no_telepon" => "", "foto" => ""];
$menu = [];

if ($id) {
    $st = $conn->prepare("SELECT * FROM penyedia_makanan WHERE id_penyedia = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();
    if (!$row) { http_response_code(404); exit("Data tidak ditemukan."); }
    $p = $row;
    $p["jam_buka"]  = substr($p["jam_buka"], 0, 5);
    $p["jam_tutup"] = substr($p["jam_tutup"], 0, 5);

    $sm = $conn->prepare("SELECT nama_menu, harga FROM menu_makanan WHERE id_penyedia = ? ORDER BY id_menu");
    $sm->bind_param("i", $id);
    $sm->execute();
    $menu = $sm->get_result()->fetch_all(MYSQLI_ASSOC);
}
while (count($menu) < 3) $menu[] = ["nama_menu" => "", "harga" => ""];
$kategori = $conn->query("SELECT id_kategori, nama_kategori FROM kategori_makanan ORDER BY nama_kategori");
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $id ? "Ubah" : "Tambah" ?> Penyedia</title><link rel="stylesheet" href="admin.css?v=2"></head>
<body>
<header><h1><?= $id ? "Ubah" : "Tambah" ?> Penyedia Makanan</h1><a href="index.php">← Kembali</a></header>
<div class="wrap"><div class="card">
<form method="post" action="simpan.php" enctype="multipart/form-data">
  <input type="hidden" name="id" value="<?= $id ?>">

  <label>Nama penyedia</label>
  <input type="text" name="nama_penyedia" value="<?= h($p["nama_penyedia"]) ?>" required maxlength="100">

  <label>Kategori</label>
  <select name="id_kategori" required>
    <option value="">-- pilih --</option>
    <?php while ($k = $kategori->fetch_assoc()): ?>
      <option value="<?= (int)$k["id_kategori"] ?>" <?= $k["id_kategori"] == $p["id_kategori"] ? "selected" : "" ?>><?= h($k["nama_kategori"]) ?></option>
    <?php endwhile; ?>
  </select>

  <label>Alamat</label>
  <textarea name="alamat" rows="2" required><?= h($p["alamat"]) ?></textarea>

  <div class="baris">
    <div><label>Latitude</label><input type="text" name="latitude" value="<?= h($p["latitude"]) ?>" required placeholder="-6.9175"></div>
    <div><label>Longitude</label><input type="text" name="longitude" value="<?= h($p["longitude"]) ?>" required placeholder="107.6091"></div>
  </div>
  <small>Tips: di Google Maps klik kanan pada lokasi lalu klik angka koordinat untuk menyalinnya.</small>

  <div class="baris">
    <div><label>Jam buka</label><input type="time" name="jam_buka" value="<?= h($p["jam_buka"]) ?>" required></div>
    <div><label>Jam tutup</label><input type="time" name="jam_tutup" value="<?= h($p["jam_tutup"]) ?>" required></div>
    <div><label>No. telepon</label><input type="text" name="no_telepon" value="<?= h($p["no_telepon"]) ?>" maxlength="20"></div>
  </div>

  <label>Foto (jpg/png/webp, maks 2 MB)</label>
  <?php if ($p["foto"]): ?><img class="thumb" src="../foto/<?= h($p["foto"]) ?>" alt="" onerror="this.style.display='none'"><?php endif; ?>
  <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp">

  <label>Menu (minimal 3)</label>
  <div id="menuList">
    <?php foreach ($menu as $m): ?>
    <div class="menu-row">
      <input type="text" name="menu_nama[]" placeholder="Nama menu" value="<?= h($m["nama_menu"]) ?>">
      <input type="number" name="menu_harga[]" placeholder="Harga" min="0" step="500" value="<?= $m["harga"] !== "" ? (int)$m["harga"] : "" ?>">
      <button type="button" class="btn abu" onclick="this.parentNode.remove()">✕</button>
    </div>
    <?php endforeach; ?>
  </div>
  <button type="button" class="btn abu" id="tambahMenu">+ Tambah menu</button>

  <p><button class="btn" type="submit">Simpan</button></p>
</form>
</div></div>
<script>
document.getElementById("tambahMenu").addEventListener("click", function () {
  const d = document.createElement("div");
  d.className = "menu-row";
  d.innerHTML = '<input type="text" name="menu_nama[]" placeholder="Nama menu">' +
                '<input type="number" name="menu_harga[]" placeholder="Harga" min="0" step="500">' +
                '<button type="button" class="btn abu" onclick="this.parentNode.remove()">✕</button>';
  document.getElementById("menuList").appendChild(d);
});
</script>
</body></html>
