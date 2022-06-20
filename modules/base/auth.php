<?php
class auth {

    public static $uid = "";
    public static $token = "";

    public static $users = [];
    private static $classloaded = false;
    private static $usersloaded = false;

    private static $secret = "[A SECRET]";

    public static $excludemethods = ['resources','isauthed','createaccount','login','logout'];

    public function database() {
        pdo_query("CREATE TABLE IF NOT EXISTS users (
            uid bigint(20) NOT NULL AUTO_INCREMENT,
            ativo tinyint(1) DEFAULT '1',
            login varchar(200) NULL DEFAULT NULL,
            senha longtext NULL,
            nome varchar(200) NULL DEFAULT NULL,
            tel varchar(200) NULL DEFAULT NULL,
            email varchar(200) NULL DEFAULT NULL,
            configs json NULL,
            devices json NULL,
            permission json NULL,
            lastseen bigint(20) NULL DEFAULT NULL,
            created bigint(20) NULL DEFAULT NULL,
            PRIMARY KEY (uid),
            UNIQUE KEY login (login))");
    }

    public function __construct() {
        self::$classloaded = true;
        //caso esteja logando
        if(isset($_REQUEST['login']) && isset($_REQUEST['passw']))
          return self::login($_REQUEST['login'], $_REQUEST['passw']);
        //caso já tenha um token, validar
        if(empty($actk = $token = ($_REQUEST['actk'] ?? ($_COOKIE['actk'] ?? '')))) return -2;
        if(!is_array($actk = explode('_',$actk))) return -3;
        if(count($actk) !== 2) return -4;
        if(!is_numeric($uid = $actk[0])) return -5;
        if(empty($hash = base64_decode($actk[1]))) return -6;
        if(!is_array($infos = pdo_fetch_row(pdo_query("select * from users where uid='$uid' and ativo='1' limit 1")))) return -7;
        if(empty($infos['senha'] ?? '')) return -8;
        if(!password_verify(md5($infos['senha'].((!($_SERVER['DEVELOPING'] ?? false)) ? filtereduseragent() : '').self::$secret), $hash)) return -9;
        if((!($_SERVER['DEVELOPING'] ?? false)) && is_array($d = validatearray($infos['devices'] ?? '')) && empty($d[strval(filtereduseragent())][$token]['lastseen'] ?? 0)) return -498;
        //salvar informações sobre a sessão atual
        return self::getmyinfos($infos, (self::$token = $token));
    }

    public static function listusers($someone=null,$force=false) {
        if(self::$usersloaded && !$force) return true;
        else self::$usersloaded = true;
        if(is_array($all = pdo_fetch_array(pdo_query("select * from users ".(((!empty($someone)) && is_numeric($someone)) ? "where uid='$someone'" : "")))))
            foreach($all as $infos)
                if(!empty($infos['uid'] ?? '')) 
                    if(is_array($infos['configs'] = validatearray($infos['configs'])))
                        if(is_array($infos['devices'] = validatearray($infos['devices'])))
                            self::$users[strval($infos['uid'])] = $infos;
        return (!empty(self::$users[1] ?? ''));
    }

    public static function isauthed() {
        if(empty($result = self::getuser('uid'))) return false;
        return [
            'result' => true,
            'uid'    => intval(self::$uid),
            'token'  => self::$token ];
    }

