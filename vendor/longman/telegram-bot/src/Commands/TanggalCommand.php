<?php

/*
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/
namespace Longman\TelegramBot\Commands;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Command;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;

class TanggalCommand extends Command
{
    protected $name = 'tanggal';
    protected $description = 'Menampilkan tanggal/waktu berdasarkan lokasi';
    protected $usage = '/tanggal <lokasi>';
    protected $version = '1.2.0';
    protected $enabled = true;
    protected $public = true;

    private $base_url = 'https://maps.googleapis.com/maps/api';
    private $date_format = 'd-m-Y H:i:s';

    private function getCoordinates($location)
    {


        $url = $this->base_url . '/geocode/json?';
        $params = 'address=' . urlencode($location);

        $google_api_key = $this->getConfig('google_api_key');
        if (!empty($google_api_key)) {
            $params .= '&key=' . $google_api_key;
        }

        $data = $this->request($url . $params);
        if (empty($data)) {
            return false;
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            return false;
        }

        if ($data['status'] !== 'OK') {
            return false;
        }

        $lat = $data['results'][0]['geometry']['location']['lat'];
        $lng = $data['results'][0]['geometry']['location']['lng'];
        $acc = $data['results'][0]['geometry']['location_type'];
        $types = $data['results'][0]['types'];

        return array($lat, $lng, $acc, $types);
    }

    private function getDate($lat, $lng)
    {
        $url = $this->base_url . '/timezone/json?';

        $date_utc = new \DateTime(null, new \DateTimeZone("UTC"));

        $timestamp = $date_utc->format('U');

        $params = 'location=' . urlencode($lat) . ',' . urlencode($lng) . '&timestamp=' . urlencode($timestamp);

        $google_api_key = $this->getConfig('google_api_key');
        if (!empty($google_api_key)) {
            $params.= '&key=' . $google_api_key;
        }

        $data = $this->request($url . $params);
        if (empty($data)) {
            return false;
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            return false;
        }

        if ($data['status'] !== 'OK') {
            return false;
        }

        $local_time = $timestamp + $data['rawOffset'] + $data['dstOffset'];

        return array($local_time, $data['timeZoneId']);
    }

    private function getFormattedDate($location)
    {
        if (empty($location)) {
            return 'Waktu di kota antah berantah tidak ada';
        }
        list($lat, $lng, $acc, $types) = $this->getCoordinates($location);

        if (empty($lat) || empty($lng)) {
            return 'Sepertinya di kota "' . $location . '" tidak terdapat konsep waktu.';
        }

        list($local_time, $timezone_id) = $this->getDate($lat, $lng);

        $date_utc = new \DateTime(gmdate('Y-m-d H:i:s', $local_time), new \DateTimeZone($timezone_id));

        $return = 'Waktu lokal di kota ' . $location . ' adalah: ' . $date_utc->format($this->date_format) . '';

        return $return;
    }

    private function request($url)
    {
        $response = file_get_contents($url);
        return $response;
    }

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText(true);

        if (empty($text)) {
            $text = 'Anda harus menyebutkan kota dengan format : /date <nama_kota>';
        } else {
            $date = $this->getformattedDate($text);
            if (empty($date)) {
                $text = 'Tidak dapat menemukan lokasi : ' . $text;
            } else {
                $text = $date;
            }
        }

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['reply_to_message_id'] = $message_id;
        $data['text'] = $text;

        $result = Request::sendMessage($data);
    }
}
