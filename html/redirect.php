<?php
// ini_set('display_errors', 1);
$path = substr(trim($_GET['q']),1);
$pos = strpos($path,'.');
if(!$pos || $pos == 5 || $pos == 6){
	$hash = ($pos) ? substr($path,0,$pos) : substr($path,0,6);
	$from_apc = apc_fetch($hash,$apc_success);

	if($apc_success && $from_apc){
		header('Server: ');
		header("Location: $from_apc");
		}
	else if(preg_match("/^[a-zA-Z0-9]+$/",$hash)){
		require_once 'AWSSDKforPHP/sdk.class.php';
		$sdb = new AmazonSDB();
		$response = $sdb->get_attributes('addresses',$hash,'address');
		$success = $response->isOK();
		if($success){
			$url = (string)$response->body->GetAttributesResult->Attribute->Value;
			//print_r($response);
			if($url){ // TODO: Perhaps there's a better way to confirm a result from sub
				apc_add($hash,$url,86400);
				header("Location: $url");
				}
			else{header("HTTP/1.0 404 Not Found");}
			}
		else{
			header("HTTP/1.0 404 Not Found");
			}
		}
	else{header("HTTP/1.0 404 Not Found");}
	}
else{
	header("HTTP/1.0 404 Not Found");
	}