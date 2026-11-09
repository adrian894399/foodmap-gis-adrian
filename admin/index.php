<?php
require "../koneksi.php";
$sql = "SELECT p.id_penyedia, p.nama_penyedia, k.nama_kategori, p.alamat, p.latitude, p.longitude,
               (SELECT COUNT(*) FROM menu_makanan m WHERE m.id_penyedia = p.id_penyedia) AS jml_menu
        FROM penyedia_makanan p JOIN kategori_makanan k ON k.id_kategori = p.id_kategori
        ORDER BY p.id_penyedia";
$hasil = $conn->query($sql);
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin FoodMap GIS</title><link rel="stylesheet" href="admin.css?v=2"></head>
<body>
<header><h1>Admin FoodMap GIS</h1><a href="../index.html">← Ke peta</a></header>
<div class="wrap">
  <?php if (isset($_GET["ok"])): ?><div class="pesan ok"><?= h($_GET["ok"]) ?></div><?php endif; ?>
  <div class="card">
    <p><a class="btn" href="form.php">+ Tambah Penyedia</a></p>
    <table>
      <tr><th>ID</th><th>Nama</th><th>Kategori</th><th>Koordinat</th><th>Menu</th><th>Aksi</th></tr>
      <?php while ($r = $hasil->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$r["id_penyedia"] ?></td>
        <td><?= h($r["nama_penyedia"]) ?><br><small><?= h($r["alamat"]) ?></small></td>
        <td><?= h($r["nama_kategori"]) ?></td>
        <td><?= h($r["latitude"]) ?>, <?= h($r["longitude"]) ?></td>
        <td><?= (int)$r["jml_menu"] ?></td>
        <td>
          <a class="btn" href="form.php?id=<?= (int)$r["id_penyedia"] ?>">Ubah</a>
          <form method="post" action="hapus.php" style="display:inline" onsubmit="return confirm('Hapus penyedia ini beserta menunya?')">
            <input type="hidden" name="id" value="<?= (int)$r["id_penyedia"] ?>">
            <button class="btn merah" type="submit">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</div>
</body></html>
