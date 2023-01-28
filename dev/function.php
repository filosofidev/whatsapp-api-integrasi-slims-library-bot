<?php
function menu(&$jawab,&$jawab1,$member){
  if ($member == "member"){
  $jawab = "
*= PERPUSTAKAAN SMAN 1 CEPU =*
_By Tangguh Filosofi Ugra N, S.T._
  
*=== MENU MEMBER ===*
1. Biodata
2. Peminjaman
3. Denda Peminjaman
4. Sejarah Peminjaman

*=== MENU PENCARIAN BUKU ===*
(a) Melakukan Pencarian berdasarkan Judul Buku
(b) Melakukan Pencarian berdasarkan Pengarang
(c) Melakukan Pencarian berdasarkan Topik
";

$jawab1 = "_Ketik *Angka / huruf* di dalam kurung untuk menjalankan perintah_
_Ketik *FAQ* untuk melihat pertanyaan yang sering diajukan_";
} else if ($member == "non-member"){
  $jawab = "*Menu yang bisa Anda gunakan*
(a) Melakukan Pencarian berdasarkan Judul Buku
(b) Melakukan Pencarian berdasarkan Pengarang
(c) Melakukan Pencarian berdasarkan Topik
";

$jawab1 = "_Ketik *Angka / huruf* di dalam kurung untuk menjalankan perintah_
_Ketik *FAQ* untuk melihat pertanyaan yang sering diajukan_";
} else if ($member == "admin"){
  $jawab = "
*= PERPUSTAKAAN SMAN 1 CEPU =*
_By Tangguh Filosofi Ugra N, S.T._
  
*=== MENU ADMIN ===*
10. Biodata member
20. Peminjaman member
30. Denda Peminjaman member
40. History Peminjaman member
50. Summary peminjaman

*=== MENU MEMBER ===*
1. Biodata
2. Peminjaman
3. Denda Peminjaman
4. Sejarah Peminjaman

*=== MENU PENCARIAN BUKU ===*
(a) Melakukan Pencarian berdasarkan Judul Buku
(b) Melakukan Pencarian berdasarkan Pengarang
(c) Melakukan Pencarian berdasarkan Topik
";

$jawab1 = "_Ketik *Angka / huruf* di dalam kurung untuk menjalankan perintah_
_Ketik *FAQ* untuk melihat pertanyaan yang sering diajukan_";
}
}

function faq_menu($sender,$db,&$jawab,&$jawab1,$member_id){
$query = "SELECT * from wa_faq where used='yes' order by id asc";
$hasil = mysqli_query($db, $query);
if(mysqli_num_rows($hasil) > 0 ){
$jawab = "*===F A Q===*
*Frequently Asked Questions:*";
while($x = mysqli_fetch_array($hasil)){
$jawab = $jawab."
". $x['id'].". ".$x['question'];
}
}else {}
$jawab1 = "_Ketik *Angka / huruf* di dalam kurung untuk menjalankan perintah_
_ketik *menu* untuk ke menu awal_";

}

function faq_answer($no,$db,&$jawab,&$jawab1,$member_id){
$query = "SELECT * from wa_faq where id = '$no' and used='yes' order by id asc ";
$hasil = mysqli_query($db, $query);
if(mysqli_num_rows($hasil) > 0 ){
$jawab = "*===F A Q===*
*Frequently Asked Questions:*
";
while($x = mysqli_fetch_array($hasil)){
$question = $x['question'];
$answer = $x['answer'];
$jawab = $jawab."
*PERTANYAAN :*
$question

*JAWABAN :*
$answer
";
}
}else {$jawab = "_maaf tidak tersedia pilihan $no ._";}
$jawab1 = "_ketik *menu* untuk ke menu awal_";
}

