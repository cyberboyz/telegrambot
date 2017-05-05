<?php
date_default_timezone_set('Asia/Jakarta');
$t=time();
$date1="'".date("Y-m-d H:i:s",$t)."'";

$GLOBALS['token_bot']="126456181:AAEHTxqNBcbujlsmweSkWS1c-yAMvUbH8rQ";

function kirimperintah($perintah, array $keterangan=null)
{
$url="https://api.telegram.org/bot".$GLOBALS['token_bot']."/";
$url.=$perintah."?";
$ch=curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$keterangan);
$output = curl_exec($ch);
curl_close($ch);
return $output;
}

$kota="'YOGYAKARTA'";

//$sql3="SELECT act_alarm_yog.ALARM_NAME,act_alarm_yog.NE_NAME,act_alarm_yog.ALARM_TIME,dapotradio.BAND_COVERAGE,dapotradio.SITECLASS,dapotradio.TOWERPROVIDER,dapotradio.RTPO FROM act_alarm_yog LEFT JOIN dapotradio ON act_alarm_yog.NE_NAME=dapotradio.SERVERBTSNODEBNAME WHERE RTPO=".$kota." AND ALARM_TIME>SUBTIME('2015-09-28 09:00:01','1 1:1') GROUP BY act_alarm_yog.NE_NAME";
//$sql3="SELECT act_alarm_yog.ALARM_NAME,act_alarm_yog.NE_NAME,act_alarm_yog.ALARM_TIME,dapotradio.BAND_COVERAGE,dapotradio.SITECLASS,dapotradio.TOWERPROVIDER,dapotradio.RTPO FROM act_alarm_yog LEFT JOIN dapotradio ON act_alarm_yog.NE_NAME=dapotradio.SERVERBTSNODEBNAME WHERE (RTPO='YOGYAKARTA' OR RTPO='MAGELANG' OR RTPO='BANJARNEGARA') AND ALARM_TIME>SUBTIME($date1,'4:0') GROUP BY act_alarm_yog.NE_NAME";
$sql3="SELECT act_alarm_yog.ALARM_NAME,act_alarm_yog.NE_NAME,act_alarm_yog.SEVERITY,act_alarm_yog.ALARM_TIME,dapotradio.BAND_COVERAGE,dapotradio.SITECLASS,dapotradio.TOWERPROVIDER,dapotradio.RTPO FROM act_alarm_yog LEFT JOIN dapotradio ON act_alarm_yog.NE_NAME=dapotradio.SERVERBTSNODEBNAME  WHERE (RTPO='YOGYAKARTA' OR RTPO='MAGELANG' OR RTPO='BANJARNEGARA' OR RTPO IS NULL) AND NE_NAME NOT LIKE '%SMG%' AND NE_NAME NOT LIKE '%KND%' AND NE_NAME NOT LIKE '%UNR%' AND NE_NAME NOT LIKE '%BATANG%' AND NE_NAME NOT LIKE '%PURWOKERTO%' AND NE_NAME NOT LIKE '%BANYUMAS%' AND NE_NAME NOT LIKE '%SALATIGA%' AND ALARM_TIME>SUBTIME($date1,'24:0') GROUP BY act_alarm_yog.NE_NAME";

$username = "sai_vlr";
$password = "s41_vlr";
$hostname = "10.6.101.40";

$dbhandle = mysql_connect($hostname, $username, $password) or die("Tidak bisa konek ke MySQL");
$selected = mysql_select_db("Vlr_LocUp",$dbhandle) or die("Tidak ada database");
$result = mysql_query($sql3);

$i=1;
$arrtext=array();
while ($row = mysql_fetch_array($result))
{
$arrtext[$i]['time']=$row{'ALARM_TIME'};
$arrtext[$i]['nama_alarm']=$row{'ALARM_NAME'};
$arrtext[$i]['ne_name']=$row{'NE_NAME'};
$arrtext[$i]['severity']=$row{'SEVERITY'};
$arrtext[$i]['band']=$row{'BAND_COVERAGE'};
$arrtext[$i]['siteclass']=$row{'SITECLASS'};
$arrtext[$i]['tower']=$row{'TOWERPROVIDER'};
$arrtext[$i]['rtpo']=$row{'RTPO'};
$i++;
}

$jum_arr=count($arrtext);
$per_hal=5;
if ($jum_arr<=$per_hal){
	$jumlah=$jum_arr;
}
else{
	$jumlah=$per_hal;
}
$nomor=1;
$halaman=1;


for($k=1;$k<=$jum_arr;$k+=5){
$tes="-ALARM TELKOMSEL HAL ".$halaman."-\n\n";

for($j=$k;$j<$k+5;$j++){
if($arrtext[$j]){
$tes.=$nomor.". Nama Site : ".$arrtext[$j]['ne_name']."\n Info Alarm : ".$arrtext[$j]['nama_alarm']."\n Severity : ".$arrtext[$j]['severity']."\n Band : ".$arrtext[$j]['band']."\n Site Class : ".$arrtext[$j]['siteclass']."\n Provider Tower : ".$arrtext[$j]['tower']."\n RTPO : ".$arrtext[$j]['rtpo']."\n Waktu : ".$arrtext[$j]['time']."\n\n";
$nomor++;
}

}
$tes.="----ALARM TELKOMSEL----\n";
$halaman++;
//$data['chat_id']=130553029;
$data['chat_id']=-5590065;
$data['text']=$tes;
kirimperintah("sendMessage",$data);
}

echo 'done';
mysql_close($dbhandle);
?>