    public static function setuser($key=null,$value=null,$from='me') {
        if(!self::$classloaded) self::__construct();
        if(is_array($key) && is_array($r = [])) {
            if(isset($key['uid'])) 
                if(self::getuser('_admin') === "") return false;
                else $from = $key['uid'];
            foreach($key as $k => $v)
                if((!(strpos($k, '_') !== false)) && !empty($k = preg_replace('/[^0-9a-z]/','',strtolower($k))))
                    if(strlen($k) > 3 && $k !== 'module' && $k !== 'method' && $k !== 'configs' && $k !== 'devices' && $k !== 'senha')
                        $r[] = self::setuser($k, $v, $from); return $r; }
        if($from !== 'me') self::listusers();
        else $from = (self::$uid ?? '');
        if(empty($from)) return '';
        if(!is_numeric($from)) return '';
        if(!is_array(self::$users[strval($from)] = validatearray(self::$users[strval($from)] ?? ''))) return '';
        if(!is_array(self::$users[strval($from)]['configs'] = validatearray(self::$users[strval($from)]['configs'] ?? ''))) return '';
        if(!is_array(self::$users[strval($from)]['devices'] = validatearray(self::$users[strval($from)]['devices'] ?? ''))) return '';
        if(empty($key = preg_replace('/[^A-Za-z0-9\_]/','',$key))) return '';
        if($key === 'senha') $value = hash('sha512', $value);
        if(isset(self::$users[strval($from)][$key]))
            return ((pdo_query("update users set $key=:value where uid='$from' limit 1",[ 'value' => ((is_array(self::$users[strval($from)][$key] = $value))?json_encode($value):$value) ]) > -1) ? $value : "");
        self::$users[strval($from)]['configs'][$key] = $value;
        return ((pdo_query("update users set configs=:values where uid='$from' limit 1",[ 'values' => json_encode(self::$users[strval($from)]['configs']) ]) > -1) ? $value : "");
    }

    public static function getuser($key=null,$from='me') {
        if(!self::$classloaded) self::__construct();
        if($from !== 'me') self::listusers();
        else $from = self::$uid;
        if(empty($key) || is_array($key)) return (self::$users[strval($from)] ?? '');
        return (self::$users[strval($from)][$key] 
            ?? (self::$users[strval($from)]['configs'][$key] 
            ?? ('')));
    }

    private function getmyinfos($infos,$actk=null) {
        if(empty($uid = self::$uid = ($infos['uid'] ?? ''))) return -10;
        //valida se as chaves sao arrays
        if(!is_array($infos = validatearray($infos))
        || !is_array($infos['configs'] = validatearray($infos['configs'] ?? ''))
        || !is_array($infos['devices'] = validatearray($infos['devices'] ?? ''))
        || !is_array($infos['devices'][strval(filtereduseragent())] = validatearray($infos['devices'][strval(filtereduseragent())] ?? ''))) return -11;
        //preenche a sessao de usuario e atribui push tokens se houver
        if(!empty($actk) && is_array($infos['devices'][strval(filtereduseragent())][$actk] = validatearray($infos['devices'][strval(filtereduseragent())][$actk] ?? ''))) {
            $infos['devices'][strval(filtereduseragent())][$actk]['lastseen'] = ($now = strtotime('now'));
            if(!empty($pushid = ($_REQUEST['pushid'] ?? ($_COOKIE['pushid'] ?? null)))) $infos['devices'][strval(filtereduseragent())][$actk]['pushid'] = $pushid; }
        //atribui todos dados ao usuario
        self::$users[strval($uid)] = []; foreach($infos as $k=>$v) if(!is_numeric($k)) self::$users[strval($uid)][$k] = $v;
        self::$users[strval($uid)]['senha'] = substr((self::$users[strval($uid)]['senha'] ?? ''),0,50);
        self::$users[strval($uid)]['lastseen'] = ($now = strtotime('now'));
        pdo_query("update users set lastseen=:ls ,devices=:dv where uid='$uid' ", ['ls'=>$now, 'dv'=>json_encode(self::$users[strval($uid)]['devices'])]);
        return self::isauthed();
    }

    private function authenticate($infos,$storesession=true) {
        if(empty($uid = ($infos['uid'] ?? ''))) return -12;
        if(empty($hash = ($infos['senha'] ?? ''))) return -13;
        self::$token = $_REQUEST[($tokenkey = 'actk')] = savecookie($tokenkey, ($actk = ($uid."_".base64_encode(password_hash(md5($hash.((!($_SERVER['DEVELOPING'] ?? false)) ? filtereduseragent() : '').self::$secret), PASSWORD_DEFAULT)))) );
        return self::getmyinfos($infos,(($storesession) ? $actk : null));
    }

    public function impersonate($uid, $lock=true) {
        if(self::getuser('admin') == '' && $lock) return -401;
        if(is_array($uid)) $uid = ($uid['uid'] ?? '');
        if(empty($uid)) return -400;
        if(!is_array($infos = pdo_fetch_row(pdo_query("select * from users where uid='$uid' and ativo='1' limit 1")))) return -404;
        return self::authenticate($infos,false);
    }

