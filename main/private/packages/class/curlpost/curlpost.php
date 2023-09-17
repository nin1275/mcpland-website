<?php

  namespace LeaderOS\Http;

  class CurlPost {
    private $url;
    private $options;
    private $userAgent;

    /**
    * @param string $url     Request URL
    * @param array  $options cURL options
    */
    public function __construct($url, array $options = []) {
      $this->url = $url;
      $this->options = $options;
      $this->userAgent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';
    }

    /**
    * Get the response
    * @return string
    * @throws \RuntimeException On cURL error
    */
    public function __invoke(array $post) {
      $curl = curl_init($this->url);

      foreach ($this->options as $key => $val) {
        curl_setopt($curl, $key, $val);
      }

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
      curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
      curl_setopt($curl, CURLOPT_REFERER, ((isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? "https" : "http")."://".$_SERVER["SERVER_NAME"]));
      curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent);

      $response = curl_exec($curl);
      $error    = curl_error($curl);
      $errno    = curl_errno($curl);

      if (is_resource($curl)) {
        curl_close($curl);
      }

      if ($errno !== 0) {
        throw new \RuntimeException($error, $errno);
      }

      return $response;
    }
  }
