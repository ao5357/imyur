<?php
ini_set('display_errors', 1);
require_once 'AWSSDKforPHP/sdk.class.php';
$dynamodb = new AmazonDynamoDB();
$table_name = 'addresses';
$allowed_schemes = array('http','https','shttp','ssl','spdy');
$input_url = substr(trim($_GET['q']),8);
$callback = trim($_GET['callback']);
$url_parts = parse_url($input_url);
$output = '';

/* Functions */
function base62encode($data){
	$outstring = '';
	$l = strlen($data);
	for($i = 0;$i < $l;$i += 8){
		$chunk = substr($data, $i, 8);
		$outlen = ceil((strlen($chunk) * 8)/6); //8bit/char in, 6bits/char out, round up
		$x = bin2hex($chunk); //gmp won't convert from binary, so go via hex
		$w = gmp_strval(gmp_init(ltrim($x, '0'), 16), 62); //gmp doesn't like leading 0s
		$pad = str_pad($w, $outlen, '0', STR_PAD_LEFT);
		$outstring .= $pad;
		}
	return $outstring;
	}

function counter(){
	$prev = (int)file_get_contents('/home/ec2-user/imco.txt');
	$cur = $prev + 1;
	file_put_contents('/home/ec2-user/imco.txt', $cur);
	return $cur + 14776335;
	}

function save_url($input_url){
	return counter();
	}

/* Core conditional logic */
if($url_parts['scheme'] && in_array($url_parts['scheme'],$allowed_schemes)){
	$output = "Passed basic URL validation";
	$safe_lookup = file_get_contents('https://sb-ssl.google.com/safebrowsing/api/lookup?client=imyur&appver=1.0&apikey=ABQIAAAA8mLG1wxBrySac59O6cUIzhT3haXetYFvqARH2WifqKz48noHcg&pver=3.0&url=' . urlencode($input_url));
	if($http_response_header[0] == 'HTTP/1.1 204 No Content'){
		$output = save_url($input_url);
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
