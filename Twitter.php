<?php
/*
 *  Class to integrate with Twitter's API.
 *    Authenticated calls are done using OAuth and require access tokens for a user.
 *    API calls which do not require authentication do not require tokens (i.e. search/trends)
 * 
 *  Full documentation available on github
 *    http://wiki.github.com/jmathai/twitter-async
 * 
 *  @author Jaisen Mathai <jaisen@jmathai.com>
 */
 
namespace \Twitter;

class Twitter extends \Twitter\OAuth
{
  const TWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
  const TWITTER_AUTH_OAUTH = 'oauth';
  const TWITTER_AUTH_BASIC = 'basic';
  protected $requestTokenUrl= 'https://api.twitter.com/oauth/request_token';
  protected $accessTokenUrl = 'https://api.twitter.com/oauth/access_token';
  protected $authorizeUrl   = 'https://api.twitter.com/oauth/authorize';
  protected $authenticateUrl= 'https://api.twitter.com/oauth/authenticate';
  protected $apiUrl         = 'http://api.twitter.com';
  protected $userAgent      = '';
  protected $apiVersion     = '1';
  protected $isAsynchronous = false;
  /**
   * The Twitter API version 1.0 search URL.
   * @var string
   */
  protected $searchUrl      = 'http://search.twitter.com';

  /* OAuth methods */
  public function delete($endpoint, $params = null)
  {
    return $this->request('DELETE', $endpoint, $params);
  }

  public function get($endpoint, $params = null)
  {
    return $this->request('GET', $endpoint, $params);
  }

  public function post($endpoint, $params = null)
  {
    return $this->request('POST', $endpoint, $params);
  }

  /* Basic auth methods */
  public function delete_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('DELETE', $endpoint, $params, $username, $password);
  }

  public function get_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('GET', $endpoint, $params, $username, $password);
  }

  public function post_basic($endpoint, $params = null, $username = null, $password = null)
  {
    return $this->request_basic('POST', $endpoint, $params, $username, $password);
  }

  public function useApiUrl($url = '')
  {
    $this->apiUrl = rtrim( $url, '/' );
  }

  public function useApiVersion($version = null)
  {
    $this->apiVersion = $version;
  }

  public function useAsynchronous($async = true)
  {
    $this->isAsynchronous = (bool)$async;
  }

  public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
  {
    parent::__construct($consumerKey, $consumerSecret, self::TWITTER_SIGNATURE_METHOD);
    $this->setToken($oauthToken, $oauthTokenSecret);
  }

  public function __call($name, $params = null/*, $username, $password*/)
  {
    $parts  = explode('_', $name);
    $method = strtoupper(array_shift($parts));
    $parts  = implode('_', $parts);
    $endpoint   = '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
    /* HACK: this is required for list support that starts with a user id */
    $endpoint = str_replace('//','/',$endpoint);
    $args = !empty($params) ? array_shift($params) : null;

    // calls which do not have a consumerKey are assumed to not require authentication
    if($this->consumerKey === null)
    {
      $username = null;
      $password = null;

      if(!empty($params))
      {
        $username = array_shift($params);
        $password = !empty($params) ? array_shift($params) : null;
      }

      return $this->request_basic($method, $endpoint, $args, $username, $password);
    }

    return $this->request($method, $endpoint, $args);
  }

  private function getApiUrl($endpoint)
  {
    if ($this->apiVersion === '1' && preg_match('@^/search[./]?(?=(json|daily|current|weekly))@', $endpoint))
    {
      return $this->searchUrl.$endpoint;
    }

    return $this->apiUrl.'/'.$this->apiVersion.$endpoint;
  }

  private function request($method, $endpoint, $params = null)
  {
    $url = $this->getUrl($this->getApiUrl($endpoint));
    $resp= new JSON(call_user_func(array($this, 'httpRequest'), $method, $url, $params, $this->isMultipart($params)), $this->debug);
    if(!$this->isAsynchronous)
      $resp->response;

    return $resp;
  }

  private function request_basic($method, $endpoint, $params = null, $username = null, $password = null)
  {
    $url = $this->getApiUrl($endpoint);
    if($method === 'GET')
      $url .= is_null($params) ? '' : '?'.http_build_query($params, '', '&');
    $ch  = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if($method === 'POST' && $params !== null)
    {
      if($this->isMultipart($params))
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      else
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->buildHttpQueryRaw($params));
    }
    if(!empty($username) && !empty($password))
      curl_setopt($ch, CURLOPT_USERPWD, "{$username}:{$password}");

    $resp = new JSON(Curl::getInstance()->addCurl($ch), $this->debug);
    if(!$this->isAsynchronous)
      $resp->response;

    return $resp;
  }
}

?>
