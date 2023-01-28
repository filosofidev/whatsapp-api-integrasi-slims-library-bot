<?php
$isipesan = preg_replace("/[^a-z A-Z0-9]/", "", $isipesan); //hanya menerima karakter a-z A-Z 0-9
$isipesan = strtolower($isipesan);                          //Ganti pesan ke ke huruf kecil

$key = explode(" ",$isipesan);
include "rule-isset.php"; //Explode $key1,key2,$key3, dst
include "dev/function.php";

$sender = substr($sender,2);
#curdate = Say Welcome message everyday
$query = "SELECT * FROM wa_data_answer WHERE sender like '%$sender' and STR_TO_DATE(begin , '%Y-%m-%d') = CURDATE() and ket is null limit 1";
$hasil = mysqli_query($db, $query);
$jumlah = mysqli_num_rows($hasil);
  if(mysqli_num_rows($hasil) > 0 ){
  while($x = mysqli_fetch_array($hasil)){
    if (!empty($x['id_member'])){
        if ($x['id_member']=="admin" ){
          $member = "admin";
        }else {$member = "member";}
    } else {$member = "non-member";}
    $last = $x['p_last'];
    $member_id = $x['id_member'];
    $nama = $x['nama'];
    $id = $x['id'];
    $begin = $x['begin'];
    $prodi = $x['prodi'];
    $tanggal_lahir = $x['tanggal_lahir'];
    $alamat = $x['alamat'];
    $last = $x['p_last'];
  }
}
else {include "dev/cekmember.php"; } //CekMember and Welcome Message

