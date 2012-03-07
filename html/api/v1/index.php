<?php
require_once 'AWSSDKforPHP/sdk.class.php';
try{$dynamodb = new AmazonDynamoDB();} catch(Exception $e){
		throw $e;
		var_dump($e->getMessage());
		}
$table_name = 'addresses';

/* Deal with input parameters */
$input_url = trim($_GET['q']);
echo $input_url;