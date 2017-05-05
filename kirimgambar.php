<?php

$chat_id=130553029;
$bot_url    = "https://api.telegram.org/bot126456181:AAEHTxqNBcbujlsmweSkWS1c-yAMvUbH8rQ/";
$url        = $bot_url . "sendPhoto?chat_id=" . $chat_id ;

$photo='/data/sai_vlr/alarm/images.jpg';

$post_fields = array('chat_id'   => $chat_id,
    'photo'     => '@'.realpath($photo)
);

echo realpath($photo);

$ch = curl_init(); 
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type:multipart/form-data"
));
curl_setopt($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
$output = curl_exec($ch);
echo "done";
?>
