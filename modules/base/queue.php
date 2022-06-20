<?php
class queue {

    public static function database() {
        pdo_query("CREATE TABLE IF NOT EXISTS `sms_queue` (
            `id` int NOT NULL AUTO_INCREMENT,
            `receipt` varchar(200) DEFAULT NULL,
            `sender` varchar(200) DEFAULT '',
            `message` longtext,
            `response` longtext,
            `provider` varchar(50) DEFAULT NULL,
            `sendat` int DEFAULT '0',
            PRIMARY KEY (`id`))");

        pdo_query("CREATE TABLE IF NOT EXISTS `email_queue` (
            `id` int NOT NULL AUTO_INCREMENT,
            `email` varchar(200) DEFAULT NULL,
            `subject` varchar(200) DEFAULT NULL,
            `body` longtext,
            `headers` longtext,
            `response` longtext,
            `sendat` int DEFAULT '0',
            PRIMARY KEY (`id`))");
    }

    public static function smsqueue() {

    }

    public static function emailqueue() {

    }

    public static function pagamentoqueue() {

    }

    public static function process() {
        return ['smsqueue','emailqueue','pagamentoqueue'];
    }

    public static function appjs() { 
        ?><script>
            var queuetimer = null;
            var queuetryin = false;
            var queuetimeout = 59000;

            var varqueueallrequests = getitem('queueallrequests');

            function queueallrequests(bool) {
            varqueueallrequests = (bool) ? '1' : '';
            setitem('queueallrequests', ((bool)?'1':''));
            }

            function queueload() {
            var queueline = getitem('queuetasks');
            if(!(typeof queueline === 'object')) queueline = [];
            return queueline;
            }

            function queuesave(q) {
            if(!(typeof q === 'object')) q = [];
            try {
                if(q.length > 3) $('.queuewarning').show(); 
                else $('.queuewarning').hide();
            } catch(e) { }
            return ((setitem('queuetasks', ((q.length < 1) ? '' : q)) === false) ? false : true);
            }

            function queuepush(row) {
            var found = false;
            var queueline = queueload();
            var paramline = $.extend(true, {}, row.params);
            var paramline = $.extend(true, paramline, { time:0 });
            
            try { /* para marcar fazendo */
                if(typeof row.params.queueindue !== 'undefined')
                row.queueindue = row.params.queueindue;
            } catch(e) {}

            try { paramline = JSON.stringify(paramline); } catch(e) { console.log('could not strfy paramline'); }
            var qbytes = queuehashCode(row.uri.toString()+''+paramline.toString()+''+row.func.toString());
            $(queueline).each(function(index,item){ if(item.qbytes) if(item.qbytes == qbytes) found = true; });
            if(!(typeof row === 'object')) return false;
            if(found) return true;
            row.qbytes = qbytes;
            queueline.push(row); 
            return queuesave(queueline);
            }

            function postqueue(uri,params,instant,onsuccesscallback,onerrorcallback) {
            try { params.time = Math.floor(Date.now() / 1000); } catch(e) { }
            if(typeof onsuccesscallback !== 'function') onsuccesscallback = function(){};
            if(typeof onerrorcallback !== 'function') onerrorcallback = function(){};
            if(typeof instant !== 'function') instant = function(){};

            if(queuepush({ 'uri':uri, 'params':params, 'errmsg':'', 'func':onsuccesscallback.toString(), 'funce':onerrorcallback.toString() }))
                return instant({'queued':'1'});
            }

            function queueremove(uri) { var newobj = []; var jafoi = false;
            var queueline = queueload();
            $(queueline).each(function(index,item){
                if(item.uri) {
                if((jafoi) || (item.uri.indexOf(uri) < 0)) newobj.push(item);
                else jafoi = true;
                }
            }); queuesave(newobj);
            }

            function queuehashCode(str) {
            var hash = 0, i, chr;
            if (str.length === 0) return hash;
            for (i = 0; i < str.length; i++) {
                chr   = str.charCodeAt(i);
                hash  = ((hash << 5) - hash) + chr;
                hash |= 0; }
            return (hash > 0) ? hash : (hash * -1);
            }

            var queuetimer = function(){ 
            if(queuetryin) return;
            var queueline = queueload();
            if(!queueline[0]) return;

            if(varqueueallrequests == '1') return;
            else queuetryin = true;
            var r = queueline[0];
            try { $.ajax({ url:r.uri, timeout:queuetimeout, method:"POST", data:r.params })
                    .always(function(data){ queuetryin = false;
                    /* caso deu erro
                    if((((typeof data.readyState !== 'undefined') && (data.readyState < 1)))
                    || (typeof data.result === 'undefined')) { */
                    if(data.state !== 1) {
                        queueline[0].errmsg = (String(data)+' '+String(JSON.stringify(data)).substr(0,50));
                        if(typeof r.funce !== 'undefined') queueline[0].funce = undefined; queuesave(queueline);
                        if(typeof r.funce !== 'undefined') eval('let fe='+r.funce+'; try { fe(data); } catch(e) { console.log("QE-E:",e); }');
                        return;
                    } else { /* caso deu certo */
                        queueline.shift();
                        queuesave(queueline);
                        if(r.func != undefined) eval('let f='+r.func+'; try { f(data); } catch(e) { console.log("QE-E:",e); }'); }
                }); } catch(e) { }
            };
            /* initialize queue tasks */
            queuetimer = setInterval(queuetimer,2100);
        </script><?php 
    }

}
?>