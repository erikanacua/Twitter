<?php

namespace Twitter\Exception;

class OAuthException extends \Exception
{
  public static function raise($response, $debug)
  {
    $message = $response->responseText;

    switch($response->code)
    {
      case 400:
        throw new BadRequest($message, $response->code);
      case 401:
        throw new Unauthorized($message, $response->code);
      default:
        throw new OAuthException($message, $response->code);
    }
  }
}


?>
