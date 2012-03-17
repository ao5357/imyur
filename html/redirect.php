<?php
ini_set('display_errors', 1);
$path = substr(trim($_GET['q']),1);
$pos = strpos($path,'.');
if($pos == 5 || $pos == 6){
	$hash = substr($path,0,$pos);
	$from_apc = apc_fetch($hash,$apc_success);

	if($apc_success && $from_apc){
		header('Server: ');
		header("Location: $from_apc");
		}
	else{
		require_once 'AWSSDKforPHP/sdk.class.php';
		$sdb = new AmazonSDB();
		$response = $sdb->get_attributes('addresses',$hash,'address');
		$success = $response->isOK();
		if($success){
			$url = $response->body->GetAttributesResult->Attribute->Value;
			if($url && $url !== null){ // TODO: Perhaps there's a better way to confirm a result from sub
				apc_add($hash,$url,86400);
				header("Location: $url");
				}
			else{header("HTTP/1.0 404 Not Found");}
			}
		else{
			header("HTTP/1.0 404 Not Found");
			}
		}
	}
else{
	header("HTTP/1.0 404 Not Found");
	}