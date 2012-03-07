<?php
ini_set('display_errors', 1);
require_once 'AWSSDKforPHP/sdk.class.php';
$dynamodb = new AmazonDynamoDB();
$table_name = 'addresses';

/* Deal with input parameters */
$input_url = trim($_GET['q']);
echo $input_url;