    public static function login($user,$pass='mudar123',$encode=true) {
        if(is_array($user)) return self::isauthed();
        if(strpos($user,'@') !== false && (self::$permitmail ?? false)) $user = (explode('@',$user)[0] ?? '');
        if(empty($user = preg_replace('/[^0-9a-zA-Z\-\.\_\@]/','',$user))) return -14;
        if(empty($phrs = (($encode) ? hash('sha512',$pass) : $pass))) return -15;
        if(!is_array($infos = pdo_fetch_row(pdo_query("select * from users where (login='$user' or email='$user' or tel='$user' or configs->>'$.doc'='$user') and senha='$phrs' and ativo='1' limit 1")))) return -403;
        return self::authenticate($infos);
    }

    public static function user_exists($login=[]) {
        if(is_array($login))
            if(isset($login['username']))
                return self::user_exists($login['username']);
            else foreach($login as $k => $v)
                 if(strlen($k) > 3 && $k !== 'module' && $k !== 'method')
                    if(self::user_exists($k))
                        return true;
        return (!empty(pdo_fetch_row(pdo_query("select uid from users where login like '$login' limit 1"))['uid'] ?? ''));
    }

    public static function getloginfromname($name='Novo usuario') {
        if(is_array($name) && isset($name['name'])) $name = $name['name'];
        if(!(is_array($aname = explode(" ",($name = strtolower(rmA($name))))) && count($aname) > 1 && is_numeric($i = 1))) 
            return preg_replace('/[^0-9a-z]/','',strtolower($name));
        if(self::user_exists($name = (($aname[0].$aname[count($aname)-1]))))
            if(self::user_exists($name = (($aname[0].$aname[count($aname)-2]))))
                while (self::user_exists($name = (($aname[0].$aname[count($aname)-1]).strval($i)))) $i++;
        return $name;
    }

    public static function createaccount($user=[],&$passunhashed=null,$autologin=true) {
        //se veio nome completo, gerar login e verificar se existe no banco
        if(is_string($user) && strpos($user,' ') !== false) $user = ['nome'=>$user, 'login'=>self::getloginfromname($user)];
        //verifica se a veriavel e um array de dados
        if(!is_array($user)) $user = ["login"=>$user];
        //verificar configuracao de autologin
        if(isset($user['noautologin'])) $autologin = false;
        //corrige qualquer informacao incorreta nos nomes de chave da variavel
        $previous = $user; $user = [];
        $convert = [
            'credential' => 'login', 'credencial' => 'login', 'username' => 'login', 'usuario' => 'login', 'acesso' => 'login', 'auth' => 'login', 'fullname' => 'nome', 'name' => 'nome',
            'mail' => 'email', 'passw' => 'senha', 'keypass' => 'senha', 'password' => 'senha', 'keyphrase' => 'senha', 'registered' => 'created' ];
        $known = [
            'uid','ativo','login','senha','nome','tel','email','configs','devices','lastseen','created' ];
            
        $user['configs'] = (validatearray($user['configs'] ?? []));
        $user['devices'] = (validatearray($user['devices'] ?? []));
        foreach($previous as $k=>$v)
            if(!is_numeric($k) && !empty($c = preg_replace('/[^0-9a-z]/','',strtolower($k))))
                if(in_array(($c = ($convert[$c] ?? $c)),$known)) $user[$c] = $v;
                else if(!in_array($c, ['api','module','method'])) $user['configs'][$c] = $v;
        
        //identifica e cria a senha
        if(!isset($user["senha"])) $user["senha"] = $passunhashed = ($passunhashed ?? substr(preg_replace('/[^0-9]/','',hash('sha512',uniqid())),0,6));
        //verifica outras variaveis faltando
        if(!isset($user['nome'])) $user['nome'] = ($user['login'] ?? 'Novo usuário');
        if(!isset($user['email'])) $user['email'] = $user['login'].'@'.($_SERVER['SERVER_NAME'] ?? 'localhost');
        if(!isset($user['login'])) $user['login'] = self::getloginfromname($user['nome']);
        //validar campos
        $user['nome'] = ucstrname(strtolower(urldecode($user['nome'])));
        $user['senha'] = ((strlen($user['senha']) < 128) ? hash('sha512',$user['senha']) : $user['senha']);
        $user['senha'] = substr(preg_replace('/[^a-z0-9]/','',@strtolower($user['senha'])),0,200);
        $user['login'] = substr(preg_replace('/[^a-z0-9\@\.\_\-\+\/]/','',@strtolower($user['login'])),0,100);
        $user['email'] = substr(preg_replace('/[^a-z0-9\@\.\_\-\+]/','',@strtolower($user['email'])),0,100);
        $user['created'] = substr(preg_replace('/[^0-9]/','',($user['created'] ?? strtotime('now'))),0,20);
        $user['lastseen'] = substr(preg_replace('/[^0-9]/','',($user['lastseen'] ?? strtotime('now'))),0,20);
        //Necessário para correção de um bug
        $user['configs'] = json_encode($user['configs']);
        $user['devices'] = json_encode($user['devices']);
        //criar usuário
        $result = ((pdo_query("insert into users (".implode(',',array_keys($user)).") values (:".implode(',:',array_keys($user)).") ",$user)) ? pdo_insert_id() : 0);
        if($result && $autologin) self::impersonate($result, false);
        return [
            'result'=>$result,
            'uid'=>self::$uid,
            'token'=>self::$token ];
    }

