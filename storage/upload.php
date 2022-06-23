<?php
if(isset($_REQUEST['pingchk']))
  exit(json_encode([
    'result'=>strtotime('now'),
    'header'=>header('Content-Type: application/json'),
    'policy'=>header('Access-Control-Allow-Origin: *'),
    'modules'=>@include(__DIR__ .'/../config.inc.php'),
    'host'=>($_SERVER['STORAGEURL'] ?? ('https://'.($_SERVER['SERVER_NAME'] ?? 'storage.infoss.com.br').'/'))
  ]));

@session_start();
@ob_start();

@ini_set('upload_max_filesize', '99M');
@ini_set('post_max_size', '99M');

@ini_set('display_errors', '1');
@error_reporting(1);

@header('Access-Control-Allow-Origin: *');

if(strpos(($_SERVER['REDIRECT_URL'] ?? ''),'/upload.js') !== false) {

  @header("Cache-Control: max-age=604800");
?>
/*###############################################################
##           ## INFOS UPLOAD SCRIPT WITH QUEUE ##              ##
##                     ### USAGE ###                           ##
#################################################################
  Setup a form with a file input:
  
  <form><input type="file" id="file"></form>
  $(document).ready(... //After JQuery initialization
  
    var onstart = function(data){ //imediatly exec when a file is being uploaded };
    var ondone = function(data){ //execute as soon as the file finishes upload };
  
    bindupload('#file',{ 'f':'name_sufix', 'p':'path/' [, 'token':'for-permanent-tokens'] }, onstart, ondone);
#################################################################
*/
<?php exit(str_replace(["\n","\r","  "], "", 
           str_replace('[SERVER_NAME]', ($_SERVER['SERVER_NAME'] ?? 'localhost').'/storage', 
           @file_get_contents(__DIR__ .'/upload.js') )) );
} else
  @header('Content-Type: application/json');

function exitcod($result=null,$msg='') { 
  $buffer = @ob_get_contents();
  @ob_end_clean();
  exit(json_encode([
    'result'=>$result,
    'group' => ($_SERVER['GROUP'] ?? null),
    'url' => (($server = 'http'.((!empty($_SERVER['HTTPS'] ?? '')) ? 's':'').'://'.($_SERVER['SERVER_NAME'] ?? 'localhost').'/storage/').$result),
    'server' => $server,
    'buffer' => $buffer,
    'msg' => $msg
  ]));
}

function getext($name = '') {
  if(isset($_REQUEST['e'])) return '.'.substr(preg_replace('/[^a-zA-Z0-9]/','',$_REQUEST['e']),0,9);
  $ext = (explode('.','.download'.$name)); $exti = intval(count($ext) - 1);
  $ext = ($ext[$exti] ?? '');
  $ext = (preg_replace('/[^a-zA-Z0-9\.]/','',((empty($ext)) ? '.txt' : '.'.$ext))); 
  while (strpos($ext,'..') !== false) { $ext = str_replace('..','.',$ext); }
  return str_replace('.download','',substr($ext,0,9)); }

function storefile($filename, $content) {
    $checksum = hash('sha256', $content);
    $strtotime = strtotime('now');
    $uid = ((isset($_COOKIE['uid'])) ? (intval($_COOKIE['uid']) - 999) : '');
    //verifica se a referencia do arquivo existe. se nao, insere
    if(!empty($exist = ($query = pdo_fetch_item(pdo_query(
        "SELECT * FROM vault_files WHERE hashcheck='$checksum' order by id desc limit 1"
      )))['filepath'] ?? ''))
        $filename = str_replace(' ','/',trim(str_replace('/',' ',$exist)));
    else
       pdo_query("INSERT INTO vault_files (filepath,hashcheck,lastseen,lastchange,registered,uid) ".
                 "VALUES ('/$filename', '$checksum', '0', '0', '$strtotime', '$uid') ");
    if(pdo_query("UPDATE vault_hashes SET lastcheck='$strtotime' WHERE hashcheck='$checksum' order by id desc limit 1") < 1)
        pdo_query("INSERT INTO vault_hashes (hashcheck,registered,lastcheck,content) VALUES ('$checksum','$strtotime', '0', ?)",[$content]);
    exitcod($filename);
}

