<?php
ini_set('display_errors', 1);
$path = substr(trim($_GET['q']),1); echo $path;
$pos = strpos($path,'.'); echo $pos;
if($pos == 5 || $pos == 6){
	echo "Condition for pos was true";
	$hash = substr($path,0,$pos);
	echo $hash;
	$from_apc = apc_fetch($hash);
	echo $from_apc;
	if(!$from_apc){
		require_once 'AWSSDKforPHP/sdk.class.php';
		$sdb = new AmazonSDB();
		$response = $sdb->get_attributes('addresses',$hash);
		$success = $response->isOK();
		if($success){
			print_r($response);
			//header('Location: ' . $address);
			}
		else{
			header("HTTP/1.0 404 Not Found")
			}
		}
	else{
		header('Location: ' . $from_apc);
		}
	}
else{
	// header("HTTP/1.0 404 Not Found");
	}