    public static function logout(){
        if(self::isauthed() && !empty(self::$users[strval(self::$uid)]['devices'][strval(filtereduseragent())][self::$token]['lastseen'] ?? [])) {
            unset(self::$users[strval(self::$uid)]['devices'][strval(filtereduseragent())][self::$token]);
            pdo_query("update users set devices=:value where uid='".self::$uid."' limit 1",[ 'value' => json_encode(self::$users[strval(self::$uid)]['devices']) ]); }
        delcookie('pushid');
        delcookie('actk');
        self::$token = NULL;
        self::$uid = NULL;
        return true;
    }

    public static function chpass($data=[]) {
        if(!self::isauthed()) return -1;
        if(!is_array($data)) return -2;
        if(empty($atual = ($data['atual'] ?? ($data['current'] ?? ($data['before'] ?? ''))))) return -2;
        if(empty($pass = ($data['pass'] ?? ($data['passw'] ?? ($data['password'] ?? ($data['senha'] ?? ($data['new'] ?? ''))))))) return -3;
        if(empty($conf = ($data['conf'] ?? ($data['passconf'] ?? ($data['confirm'] ?? ($data['confirmation'] ?? ($data['confirma'] ?? ''))))))) return -4;
        if(self::getuser('passw') !== hash('sha512',$atual)) return -5;
        if($pass !== $conf) return -6;
        return self::setuser('senha', $pass);
    }

