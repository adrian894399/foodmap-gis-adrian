<?php
require "../koneksi.php";
if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: index.php"); exit; }

$id = (int)($_POST["id"] ?? 0);
if ($id > 0) {
    $st = $conn->prepare("SELECT foto FROM penyedia_makanan WHERE id_penyedia = ?");
    $st->bind_param("i", $id);
    $st->execute();
    $foto = $st->get_result()->fetch_row()[0] ?? "";

    // menu ikut terhapus otomatis (ON DELETE CASCADE)
    $del = $conn->prepare("DELETE FROM penyedia_makanan WHERE id_penyedia = ?");
    $del->bind_param("i", $id);
    $del->execute();

    $path = __DIR__ . "/../foto/" . basename($foto);
    if ($foto !== "" && is_file($path)) @unlink($path);
}
header("Location: index.php?ok=" . urlencode("Data berhasil dihapus."));
