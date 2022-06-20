<?php
class globals {

  public static function resources($data=[]) {
    $action = ((!isset($_REQUEST['deploy'])) ? 'getcontentparam' : 'file_put_contents');
    $version = trim((file_exists($v = REPODIR.'/.commit'))?(@file_get_contents($v)):"1");
    if($version !== '1' && $version == ($_REQUEST['current'] ?? '')) return false;
    if(!(isset($_REQUEST['t']) && is_numeric($_REQUEST['t']))) return false;
    if(!(is_array($modules = listmodules()))) return false;
    return [
        'result'=>1,
        'version'=>$version,
        'style'=>((file_exists($d = REPODIR.'/'.($c = 'appcss'))) ? @file_get_contents($d) : $action($d,self::parselines(getresource($modules,$c)))),
        'body'=>((file_exists($d = REPODIR.'/'.($c = 'apphtml'))) ? @file_get_contents($d) : $action($d,self::parselines(getresource($modules,$c)))),
        'script'=>((file_exists($d = REPODIR.'/'.($c = 'appjs'))) ? @file_get_contents($d) : $action($d,self::parselines(getresource($modules,$c))))
    ];
  }

  private static function parselines($data) {
    $result = ''; $data = explode("\n", ($data."\n"));
    $rmnbsp = function($t) { while (strpos($t,($s='   ')) !== false) $t = str_replace($s,' ',$t); return $t; };
    foreach($data as $line) if(!empty($line = trim($rmnbsp($line)))) $result .= $line."\n";
    return $result;
  }

  public static function database() {
      pdo_query("CREATE TABLE IF NOT EXISTS configs (
        cid bigint(20) NOT NULL AUTO_INCREMENT,
        ckey varchar(100) NOT NULL,
        cvalue longtext NULL,
        ctime bigint(20) NULL DEFAULT '0',
        PRIMARY KEY (cid),
        UNIQUE KEY ckey (ckey))");
  }

