<?php

namespace Twitter;
class JSON implements \ArrayAccess, \Countable, \IteratorAggregate
{
  private $debug;
  private $__resp;
  public function __construct($response, $debug = false)
  {
    $this->__resp = $response;
    $this->debug  = $debug;
  }

  // ensure that calls complete by blocking for results, NOOP if already returned
  public function __destruct()
  {
    $this->responseText;
  }

  // Implementation of the IteratorAggregate::getIterator() to support foreach ($this as $...)
  public function getIterator ()
  {
    if ($this->__obj) {
      return new \ArrayIterator($this->__obj);
    } else {
      return new \ArrayIterator($this->response);
    }
  }

  // Implementation of Countable::count() to support count($this)
  public function count ()
  {
    return count($this->response);
  }
  
  // Next four functions are to support ArrayAccess interface
  // 1
  public function offsetSet($offset, $value) 
  {
    $this->response[$offset] = $value;
  }

  // 2
  public function offsetExists($offset) 
  {
    return isset($this->response[$offset]);
  }
  
  // 3
  public function offsetUnset($offset) 
  {
    unset($this->response[$offset]);
  }

  // 4
  public function offsetGet($offset) 
  {
    return isset($this->response[$offset]) ? $this->response[$offset] : null;
  }

  public function __get($name)
  {
    $accessible = array('responseText'=>1,'headers'=>1,'code'=>1);
    $this->responseText = $this->__resp->data;
    $this->headers      = $this->__resp->headers;
    $this->code         = $this->__resp->code;
    if(isset($accessible[$name]) && $accessible[$name])
      return $this->$name;
    elseif(($this->code < 200 || $this->code >= 400) && !isset($accessible[$name]))
      \Twitter\Exception\TwitterException::raise($this->__resp, $this->debug);

    // Call appears ok so we can fill in the response
    $this->response     = json_decode($this->responseText, 1);
    $this->__obj        = json_decode($this->responseText);

    if(gettype($this->__obj) === 'object')
    {
      foreach($this->__obj as $k => $v)
      {
        $this->$k = $v;
      }
    }

    if (property_exists($this, $name)) {
      return $this->$name;
    }
    return null;
  }

  public function __isset($name)
  {
    $value = self::__get($name);
    return !empty($name);
  }
}

?>
