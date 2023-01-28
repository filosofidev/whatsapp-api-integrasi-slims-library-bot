<?php
$APIKEY = '0813';
$dbjoin = 'no';
$nomoradmin = "6281327663511";
$web = "http://localhost/elibrary";
date_default_timezone_set('Asia/Jakarta');
// Konfigurasi Database
if ($dbjoin == 'no'){ //Konfigurasi jika Server Terpisah
    $db_server   = "localhost";
    $db_username = "root";
    $db_password = "smansa";
    $db_database = "whatsapp";
    $db = mysqli_connect($db_server,$db_username,$db_password,$db_database);
    // Konfigurasi Database
    $slims_server   = "localhost";
    $slims_username = "root";
    $slims_password = "smansa";
    $slims_database = "katalog";
    $slims = mysqli_connect($slims_server,$slims_username,$slims_password,$slims_database);
}
else if ($dbjoin == 'yes'){ //Konfigurasi jika Server jadi satu
    $slims_server   = "localhost";
    $slims_username = "root";
    $slims_password = "smansa";
    $slims_database = "katalog";
    $slims = mysqli_connect($slims_server,$slims_username,$slims_password,$slims_database);
}


?>
