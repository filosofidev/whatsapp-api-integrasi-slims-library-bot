<?php
include 'config.php';
$data = file_get_contents("php://input");
$data = utf8_encode($data);
$data = json_decode($data, true);

if (isset($data['senderMessage']))
{
    $app = "Autoreply";
    $isipesan = $data['senderMessage'];
	$isipesan = str_replace("'"," ",$isipesan);
    $paket = $data['receiveMessageAppId'];

    $sender = $data['senderName'];
    $sender = preg_replace("/[^0-9]/", "", $sender);

    $pesanin = $data['messageDateTime'];
    $dateapp = date("Y-m-d H:i:s", substr("$pesanin", 0, 10));
    $tanggal = date("Y-m-d H:i:s");
    $debugfile = "
  Waktu Asli : $pesanin
  Waktu masuk : $dateapp
  Waktu server : $tanggal
  Pengirim : " . $isipesan . "
  isi pesan : " . $isipesan . "
  app : " . $app;
}

if (isset($data['appPackageName']))
{
    $app = "Autoresponder";
    $sender = $data['query']['sender'];
    $sender = preg_replace("/[^0-9]/", "", $sender);

    $isipesan = $data['query']['message'];
	  $isipesan = str_replace("'"," ",$isipesan);
    $rule = $data['query']['ruleId'];
    $tanggal = date("Y-m-d H:i:s");
    $dateapp = "2000-01-01 01:01:01";

    $debugfile = "
  Waktu Asli :
  Waktu masuk :
  Waktu server : $tanggal
  Pengirim : " . $sender . "
  isi pesan : " . $isipesan . "
  app : " . $app;
}

$POSTKEY = $_SERVER['HTTP_APIKEY'];
if ($APIKEY == $POSTKEY){
$count= 1;}
else {
  $jawab = "API KEY SALAH, Silahkan hubungi administrator Anda";
  $count = 0;}


if ($count == 1)
{
    if (isset($isipesan))
    { // Insert message to database
        $query1 = "INSERT INTO wa_message_in (sender, message_in, app, dateapp) VALUES
        ('" . $sender . "', '" . $isipesan . "','" . $app . "','" . $dateapp . "')";
        // echo  $query1;
        $idmasuk1 = mysqli_query($db, $query1) or die(mysqli_error($db));

        if (mysqli_insert_id($db) !== null)
        {// Mencari ID Pesan
            $idmasuk = mysqli_insert_id($db);
        }
        else
        {
            $idmasuk = 0;
        }

    }
    else
    {
        $isipesan = "Message Empty!";
    }
    // Analysys message
    include 'rule-poll.php';

    // Insert hasil Jawaban kedalam database
    $query = "UPDATE wa_message_in SET message_out='" . $jawab . "' WHERE  id=" . $idmasuk;
    mysqli_query($db, $query);

}

if (isset($jawab)){
  $jawab = $jawab;
  $jawaban = '
  {
    "message":"' . $jawab . '"
  }';
}else {$jawab = $jawaban = '
{
  "message":"jawab empty message"
}';
}

if (isset($jawab1)){
  $jawab1 = $jawab1;
  $jawaban = $jawaban . ',
{
 "message":"' .$jawab1. ' "
}';
}
    else {$jawab1 = "";}

if (isset($jawab2)){
  $jawab2 = $jawab2;
  $jawaban = $jawaban . ',
{
  "message":"' .$jawab2. ' "
}';
}else {$jawab2 = "";}

if (isset($jawab3)){
  $jawab3 = $jawab3;
  $jawaban = $jawaban . ',
{
  "message":"' .$jawab3. ' "
}';
}
else {$jawab3 = "";}

if (isset($jawab4)){
  $jawab4 = $jawab4;
  $jawaban = $jawaban . ',
  {
    "message":"' .$jawab4. ' "
  }';
}
else {$jawab4 = "";}


// RESPONSE
if (isset($app))
{
    switch ($app)
    {
        case 'Autoresponder':
print '{
      "replies":[
             '.$jawaban.'
             ]
    }';
        break;

        case 'Autoreply':
print '{
      "data":[
             '.$jawaban.'
             ]
           }';
        break;
    }
}
// END RESPONSE

?>
