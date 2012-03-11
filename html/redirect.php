<?php
$path = substr(trim($_GET['q']),1);
$pos = strpos($path,'.');
if($pos == 5 || $pos == 6){
	$hash = substr($path,0,$pos);
	$from_apc = apc_fetch($hash);
	if(!$from_apc){
		require_once 'AWSSDKforPHP/sdk.class.php';
		$sdb = new AmazonSDB();
		$response = $sdb->get_attributes('addresses',$hash);
		$success = $sdb->isOK();
		if($success){
			var_dump($response);
			//header('Location: ' . $address);
			}
		else{
			header("HTTP/1.1 404 Not Found")
			}
		}
	else{
		header('Location: ' . $from_apc);
		}
	}
else{
	header("HTTP/1.1 404 Not Found");
	}