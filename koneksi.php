<?php
// Koneksi ke database MySQL foodmap_gis
$host = "localhost";
$user = "root";     // default XAMPP
$pass = "";         // default XAMPP kosong
$db   = "foodmap_gis";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode(["error" => "Koneksi database gagal"]);
    exit;
}