$source = (isset($_REQUEST['f'])) ? preg_replace('/[^0-9a-zA-Z\-\_]/','',$_REQUEST['f']) : 'non';
$path = (isset($_REQUEST['p'])) ? preg_replace('/[^a-z\/\_]/','',$_REQUEST['p']) : '';
$nome = ($source . '_' . ((isset($_REQUEST['n'])) ? preg_replace('/[^0-9a-zA-Z\-\_]/','',$_REQUEST['n']) : uniqid(strtotime('now'))));

try { if(strlen($path) > 0) {
  if($path[strlen($path)-1] != '/') $path[strlen($path)] = '/';
  if($path[0] == '/') $path = substr($path,1,strlen($path)); }
} catch(Exception $e) { $path = ''; }

while (strpos($path,'__') !== false) { $path = str_replace('__','_',$path); }
while (strpos($path,'//') !== false) { $path = str_replace('//','/',$path); }

if(strlen(trim($path)) <= 4) $path = 'root/';


//conecta-se com o banco de destino
@include(__DIR__ .'/../config.inc.php');

if(!(pdoclass::$con ?? false)) exitcod('', 'database not conected');
else {
    pdo_query("CREATE TABLE IF NOT EXISTS vault_hashes (
	      id bigint(20) NOT NULL AUTO_INCREMENT,
        hashcheck longtext NOT NULL,
        content longblob NULL DEFAULT NULL,
        lastcheck bigint(20) NOT NULL,
        registered bigint(20) NOT NULL,
		  PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci");

    pdo_query("CREATE TABLE IF NOT EXISTS vault_files (
	      id bigint(20) NOT NULL AUTO_INCREMENT,
        uid varchar(50) NULL DEFAULT NULL,
        filepath longtext NOT NULL,
        hashcheck longtext NOT NULL,
        lastseen bigint(20) NOT NULL,
        lastchange bigint(20) NOT NULL,
        registered bigint(20) NOT NULL,
		  PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci"); }


//metodo via download de url: ?download=1&file=http://remote/stream
if((isset($_REQUEST['download'])) && (isset($_REQUEST['file']))) {
  $novonome = ($path . ($nome .= getext($_REQUEST['f'] ?? $_REQUEST['file'])));
  if(!($fp_remote = @fopen($_REQUEST['file'], 'rb'))) exitcod('','error reading remote file');
  if(!($fp_local = @fopen($df = "/tmp/".$nome, 'wb'))) exitcod('','error writing local file');
  while($buffer = @fread($fp_remote, 8192)) @fwrite($fp_local, $buffer);
  @fclose($fp_remote); @fclose($fp_local);
  if(!file_exists($df)) exitcod('','error downloading. probably /tmp/ permission issue');
  else storefile($novonome, @file_get_contents($df));

  //metodo via copia de url externa: ?fromurl=1&file=http://remote/image.png
} else
  if((isset($_REQUEST['fromurl'])) && (isset($_REQUEST['file']))) {
    $novonome = ($path . ($nome .= getext($_REQUEST['f'] ?? $_REQUEST['file'])));
    if(!(@copy($_REQUEST['file'], ($df = "/tmp/".$nome)))) exitcod('','error. copy not completed');
    else storefile($novonome, @file_get_contents($df));

    //metodo via base64: ?base64=1&file=base64:file;ABCdefGHT123==
  } else
    if((isset($_REQUEST['base64'])) && (isset($_REQUEST['file']))) {
      $novonome = ($path . ($nome .= getext($_REQUEST['f'] ?? $_REQUEST['file'])));
      list($tipo, $dados) = explode(';', $_REQUEST['file']);
      list(, $tipo) = explode(':', $tipo);
      list(, $dados) = explode(',', $dados);
      $arquivo_tmp = base64_decode($dados);
      storefile($novonome, $arquivo_tmp);

      //metodo via escrita direta: ?getfile=1&file=content
    } else
      if((isset($_REQUEST['getfile'])) && (isset($_REQUEST['file']))) {
        $novonome = ($path . ($nome .= getext($_REQUEST['f'] ?? $_REQUEST['file'])));
        storefile($novonome, $_REQUEST['file']);

        //metodo via form upload
      } else
        if((isset($_FILES['file'])) && (!empty(@$_FILES['file']))) {
            $novonome = ($path . ($nome .= getext($_FILES['file']['name'] ?? '')));
            if(!file_exists($df = ($_FILES['file']['tmp_name'] ?? './void'))) exitcod('', 'error. file not found on tmp_dir '.$df);
            else storefile($novonome, @file_get_contents($df));

        } else
          exitcod('', 'error. no file found on parameters');

register_shutdown_function('exitcod');
?>