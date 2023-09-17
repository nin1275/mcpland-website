<?php
  class OneSignal {
    private $appID;
    private $restApiKey;
    private $playerList = array();
    public function __construct($appID = null, $restApiKey = null, $playerList = array()) {
      $this->appID = $appID;
      $this->restApiKey = $restApiKey;
      $this->playerList = $playerList;
    }
    public function sendMessage($title = null, $message = null, $url = null) {
      $heading = array(
        'en' => $title
      );
      $content = array(
        'en' => $message
      );
      $data = array(
        'app_id'              => $this->appID,
        'include_player_ids'  => $this->playerList,
        'headings'            => $heading,
        'contents'            => $content,
        'url'                 => $url
      );
      $data = json_encode($data);
      $curl = curl_init('https://onesignal.com/api/v1/notifications');
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.$this->restApiKey
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      return $response;
    }
  }