// CEK Last MESSAGE
if (!empty($id)){
  if ($key1 == "menu"){ //Show Menu
      $update = "UPDATE wa_data_answer SET p_last='welcome' WHERE id='$id'"; //Save Answer to wa_data_answer became to Session
      mysqli_query($db, $update);
      menu($jawab,$jawab1,$member);
  }else if ($last=="a") { //session A to Search Book by Title (look at Function and Menu)
  switch ($key['0']) {
      case 'cd'; //Cd = Check Detail if change to other word change menu too
      cd($sender,$slims,$key1,$key2,$web,$jawab);
      break;

      default;  // If not cd all message reply with find book
      cb($sender,$slims,$key1,$key2,$key3,$key4,$nomoradmin,$jawab,$jawab1,$jawab2);
      break;   }

  } else if($last=="b") { //session B to Search Book by Author (look at Function and Menu)
  switch ($key['0']) {
      case 'cd'; //Cd = Check Detail if change to other word change menu too
      cd($sender,$slims,$key1,$key2,$web,$jawab);
      break;

      default; // If not CD, all message reply with find book by author
      cp($sender,$slims,$key1,$key2,$key3,$key4,$nomoradmin,$jawab,$jawab1,$jawab2);
      break;  }
  } else if($last=="c") {
  switch ($key['0']) {
      case 'cd'; //Cd = Check Detail if change to other word change menu too
      cd($sender,$slims,$key1,$key2,$web,$jawab);
      break;

      default;  // If not CD, all message reply with find book by Topic
      cs($sender,$slims,$key1,$key2,$key3,$key4,$key5,$nomoradmin,$jawab,$jawab1,$jawab2);
      break;
  }}else if($last=="faq") { //session FAQ (SUBMENU)
        faq_answer($key1,$db,$jawab,$jawab1,$member_id);
  } else if($last=="10") { //Session Biodata Member
        cbp($sender,$slims,$key1,$jawab,$jawab1);
  } else if($last=="20") { //Session Peminjaman member
        csp($sender,$slims,$key1,$jawab,$jawab1);
  } else if($last=="30") { //Session Denda Peminjaman Member
        caridenda($sender,$slims,$jawab,$jawab1,$key1);
  } else if($last=="40") { //Session History Peminjaman Member
        chp($sender,$slims,$jawab,$jawab1,$key1);
  } else {
// =====================================================
$menunon = "Maaf menu hanya tersedia untuk member.
untuk melihat menu yang tersedia, silahkan ketik *menu*";
$menunonadmin = "Maaf menu hanya tersedia untuk Admin Perpustakaan.
untuk melihat menu yang tersedia, silahkan ketik *menu*";
// =====================================================

switch ($key['0']) {  //TOP MENU
case '1'; // Member Menu, Look At Menu
      if ($member == "member" || $member == "admin"){
        biodata($sender,$slims,$jawab,$member_id);
        $jawab = $jawab;
        } else {$jawab = $menunon;}
break;

case '2'; // Member Menu, Look At Menu
      if ($member == "member" || $member == "admin"){
      cekpinjam($sender,$slims,$jawab,$member_id);
      $jawab = $jawab;
      } else {$jawab = $menunon;}
break;

case '3'; // Member Menu, Look At Menu
      if ($member == "member" || $member == "admin"){
      denda($sender,$slims,$jawab,$member_id);
      } else {$jawab = $menunon;}
break;

case '4'; // Member Menu, Look At Menu
      if ($member == "member" || $member == "admin"){
      historypinjam($sender,$slims,$jawab,$member_id);
      $jawab = $jawab;
      } else {$jawab = $menunon;}
break;

case '10'; // Admin Menu, Look At Menu
if ($member == "admin"){
      $update = "UPDATE wa_data_answer SET p_last='10' WHERE id='$id'";
        mysqli_query($db, $update);
      $jawab = "*Masukan *ID Peminjam* untuk Biodata Peminjam*";
      } else {$jawab = $menunonadmin;}
break;

case '20'; // Admin Menu, Look At Menu
      if ($member == "admin"){
        $update = "UPDATE wa_data_answer SET p_last='20' WHERE id='$id'";
        mysqli_query($db, $update);
      $jawab = "*Masukan ID Peminjam untuk melihat peminjaman aktif*";
      } else {$jawab = $menunonadmin;}
break;

case '30'; // Admin Menu, Look At Menu
      if ($member == "admin"){
        $update = "UPDATE wa_data_answer SET p_last='30' WHERE id='$id'";
        mysqli_query($db, $update);
      $jawab = "*Masukan ID Peminjam untuk melihat Denda Peminjaman*";
      } else {$jawab = $menunonadmin;}
break;

case '40'; // Admin Menu, Look At Menu
      if ($member == "admin"){
        $update = "UPDATE wa_data_answer SET p_last='40' WHERE id='$id'";
        mysqli_query($db, $update);
      $jawab = "*Masukan ID Peminjam untuk melihat History Peminjaman*";
      } else {$jawab = $menunonadmin;}
break;

case '50'; // Admin Menu, Look At Menu
      if ($member == "admin"){
         sumpinjam($sender,$slims,$jawab,$key1);
      } else {$jawab = $menunonadmin;}
break;

case 'faq'; //From Topmenu go to submenu FAQ look at function
      $update = "UPDATE wa_data_answer SET p_last='faq' WHERE id='$id'";
      mysqli_query($db, $update);
      faq_menu($sender,$db,$jawab,$jawab1,$member_id);
break;

case 'a'; //General Menu to find a book by Title
      $update = "UPDATE wa_data_answer SET p_last='a' WHERE id='$id'";
      mysqli_query($db, $update);
      $jawab = "*Ketikkan _Judul Buku_ yang dinginkan...*";
break;

case 'b';  //General Menu to find a book by Author
      $update = "UPDATE wa_data_answer SET p_last='b' WHERE id='$id'";
      mysqli_query($db, $update);
      $jawab = "*Ketikkan _Nama Pengarang_ yang dinginkan...*";
break;

case 'c';   //General Menu to find a book by Topic
      $update = "UPDATE wa_data_answer SET p_last='c' WHERE id='$id'";
      mysqli_query($db, $update);
      $jawab = "*Ketikkan _Topik_ yang dinginkan...*";
break;

case 'info';  //Direct Answer, look at menu
$jawab = "*Info Ngopi Mazseh.. Wkakakakakakaka*";
break;

case 'bantuan';  //Direct Answer, look at menu
$jawab = "*Minta bantuan apa Mazseh, BLT, PKH, PIP?? Wkakakakkaa*";
break;


//SAMPLE DIRECT ANSWER, Remove simbol '//'
// case 'j'; //KeyWord
// $jawab = "";
// break;

case 'menu';
$update = "UPDATE wa_data_answer SET p_last='menu', ket = 'END' WHERE id = '$id';";
mysqli_query($db, $update);
$jawab = "*Ingin mencari buku dengan penerbit manakah?* $update";
break;

default;
$jawab = "Maaf kata kunci yang Anda masukkan salah/tidak terdaftar di database kami. ketik *menu* untuk menjalankan RAMANSA LIBRARY..";
break;
}
} //Close TOP MENU
}

?>