function caridenda($sender,$slims,&$jawab,&$jawab1,$member_id){
  $query = "SELECT a.loan_id, a.item_code, a.loan_date, a.due_date, b.biblio_id, b.title FROM loan AS a
  LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
  WHERE a.member_id = '$member_id' AND a.is_return = '0'";
  $hasil = mysqli_query($slims, $query);
  $totaldenda=0;  $jmlbuku =0;
  $jumlah = mysqli_num_rows($hasil);
    if(mysqli_num_rows($hasil) > 0 ){
      $jawab = "*Denda Keterlambatan buku dengan Member ID $member_id* :
*====================================*";
    while($x = mysqli_fetch_array($hasil)){
      $id_pinjam = $x['loan_id'];$item_code = $x['item_code'];
      $tgl_pinjam = $x['loan_date'];$batas_pinjam = $x['due_date'];
      $biblio_id = $x['biblio_id'];$title = $x['title'];
  $query = "SELECT a.due_date AS tgl_denda, CURDATE() AS tgl_skg,
  datediff(current_date(), due_date)  AS jumlah_hari,
  a.loan_rules_id,  a.item_code, a.member_id,
  1 AS holiday,  c.fine_each_day as denda
  FROM loan AS a
  LEFT join member AS b ON a.member_id = b.member_id
  LEFT JOIN mst_loan_rules AS c ON c.member_type_id = b.member_type_id
  WHERE loan_id='$id_pinjam';";
  $hasil1 = mysqli_query($slims, $query);
  if(mysqli_num_rows($hasil1) > 0 ){
  while($y = mysqli_fetch_array($hasil1)){
    $jmlbuku++;
    $date1 = $y['tgl_denda'];
    $date2 = $y['tgl_skg'];
    $jumlah_hari = $y['jumlah_hari'];
    $denda = $y['denda'];
    $pecahTgl1 = explode("-", $date1);// memecah bagian-bagian dari tanggal $date1
    $thn1 = $pecahTgl1[0]; $bln1 = $pecahTgl1[1]; $tgl1 = $pecahTgl1[2];
    $i = 0; $sum = 0;
    if ($date2 > $date1){
    do
    { // Pencarian Hari Libur //0minggu,1senin,2selasa,...,6sabtu
       $tanggal = date("Y-m-d", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1));
       if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 0) //minggu
       {$sum++;}
       if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 6)//sabtu
       {$sum++;}
       $i++;
    }
    while ($tanggal != $date2);
    $haridenda = ($jumlah_hari - $sum - 1 );
  } else {
    $haridenda = 0;
  }
    $jmldenda = $haridenda * $denda;
    $totaldenda = $totaldenda + $jmldenda;
    $jawab = $jawab . "
id Pinjam = $id_pinjam
id Buku = $item_code
Judul Buku = $title
Tanggal Pinjam = $tgl_pinjam
Batas Pinjam = $batas_pinjam
Terlambat = $haridenda Hari
Denda = $jmldenda
----------------------------";
  }  }    }
$jawab = $jawab."
*====================================*
*JUMLAH BUKU DIPINJAM = $jmlbuku*
*TOTAL DENDA = $totaldenda*";
  }else {
  $jawab = "Maaf, Member ID $member_id tidak memiliki peminjaman buku Aktif";
  }
  $jawab1 = "_Silahkan ketik kembali *ID PEMINJAM* yang diinginkan atau_
_ketik *MENU* untuk kembali ke menu awal_";
}

