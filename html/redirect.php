<?php

// ini_set('display_errors', 1);

$path = substr(trim($_GET['q']), 1);
$pos = strpos($path, '.');

if ((!$pos && strlen($path) == 5) || $pos == 5) {
  $hash = ($pos) ? substr($path, 0, $pos) : $path;
  $from_apc = apc_fetch($hash, $apc_success);

  if ($apc_success && $from_apc != "no") {
    header('Server: ');
    header("Location: $from_apc");
  }
  else {
    if ($apc_success && $from_apc == "no") {
      header("HTTP/1.0 404 Not Found");
    }
    else {
      if (preg_match("/^[a-zA-Z0-9]+$/", $hash)) {
        require_once 'AWSSDKforPHP/sdk.class.php';
        $sdb = new AmazonSDB();
        $response = $sdb->get_attributes('addresses', $hash, 'address');
        $success = $response->isOK();
        if ($success) {
          $url = (string) $response->body->GetAttributesResult->Attribute->Value;
          //print_r($response);
          if ($url) { // TODO: Perhaps there's a better way to confirm a result from sub
            apc_add($hash, $url, 0);
            header("Location: $url");
          }
          else {
            apc_add($hash, "no", 0);
            header("HTTP/1.0 404 Not Found");
          }
        }
        else {
          apc_add($hash, "no", 0);
          header("HTTP/1.0 404 Not Found");
        }
      }
      else {
        header("HTTP/1.0 404 Not Found");
      }
    }
  }
}
else {
  header("HTTP/1.0 404 Not Found");
}
