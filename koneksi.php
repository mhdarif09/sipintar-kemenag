<?php

$host = "127.0.0.1";
$user = "sipintar_local";
$pass = "PasswordLokalBaru123!";
$db   = "sipintar_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die(json_encode([
        "success" => false,
        "message" => "Koneksi database gagal!"
    ]));
}

mysqli_set_charset($conn, "utf8mb4");

?>
