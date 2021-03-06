<?php

/*
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * written by Marco Boretto <marco.bore@gmail.com>
*/
namespace Longman\TelegramBot\Commands;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Command;
use Longman\TelegramBot\Entities\Update;

use Longman\TelegramBot\Entities\ReplyKeyboardMarkup;
use Longman\TelegramBot\Entities\ReplyKeyboardHide;
use Longman\TelegramBot\Entities\ForceReply;

class KeyboardCommand extends Command
{
    protected $name = 'keyboard';
    protected $description = 'Memperlihatkan keyboard, untuk menyembunyikan ketik /hidekeyboard';
    protected $usage = '/keyboard';
    protected $version = '0.0.5';
    protected $enabled = true;
    protected $public = true;

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();
        $message_id = $message->getMessageId();

        $chat_id = $message->getChat()->getId();
        $text = $message->getText(true);

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['text'] = 'Tekan tombol:';
        #$data['reply_to_message_id'] = $message_id;

        #Keyboard examples
        $keyboards = array();

        //0
        $keyboard[] = array('/cuaca','/siapasaya');
        $keyboard[] = array('/tanggal','/echo');
        $keyboard[] = array('/bantuan','/hidekeyboard');
       
        $keyboards[] = $keyboard;
        unset($keyboard);
/*
        //1
        $keyboard[] = ['7','8','9','+'];
        $keyboard[] = ['4','5','6','-'];
        $keyboard[] = ['1','2','3','*'];
        $keyboard[] = [' ','0',' ','/'];

        $keyboards[] = $keyboard;
        unset($keyboard);


        //2
        $keyboard[] = ['A'];
        $keyboard[] = ['B'];
        $keyboard[] = ['C'];

        $keyboards[] = $keyboard;
        unset($keyboard);

        //3
        $keyboard[] = ['A'];
        $keyboard[] = ['B'];
        $keyboard[] = ['C','D'];

        $keyboards[] = $keyboard;
        unset($keyboard);
*/

        $json_1 = new ReplyKeyboardMarkup(array(
                    'keyboard' => $keyboards[0] ,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => false,
                    'selective' => false));
	$json=$json_1->toJSON();
        $data['reply_markup'] = $json;

        $result = Request::sendMessage($data);
        return $result;
    }
}
