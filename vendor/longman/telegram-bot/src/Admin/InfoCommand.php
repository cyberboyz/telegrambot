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

class InfoCommand extends Command
{
    protected $name = 'info';
    protected $description = 'Menampilkan info semua alarm';
    protected $usage = '/info';
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $public = true;

    public function execute(){
        $update = $this->getUpdate();
        $message = $this->getMessage();

        $chat_id = $message->getChat()->getId();
        $message_id = $message->getMessageId();
        $text = $message->getText(true);

	Request::kirimfinal(NULL,$chat_id);
	}
}
