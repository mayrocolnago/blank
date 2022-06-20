<?php
if((!defined('PDC_DBHOST')) || (!defined('PDC_DBUSER')) || (!defined('PDC_DBBASE'))) $_SERVER['autocreatedbcheck'] = '0';

if(!isset($_SERVER['autocreatedbcheck'])) { $_SERVER['autocreatedbcheck'] = '1'; 
    if(isset($_REQUEST['autocreatedbtables'])) echo "<!-- Updating database... -->\r\n";

    if($rc = new pdoclass()) $rc::pdo_query("CREATE DATABASE IF NOT EXISTS `".PDC_DBBASE."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");

    if(pdo_connect(PDC_DBHOST.':'.PDC_DBPORT, PDC_DBUSER, PDC_DBPASS, PDC_DBBASE))
        if(function_exists('listmodules'))
            if(is_array($modules = listmodules()))
                foreach($modules as $m)
                    if(!empty($f = "$m::database"))
                        if(class_exists($m, true) && (method_exists($m,'database')) && ($f() ?? true))
                            if(isset($_REQUEST['autocreatedbtables'])) 
                                echo "<!-- Applying database for $m module... -->\r\n";

    if(isset($_REQUEST['autocreatedbtables'])) echo "<!-- Database Updated. -->\r\n";
}
?>