// MEMBER MENU --> CARI DENDA
function denda($sender,$slims,&$jawab,$member_id){
  $query = "SELECT a.loan_id, a.item_code, a.loan_date, a.due_date, b.biblio_id, b.title FROM loan AS a
  LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
  WHERE a.member_id = '$member_id' AND a.is_return = '0'";
  $hasil = mysqli_query($slims, $query);
  $totaldenda=0;
  $jmlbuku =0;
  $jumlah = mysqli_num_rows($hasil);
    if(mysqli_num_rows($hasil) > 0 ){
      $jawab = "*Denda Keterlambatan*
*====================================*";
    while($x = mysqli_fetch_array($hasil)){
      $id_pinjam = $x['loan_id'];$item_code = $x['item_code'];
      $tgl_pinjam = $x['loan_date'];$batas_pinjam = $x['due_date'];
      $biblio_id = $x['biblio_id'];$title = $x['title'];
  $query = "SELECT a.due_date AS tgl_denda, CURDATE() AS tgl_skg,
  datediff(current_date(), due_date)  AS jumlah_hari,  a.loan_rules_id,
  a.item_code, a.member_id,  1 AS holiday, c.fine_each_day as denda
  FROM loan AS a
  LEFT join member AS b ON a.member_id = b.member_id
  LEFT JOIN mst_loan_rules AS c ON c.member_type_id = b.member_type_id
  WHERE loan_id='$id_pinjam';";
  $hasil1 = mysqli_query($slims, $query);
  if(mysqli_num_rows($hasil1) > 0 ){
  while($y = mysqli_fetch_array($hasil1)){
    $jmlbuku++;
    $date1 = $y['tgl_denda'];
    $date2 = $y['tgl_skg'];
    $jumlah_hari = $y['jumlah_hari'];
    $denda = $y['denda'];
    $pecahTgl1 = explode("-", $date1);
    $thn1 = $pecahTgl1[0]; $bln1 = $pecahTgl1[1]; $tgl1 = $pecahTgl1[2];
    $i = 0; $sum = 0;
    if ($date2 > $date1){
    do
    { // Pencarian Hari Libur //0minggu,1senin,2selasa,...,6sabtu
       $tanggal = date("Y-m-d", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1));
       // error_log("tanggal $tanggal !", 0);
       if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 0) //minggu
       {$sum++;}
       if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 6)//sabtu
       {$sum++;}
       $i++;
    }
    while ($tanggal != $date2);
    $haridenda = ($jumlah_hari - $sum - 1 );
  } else {
    $haridenda = 0;
  }
    $jmldenda = $haridenda * $denda;
    $totaldenda = $totaldenda + $jmldenda;
  $jawab = $jawab . "
  id Pinjam = $id_pinjam
  id Buku = $item_code
  Judul Buku = $title
  Tanggal Pinjam = $tgl_pinjam
  Batas Pinjam = $batas_pinjam
  Terlambat = $haridenda Hari
  *Denda = $jmldenda*
----------------------------";
  }  }   }
$jawab = $jawab."
*====================================*
*JUMLAH BUKU DIPINJAM = $jmlbuku*
*TOTAL DENDA = $totaldenda*";
  }else {
  $jawab = "Maaf, Anda tidak memiliki peminjaman buku Aktif";
  }
}

// MEMBER MENU --> CEK PEMINJAMAN BUKU AKTIF
function cekpinjam($sender,$slims,&$jawab,$member_id){
  $query = "SELECT a.loan_id, a.item_code, a.loan_date, a.due_date, b.biblio_id, b.title FROM loan AS a
LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
WHERE a.member_id = '$member_id' AND a.is_return = '0'";
  $hasil = mysqli_query($slims, $query);
  $jumlah = mysqli_num_rows($hasil);
    if(mysqli_num_rows($hasil) > 0 ){
      $jawab = "*Buku yang anda pinjam saat ini* :
*====================================*";
    while($x = mysqli_fetch_array($hasil)){
      $id_pinjam = $x['loan_id'];$item_code = $x['item_code'];
      $tgl_pinjam = $x['loan_date'];$batas_pinjam = $x['due_date'];
      $biblio_id = $x['biblio_id'];$title = $x['title'];
      $jawab = $jawab . "
ID Pinjam = $id_pinjam
ID Buku = $item_code
Judul Buku = $title
Tanggal Pinjam = $tgl_pinjam
Batas Pinjam = $batas_pinjam
----------------------------";
    }
  }else {
  $jawab = "Maaf, Anda tidak memiliki peminjaman buku Aktif";
}
}

