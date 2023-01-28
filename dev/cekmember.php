<?php
$query = "SELECT * FROM wa_admin WHERE phone_number like '%$sender' limit 1";
$hasil = mysqli_query($db, $query);
$jumlah = mysqli_num_rows($hasil);
   if(mysqli_num_rows($hasil) > 0 ){ //Find USER ADMIN
        while($x = mysqli_fetch_array($hasil)){
        $key[0] = "ok";
        $nama = $x['name'];
        $member_id = "admin";

$jawab = "*Hai Sobat Perpustakaan...*

Selamat Datang *$nama*
di sistem Layanan Perpustakaan SMAN 1 CEPU.

Nomor Anda Terdaftar sebagai *ADMIN Perpustakaan*

untuk melihat menu yang tersedia. silahkan ketik *menu*

";
        $update = "INSERT INTO wa_data_answer (sender, p_last,nama,id_member)
        VALUES ('$sender', 'welcome','$nama','admin')";
        mysqli_query($db, $update);
        }
} else {
//
$query = "SELECT * FROM member WHERE member_phone like '%$sender' limit 1";
 $hasil = mysqli_query($slims, $query);
 $jumlah = mysqli_num_rows($hasil);
   if(mysqli_num_rows($hasil) > 0 ){
        while($x = mysqli_fetch_array($hasil)){
        $key[0] = "ok";
        $nama = $x['member_name'];
        $prodi = $x['inst_name'];
        $tanggal_lahir = $x['birth_date'];
        $alamat = $x['member_address'];
        $member_id = $x['member_id'];
$jawab = "_Hai Sobat Perpustakaan..._

Selamat Datang *$nama*
di sistem Layanan Perpustakaan SMAN 1 CEPU.

Nomor Anda Terdaftar sebagai *MEMBER PERPUSTAKAAN*

Untuk melihat menu yang tersedia silahkan ketik *menu*
";
        $update = "INSERT INTO wa_data_answer (sender, p_last,nama,prodi,tanggal_lahir,alamat,id_member)
        VALUES ('$sender', 'welcome','$nama','$prodi','$tanggal_lahir','$alamat','$member_id')";
        mysqli_query($db, $update);
        }

} else {
   $key[0] = "ok";
   $update = "INSERT INTO wa_data_answer (sender, p_last)
   VALUES ('$sender', 'welcome')";
   mysqli_query($db, $update);

   $jawab = "_Hai Sobat Perpustakaan..._
   
Selamat Datang di sistem Layanan Perpustakaan SMAN 1 CEPU.

Untuk melihat menu yang tersedia silahkan ketik *menu*

Info : Anda bisa menggunakan lebih banyak menu, jika Anda menjadi anggota dan nomor Anda terdaftar di database Perpustakaan SMAN 1 CEPU.
";
 }

}
 ?>
