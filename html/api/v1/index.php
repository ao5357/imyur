<?php
require_once 'AWSSDKforPHP/sdk.class.php';
$dynamo = new AmazonDynamoDB();
$table_name = 'addresses';

/* Deal with input parameters */
$input_url = trim($_GET['q']);
echo $input_url;