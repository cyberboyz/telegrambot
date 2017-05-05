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

class CuacaCommand extends Command
{
    protected $name = 'cuaca';
    protected $description = 'Menampilkan cuaca berdasarkan lokasi';
    protected $usage = '/cuaca <lokasi>';
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $public = true;

    private function getWeather($location)
    {
        $url = 'http://api.openweathermap.org/data/2.5/weather?q=' . $location . '&units=metric';

        $ch = curl_init();
        $curlConfig = array(CURLOPT_URL => $url,

        //CURLOPT_POST              => true,
        CURLOPT_RETURNTRANSFER => true,

        //CURLOPT_HTTPHEADER        => array('Content-Type: text/plain'),
        //CURLOPT_POSTFIELDS            => $data
        //CURLOPT_VERBOSE               => true,
        //CURLOPT_HEADER                => true,
        );

        curl_setopt_array($ch, $curlConfig);
        $response = curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code !== 200) {
            throw new \Exception('Error receiving data from url');
        }
        curl_close($ch);

        return $response;
    }

    private function getWeatherString($location)
    {
        if (empty($location)) {
            return false;
        }

        // try/catch
        $data = $this->getWeather($location);

        $decode = json_decode($data, true);
        if (empty($decode) || $decode['cod'] != 200) {
            return false;
        }
        $city = $decode['name'];
        $country = $decode['sys']['country'];
        $temp = 'Temperatur di kota ' . $city . ' (' . $country . ') adalah ' . $decode['main']['temp'] . '°C';
        $conditions = 'Kondisi cuacanya adalah : ' . $decode['weather'][0]['description'];

        switch (strtolower($decode['weather'][0]['main'])) {
            case 'clear':
                $conditions.= ' ☀';
                break;

            case 'clouds':
                $conditions.= ' ☁☁';
                break;

            case 'rain':
                $conditions.= ' ☔';
                break;

            case 'thunderstorm':
                $conditions.= ' ☔☔☔☔';
                break;
        }

        return $temp . "\n" . $conditions;
    }

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText(true);

        if (empty($text)) {
            $text = 'Anda harus menuliskan lokasi dengan format: /cuaca <kota>';
        } else {
            $weather = $this->getWeatherString($text);
            if (empty($weather)) {
                $text = 'Tidak dapat mencari cuaca untuk lokasi: ' . $text;
            } else {
                $text = $weather;
            }
        }

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['reply_to_message_id'] = $message_id;
        $data['text'] = $text;

        $result = Request::sendMessage($data);
        return $result;
    }
}
