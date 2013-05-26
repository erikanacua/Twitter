Twitter APIs (PHP)
=======================
*an updated\restructured\cleaned up version of <a href="https://github.com/jmathai/twitter-async">jmathai/twitter-async</a>*

Looking for an example? <a href="https://github.com/kaosdynamics/Twitter/blob/master/Examples/index.php">Here we go</a>

#### A fast PHP Twitter library
* PSR-0 Compliant
* Supporting API version 1.1 (1.0 will be totally retired on <a href="http://goog.l/gA2nS">June 11, 2013</a>)
* Supporting Media Upload

#### Short version of documentation
Once you authenticated (see example)

You need to call a function (based on method you are willing to use)
```php
/* $twitter is referring to a variable set in the example */
$twitter->get(...);
$twitter->post(...);
$twitter->delete(...);
```
then specify endpoint (see Twitter APIs documentation)
and the params
(like described in the <a href="https://github.com/kaosdynamics/Twitter/blob/master/Examples/index.php">example</a>)

#### Contributions from 
   * https://github.com/jmathai
   * https://github.com/arikfr 
   * https://github.com/ericmmartin
   * https://github.com/tahpot
   * https://github.com/dingram
   * https://github.com/ngnpope
