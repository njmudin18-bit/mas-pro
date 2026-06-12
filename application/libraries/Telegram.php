<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram {
  private $token; // Your Telegram Bot token
  private $apiUrl = 'https://api.telegram.org/bot';

  public function __construct() 
  {
    //$this->token = '7346943443:AAEHnmTW-S_nxnAptyQBWhIA8o76NI9rc5s';
    $this->token = '8233076155:AAHSu07XsrEu3TdfpDi7o0QGdxgcnZGIBeM';
  }

  public function sendMessage($chatId, $message, $parseMode = 'HTML') {
    $url = $this->apiUrl . $this->token . '/sendMessage';
    $data = array(
      'chat_id'       => $chatId,
      'text'          => $message,
      'parse_mode'    => $parseMode, // Specify parse mode as HTML
    );
    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => json_encode($data),
        'header'  =>  "Content-Type: application/json\r\n" .
                      "Accept: application/json\r\n"
      )
    );

    $context  = stream_context_create($options);
    $result   = file_get_contents($url, false, $context);

    return $result;
  }
}
?>
