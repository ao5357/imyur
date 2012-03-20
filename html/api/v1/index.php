<?php
// error_reporting(E_ALL | E_STRICT);
// ini_set('display_errors', 1);
/* Allowed Inputs */
$ext = (isset($_POST['extension'])) ? '.' . substr(preg_replace('/[^a-zA-Z0-9]/','',$_POST['extension']),0,25) : '';
$subdomain = (isset($_POST['subdomain']) && in_array($_POST['subdomain'],array('i','self','www'))) ? $_POST['subdomain'] . '.' : '';
$input_url = (isset($_POST['url'])) ? clean_url(trim($_POST['url'])) : '';
$rest_file = trim($_GET['q']);

if($rest_file == '/api/v1/shorten.html'){$rest_file = 'html';}
else if($rest_file == '/api/v1/shorten.json'){$rest_file = 'json';}
else{$rest_file = false;}

$url_parts = parse_url($input_url);
$scheme_good = (isset($url_parts['scheme']) && in_array($url_parts['scheme'],array('http','https','shttp','ssl','spdy')));
$not_imyur = (isset($url_parts['host']) && strrpos($url_parts['host'],'imyur.com') === false);

$output = array();
$success = false;

/* Functions */
function clean_url($url){
	$url = preg_replace('|[^a-z0-9-~+_.?\[\]\^#=!&;,/:%@$\|*\'"()\\x80-\\xff]|i','',$url);
	$strip = array('%0d','%0a','%0D','%0A');
	$url = deep_replace($strip,$url);
	$url = str_replace(';//','://',$url);
	$url = str_replace('&amp;','&',$url);
	return $url;
	}

function deep_replace($search,$subject){
	$found = true;
	while($found){
		$found = false;
		foreach((array)$search as $val){
			while(strpos($subject,$val) !== false){
				$found = true;
				$subject = str_replace($val,'',$subject);
				}
			}
		}
	return $subject;
	}

function counter(){
	$prev = (int)file_get_contents('/home/www/imco.txt');
	$cur = $prev + 1;
	file_put_contents('/home/www/imco.txt', $cur);
	return gmp_strval(gmp_init($cur + 16000000,10),62);
	}

function save_url($input_url){
	$hash = counter();
	require_once 'AWSSDKforPHP/sdk.class.php';
	$sdb = new AmazonSDB();
	$response = $sdb->put_attributes('addresses', $hash, array('address' => $input_url), true);
	$awssuccess = $response->isOK();
	if($awssuccess){
		apc_add($hash,$input_url,0);
		return array($awssuccess,$hash);
		}
	else{
		return array($awssuccess);
		}
	}

/* Core conditional logic */
if($scheme_good && $not_imyur){
	$safe_lookup = file_get_contents('https://sb-ssl.google.com/safebrowsing/api/lookup?client=imyur&appver=1.0&apikey=ABQIAAAA8mLG1wxBrySac59O6cUIzhT3haXetYFvqARH2WifqKz48noHcg&pver=3.0&url=' . urlencode($input_url));
	if($http_response_header[0] == 'HTTP/1.0 204 No Content' || substr($http_response_header[0],0,12) == 'HTTP/1.0 503'){
		$save_attempt = save_url($input_url);
		if($save_attempt[0]){
			$success = true;
			$output['hash'] = $save_attempt[1];
			}
		else{
			$output['error'] = 'failed to save to db';
			}
		}
	else if(substr($http_response_header[0],0,12) == 'HTTP/1.0 200'){
		$output['error'] = $safe_lookup;
		}
	else{
		$output['error'] = $http_response_header[0];
		}
	}
else{
	$output['error'] = 'failed basic URL validation';
	}

/* Output */
if($success && $rest_file == 'html'){
	echo 'Your shortened link is <a href="http://' . $subdomain . 'imyur.com/' . $output['hash'] . $ext . '">http://' . $subdomain . 'imyur.com/' . $output['hash'] . $ext . '</a>';
	}
else if($success && $rest_file == 'json'){
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($output);
	}
else if(!$success && isset($output['error']) && $rest_file == 'html'){
	header("HTTP/1.0 400 Bad Request");
	echo 'There was a problem with your request.<br /><strong>Error output</strong>: ' . $output['error'];
	}
else if(!$success && isset($output['error']) && $rest_file == 'json'){
	header("HTTP/1.0 400 Bad Request");
	header("Content-Type: application/json; charset=UTF-8");
	echo json_encode($output);
	}
else{
	header("HTTP/1.0 404 Not Found");
	}