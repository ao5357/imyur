<?php
ini_set('display_errors', 1);
$allowed_schemes = array('http','https','shttp','ssl','spdy');
$input_url = trim($_GET['q']);
if(substr($input_url,0,8) == '/api/v1/'){
	$input_url = substr($input_url,8);
	}
$callback = trim($_GET['callback']);
$hascallback = strlen($callback);
$url_parts = parse_url($input_url);
$output = '';

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
	$response = $sdb->put_attributes('addresses', $hash, array(
    'address' => $input_url)
    );
  print_r($response);
	}

/* Core conditional logic */
if($url_parts['scheme'] && in_array($url_parts['scheme'],$allowed_schemes)){
	$output = "Passed basic URL validation";
	$safe_lookup = file_get_contents('https://sb-ssl.google.com/safebrowsing/api/lookup?client=imyur&appver=1.0&apikey=ABQIAAAA8mLG1wxBrySac59O6cUIzhT3haXetYFvqARH2WifqKz48noHcg&pver=3.0&url=' . urlencode($input_url));
	if($http_response_header[0] == 'HTTP/1.0 204 No Content'){
		$output = save_url($input_url);
		}
	else if(substr($http_response_header[0],0,12) == 'HTTP/1.0 200'){
		$output = $safe_lookup;
		}
	else{
		$output = $http_response_header[0];
		}
	}
else{
	$output = "failed basic URL validation";
	}

/* Output */
// echo 'Input URL: ' . $input_url . ' |callback: ' . $callback . "\r\n";
// print_r($url_parts);
echo ($callback) ? $callback . '(' . json_encode($output) . ');' : json_encode($output);
