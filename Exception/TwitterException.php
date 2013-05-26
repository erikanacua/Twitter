<?php

namespace Twitter\Exception;

class TwitterException extends \Exception 
{
  public static function raise($response, $debug)
  {
    $message = $response->data;
    switch($response->code)
    {
      case 400:
        throw new TwitterBadRequest($message, $response->code);
      case 401:
        throw new TwitterNotAuthorized($message, $response->code);
      case 403:
        throw new TwitterForbidden($message, $response->code);
      case 404:
        throw new TwitterNotFound($message, $response->code);
      case 406:
        throw new TwitterNotAcceptable($message, $response->code);
      case 420:
        throw new TwitterUnknown($message, $response->code);
      case 500:
        throw new TwitterInternal($message, $response->code);
      case 502:
        throw new TwitterBadGateway($message, $response->code);
      case 503:
        throw new TwitterUnavailable($message, $response->code);
      default:
        throw new TwitterException($message, $response->code);
    }
  }
}
?>
