<?php
require "../koneksi.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit; }

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
function gagal($errors) {
    echo "<!DOCTYPE html><meta charset='UTF-8'><link rel='stylesheet' href='admin.css?v=2'>";
    echo "<div class='wrap'><div class='card'><div class='pesan err'><strong>Data belum disimpan:</strong><ul>";
    foreach ($errors as $e) echo "<li>" . h($e) . "</li>";
    echo "</ul></div><a class='btn abu' href='javascript:history.back()'>← Kembali ke form</a></div></div>";
    exit;
}

$id        = (int)($_POST["id"] ?? 0);
$nama      = trim($_POST["nama_penyedia"] ?? "");
$idKat     = (int)($_POST["id_kategori"] ?? 0);
$alamat    = trim($_POST["alamat"] ?? "");
$latStr    = str_replace(",", ".", trim($_POST["latitude"] ?? ""));
$lngStr    = str_replace(",", ".", trim($_POST["longitude"] ?? ""));
$jamBuka   = $_POST["jam_buka"] ?? "";
$jamTutup  = $_POST["jam_tutup"] ?? "";
$telp      = trim($_POST["no_telepon"] ?? "");

$errors = [];
if ($nama === "") $errors[] = "Nama penyedia wajib diisi.";
if ($alamat === "") $errors[] = "Alamat wajib diisi.";
if ($idKat <= 0) $errors[] = "Kategori wajib dipilih.";
if (!is_numeric($latStr) || $latStr < -90 || $latStr > 90) $errors[] = "Latitude harus angka antara -90 dan 90.";
if (!is_numeric($lngStr) || $lngStr < -180 || $lngStr > 180) $errors[] = "Longitude harus angka antara -180 dan 180.";
if (!preg_match('/^\d{2}:\d{2}$/', $jamBuka) || !preg_match('/^\d{2}:\d{2}$/', $jamTutup)) $errors[] = "Format jam tidak valid.";

// Menu: ambil baris yang terisi, minimal 3
$menu = [];
$namaMenu = $_POST["menu_nama"] ?? [];
$hargaMenu = $_POST["menu_harga"] ?? [];
foreach ($namaMenu as $i => $nm) {
    $nm = trim($nm);
    $hg = $hargaMenu[$i] ?? "";
    if ($nm === "" && $hg === "") continue;
    if ($nm === "" || !is_numeric($hg) || $hg < 0) { $errors[] = "Menu ke-" . ($i + 1) . " tidak lengkap atau harga tidak valid."; continue; }
    $menu[] = [$nm, (float)$hg];
}
if (count($menu) < 3 && !$errors) $errors[] = "Minimal harus ada 3 menu beserta harganya.";

// Foto (opsional)
$fotoBaru = null;
if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $f = $_FILES["foto"];
    $ext = strtolower(pathinfo($f["name"], PATHINFO_EXTENSION));
    if ($f["error"] !== UPLOAD_ERR_OK) $errors[] = "Upload foto gagal.";
    elseif (!in_array($ext, ["jpg", "jpeg", "png", "webp"], true)) $errors[] = "Foto harus berformat jpg, png, atau webp.";
    elseif ($f["size"] > 2 * 1024 * 1024) $errors[] = "Ukuran foto maksimal 2 MB.";
    elseif (@getimagesize($f["tmp_name"]) === false) $errors[] = "File foto bukan gambar yang valid.";
    else $fotoBaru = uniqid("foto_", true) . "." . $ext;
}
if ($errors) gagal($errors);

$lat = (float)$latStr; $lng = (float)$lngStr;
$dirFoto = __DIR__ . "/../foto/";
if (!is_dir($dirFoto)) mkdir($dirFoto, 0777, true);

$conn->begin_transaction();
try {
    if ($id) {
        $fotoLama = $conn->query("SELECT foto FROM penyedia_makanan WHERE id_penyedia = " . $id)->fetch_row()[0] ?? "";
        $foto = $fotoBaru ?? $fotoLama;
        $st = $conn->prepare("UPDATE penyedia_makanan SET id_kategori=?, nama_penyedia=?, alamat=?, latitude=?, longitude=?, jam_buka=?, jam_tutup=?, no_telepon=?, foto=? WHERE id_penyedia=?");
        $st->bind_param("issddssssi", $idKat, $nama, $alamat, $lat, $lng, $jamBuka, $jamTutup, $telp, $foto, $id);
        $st->execute();
        $conn->query("DELETE FROM menu_makanan WHERE id_penyedia = " . $id);
    } else {
        $foto = $fotoBaru ?? "";
        $st = $conn->prepare("INSERT INTO penyedia_makanan (id_kategori, nama_penyedia, alamat, latitude, longitude, jam_buka, jam_tutup, no_telepon, foto) VALUES (?,?,?,?,?,?,?,?,?)");
        $st->bind_param("issddssss", $idKat, $nama, $alamat, $lat, $lng, $jamBuka, $jamTutup, $telp, $foto);
        $st->execute();
        $id = $conn->insert_id;
    }

    $sm = $conn->prepare("INSERT INTO menu_makanan (id_penyedia, nama_menu, harga) VALUES (?,?,?)");
    foreach ($menu as [$nm, $hg]) {
        $sm->bind_param("isd", $id, $nm, $hg);
        $sm->execute();
    }
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    gagal(["Terjadi kesalahan database: " . $e->getMessage()]);
}

// Simpan file foto setelah database sukses; hapus foto lama jika diganti
if ($fotoBaru) {
    move_uploaded_file($_FILES["foto"]["tmp_name"], $dirFoto . $fotoBaru);
    if (!empty($fotoLama) && is_file($dirFoto . basename($fotoLama))) @unlink($dirFoto . basename($fotoLama));
}
header("Location: index.php?ok=" . urlencode("Data berhasil disimpan."));
