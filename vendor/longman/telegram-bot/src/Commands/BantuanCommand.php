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

class BantuanCommand extends Command
{
    protected $name = 'bantuan';
    protected $description = 'Menampilkan bantuan terkait perintah bot';
    protected $usage = '/bantuan atau /bantuan <nama_perintah>';
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

        $commands = $this->telegram->getCommandsList();

        if (empty($text)) {
            $msg = 'Bot TugasOOP v1.0' . $this->telegram->getVersion() . "\n\n";
            $msg .= 'Daftar Perintah : ' . "\n";
            foreach ($commands as $command) {
                if (is_object($command)) {
                    if (!$command->isEnabled()) {
                        continue;
                    }
                    if (!$command->isPublic()) {
                        continue;
                    }

                    $msg .= '/' . $command->getName() . ' - ' . $command->getDescription() . "\n";
                }
            }

            $msg .= "\n" . 'Untuk bantuan terkait perintah tertentu, ketik : /bantuan <nama_perintah>';
        } else {
            $text = str_replace('/', '', $text);
            if (isset($commands[$text])) {
                $command = $commands[$text];
                if (!$command->isEnabled() || !$command->isPublic()) {
                    $msg = 'Perintah ' . $text . ' tidak ditemukan';
                } else {
                    $msg = 'Perintah: ' . $command->getName() . ' v' . $command->getVersion() . "\n";
                    $msg .= 'Deskripsi: ' . $command->getDescription() . "\n";
                    $msg .= 'Penggunaan: ' . $command->getUsage();
                }
            } else {
                $msg = 'Perintah ' . $text . ' tidak ditemukan';
            }
        }

        $data = array();
        $data['chat_id'] = $chat_id;
        $data['reply_to_message_id'] = $message_id;
        $data['text'] = $msg;

        $result = Request::sendMessage($data);
        return $result;
    }
}
