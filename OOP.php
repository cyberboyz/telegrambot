#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';

$API_KEY = '358811642:AAGl73l9FWgYZSOQ5aWQEKBJffjFWNGZXqQ';
$BOT_NAME = 'TugasOOPBot';
$mysql_credentials = [
   'host'     => 'localhost',
   'user'     => 'root',
   'password' => '',
   'database' => 'telegram',
];

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram($API_KEY, $BOT_NAME);

    // Enable MySQL
    $telegram->enableMySQL($mysql_credentials);

    // Handle telegram getUpdate request
    $telegram->handleGetUpdates();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // log telegram errors
    echo $e;
}