    public function appjs() { ?>
        <script>
            var resizetimer = null;

            $(window).on("onload",function(){
                $('#login* input').on("keyup",function(e){ let nf = true; if(e.which === 13) { 
                    $('#login*:visible input').each(function(index,item){
                        if(String($(item).val()).replace('undefined','').replace('null','').trim() == '') {
                        $(item).focus(); return (nf = false); } }); 
                    if(nf) $('#login*:visible .btnsubmit').click(); } });
                
                $('.loginmask').unmask().keyup(function(event) { try {
                    if(String($(this).val()).length == 3 && $.isNumeric(String($(this).val()).substr(0,2)))
                    return $(this).mask("000.000.000-00"); } catch(e) { } })
                .keydown(function(event){ try {
                    if(String($(this).val()).length < 3) return $(this).unmask(); } catch(e) { }
                });
            });

            $(window).on("screen_onload",function(state){
                
                $('#login*:visible input:not(.btnsubmit)').val('');
            
                setTimeout(function(){
                    if(!($('#topbar .toparea .tbelements .logoutbtn').length))
                        $('#topbar .toparea .tbelements').append(`<a href="#" onclick="logout();" class="logoutbtn" style="float:left;margin:1.2rem 0.75rem 0px 0.75rem;"><i class="fa fa-arrow-right-from-bracket" style="color:#fff;"></i></a>`);
                },1234);
            });

            $(window).on("screen_onstart",function(state){ 
                let newsrc = state.to;
                if(newsrc == '#login') return;
                if($('.screen.loginscreen').is(':visible')) return;
                if(getitem('uid') !== "") {
                    if($('#topbar').is(':hidden'))
                        $('#topbar, .sidenav').slideDown(1000);
                } else { 
                    $('#topbar, .sidenav').hide();
                    if(String(newsrc).indexOf('login') < 0)
                        setitem('screen','#login'); }    
             });

            function storelogin(data) {
                if(!data.result || data.result < 1) return;
                if(!data.uid || data.uid < 1) return;
                if(!data.token) return;
                post("api/auth/getuser",{ 'actk':data.token },function(info){
                    if(!info.result) return logout();
                    setitem('user', info.data);
                    setitem('token', data.token);
                    setitem('uid', data.uid);
                },null,function(always){
                    switchtab('#home');
                });
            }

            function logout() {
                var token = $_cookie('actk');
                setitem('token', '');
                setitem('user', '');
                setitem('uid', '');
                delcookie('actk');
                $('#topbar, .sidenav').slideUp();
                switchtab('#login', true);
                post("api/auth/logout",{'actk':token});
            }

            function createaccount(e) {
                if($('#login_create input[name=confirmpass]').val() !== $('#login_create input[name=pass]').val())
                    return toast('Password does not match');
                if(typeof e !== 'undefined' && $(e).length)
                    if($(e).hasClass('disabled')) return;
                    else if($(e).addClass('disabled') || true)
                            setTimeout(function(){ $(e).removeClass('disabled'); },10987);
                if(!($('#login_create #create').hasClass('active'))) 
                    if(toast('Please confirm that you understand the risks of cloud key storage before proceeding'))
                        return $(e).removeClass('disabled');
                if(((name = String($('#login_create input[name=name]').val()).trim()) == '')
                || ((lastname = String($('#login_create input[name=lastname]').val()).trim()) == '')
                || ((email = String($('#login_create input[name=email]').val()).trim()) == '')
                || ((senha = String($('#login_create input[name=pass]').val()).trim()) == '')) 
                    if(toast('Please fill up all fields before continue'))
                        return $(e).removeClass('disabled');
                post("api/auth/createaccount",{
                    'name':(name+' '+lastname), 'doc':doc, 'email':email, 'senha':senha
                },function(data){
                    if(data.exists > 0) return toast('User already exists on the database',data);
                    if(data.result < 1) return toastdetails('Error creating your account at this moment. Please check your informations and try again',data);
                    try { if(typeof md5 === 'function') data.md5 = md5(senha); } catch(e) { }
                    toast('Account created successfuly. Welcome!');
                    storelogin(data);
                },null,function(always){
                    $(e).removeClass('disabled');
                });
            }

            function performlogin(e) {
                if(typeof e !== 'undefined' && $(e).length)
                    if($(e).hasClass('disabled')) return;
                    else if($(e).addClass('disabled') || true)
                            setTimeout(function(){ $(e).removeClass('disabled'); },10987);
                if((String($('#login input[name=login]').val()).trim() == '')
                || (String($('#login input[name=passw]').val()).trim() == '')) if(M.toast({'html':'Please fill up all fields before continue'}) || true) return $(e).removeClass('disabled');
                post("api/auth/login?login="+String($('#login input[name=login]').val()).toLowerCase().replace(/[^0-9a-z\@\.\_\-]/gi,''),{ 'passw':String($('#login input[name=passw]').val()) },function(data){
                    if(!(data.result > 0)) if(toast('Invalid login name or password')) return $(e).removeClass('disabled');
                    try { if(typeof md5 === 'function') data.md5 = md5(String($('#login input[name=passw]').val()).trim()); } catch(e) { }
                    storelogin(data);
                },null,function(always){
                    $('#login input[name=passw]').val('');
                    $(e).removeClass('disabled');
                });
            }

            $(window).on('resulterr',function(data){
                if(data.state.result !== -403) return;
                setTimeout(function(){ 
                    if($('.loginscreen').is(':visible')) return;
                    logout(); 
                },2345);
            });
        </script>
    <?php }

} 
?>