// ADMIN MENU --> Cek Summary Pinjam
function sumpinjam($sender,$slims,&$jawab,$member_id){
$query = "SELECT count(DISTINCT a.member_id) AS peminjam,
COUNT(a.item_code) AS Buku FROM loan AS a WHERE a.is_return = 0;";
$hasil = mysqli_query($slims, $query);
$jumlah = mysqli_num_rows($hasil);
if(mysqli_num_rows($hasil) > 0 ){
while($x = mysqli_fetch_array($hasil)){
$aktifpinjam = $x['peminjam'];$aktifbuku = $x['Buku'];
}
}else {}
$query = "SELECT count(DISTINCT a.member_id) AS peminjam,
COUNT(a.item_code) AS Buku FROM loan AS a WHERE a.is_return = 1  ;";
$hasil = mysqli_query($slims, $query);
$jumlah = mysqli_num_rows($hasil);
if(mysqli_num_rows($hasil) > 0 ){
while($x = mysqli_fetch_array($hasil)){
$pasifpinjam = $x['peminjam'];$pasifbuku = $x['Buku'];
}
}else {}
$query = "SELECT count(DISTINCT a.member_id) AS peminjam,
COUNT(a.item_code) AS Buku FROM loan AS a ;  ";
$hasil = mysqli_query($slims, $query);
$jumlah = mysqli_num_rows($hasil);
if(mysqli_num_rows($hasil) > 0 ){
while($x = mysqli_fetch_array($hasil)){
$totalpinjam = $x['peminjam'];$totalbuku = $x['Buku'];
}
}else {}

$jawab = "*Summary Peminjaman Perpustakaan SMAN 1 CEPU*
*====================================*;
*Peminjaman Aktif*
Jumlah Peminjam  : *$aktifpinjam member*
Buku yang dipinjam : *$aktifbuku Buku*

*Peminjaman Kembali*
Jumlah Peminjam  : *$pasifpinjam member*
Buku yang dipinjam : *$pasifbuku Buku*

*Peminjaman Total*
Jumlah Peminjam  : *$totalpinjam member*
Buku yang dipinjam : *$totalbuku Buku*
";
}//end Function cek pinjam



// MEMBER MENU --> History Pinjam
function historypinjam($sender,$slims,&$jawab,$member_id){
  $query = "SELECT a.loan_id, a.item_code, a.loan_date, a.due_date, b.biblio_id, b.title FROM loan AS a
  LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
  WHERE a.member_id = '$member_id' ";
  $hasil = mysqli_query($slims, $query);
  $jumlah = mysqli_num_rows($hasil);
    if(mysqli_num_rows($hasil) > 0 ){
      $jawab = "*Sejarah Peminjaman Buku Anda* :
*====================================*";
    while($x = mysqli_fetch_array($hasil)){
      $id_pinjam = $x['loan_id'];$item_code = $x['item_code'];
      $tgl_pinjam = $x['loan_date'];$batas_pinjam = $x['due_date'];
      $biblio_id = $x['biblio_id'];$title = $x['title'];
      $jawab = $jawab . "
id Buku = $item_code
Judul Buku = $title
Tanggal Pinjam = $tgl_pinjam
----------------------------";
    }
  }else {
  $jawab = "Maaf, Anda tidak memiliki peminjaman buku Aktif";
  }
}

//ADMIN MENU --> chp = Cek History Pinjam
function chp($sender,$slims,&$jawab,&$jawab1,$key1){
  $query = "SELECT a.loan_id, a.item_code, a.loan_date, a.due_date, b.biblio_id, b.title FROM loan AS a
  LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
  WHERE a.member_id = '$key1' ";
  $hasil = mysqli_query($slims, $query);
  $jumlah = mysqli_num_rows($hasil);
    if(mysqli_num_rows($hasil) > 0 ){
      $jawab = "*Sejarah Buku yang pernah dipinjam oleh Member ID $key1* :
*====================================*;";
    while($x = mysqli_fetch_array($hasil)){
      $id_pinjam = $x['loan_id'];$item_code = $x['item_code'];
      $tgl_pinjam = $x['loan_date'];$batas_pinjam = $x['due_date'];
      $biblio_id = $x['biblio_id'];$title = $x['title'];
      $jawab = $jawab . "
id Buku = $item_code
Judul Buku = $title
Tanggal Pinjam = $tgl_pinjam
----------------------------";
    }
  }else {
  $jawab = "Maaf,Member ID *$key1*  tidak memiliki peminjaman buku Aktif";
  }
  $jawab1 = "_Silahkan ketik kembali *ID PEMINJAM* yang diinginkan atau_
_ketik *MENU* untuk kembali ke menu awal_";
}//end Function historypinjam

