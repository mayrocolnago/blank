<?php
@include(__DIR__ .'/../config.inc.php');

$request = str_ireplace('/storage','',$_SERVER['REDIRECT_URL'] ?? '');
$strtotime = strtotime('now');

function readfromoriginal() {
    $url = "http://storage.".($_SERVER['GROUP'] ?? 'dev').".infoss.com.br/".str_replace('/storage','',($_SERVER['REDIRECT_URL'] ?? ''));
    exit(curlsend($url, null, 15));
}

function get_mime_type($filename) {
  $fileext = substr(strrchr($filename, '.'), 1);
  if (empty($fileext)) return (false);
  $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
  $lines = file(__DIR__ ."/mime.types");
  foreach($lines as $line) {
     if (substr($line, 0, 1) == '#') continue;
     $line = rtrim($line) . " ";
     if (!preg_match($regex, $line, $matches)) continue;
     return ($matches[1]);
  }
  return (false);
}

http_response_code(200);
header('Access-Control-Allow-Origin: *');
header('Content-Type: '.get_mime_type($request));

if(!(pdoclass::$con ?? false)) 
  readfromoriginal();

if(empty($h = ($file = pdo_fetch_item(pdo_query("SELECT * FROM vault_files WHERE filepath='$request' LIMIT 1")))['hashcheck'] ?? ''))
  readfromoriginal();

pdo_query("UPDATE vault_files SET lastseen='$strtotime' WHERE id='".$file['id']."' LIMIT 1");

if(empty($i = ($hash = pdo_fetch_item(pdo_query("SELECT * FROM vault_hashes WHERE hashcheck='$h' LIMIT 1")))['id'] ?? ''))
  readfromoriginal();

exit($hash['content'] ?? '');
?>