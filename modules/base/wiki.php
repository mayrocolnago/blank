<?php
class wiki {

    private static $overwrite = true;

    public static $replaces = [
        "from" => ['asaas'],
        "to"   => ['pagamento']
    ];

    public function __construct() {
        if(empty($c = preg_replace('/[^a-zA-Z]/','',($_REQUEST['c'] ?? ($_REQUEST['module'] ?? ''))))) return;
        if(empty($f = preg_replace('/[^a-zA-Z\_]/','',($_REQUEST['f'] ?? ($_REQUEST['method'] ?? ''))))) return;

        if(empty($headpath = str_ireplace(self::$replaces["from"],self::$replaces["to"], (explode('?',($_SERVER['REQUEST_URI'] ?? '').'?')[0] ?? '')))) return false;
        if($headpath == '/api/module') return false;
        $params = [];
        $urlres = explode('/',$headpath);
        foreach($_REQUEST as $k => $v)
            if(!in_array($v,$urlres) && $k !== 'forcewiki')
                $params[$k] = gettype($v);
        $hash = md5(preg_replace('/[^a-z]/','',substr($headpath,0,25)));
        $qtdp = count($params);
        $para = $params;
        $titl = $desc = null;
        
        if(is_array($gcv = get_class_vars($c)))
            $description  = ($titl = ($gcv['description'][$f]['title'] ?? ''))
                          . ($desc = ($gcv['description'][$f]['desc'] ?? ''));

        $api = [
            'uri'=>$headpath,
            'hash'=>$hash,
            'path'=>REPODIR."/wikis/",
            'class'=>str_ireplace(self::$replaces["from"],self::$replaces["to"],$c),
            'method'=>$f,
            'countparams'=>$qtdp,
            'params'=>$para,
            'title'=>($titl ?? ''),
            'desc'=>($desc ?? '')
        ];

        return self::startcapture($api);
    }


    private function startcapture($id) {
        if(empty($_SERVER['WIKI_CAPTUREID'] = ($id ?? ''))) return;
        register_shutdown_function(function(){ wiki::getresponse(); });
        @ob_start();
    }


    private function prettyjson($array) {
        if(is_array($array)) $array = json_encode($array);
        $array = str_replace("{","{\n",$array);
        $array = str_replace("}","\n}",$array);
        $array = str_replace(",\"",",\n\"",$array);
        $array = explode("\n", $array); $esp = 0;
        foreach($array as &$item) {
            if(strpos($item,'}') !== false) $esp--;
            for($i=0;$i<=$esp;$i++) $item = '  '.$item;
            if(strpos($item,'{') !== false) $esp++; }
        $array = implode("\n",$array);
        return $array;
    }


    public function generatesidebar($dir) {
        if(!function_exists('listmodules')) return;
        if(is_array($dir)) $dir = ($dir['dir'] ?? './');
        $modules = listmodules();
        $file = "[`Home`](home)\n"; $buffer = '';
        foreach($modules as $m) 
            if($m !== 'wiki') { 
                $buffer = ''; 

                if(!class_exists($m)) $fs = []; else
                    if(!(@$fsr = new ReflectionClass($m))) $fs = get_class_methods($m); 
                    else { $fs = [];
                        foreach($fsr->getMethods(ReflectionMethod::IS_PUBLIC) as $fsq)
                            $fs[] = $fsq->name; }

                $m = str_ireplace(self::$replaces["from"],self::$replaces["to"],$m);
                if(is_array($fs))
                    foreach($fs as $f)
                        if($f !== '__construct' && $f !== '__callStatic' && $f !== 'database'
                        &&($f !== 'appcss' && $f !== 'apphtml' && $f !== 'appjs' && $m !== 'globals'))
                            if(!file_exists($dir."$m/$f.md")) $buffer .= "- $f\n";
                            else $buffer .= "- [`$f`]($m/$f)\n"; 
                if(!empty($buffer))
                    $file .= "\n**".ucfirst($m)."**\n\n$buffer"; }
        return @file_put_contents($dir.'_sidebar.md', $file);
    }


    public static function getresponse() {
        if(empty($wc = ($_SERVER['WIKI_CAPTUREID'] ?? '')) || (!is_array($wc))) return;
        $response = @json_decode(@ob_get_contents(), true); $result = [];
        foreach($response as $k => $v)
            if($k !== 'request')
                if(!in_array($k,['data'])) $result[$k] = gettype($v);
                else if(gettype($v) !== 'array') $result[$k] = $v;
                    else foreach($v as $sk => $vk)
                            if(is_array($result[$k] = ($result[$k] ?? [])) && (!empty($sk)) && (!empty($vk)))
                                if(!(is_numeric($sk) && gettype($vk) == "array"))
                                    $result[$k][$sk] = gettype($vk);
                                else {
                                    $result[$k] = [];
                                    $arrayofarray = [];
                                    foreach($vk as $msk => $msv)
                                        if(!is_numeric($msk))
                                            $arrayofarray[$msk] = gettype($msv);
                                    $result[$k][] = $arrayofarray; }
        
        //if(intval($response['result'] ?? 0) <= 0) return @ob_end_flush();

        $file = "# Método\n\n- `".$wc['uri']."`\n";
        //$file .= "- *".$wc['hash']."*\n";

        if(!empty($wc['title'])) $file .= "\n".$wc['title']."\n-\n";
        if(!empty($wc['desc'])) $file .= "\n".$wc['desc']."\n";

        if($wc['countparams'] <= 0)
            $file .= "\n> Este método não requer parâmetros de entrada\n";
        else {
            $file .= "\n# Request\n";
            $file .= "\n| Parâmetro | Tipo |\n| ------ | ------ |\n";
            foreach($wc['params'] as $k => $v) $file .= "| $k | $v |\n"; }

        $file .= "\n# Response\n\n```json\n".self::prettyjson($result)."\n```\n\n";
        $renw = false;

        if(!is_dir($scriptpath = $wc['path'].'/'.$wc['class']))
            if(@mkdir($scriptpath, 0777, true))
                $renw = true;

        if(!file_exists($filepath = ($scriptpath."/".$wc['method'].".md"))) $renw = true;
        else if(!self::$overwrite) return @ob_end_flush();

        @file_put_contents($filepath, $file);
        
        if($renw) self::generatesidebar($wc['path']);

        @ob_end_flush();
    }

} 
?>