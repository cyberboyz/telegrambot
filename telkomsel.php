<?php
$loader = require __DIR__.'/vendor/autoload.php';

$token_bot = '126456181:AAEHTxqNBcbujlsmweSkWS1c-yAMvUbH8rQ';
$nama_bot = 'telkomselAlarmBot';
$hostname='10.6.101.40';
$username='sai_vlr';
$password='s41_vlr';
$database='Vlr_LocUp';

$ID_Penerima=-5590065;
//$ID_Penerima=130553029;

$sql3="SELECT act_alarm_yog.ALARM_NAME,act_alarm_yog.NE_NAME,act_alarm_yog.SEVERITY,act_alarm_yog.ALARM_TIME,dapotradio.BAND_COVERAGE,dapotradio.SITECLASS,dapotradio.TOWERPROVIDER,dapotradio.RTPO FROM act_alarm_yog LEFT JOIN dapotradio ON act_alarm_yog.NE_NAME=dapotradio.SERVERBTSNODEBNAME  WHERE (RTPO='YOGYAKARTA' OR RTPO='MAGELANG' OR RTPO='BANJARNEGARA' OR RTPO IS NULL) AND NE_NAME NOT LIKE '%SMG%' AND NE_NAME NOT LIKE '%KND%' AND NE_NAME NOT LIKE '%UNR%' AND NE_NAME NOT LIKE '%BATANG%' AND NE_NAME NOT LIKE '%PURWOKERTO%' AND ALARM_TIME>SUBTIME('2015-09-28 09:00:01','124:0') GROUP BY act_alarm_yog.NE_NAME";

$credentials = array('host'=>$hostname, 'user'=>$username, 'password'=>$password, 'database'=>$database);
$dbhandle = mysql_connect($hostname, $username, $password) or die("Tidak bisa konek ke MySQL");
$selected = mysql_select_db($database,$dbhandle) or die("Tidak ada database");
try {
    // create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($token_bot, $nama_bot);
$telegram->enableAdmins(array('130553029'));
    //Options
    $telegram->enableMySQL($credentials);
	$telegram->handleGetUpdates();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
     echo $e->getMessage();
}