//
// Member MENU --> Cek Biodata Pustakawan
function biodata($sender,$slims,&$jawab,$member_id){
$query = "SELECT * FROM member WHERE member_id = '$member_id' limit 1";
$hasil = mysqli_query($slims, $query);
$jumlah = mysqli_num_rows($hasil);
 if(mysqli_num_rows($hasil) > 0 ){
   $jawab = "*Data anda pada Sistem* :
*====================================*";
while($x = mysqli_fetch_array($hasil)){
$key[0] = "ok";
$nama = $x['member_name'];
$prodi = $x['inst_name'];
$tanggal_lahir = $x['birth_date'];
$alamat = $x['member_address'];
$member_id = $x['member_id'];
$jawab = "
*=== Biodata Pada Sistem ===*

ID Anggota    : $member_id
Nama             : $nama
Kelas              : $prodi
Tanggal Lahir  : $tanggal_lahir
Alamat            : $alamat
";
}
} else {$jawab = "Maaf anda Terdaftar sebagai *$member_id*
 ketik *menu* untuk menampilkan menu";}

}

//
// Admin MENU -->  CBP = Cek Biodata Pustakawan
function cbp($sender,$slims,$key1,&$jawab,&$jawab1){
$query = "SELECT * FROM member WHERE member_id = '$key1' limit 1";
$hasil = mysqli_query($slims, $query);
$jumlah = mysqli_num_rows($hasil);
 if(mysqli_num_rows($hasil) > 0 ){
   $jawab = "*Biodata pada Sistem* :
*====================================*;";
while($x = mysqli_fetch_array($hasil)){
$key[0] = "ok";
$nama = $x['member_name'];
$prodi = $x['inst_name'];
$tanggal_lahir = $x['birth_date'];
$alamat = $x['member_address'];
$member_id = $x['member_id'];
$jawab = "
Member ID    : $member_id
Nama             : $nama
Kelas              : $prodi
Tanggal Lahir  : $tanggal_lahir
Alamat             : $alamat
";
}
} else {$jawab = "Tidak ada Pustakawan dengan member ID *$key1*";
}
$jawab1 = "_Silahkan ketik kembali *ID PEMINJAM* yang diinginkan atau_
_ketik *MENU* untuk kembali ke menu awal_";
}


//
// Admin --> Cek Status Pinjam
function csp($sender,$slims,$key1,&$jawab,&$jawab1){
$query = "
SELECT a.loan_id, a.item_code, a.member_id, a.loan_date, a.due_date, a.is_return, b.title, d.member_name FROM loan AS a
LEFT JOIN loan_history AS b ON a.loan_id = b.loan_id
LEFT JOIN biblio AS c ON b.biblio_id = c.biblio_id
LEFT JOIN member AS d ON d.member_id = a.member_id
WHERE a.member_id = '$key1' AND a.is_return = 0";
$hasil = mysqli_query($slims, $query) or die(mysqli_error($slims));

  if(mysqli_num_rows($hasil) > 0 ){
    $jawab = "Status Peminjaman BUKU Member ID *$key1*
*====================================*";
    while($x = mysqli_fetch_array($hasil)){
          $jawab = $jawab."
Judul : ". $x['title']."
Item Code : ". $x['item_code']."
tanggal Pinjam : ".$x['loan_date']."
tanggal Akhir Pinjam : ".$x['due_date']."
----------------------------------";
}

} else {$jawab = "Tidak ada peminjaman untuk ID $key1";}
$jawab1 = "_Silahkan ketik kembali *ID PEMINJAM* yang diinginkan atau_
_ketik *MENU* untuk kembali ke menu awal_";
}


//
// UMUM --> PENCARIAN BUKU by Judul
function cb($sender,$slims,$key1,$key2,$key3,$key4,$nomoradmin,&$jawab,&$jawab1,&$jawab2){
$query = "SELECT a.biblio_id,a.title, c.author_name, a.call_number
         FROM biblio AS a
         LEFT JOIN biblio_author AS b ON a.biblio_id = b.biblio_id
         LEFT JOIN mst_author AS c on b.author_id = c.author_id
         WHERE title LIKE '%$key1%$key2%' or title LIKE '%$key2%$key1%'
         limit 310";
$hasil = mysqli_query($slims, $query) or die(mysqli_error($slims));
$jumlah = mysqli_num_rows($hasil);
if (isset($key1)){$key = $key1;}
if (isset($key2)){$key = $key." ".$key2;}
if (isset($key3)){$key = $key." ".$key3;}
if (isset($key4)){$key = $key." ".$key4;}

// $key = $key1. " ". $key2 . " " . $key3;
$jawab = "*Hasil Pencarian Buku dengan Judul =* $key";
$jawab1 ="";
 if(mysqli_num_rows($hasil) > 0 ){
   $jawab1 = "*=== DAFTAR BUKU ===*";
   if($jumlah > 300 ){
     $jawab = $jawab."terdapat *lebih dari 300 buku* ditemukan";
} else {
$jawab = $jawab."terdapat $jumlah buku ditemukan";
}
   while($x = mysqli_fetch_array($hasil)){
         $judul = $x['title'];
         $idbuku = $x['biblio_id'];
         $judul = preg_replace("/[^a-z A-Z0-9 , .]/", "", $judul);
         $jawab1 = $jawab1."

Judul : ". $judul."
karya : ". $x['author_name']."
Detail : ketik ==>  cd $idbuku
*------------------------------------*";
}  } else { $jawab = $jawab. "
Buku dengan *Judul $key1 $key2 tidak ditemukan*.
ketik *_menu_* untuk kembali ke menu utama";  }
$jawab2 = "_Silahkan kembali ketik *Judul Buku* yang diinginkan atau_
_ketik *MENU* untuk kembali ke awal_";
}


//
// UMUM --> PENCARIAN BUKU by subject / Topic
function cs($sender,$slims,$key1,$key2,$key3,$key4,$key5,$nomoradmin,&$jawab,&$jawab1,&$jawab2){
$query = "SELECT a.title, a.author, a.biblio_id, a.topic FROM search_biblio AS a
          LEFT JOIN biblio AS b ON a.biblio_id = b.biblio_id
         WHERE topic LIKE '%$key1%$key2%' or topic LIKE '%$key2%$key1%'
         limit 310";
$hasil = mysqli_query($slims, $query) or die(mysqli_error($slims));
$jumlah = mysqli_num_rows($hasil);
$key = $key1. " ". $key2 . " " . $key3;
$jawab = "*Hasil Pencarian dengan Topik =* $key1 $key2 ";
 if(mysqli_num_rows($hasil) > 0 ){
   if($jumlah > 300 ){
     $jawab = $jawab."terdapat *lebih dari 300 buku* ditemukan";
} else {
  $jawab = $jawab."terdapat $jumlah buku ditemukan";
}
$jawab1 = "*=== DAFTAR BUKU ===*";
   while($x = mysqli_fetch_array($hasil)){
         $judul = $x['title'];
         $author = $x['author'];
         $topic = $x['topic'];
         $judul = preg_replace("/[^a-z A-Z0-9 , .]/", "", $judul);
         $author = preg_replace("/[^a-z A-Z0-9 , .]/", "", $author);
         $jawab1 = $jawab1."
		 
Judul : ". $judul."
Karya : ". $author."
Topik : ". $topic."
Detail : ketik ==> cd ".$x['biblio_id']."
*------------------------------------*";
 }
} else {
  $jawab1 = $jawab1. "
  buku dengan topik $key1 $key2 tidak ditemukan dalam pencarian";
}
$jawab2 = "_Silahkan kembali ketik *Judul Topik* yang diinginkan atau_
_ketik *MENU* untuk kembali ke awal_";
}



//
// Umum --> CP = Cari Pengarang, PENCARIAN Pengarang
function cp($sender,$slims,$key1,$key2,$key3,$key4,$nomoradmin,&$jawab,&$jawab1,&$jawab2){
$query = "SELECT * FROM search_biblio AS a
          LEFT JOIN biblio AS b ON a.biblio_id = b.biblio_id
          WHERE a.author like '%$key2%$key1%' or a.author like '%$key1%$key2%' limit 300";

          if (isset($key1)){$key = $key1;}
          if (isset($key2)){$key = $key." ".$key2;}
          if (isset($key3)){$key = $key." ".$key3;}
          if (isset($key4)){$key = $key." ".$key4;}


$hasil = mysqli_query($slims, $query) or die(mysqli_error($slims));
$jumlah = mysqli_num_rows($hasil);
if($jumlah > 299) {$jawab = "*Hasil Pencarian Nama Pengarang =* $key
terdapat *Lebih dari $jumlah buku* ditemukan";}
else if($jumlah > 0 ){$jawab = "*Hasil Pencarian Nama Pengarang =* $key
terdapat $jumlah buku ditemukan";}

$jawab1 = "*=== DAFTAR BUKU ===*";
  if(mysqli_num_rows($hasil) > 0 ){
    while($x = mysqli_fetch_array($hasil)){
          $judul = $x['title'];
          $judul = preg_replace("/[^a-z A-Z0-9 , .]/", "", $judul);
          $jawab1 = $jawab1."
		  
Judul : ". $judul."
Karya : ". $x['author']."
Detail : ketik ==> cd ".$x['biblio_id']."
*------------------------------------*";
}
}else {
  $jawab1 = $jawab1."hasil pencarian Buku dengan Pengarang
  ketik *menu* untuk menampilkan menu";
}

$jawab2 = "_Silahkan kembali ketik *Nama Pengarang* yang diinginkan atau_
_ketik *MENU* untuk kembali ke awal_";}

// GENERAL MENU --> CD = CEK DETAIL PENCARIAN DETAIL BUKU
function cd($sender,$slims,$key1,$key2,$web,&$jawab){
$query = "SELECT a.biblio_id, a.title, e.topic, a.isbn_issn, a.classification, a.notes,
          a.collation, a.spec_detail_info,c.author_name , d.publisher_id,d.publisher_name, a.image,
          a.publish_year, a.call_number
          FROM biblio AS a
          LEFT JOIN biblio_author AS b ON a.biblio_id = b.biblio_id
          LEFT JOIN mst_author AS c on b.author_id = c.author_id
          LEFT JOIN mst_publisher AS d ON a.publisher_id = d.publisher_id
          LEFT JOIN search_biblio AS e ON a.biblio_id = e.biblio_id
          WHERE a.biblio_id = '$key2' limit 10";
$jawab = "ID : $key2";
$hasil = mysqli_query($slims, $query) or die(mysqli_error($slims));
  if(mysqli_num_rows($hasil) > 0 ){
    while($x = mysqli_fetch_array($hasil)){
$judul = $x['title'];
$judul = preg_replace("/[^a-z A-Z0-9 , .]/", "", $judul);
$jawab = $jawab."
*==========DETAIL==========*
Judul : ". $judul."
Pengarang : ". $x['author_name']."
Penerbit : ". $x['publisher_name']."
Tahun Terbit : ". $x['publish_year']."
Nomor Panggil : ". $x['call_number']."
Deskripsi Fisik : ". $x['collation']."
ISBN / ISSN : ". $x['isbn_issn']."
Website : $web/index.php?p=show_detail&id=$key2

*=========Ketersediaan========*";

                  $item = "SELECT * FROM item g WHERE g.biblio_id = '$key2'";
                  $hasilitem = mysqli_query($slims, $item) or die(mysqli_error($slims));
                  if(mysqli_num_rows($hasilitem) > 0 ){
                    while($y = mysqli_fetch_array($hasilitem)){
                      $itemcode = $y['item_code'];

                                  $status = "
                                  SELECT z.item_code, z.item_status_id, z.biblio_id, y.due_date, x.*  FROM item z
LEFT JOIN loan y ON y.item_code = z.item_code
LEFT JOIN mst_item_status x ON z.item_status_id = x.item_status_id
WHERE z.item_code = '$itemcode' and y.is_return = 0";

                                  $hasilstatus = mysqli_query($slims, $status) or die(mysqli_error($slims));
                                  if(mysqli_num_rows($hasilstatus) > 0 ){
                                    while($z = mysqli_fetch_array($hasilstatus)){
                                      $jatuhtempo = $z['due_date'];
$jawab = $jawab."
Item Code : $itemcode | Dipinjam (Jatuh Tempo $jatuhtempo) ";
}}else {
if ($y['item_status_id'] == "NL"){
$jawab = $jawab."
Item Code : $itemcode | *Tersedia dan tidak untuk dipinjamkan*";
} else {
$jawab = $jawab."
Item Code : $itemcode | *Tersedia*";
}}

} }else {
  $jawab = "Detail buku dengan ID $key2 tidak ditemukan.
  silahkan ketik *menu* untuk menampilkan Menu tersedia ";
} $jawab = $jawab."
"; //Jeda Detail Buku
}}}


?>
