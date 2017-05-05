<?php

/*
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Written by Marco Boretto <marco.bore@gmail.com>
*/

namespace Longman\TelegramBot\Commands;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\Command;
use Longman\TelegramBot\Entities\Update;

class SiapasayaCommand extends Command
{
    protected $name = 'siapasaya';
    protected $description = 'Menampilkan id, nama, dan username';
    protected $usage = '/siapasaya';
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $public = true;

    public function execute()
    {
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText(true);

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['reply_to_message_id'] = $message_id;
        $data['text'] = 'ID anda adalah : ' . $message->getFrom()->getId();
        $data['text'] .= "\n" . 'Nama : ' . $message->getFrom()->getFirstName()
                                . ' ' . $message->getFrom()->getLastName();
        $data['text'] .= "\n" . 'Username : ' . $message->getFrom()->getUsername();

        $result = Request::sendMessage($data);
        return $result;
    }
}
