<?php

/*
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
namespace Longman\TelegramBot;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Entities\ServerResponse;

class Request
{
    private static $telegram;
    private static $input;
    private static $server_response;

    private static $methods = array(
        'getMe',
        'sendMessage',
        'forwardMessage',
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendSticker',
        'sendVideo',
        'sendLocation',
        'sendChatAction',
        'getUserProfilePhotos',
        'getUpdates',
        'setWebhook',
    );

    public static function initialize(Telegram $telegram)
    {
        if (is_object($telegram)) {
            self::$telegram = $telegram;
        } else {
            throw new TelegramException('Telegram pointer is empty!');
        }
    }

    public static function getInput()
    {
        if ($update = self::$telegram->getCustomUpdate()) {
            self::$input = $update;
        } else {
            self::$input = file_get_contents('php://input');
        }
        self::log();
        return self::$input;
    }

    public static function getUpdates($data)
    {
        if ($update = self::$telegram->getCustomUpdate()) {
            self::$input = $update;
        } else {
            self::$input = self::send('getUpdates', $data);
        }
        self::log(); //TODO
        return self::$input;
    }


    private static function log()
    {
        if (!self::$telegram->getLogRequests()) {
            return false;
        }
        $path = self::$telegram->getLogPath();
        if (!$path) {
            return false;
        }

        $status = file_put_contents($path, self::$input . "\n", FILE_APPEND);

        return $status;
    }

    public static function generateGeneralFakeServerSesponse($data = null)
    {
        //PARAM BINDED IN PHPUNIT TEST FOR TestServerResponse.php
        //Maybe this is not the best possible implementation

        //No value set in $data ie testing setWekhook
        //Provided $data['chat_id'] ie testing sendMessage

        $fake_response['ok'] = true; // :)

        if (!isset($data)) {
            $fake_response['result'] = true;
        }

        //some data to let iniatilize the class method SendMessage
        if (isset($data['chat_id'])) {
            $data['message_id'] = '1234';
            $data['date'] = '1441378360';
            $data['from'] = array( 'id' => 123456789 ,'first_name' => 'botname', 'username'=> 'namebot');
            $data['chat'] = array('id'=> $data['chat_id'] );

            $fake_response['result'] = $data;
        }

        return $fake_response;
    }

	public static function kirimperintah($perintah, array $keterangan=null)
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

	public static function kirimfinal($query=NULL,$penerima=NULL)
	{
		//Menghubungkan ke MySQL
		global $dbhandle;
		//Memilih database dengan nama dbname untuk diambil dari MySQL
		global $selected;

		global $sql3;
		if($query){
		$sql="SELECT*FROM(".$sql3.") x where x.ALARM_NAME='".$query."'";}
		else 
		$sql=$sql3;

		$result = mysql_query($sql);
		echo (is_object($result));
		if(mysql_num_rows($result)>0){
		$i=1;
		$arrtext=array();
		while ($row = mysql_fetch_array($result))
		{
		$arrtext[$i]['time']=$row{'ALARM_TIME'};
		$arrtext[$i]['nama_alarm']=$row{'ALARM_NAME'};
		$arrtext[$i]['ne_name']=$row{'NE_NAME'};
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
		$tes.=$nomor.". Nama Site : ".$arrtext[$j]['ne_name']."\n Info Alarm : ".$arrtext[$j]['nama_alarm']."\n Band : ".$arrtext[$j]['band']."\n Site Class : ".$arrtext[$j]['siteclass']."\n Provider Tower : ".$arrtext[$j]['tower']."\n RTPO : ".$arrtext[$j]['rtpo']."\n Waktu : ".$arrtext[$j]['time']."\n\n";
		$nomor++;
		}

		}
		$tes.="----ALARM TELKOMSEL----\n";
		$halaman++;
		if($penerima){
		$data['chat_id']=$penerima;}
		else {
		$data['chat_id']=$GLOBALS['ID_Penerima'];}
		$data['text']=$tes;
		//Request::kirimperintah("sendMessage",$data);
		Request::send("sendMessage",$data);
		}}
		else {
		$tes="Untuk saat ini tidak ada alarm ".$query;
		
		if($penerima){
                $data['chat_id']=$penerima;}
                else {
                $data['chat_id']=$GLOBALS['ID_Penerima'];}
		
		$data['text']=$tes;
		//Request::kirimperintah("sendMessage",$data);
		Request::send("sendMessage",$data);
		}

		//Menutup database
		mysql_close($dbhandle);
	}	
	public static function send($action, array $data = null)
    {

        if (!in_array($action, self::$methods)) {
            throw new TelegramException('This methods doesn\'t exixt!');
        }

        if (defined('PHPUNIT_TESTSUITE')) {
            $fake_response = self::generateGeneralFakeServerSesponse($data);
            return new ServerResponse($fake_response, self::$telegram->getBotName());
        }
	
        $ch = curl_init();
        $curlConfig = array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . self::$telegram->getApiKey() . '/' . $action,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true
        );

        if (!empty($data)) {
            if (!empty($data['text']) && substr($data['text'], 0, 1) === '@') {
                $data['text'] = ' ' . $data['text'];
            }
            $curlConfig[CURLOPT_POSTFIELDS] = $data;
        }

        curl_setopt_array($ch, $curlConfig);
        $result = curl_exec($ch);
        curl_close($ch);

        if (empty($result)) {
            $response['ok'] = 1;
            $response['error_code'] = 1;
            $response['description'] = 'Empty server response';
            $result =json_encode($response);
        }

        $bot_name = self::$telegram->getBotName();
        return new ServerResponse(json_decode($result, true), $bot_name);
    }
	
	public static function sendMessage(array $data)
	{

		if (empty($data)) {
			throw new TelegramException('Data is empty!');
		}

		$result = self::send('sendMessage', $data);
		return $result;
    }
			
    public static function getMe()
    {

        $result = self::send('getMe');
        return $result;
    }

}
