<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);
/* Allowed Inputs */
$ext = (isset($_POST['extension'])) ? '.' . substr(preg_replace('/[^a-zA-Z0-9]/','',$_POST['extension']),0,25) : '';
$subdomain = (isset($_POST['subdomain']) && in_array($_POST['subdomain'],array('i','self','www'))) ? $_POST['subdomain'] . '.' : '';
$input_url = (isset($_POST['url'])) ? trim($_POST['url']) : '';
$rest_file = trim($_GET['q']);

if($rest_file == '/api/v1/shorten.html'){$rest_file = 'html';}
else if($rest_file == '/api/v1/shorten.json'){$rest_file = 'json';}
else{$rest_file = false;}

$url_parts = parse_url($input_url);
$scheme_good = (isset($url_parts['scheme']) && in_array($url_parts['scheme'],array('http','https','shttp','ssl','spdy')));
$not_imyur = (isset($url_parts['host']) && substr($url_parts['host'],0,9) !== 'imyur.com');

$output = array();
$success = false;

/* Functions */
function base62encode($data){
	return gmp_strval(gmp_init($data,10),62);
	}

function counter(){
	$prev = (int)file_get_contents('/home/www/imco.txt');
	$cur = $prev + 1;
	file_put_contents('/home/www/imco.txt', $cur);
	return base62encode($cur + 16000000);
	}

function save_url($input_url){
	$hash = counter();
	require_once 'AWSSDKforPHP/sdk.class.php';
	$sdb = new AmazonSDB();
	$response = $sdb->put_attributes('addresses', $hash, array('address' => $input_url), true);
	$awssuccess = $response->isOK();
	if($awssuccess){
		apc_add($hash,$input_url,86400);
		return array($awssuccess,$hash);
		}
	else{
		return array($awssuccess);
		}
	}

echo $ext . ' ' . $subdomain . ' ' . $input_url . ' ' . $rest_file . ' ' . (string)$scheme_good . ' ' . (string)$not_imyur;
echo counter();