  public static function appcss() { 
    ?><style>
        /* loadblink */
        body:not(.loadblink-disabled) .loadblink { color: transparent !important; 
          border-radius:10px; border:1px solid transparent; 
          background: linear-gradient(-45deg, #eee, #bbb, #ddd, #eee); 
          opacity:0.5; background-size: 500% 500%; 
          -webkit-animation: gradientlb 1.5s ease infinite; 
          -moz-animation: gradientlb 1.5s ease infinite; 
          animation: gradientlb 1.5s ease infinite; } 
        @-webkit-keyframes gradientlb { 0% { background-position: 0% 50% } 50% { background-position: 100% 50% } 100% { background-position: 0% 50% } } 
        @-moz-keyframes gradientlb { 0% { background-position: 0% 50% } 50% { background-position: 100% 50% } 100% { background-position: 0% 50% } } 
        @keyframes gradientlb { 0% { background-position: 0% 50% } 50% { background-position: 100% 50% } 100% { background-position: 0% 50% } } 

        body:not(.loadblink-disabled) div .loadblink:nth-of-type(2), body:not(.loadblink-disabled) table .loadblink:nth-of-type(2) { opacity:0.4; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(3), body:not(.loadblink-disabled) table .loadblink:nth-of-type(3) { opacity:0.4; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(4), body:not(.loadblink-disabled) table .loadblink:nth-of-type(4) { opacity:0.3; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(5), body:not(.loadblink-disabled) table .loadblink:nth-of-type(5) { opacity:0.3; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(6), body:not(.loadblink-disabled) table .loadblink:nth-of-type(6) { opacity:0.2; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(7), body:not(.loadblink-disabled) table .loadblink:nth-of-type(7) { opacity:0.2; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(8), body:not(.loadblink-disabled) table .loadblink:nth-of-type(8) { opacity:0.1; } 
        body:not(.loadblink-disabled) div .loadblink:nth-of-type(9), body:not(.loadblink-disabled) table .loadblink:nth-of-type(9) { opacity:0.1; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(2) { opacity:0.4; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(3) { opacity:0.4; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(4) { opacity:0.3; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(5) { opacity:0.3; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(6) { opacity:0.2; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(7) { opacity:0.2; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(8) { opacity:0.1; } 
        body:not(.loadblink-disabled) .loadblink:nth-child(9) { opacity:0.1; }

        /* darktheme */
        html { background-color:var(--background, #f0f0f0); }
        html[darkui="dark"] { overflow:visible; background-color:var(--darkbackground, #1e1e1e); }
        html[darkui="dark"] body { filter:invert(100%); min-width:100% !important; min-height:100% !important; min-width:100vw !important; min-height:100vh !important; }
        html[darkui="dark"] *:not(.fxcolor) img,
        html[darkui="dark"] *:not(.fxcolor) svg,
        html[darkui="dark"] *:not(.fxcolor) emoji,
        html[darkui="dark"] .fxcolor { filter:invert(100%); }
        html[darkui="dark"] .encolor { filter:invert(100%); }

        @media (prefers-color-scheme: dark) {
          html[darkui="auto"] { overflow:visible; background-color:var(--darkbackground, #1e1e1e); }
          html[darkui="auto"] body { filter:invert(100%); min-width:100% !important; min-height:100% !important; min-width:100vw !important; min-height:100vh !important; }
          html[darkui="auto"] *:not(.fxcolor) img,
          html[darkui="auto"] *:not(.fxcolor) svg,
          html[darkui="auto"] *:not(.fxcolor) emoji,
          html[darkui="auto"] .fxcolor { filter:invert(100%); }
          html[darkui="auto"] .encolor { filter:invert(100%); }
        }

        @media only screen and (min-width: 769px) {  
          #app.fullscreen .screen { max-width:600px; margin:auto; }
        }
    </style><?php 
  }

  public static function apphtml() { 
    ?><div id="home" class="screen" style="z-index:1;"></div><?php 
  }

  public static function appjs() {
    ?><script>
      var screen = "home";
      var inputdoctype = "CPF";
      var switchingscreen = false;

      /* app on load */
      function onload() {
          eventfire('onload');
          if((screen = getitem('screen')) == "") setitem('screen',(screen = "home"));
          if(String(screen).indexOf('_') > -1) screen = "home";
          if(!($('.screen:visible').length)) switchtab(screen);
          
          $('.telmask').mask('(99) 99999-9999');

          $('.docmask').mask("000.000.000-00")
                      .keyup(function(event) { try {
                          if(typeof event.which !== 'undefined')
                              if(((event.which >= 48) && (event.which <= 57)) || ((event.which >= 96) && (event.which <= 105)) || (event.which == 8) || (event.which == 229))
                                  if((inputdoctype == 'CNPJ') && ($(this).val().length <= 14) && (inputdoctype = 'CPF'))
                                      $(this).mask("000.000.000-00", { reverse: true }); } catch(e) { } })
                      .keydown(function(event) { try {
                          if(typeof event.which !== 'undefined')
                              if(((event.which >= 48) && (event.which <= 57)) || ((event.which >= 96) && (event.which <= 105)) || (event.which == 8) || (event.which == 229))
                                  if((inputdoctype == 'CPF') && ($(this).val().length == 14) && (inputdoctype = 'CNPJ'))
                                      if(!((event.which == 8) && ($(this).val().length == 14)))
                                          $(this).mask("00.000.000/0000-00", { reverse: true }); } catch(e) { } });

          M.AutoInit();
      }

      /* animation switch between .screen class */
      function switchtab(to,backwards) {
          setitem('screen',to);
          var vt = []; 
          var interval = 400;
          var optin = ((backwards === true) ? { direction:'left' } : { direction:'right' });
          var optout = ((backwards === true) ? { direction:'right' } : { direction:'left' });
          var newview = function(){
              to = getitem('screen');
              if(!($(to).length)) to = screen = '#home';
              $(to).show('slide', optin, interval,function(){
                  eventfire('screen_onload',{ 'to':to });
                  eventfire(String(to).replace(/[^0-9a-z\_]/gi,'')+'_onload');
                  $('html, body, fullscreen').scrollTop(0);
              });
          };
          eventfire('switchtab',{ 'to':to });
          eventfire('screen_onstart',{ 'to':to });
          if(!($('.screen:visible').length)) return newview();
          $('.screen:visible').each(function(index,item){ vt.push('#'+String($(item).attr('id'))); });
          $(vt.join(',')).hide('slide', optout, interval, newview);
      }

      /* $(window).on('onload',function(state){ console.log('onload'); });
      $(window).on('screen_onstart',function(state){ console.log('onstart: '+state.to); });
      $(window).on('screen_onload',function(state){ console.log('onload: '+state.to); }); */

      /* get value from memory */
      function getitem(qual) {
        if(!((['uid', 'token', 'user', 'screen']).includes(qual)))
          if(String(qual).indexOf('@') > -1) qual = String(qual).replace('@','');
          else qual += "_"+String(window.localStorage.getItem('uid'));
        var rt = window.localStorage.getItem(qual);
        rt = ((rt == undefined) || (rt == null) || (rt == '')) ? '' : rt;
        if(rt.indexOf('@array/object@') > -1) rt = JSON.parse(rt.replace('@array/object@',''));
        return rt;
      }

      /* set value in memory */
      function setitem(qual,val) {
        if(!((['uid', 'token', 'user', 'screen']).includes(qual)))
          if(String(qual).indexOf('@') > -1) qual = String(qual).replace('@','');
          else qual += "_"+String(window.localStorage.getItem('uid'));
        if((qual[String(qual).length-1]) == '_') return;
        if(typeof val === 'object') val = '@array/object@'+JSON.stringify(val);
        if((val == undefined) || (val == null)) val = ''; else val = val.toString();
        try { window.localStorage.setItem(qual,val);
          return ((val.indexOf('@array/object@') > -1) ? JSON.parse(val.replace('@array/object@','')) : val);
        } catch(e) { console.log(e); M.toast({html:'Erro de mem&oacute;ria excedida.&nbsp;'+
          '<a href="#" onclick="alert(\'Erro de memória excedida pode ser ocasionado por uma série de ferramentas que tentam salvar informações na memória local. Por favor, informe o erro ao suporte juntamente com informações da tela que está no momento e o que estava fazendo.\');" style="color:#eee;">detalhes</a>'}); 
          return false; }
      }

      /* event handler */
      var eventlist = [];
      function eventfire(name,state) { 
        if(!eventlist[name]) eventlist[name] = true;
        var evt = $.Event(name);
        if((typeof state === 'object') && (!Array.isArray(state)))
          Object.keys(state).forEach(function(key) { evt[key] = state[key]; });
        evt.state = state;
        $(window).trigger(evt);
      }

      /* similar html entity decode */
      const ENT_QUOTES = true;
      var htmldecoderelement = null;
      
      function html_entity_decode(htmltext) {
        if(htmldecoderelement === null) htmldecoderelement = $('<textarea/>');
        return htmldecoderelement.html(htmltext).text();
      }

      function htmlentities(htmlstr,entquotes) {
          let c = String(htmlstr).replace(/[\u00A0-\u9999<>\&]/gim, function(i) {
            return '&#'+i.charCodeAt(0)+';';
          });
          if(entquotes === true) c = c.replaceAll('"','&#x22;').replaceAll("'",'&#x27;').replaceAll('`','&#x60;');
          return c.replace(/&/gim, '&amp;');
      }

      /* get a cookie */
      var $_cookie = function(key) {
          let value = "";
          try { value = document.cookie
                  .split('; ')
                  .find(row => row.startsWith(key+'='))
                  .split('=')[1];
          } catch(e) { }
          return value;
      }
        
      /* set a cookie */
      function savecookie(key, value, expiry, domain, path) { try {
          var expires = new Date();
          var path = ';path='+((typeof path === 'undefined') ? "/" : path);
          var domain = ((typeof domain === 'undefined') ? '' : ';domain='+domain);
          if(typeof expiry === 'undefined') expiry = 365;
          expires.setTime(expires.getTime() + (expiry * 24 * 60 * 60 * 1000));
          document.cookie = key + '=' + value + ';expires=' + expires.toUTCString() + domain + path;
          } catch(e) { console.log('cant set cookie'); }
          return value;
      }
        
      /* remove any cookie */
      function delcookie(key) {
          savecookie(key, $_cookie(key), -1, '.'+window.location.hostname);
          savecookie(key, $_cookie(key), -1, window.location.hostname);
          savecookie(key, $_cookie(key), -1);
          return true;
      }

      /* check whether a function exists or not */
      function function_exists(function_name) {  
        if(typeof function_name == 'string')
              return (typeof window[function_name] == 'function');  
          else
              return (function_name instanceof Function);
      }

      /* facilitated post function */
      var post_default_params = {};
      function post(url,params,success,error,always,timeout,cache) {
        if(typeof params === 'null' || typeof params === 'undefined') params = {}; 
        if(!(typeof params !== 'object' && typeof params !== 'array')) {
          try { params = { ...params, ...post_default_params }; } catch(e) { }
          if(cache === true) params.t = (new Date().getTime()); }
        return $.post(((String(url).indexOf('//') < 0) ? serveraddress+url : url), params).always(function(data){
            if(!(!data.state)) {
              if(typeof success === 'function') success(data);
              if(data.result < 0) eventfire('resulterr',data);
            } else
              if(typeof error === 'function') error(data);
            if(typeof always === 'function') always(data);
            let e = String('//'+(String(url+'?').split('?')[0])).split('/');
            eventfire(e[e.length-2]+e[e.length-1]+'_onpost', data);
        });
      }

      /* similar function from date on php */
      function date(stringtxt, unixtimestamp) {
        if(typeof unixtimestamp === 'undefined') unixtimestamp = ((new Date()).getTime() / 1000);
        let t = parseInt(String(unixtimestamp).replace(/[^0-9]/gi,''));
        let d = new Date(t * 1000);
        let w = parseInt(d.getDay());
            if(w == 0) q = 'Dom'; if(w == 1) q = 'Seg'; if(w == 2) q = 'Ter';
            if(w == 3) q = 'Qua'; if(w == 4) q = 'Qui'; if(w == 5) q = 'Sex';
            if(w == 6) q = 'S&atilde;b';
        let u = m = parseInt(d.getMonth()+1);
            if(m == 1) m = 'Jan'; if(m == 2) m = 'Fev'; if(m == 3) m = 'Mar';
            if(m == 4) m = 'Abr'; if(m == 5) m = 'Mai'; if(m == 6) m = 'Jun';
            if(m == 7) m = 'Jul'; if(m == 8) m = 'Ago'; if(m == 9) m = 'Set';
            if(m == 10) m = 'Out'; if(m == 11) m = 'Nov'; if(m == 12) m = 'Dez';
        let h = parseInt(d.getHours());
            if(h <= 23) p = 'Noite';
            if(h <= 18) p = 'Tarde';
            if(h <= 11) p = 'Manh&atilde;';
            if(h <= 5) p = 'Madrug.';
            if(h == 0) p = 'Noite';
        let s = "";
        for(var i=0;i<stringtxt.length;i++) {
          if(stringtxt.charAt(i) == 'Y') s += d.getFullYear();
          else if(stringtxt.charAt(i) == 'y') s += String("0"+d.getFullYear()).substr(-2);
          else if(stringtxt.charAt(i) == 'm') s += String("0"+u).substr(-2);
          else if(stringtxt.charAt(i) == 'd') s += String("0"+d.getDate()).substr(-2);
          else if(stringtxt.charAt(i) == 'H') s += String("0"+h).substr(-2);
          else if(stringtxt.charAt(i) == 'i') s += String("0"+d.getMinutes()).substr(-2);
          else if(stringtxt.charAt(i) == 's') s += String("0"+d.getSeconds()).substr(-2);
          else if(stringtxt.charAt(i) == 'P') s += p;
          else if(stringtxt.charAt(i) == 'w') s += w;
          else if(stringtxt.charAt(i) == 'D') s += q;
          else if(stringtxt.charAt(i) == 'M') s += m;
          else s += stringtxt.charAt(i); }
        return s;
      }

      /* calc geolocation distance in km */
      function distancia(position1, position2) {
        "use strict"; var deg2rad = function (deg) { return deg * (Math.PI / 180); }, R = 6371,
            dLat = deg2rad(position2.lat - position1.lat),
            dLng = deg2rad(position2.lng - position1.lng),
            a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(position1.lat))
                * Math.cos(deg2rad(position1.lat)) * Math.sin(dLng / 2) * Math.sin(dLng / 2),
            c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var result = (((R * c *1000).toFixed()) / 1000).toString().replace('.',',');
        if(result.indexOf(',') > -1) { result = result.split(','); 
          if(result[1].length > 2) result[1] = result[1].substr(0,2);
          result = result[0]+','+result[1]; }
        return result;
      }

      /* dynamically load javascript */
      function loadScript( url, callback ) {
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.rel = "preload";
        script.as = "script";
        script.async = "true";
        if(script.readyState) {
          script.onreadystatechange = function() {
            if ( script.readyState === "loaded" || script.readyState === "complete" ) {
              script.onreadystatechange = null;
              if(typeof callback === 'function') callback(); }
          };
        } else script.onload = function() { if(typeof callback === 'function') callback(); };
        script.src = url;
        document.getElementsByTagName("head")[0].appendChild(script);
      }

      /* dynamically load css */
      function loadCss( url, callback ) {
        var css = document.createElement("link");
        css.rel = "stylesheet preload";
        css.as = "style";
        css.async = "true";
        if(css.readyState) {
          css.onreadystatechange = function() {
            if ( css.readyState === "loaded" || script.readyState === "complete" ) {
              css.onreadystatechange = null;
              if(typeof callback === 'function') callback(); }
          };
        } else css.onload = function() { if(typeof callback === 'function') callback(); };
        css.href = url;
        document.getElementsByTagName("head")[0].appendChild(css);
      }

      /* random blocks for loading text effect */
      function loadblink(width,height) {
        var minimum = 24;
        if(!width) width = Math.floor(Math.random() * (300 - minimum + 1) ) + minimum;
        if(!height) height = minimum;
        return '<div class="loadblink flbblock" style="height:'+height+';width:'+width+';"></div>';
      }

      /* easy toast */
      function toast(text,onfinish) {
        if(text == 'dismiss') return M.Toast.dismissAll();
        if(onfinish == undefined) onfinish = function(){ };
        M.toast({html: text, completeCallback: onfinish });
        return true;
      }

      function toastround(text,onfinish) {
        if(text == 'dismiss') return M.Toast.dismissAll();
        if(onfinish == undefined) onfinish = function(){ };
        M.toast({html: text, classes: 'rounded', completeCallback: onfinish });
        return true;
      }

      function toastdetails(text,details,onfinish) {
        if(onfinish == undefined) onfinish = function() { };
        if(details == undefined) details = '';
        try { if(typeof details === 'object') details = JSON.stringify(details).replace(/[^a-zA-Z0-9\ \(\)\-]/gi,':: '); } catch(e) { console.log('cant convert result data'); }
        M.toast({html: text+' <a href="#" onclick="alert(\':: '+String(details)+'\');" style="color:#eee !important;">detalhes</a>', completeCallback:onfinish });
        return true;
      }

      /* alert by clean auto modal */
      function alertpormodal(text) {
        if(!($('body .alertpormodaldivmsg').length))
            $('body').append('<div class="alertpormodaldivmsg fxcolor" style="position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; background-color: rgba(0, 0, 0, 0.7); z-index: 9999998;"></div>'+
                            '<div class="alertpormodaldivmsg" style="position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; z-index: 9999999;text-align:center;">'+
                                '<center class="classapmdm" style="position:relative;display:inline-block;width:auto;min-width:300px;max-width:88%;margin:4rem auto;float:center;text-align:center;">'+
                                '<div onclick="$(\'.alertpormodaldivmsg\').slideUp();" style="position:absolute;right: -1rem;top: -1rem;border:1px solid #ddd;border-radius:50%;background-color:#eee;color:#333;padding: 0px 0.7rem 2px;font-size:24px;font-weight:bold;">&times;</div>'+
                                '<div style="width:100%;height:auto;max-height:480px;max-height:77vh;overflow:auto;border:1px solid #ddd;padding:1.5rem 1rem 1rem 1rem;background-color:#fff;border-radius:10px;min-height: 70px;font-size:16px;">'+
                                '<div id="alertpmconteudo" style="max-width:100%;overflow:hidden;position:relative;"></div>'+
                            '</div></center></div>');
        $('.alertpormodaldivmsg #alertpmconteudo').html(text);
        $('.alertpormodaldivmsg').slideDown();
      }

      /* modal for configuring darktheme */
      function darktheme() {
          if(!($('body .darkthememodalsettings').length))
              $('body').append('<div class="darkthememodalsettings fxcolor" style="position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; background-color: rgba(0, 0, 0, 0.7); z-index: 9999998;"></div>'+
                              '<div class="darkthememodalsettings" style="position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; z-index: 9999999;text-align:center;">'+
                                  '<center class="classapmdm" style="position:relative;display:inline-block;width:auto;min-width: 300px;max-width: 88%;margin:4rem auto;float:center;">'+
                                  '<div onclick="$(\'.darkthememodalsettings\').slideUp();" style="position:absolute;right: -1rem;top: -1rem;border:1px solid #ddd;border-radius:50%;background-color:#eee;color:#333;padding: 0px 0.7rem 2px;font-size:24px;font-weight:bold;">&times;</div>'+
                                  '<div style="width:auto;max-width:100%;height:auto;max-height: 90%;overflow:auto;border:1px solid #ddd;padding:1.5rem 1rem 1rem 1rem;background-color:#fff;border-radius:10px;min-height: 70px;font-size:16px;">'+
                                  '<div id="dktcontent" style="width:auto;max-width:100%;min-width: 300px;overflow:hidden;position:relative;text-align:left;margin:auto;"></div>'+
                              '</div></center></div>');
          $('.darkthememodalsettings #dktcontent').html(`
              <font style="font-weight:bold;font-size:20px;margin:0px;padding:0px;">Selecione um tema:</font><br><br>
              <table class="darkthemetableconfig" border="0">
                  <!-- <tr class="dktselthemetr" onclick="let v = ($(this).find('.darkthselradio').attr('value')); $('html').attr('darkui',v); window.localStorage.setItem('darktheme',v); $('.dktselthemetr .darkthselradio').removeClass('active'); $(this).find('.darkthselradio').addClass('active');">
                    <td style="width:24px;"><div class="darkthselradio active" value="auto"></div></td>
                    <td>Detectar automaticamente</td></tr> -->
                  <tr class="dktselthemetr" onclick="let v = ($(this).find('.darkthselradio').attr('value')); $('html').attr('darkui',v); window.localStorage.setItem('darktheme',v); $('.dktselthemetr .darkthselradio').removeClass('active'); $(this).find('.darkthselradio').addClass('active');">
                    <td style="width:24px;"><div class="darkthselradio" value="light"></div></td>
                    <td>Claro</td></tr>
                  <tr class="dktselthemetr" onclick="let v = ($(this).find('.darkthselradio').attr('value')); $('html').attr('darkui',v); window.localStorage.setItem('darktheme',v); $('.dktselthemetr .darkthselradio').removeClass('active'); $(this).find('.darkthselradio').addClass('active');">
                    <td style="width:24px;"><div class="darkthselradio" value="dark"></div></td>
                    <td>Escuro</td></tr>
              </table>`);
          $('.darkthememodalsettings').slideDown();
          $('.dktselthemetr .darkthselradio').removeClass('active');
          $('.dktselthemetr .darkthselradio[value="'+(window.localStorage.getItem('darktheme'))+'"').addClass('active');
      }
    </script><?php 
  }

}

if(!function_exists('result')) {

/* exit script with a code in json format */
function result($return,$printheader=true,$printpolicy=true,$extra=null) {
	if(!is_array($_SERVER['result'] = $return)) $return = ['result'=>$return];
	if(!isset($return['result'])) $return = ['result'=>count($return), 'data'=>$return];
	if($printpolicy) $return['policy'] = header('Access-Control-Allow-Origin: *');
	if($printheader) $return['header'] = header('Content-Type: application/json');
	if($extra !== null && is_array($extra)) mergearrays($extra,$return,false,$return);
  if(is_array($return['data'] ?? false)) $return['page'] = intval($_REQUEST['page'] ?? 1);
  $return['state'] = 1;
	exit(json_encode($return));
}


/* simpler but stronger version of curl function */
function curlsend($address, $data=null, $timeout=0, $content="http_build_query", $curlopts=[]) {
  $data = (is_array($data)) ? ((function_exists($content) || function_exists($content = $content.'_encode')) ? $content($data) : $data) : $data;
	$ch = curl_init();
  if(!isset($curlopts[CURLOPT_HTTPHEADER]) && $content === "json_encode") $curlopts[CURLOPT_HTTPHEADER] = ['Content-Type: application/json'];
	if(!isset($curlopts[CURLOPT_URL])) curl_setopt($ch, CURLOPT_URL, $address);
	if(!isset($curlopts[CURLOPT_RETURNTRANSFER])) curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if(!isset($curlopts[CURLOPT_SSL_VERIFYPEER])) curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	if(!isset($curlopts[CURLOPT_SSL_VERIFYHOST])) curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if(!isset($curlopts[CURLOPT_USERAGENT])) curl_setopt($ch, CURLOPT_USERAGENT, useragent());
	if(!isset($curlopts[CURLOPT_POST])) curl_setopt($ch, CURLOPT_POST, (($data !== null) ? true : false));
	if(!isset($curlopts[CURLOPT_POSTFIELDS]) && $data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  if(!isset($curlopts[CURLOPT_CONNECTTIMEOUT]) && is_numeric($timeout) && $timeout > 0) curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  if(!isset($curlopts[CURLOPT_TIMEOUT]) && is_numeric($timeout) && $timeout > 0) curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  curl_setopt_array($ch, $curlopts);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


/* list modules */
function listmodules($path = (REPODIR.'/modules/'), &$modules=[]) {
  if(is_array($dir = scandir($path)))
    foreach($dir as $item)
      if($item !== '.' && $item !== '..' && ($item[0] ?? '') !== '.')
        if(is_dir(realpath($path.'/'.$item))) listmodules($path.'/'.$item.'/', $modules);
        else $modules[] = preg_replace('/[^a-z]/','',str_ireplace('.php','',$item));
  return $modules;
}


/* get resources from modules */
function getresource($class, $method, $return="") {
  if(!is_array($class)) $class = [$class];
  foreach($class as $c)
    if(class_exists($c, true) && (method_exists($c,$method)) && (!empty($f = "$c::$method")))
      if(@ob_start() && (@$f() ?? true) && ($r = @ob_get_clean()))
        $return .= preg_replace('!/\*.*?\*/!s','',
                   preg_replace('!\<\!\-\-.*?\-\-\>!s','',
                   str_ireplace(['<style>','</style>','<script>','</script>'],"\n",($r ?? ''))));
  return $return;
} 

function getcontentparam($resource, $content) { return $content; }


/* make sure variables are arrays */
function validatearray($array=[]) {
  if(is_object($array)) $array = json_encode($array);
  if(!is_array($array)) $array = @json_decode($array,true);
  if(!is_array($array)) $array = [];
  return $array;
}

/* sort a whole array by key recursively */
function recursiveksort(&$array) {
  foreach ($array as &$value) if (is_array($value)) recursiveksort($value);
  return ksort($array);
}


/* return user-agent info for this ip address */
function useragent($piece=false) { return ((strlen(@$_SERVER['HTTP_USER_AGENT']) > 1) ? $_SERVER['HTTP_USER_AGENT'] : @$_SERVER['REMOTE_ADDR'].' '.@$_SERVER['GATEWAY_INTERFACE'].' '.@$_SERVER['HTTP_ACCEPT_CHARSET'].' '.@$_SERVER['SERVER_SIGNATURE']).(($piece) ? ((isset($_REQUEST['gcm']))?' ('.substr($_REQUEST['gcm'].')',-7,7):'') : ''); }

/* detect devices platform */
$_SERVER['DEVICE_INFO'] = useragent();
$_SERVER['PLATFORM'] = 'desktop';

$availableplatforms = ['android','iphone','ipad','tablet'];
foreach($availableplatforms as $apt)
  if(strpos(strtolower($_SERVER['DEVICE_INFO']),$apt) !== false) $_SERVER['PLATFORM'] = $apt;

function isplatform($name) { return ($_SERVER['PLATFORM'] == $name); }
  
/* Get a filtered version of the user-agent */ 
function filtereduseragent($str='',$level=3,$match='lcnm') {
  if($str == '') $str = useragent(false); $str = str_replace(' ','_',trim($str));
  $str = str_replace(array('<','>','Mozilla/5.0_','(Linux;_','(iPhone;_','CPU_','AppleWebKit','Intel_Mac','Version/','KHTML',',','like_Gecko','address','(',')','__'),'_',$str);
  if(!((bool) strpos(strtolower($match),'p'))) $str = str_replace(';','_',$str);
  if(!((bool) strpos(strtolower($match),'b'))) $str = str_replace('/','_',$str);
  if(!((bool) strpos(strtolower($match),'n'))) $str = preg_replace('/[0-9]/','',$str);
  if(!(((bool) strpos(strtolower($match),'l')) || ((bool) strpos(strtolower($match),'c')))) $str = preg_replace('/[a-zA-Z]/','',$str); else
  if(!((bool) strpos(strtolower($match),'m'))) if((bool) strpos(strtolower($str),'mobile')) $str = str_replace(array('Chrome','Firefox','Safari','Opera','like_Mac_'),'_',$str);
  $str = str_replace(' ','_',trim(str_replace('_',' ',$str)));
  while ((bool) strpos($str,'__')) $str = str_replace('__','_',$str);
  if(($level > 0) && ($level < count(explode('_',$str)))) {
    $nstr = explode('_',$str); $str = '';
    for($i=0;$i<$level;$i++) $str .= $nstr[$i].'_'; }
  return trim(str_replace('_',' ',$str));
}


/* global configurations */
function setconfig($key='key',$value=null) {
	$_SERVER['configs'] = ($_SERVER['configs'] ?? []);
	$_SERVER['configs'][($key = preg_replace('/[^0-9a-zA-Z\_\-]/','',$key))] = $value;
	if($value === null) pdo_query("DELETE FROM configs WHERE ckey='$key' ");
	else if(!empty($envalue = @urlencode($value)))
		   if(pdo_query("UPDATE configs SET cvalue='$envalue', ctime='".strtotime('now')."' WHERE ckey='$key' ") < 1)
	   		  pdo_query("INSERT INTO configs (ckey,cvalue) VALUES ('$key','$envalue') ");
	return $value;
}

function getconfig($key='key',$def=null,$save=false) {
	$_SERVER['configs'] = ($_SERVER['configs'] ?? []);
	if(isset($_SERVER['configs'][($key = preg_replace('/[^0-9a-zA-Z\_\-]/','',$key))])) return $_SERVER['configs'][$key];
	if(is_array($cfgs = pdo_fetch_array(pdo_query("SELECT * FROM configs"))))
	  foreach($cfgs as $c => $v) $_SERVER['configs'][$c] = urldecode($v);
	if($save) setconfig($key,$def);
	return $def; 
}
 

/* cookie functions */
function delcookie($key) {
	@setcookie($key, '', (strtotime('now')-1), '/', ($_SERVER['SERVER_NAME'] ?? ""), false, false);
	unset($_COOKIE[$key]);
    return '';
}

function savecookie($key,$valor,$tempo='auto') {
	if($tempo == 'auto') $tempo = strtotime("+1 year");
	if(isset($_COOKIE[$key])) delcookie($key);
	@setcookie($key, ($_COOKIE[$key] = $valor), $tempo, '/', ($_SERVER['SERVER_NAME'] ?? ""), false, false);
  return $valor;
}

/* set debug cookie identification */
if(isset($_GET['debug'])) savecookie('debug', preg_replace('/[^0-9]/','',$_GET['debug']));


/* get the reverse color of a background */
function getcontrastcolor($hexcolor) {
  $hexcolor = str_replace('#','',$hexcolor);
  $r = @hexdec(substr($hexcolor, 0, 2));
  $g = @hexdec(substr($hexcolor, 2, 2));
  $b = @hexdec(substr($hexcolor, 4, 2));
  $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
  return ($yiq >= 128) ? '#333333' : '#f6f6f6'; 
}


/* merge two arrays into one */
function mergearrays($a1,$a2,$priorfirst=false,&$instant=null) {
  $a1 = @json_decode(json_encode($a1),true);
  $a2 = @json_decode(json_encode($a2),true);
  if(!is_array($a1)) $a1 = array();
  if(!is_array($a2)) $a2 = array();
  foreach ((array)$a2 as $a => $v) 
    if(((in_array($a,$a1)) && (!$priorfirst)) || (!in_array($a,$a1)))
      $a1[$a]=$v;
  if($instant != null) $instant = $a1;
  return $a1;
}


/* calculate the remaining time between two timestamp */
function remainingstr($value1,$value2) {
  if($value1 > $value2) $sl = $value1 - $value2;
  else $sl = $value2 - $value1; $msgst = '';
  $days = ((int) ($sl / 86400)); $sl = $sl % 86400;
  $hours = ((int) ($sl / 3600)); $sl = $sl % 3600;
  $minutes = ((int) ($sl / 60));
  $seconds = ((int) ($sl % 60));
  if($days > 0) $msgst .= $days.' day'.(($days == '1')?'':'s').' ';
  if($hours > 0) $msgst .= $hours.' hour'.(($hours == '1')?'':'s').' ';
  if($minutes > 0) $msgst .= $minutes.' minute'.(($minutes == '1')?'':'s').' ';
  if($seconds > 0) $msgst .= $seconds.' second'.(($seconds == '1')?'':'s');
  return $msgst;
}


/* names in portuguese */
function ucstrname($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("de", "da", "dos", "das", "do", "I", "II", "III", "IV", "V", "VI")) {
    $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
    foreach ($delimiters as $dlnr => $delimiter) {
        $words = explode($delimiter, $string);
        $newwords = array();
        foreach ($words as $wordnr => $word) {
          if(in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) $word = mb_strtoupper($word, "UTF-8");
          else if(in_array(mb_strtolower($word, "UTF-8"), $exceptions)) $word = mb_strtolower($word, "UTF-8");
               else if(!in_array($word, $exceptions)) $word = ucfirst($word);
          array_push($newwords, $word); }
        $string = join($delimiter, $newwords); }
   return $string;
} 

/* remove accents from string */
function rmA($string) {
	return strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}
  
/* language functions */
function isbrazil() {
  $pref_br = array('66', '138', '154', '177', '179', '187', '189', '192', '200', '201');
  $pref_ip = substr(($_SERVER['REMOTE_ADDR'] ?? '...'), 0, 3);
  return in_array($pref_ip, $pref_br);
}

}
?>