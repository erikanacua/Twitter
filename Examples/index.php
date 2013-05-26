<?php
/* Just to make errors show up */
ini_set('display_errors', TRUE);
error_reporting(E_ALL);

/* Autoloader */
spl_autoload_register(function ($class) {
	$sanitize = str_replace("\\", "/", $class);
	$external = dirname(__DIR__). "/../" . $sanitize . ".php"; 
	if(file_exists($external)) {
		require_once($external);
	}
});

/* 
	This should reflect your app settings.
	Take a look at https://dev.twitter.com/apps 
*/
$ckey = "";
$csec = "";

/*
	You should somehow get your oauth params.
	As a developer you can get them from https://dev.twitter.com/apps again in OAuth Settings
	As a user you should request them from the APIs
*/

$oauthToken = "";
$oauthSecret= "";

$twitter = new \Twitter\Twitter($ckey, $csec, $oauthToken, $oauthSecret);
/* You can also set oauth token\secret later:

	$twitter = new \Twitter\Twitter($ckey, $csec);

	$twitter->setToken($oauthToken, $oauthSecret);
*/

$endpoint = "/statuses/user_timeline.json";
$api_opts = array(
	"screen_name"      => "mpreziuso",
	"include_entities" => false,
	"include_rts"      => false,
	"count"            => 1
);

$x = $twitter->get($endpoint, $api_opts);

$headers     = $x->headers;  
$status_code = $x->code;
$response    = $x->response; /* The actual content you wanted to take from the API request is here */

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Testing Twitter APIs (https://github.com/kaosdynamics/Twitter)</title>
	</head>
	<body>
	<pre style="background: #FFFF99; font-size: 11px;"><?php print_r($response); ?></pre>
	</body>
</html>
<?php exit(0); ?>