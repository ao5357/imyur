<?php
ini_set('display_errors', 1);
$path = substr(trim($_GET['q']),1);
$pos = strpos($path,'.');
if($pos == 5 || $pos == 6){
	echo "Condition for pos was true";
	$hash = substr($path,0,$pos);
	$from_apc = apc_fetch($hash,$apc_success);

	if($apc_success){
		header('Location: ' . $from_apc);
		}
	else{
		require_once 'AWSSDKforPHP/sdk.class.php';
		$sdb = new AmazonSDB();
		$response = $sdb->get_attributes('addresses',$hash);
		$success = $response->isOK();
		if($success){
			print_r($response);
			//header('Location: ' . $address);
			}
		else{
			echo "sub did not do its fairy magic";
			//header("HTTP/1.0 404 Not Found")
			}
		}
	}
else{
	// header("HTTP/1.0 404 Not Found");
	}