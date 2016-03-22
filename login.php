<?php

$username = $env['email'];
$password = $env['password']; 
echo "Hello <br/>";
#############

$base = "https://www.buxfer.com/api";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");  

$url = "$base/login?userid=$username&password=$password";
curl_setopt($ch, CURLOPT_URL, $url);
$response = json_decode(curl_exec($ch), true);
checkError($response);
$token = $response['response']['token'];
// print_r($token);
// $token = 'g3na91ibnmgr2h2gmiinqi2vh3';
$url = "$base/contacts?token=$token";
curl_setopt($ch, CURLOPT_URL, $url);
$contacts = json_decode(curl_exec($ch), true);
checkError($contacts);
$contacts = $contacts ['response'];
echo "<pre>";
print_r($contacts);
echo "</pre>";

$url = "$base/loans?token=$token";
curl_setopt($ch, CURLOPT_URL, $url);
$response = json_decode(curl_exec($ch), true);
checkError($response);
$response = $response ['response'];
// print_r($response);
foreach ($response ['loans'] as $loans) { 
    if (strcmp($loans['key-loan']['description'],"you receive") == 0) {
        $receive[] = $loans['key-loan'];
    }
    elseif (strcmp($loans['key-loan']['description'],"you owe") == 0) {
        $pay[] = $loans['key-loan'];
    }
}
echo "<pre>";
print_r($pay);
echo "</pre>";

echo "<pre>";
print_r($receive);
echo "</pre>";



exit(0);

function checkError($response) {
  if (!isset($response['error'])) {
    return;
  }

  $error = $response['error']['message'];

  $stderr = fopen("php://stderr", "w");
  fprintf($stderr, "An error occured: %s\n", $error);
  fflush($stderr);
  exit(1);
}
?>