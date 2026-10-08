

<?php
$host = "localhost";
$user = "rdme2495_sipintar_db";
$pass = "portaldata2026";
$db   = "rdme2495_sipintar_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die(json_encode(["success" => false, "message" => "Koneksi database gagal!"]));
}
?>