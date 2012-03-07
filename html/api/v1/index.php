<?php
try{
	require_once 'AWSSDKforPHP/sdk.class.php';
	} catch(Exception $e){
		throw $e;
		var_dump($e->getMessage());
		}
//$dynamodb = new AmazonDynamoDB();
$table_name = 'addresses';

/* Deal with input parameters */
$input_url = trim($_GET['q']);
echo $input_url;