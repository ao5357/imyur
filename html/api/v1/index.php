<?php
ini_set('display_errors', 1);
require_once 'AWSSDKforPHP/sdk.class.php';
$dynamodb = new AmazonDynamoDB();
$table_name = 'addresses';

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

/* Deal with input parameters */
$input_url = substr(trim($_GET['q']),8);
$hash = substr(base62encode(md5($input_url)),0,5);
echo $input_url . ' becomes ' . $hash;