<?php

namespace \Twitter;

class CurlManager
{
  private $key;
  private $Curl;

  public function __construct($key)
  {
    $this->key = $key;
    $this->Curl = Curl::getInstance();
  }

  public function __get($name)
  {
    $responses = $this->Curl->getResult($this->key);
    return isset($responses[$name]) ? $responses[$name] : null;
  }

  public function __isset($name)
  {
    $val = self::__get($name);
    return empty($val);
  }
}

?>
