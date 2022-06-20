<?php
if(file_exists($custom = (__DIR__.'/custom.php'))) @include($custom);

if(!defined("PDC_DBBASE")) define("PDC_DBBASE", ($projectname = 'blank'));

if(!defined("SIGNATURE")) define("SIGNATURE", ($_SERVER['SIGNATURE'] = "[A SIGNATURE]"));

if(!defined("REPODIR")) define("REPODIR", __DIR__);

if(!defined('PDC_DBHOST')) define('PDC_DBHOST', ($dbhost ?? ''));
if(!defined('PDC_DBPORT')) define('PDC_DBPORT', ($dbport ?? ''));
if(!defined('PDC_DBUSER')) define('PDC_DBUSER', ($dbuser ?? ''));
if(!defined('PDC_DBPASS')) define('PDC_DBPASS', ($dbpass ?? ''));

spl_autoload_register(function($class) {
  $recursive = function($dir,$class,$r){
    if(file_exists($file = ("$dir/$class.php"))) return @include_once($file);
    if(is_array($list = scandir($dir)))
        foreach($list as $item)
            if($item !== '.' && $item !== '..')
                if(is_dir($newdir = realpath($dir.'/'.$item)))
                    if($r($newdir, $class, $r)) return; }; 
  $recursive((__DIR__ .'/modules/'), $class, $recursive);
  return (class_exists($class, false));
});

if(!class_exists('pdoclass',true)) exit('Could not load database module');
if(!class_exists('globals',true)) exit('Could not load global functions');

if(isset($_REQUEST['autocreatedbtables']))
  if(function_exists('pdo_autoconfig')) exit(pdo_autoconfig());

if(!empty($env = ($_SERVER['SERVER_NAME'] ?? '')))
  if(strpos($env,'localhost') !== false
  ||(strpos($env,'dev') !== false))
    $_SERVER['DEVELOPMENT'] = true;

if(strpos(($_SERVER['REQUEST_URI'] ?? ''),'api/'))
  register_shutdown_function(function(){
    if(!isset($_SERVER['result'])) result(-1);
  });

if(($_REQUEST['api'] ?? '') === 'automodule') {
  define('fromapi', true);
  if(empty($c = preg_replace('/[^a-zA-Z]/','',($_REQUEST['c'] ?? ($_REQUEST['module'] ?? ''))))) result(-406);
  if(empty($f = preg_replace('/[^a-zA-Z\_]/','',($_REQUEST['f'] ?? ($_REQUEST['method'] ?? ''))))) result(-406);
  if(class_exists('auth') 
  &&(method_exists('auth','isauthed')))
    if(($user = new auth()) && (!$user::isauthed()))
            if(!in_array($f,($user::$excludemethods ?? [])))
                result(['result'=>-403, 'token'=>($_COOKIE['actk'] ?? null)]);
  if($_SERVER['DEVELOPING'] ?? false) if(class_exists('wiki', true)) $documentation = new wiki;
  if(class_exists($c, true) 
  &&(method_exists($c,$f) || method_exists($c,'__callStatic')))
      if(($v = new $c) || true)
          result((@$v::$f($_REQUEST) ?? []),true,true,(($_SERVER['DEVELOPING'] ?? false) ? ['request'=>$_REQUEST] : null));
}

@date_default_timezone_set('America/Sao_Paulo');
@session_start();
?>