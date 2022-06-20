<?php
class native {

    public static function appjs() {
        ?><script>
            var getqrphotofn = null;
            var takequickphotofn = null;
            var thisisandroid = false;
            var thisisiphone = false;

            function uploadquickphoto(file,onsuccess) {
                post('storage/upload.php',{ 'f':'foto', 'e':'jpg', 'p':'/', 'base64':'1', 'file':file },null,null,
                function(data){ if(typeof onsuccess !== 'function') return;
                    if(String(data['url']).replace('null','').replace('undefined','').trim() == '') return onsuccess(file);
                    else onsuccess(data['url']); }); 
            }

            async function getquickphoto(onsuccess) {
                if(typeof navigator.camera !== 'undefined') {
                    try { navigator.camera.getPicture(function(data){ uploadquickphoto('data:image/jpg;base64,'+data,onsuccess); },
                        function(e) { console.log('could not get picture',e); },
                        { correctOrientation: true, saveToPhotoAlbum:true,
                        destinationType: Camera.DestinationType.DATA_URL,
                        quality:10, targetWidth: 500, targetHeight:500,
                        encodingType: Camera.EncodingType.JPEG });
                    } catch(e) { }
                } else {
                    takequickphotofn = onsuccess;
                    alertpormodal(`<center>
                        <div class="videoareacomponent encolor" style="width:320px;height:240px;background-color:#222;margin:1rem 0px;">
                            <video id="tkpvideocomponent" width="320" height="240" autoplay></video>
                            <canvas id="tkpcanvascomponent" width="320" height="240" style="display:none;"></canvas>
                        </div><br style="clear:both;">
                        <button class="btns encolor" onclick="if($(this).hasClass('disabled')) return; 
                            $(this).addClass('disabled').html('Salvando...');
                            var rmmod = function(){ $('.alertpormodaldivmsg #alertpmconteudo').remove(); $('.alertpormodaldivmsg').slideUp(); };
                            let canvas = document.querySelector('#tkpcanvascomponent');
                            let video = document.querySelector('#tkpvideocomponent'); let i = null;
                            try { canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                            i = canvas.toDataURL('image/jpeg'); } catch(e) { rmmod(); toastdetails('Erro ao obter a foto',e); }
                            $('#tkpvideocomponent').hide(); $('#tkpcanvascomponent').show();
                            document.querySelector('#tkpvideocomponent').srcObject.getTracks().forEach(function(track){ track.stop(); });
                            uploadquickphoto(i,function(os){ rmmod(); if(!os) toastdetails('Erro ao salvar a foto',os);
                                if(typeof takequickphotofn !== 'function') return; takequickphotofn(os); });">Tirar foto</button>
                        <br style="clear:both;"><br><a href="#" style="margin-left:0.5rem;" 
                            onclick="$('.alertpormodaldivmsg #alertpmconteudo').remove(); $('.alertpormodaldivmsg').slideUp();
                                    document.querySelector('#tkpvideocomponent').srcObject.getTracks().forEach(function(track){ 
                                        track.stop(); });">Voltar</a><br>`);
                    try { let video = document.querySelector('#tkpvideocomponent');
                        let stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        video.srcObject = stream; } catch(e) { toastdetails('Erro ao obter v&iacute;deo da c&acirc;mera',e);
                        $('.alertpormodaldivmsg #alertpmconteudo').remove(); $('.alertpormodaldivmsg').slideUp(); }
                }
            }

            function getbarcode(onsuccess) {
                if((typeof cloudSky === 'undefined')
                && (typeof cordova === 'undefined')) {
                    var qrlib = "https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js";
                    if(!($('script[src="'+qrlib+'"]')).length)
                        return loadScript(qrlib,function(){ getbarcode(onsuccess); });
                    alertpormodal(`<center>
                        <button class="btn encolor" style="display:block;width:100%;text-transform:none;" 
                            onclick="let p = prompt('Insert the QR code here',''); $('.alertpormodaldivmsg').slideUp();
                            getqrphotofn(p);">Insert manually</button>
                        <div class="qrareacomponent encolor" style="width:320px;height:auto;background-color:#222;margin:1rem 0px;">
                            <div id="tkeqrcomponent"></div></div>
                        <br style="clear:both;"></center>`);
                    var html5QrcodeScanner = new Html5QrcodeScanner("tkeqrcomponent", { fps: 10, qrbox: 250 });
                    getqrphotofn = onsuccess;
                    html5QrcodeScanner.render(function(decodedText, decodedResult){
                        console.log(`Code scanned = ${decodedText}`, decodedResult);
                        $('.alertpormodaldivmsg').slideUp();
                        if(typeof onsuccess === 'function') onsuccess(decodedResult);
                    }); return;
                }
                try { if(!thisisiphone) {
                    cloudSky.zBar.scan({ text_title: "Identificando...", text_instructions: "Enquadre com o leitor no centro da câmera" },
                    function (result) { console.log("Barcode: " +result);
                        try { if(function_exists('onsuccess')) onsuccess(result); } catch(e) { console.log('no return function'); } },
                    function (error) { console.log("Scanning failed: " + error);
                    if(function_exists('onsuccess')) onsuccess(error); }
                    );
                } else {
                    cordova.plugins.barcodeScanner.scan(
                    function (result) {
                        console.log("Barcode: " +result);
                        try { if(function_exists('onsuccess')) onsuccess(result.text);
                        } catch(e) { console.log('no return function'); } },
                    function (error) {
                        console.log("Scanning failed: " + error);
                        if(function_exists('onsuccess')) onsuccess(error);
                    },{
                        preferFrontCamera : true,
                        showFlipCameraButton : true,
                        showTorchButton : true,
                        torchOn: false,
                        saveHistory: true,
                        prompt : "Enquadre com o leitor no centro da camera",
                        resultDisplayDuration: 500,
                        disableAnimations : true,
                        disableSuccessBeep: true
                    }); }
                } catch(e) {
                    console.log('could not open scanner',e);
                    if(function_exists('onsuccess')) onsuccess('');
                    else if(function_exists('onsuccess')) onsuccess(''); 
                }
            }

            function getlocation(onsuccess,onerror,options) { try {
                if(typeof options === 'undefined') options = {}; /* { maximumAge: 3000, timeout: 5000, enableHighAccuracy: true }; */
                navigator.geolocation.getCurrentPosition(function(position){
                    var data = new Object();
                    try { data.latitude = position.coords.latitude; } catch(e) { console.log('no param'); }
                    try { data.longitude = position.coords.longitude; } catch(e) { console.log('no param'); }
                    try { data.altitude = position.coords.altitude; } catch(e) { console.log('no param'); }
                    try { data.accuracy = position.coords.accuracy; } catch(e) { console.log('no param'); }
                    try { data.altitudeaccuracy = position.coords.altitudeAccuracy; } catch(e) { console.log('no param'); }
                    try { data.heading = position.coords.heading; } catch(e) { console.log('no param'); }
                    try { data.speed = position.coords.speed; } catch(e) { console.log('no param'); }
                    try { data.timestamp = position.timestamp; } catch(e) { console.log('no param'); }
                    try { data.coords = position.coords; } catch(e) { console.log('no param'); }
                    try { data.code = position.code; } catch(e) { console.log('no param'); }
                    try { data.message = position.message; } catch(e) { console.log('no param'); }
                    window.localStorage.setItem('geolat', data.latitude);
                    window.localStorage.setItem('geolong', data.longitude);
                    if(function_exists('onsuccess')) onsuccess(data);
                }, function(error) {
                    if(function_exists('onerror')) onerror(error);
                },options);
                } catch(error) { if(function_exists('onerror')) onerror(error); }
            }

            function opennativebrowser(strurl) {
                if(strurl.indexOf('http') < 0) strurl = 'http://'+strurl;
                strurl = strurl.replace('http:////','http://');
                console.log('external url: '+strurl);
                if(thisisandroid) navigator.app.loadUrl(strurl, { openExternal:true });
                else window.open(strurl,'_system');
            }
            

            function sendMessage(msg) { window.parent.postMessage(msg, '*'); };


            function updatebatterymatter(status,callback) { try {
                if(!((status == undefined) || (status == null))) {
                    window.localStorage.setItem('batterypower', status.isPlugged);
                    window.localStorage.setItem('batterylevel', status.level);
                    return;
                } else
                navigator.getBattery().then(function(battery) { 
                    var status = { 'isPlugged':battery.charging, 'level':(battery.level * 100) };
                    window.localStorage.setItem('batterypower', status.isPlugged);
                    window.localStorage.setItem('batterylevel', status.level);
                    try { if(typeof callback === 'function') callback(status);
                    } catch(e) { console.log('error on callback battery return'); }
                });
                } catch(e) { }
            }

            $(window).on('onload',function(){
                thisisandroid = (navigator.userAgent.match(/Android/i)) == "Android" ? true : false;
                thisisiphone = (navigator.userAgent.match(/iPhone/i)) == "iPhone" ? true : false;

                try {
                    window.addEventListener("batterystatus", function(status){
                        if(cordova.platformId)
                            if((cordova.platformId == 'ios') || (thisisiphone)) { thisisiphone = true;
                                $('body').append('<div style="position:fixed;top:-120px;height:120px;left:0px;right:0px;width:100%;background-color:#fff !important;"></div>'+
                                            '<div style="position:fixed;bottom:-120px;height:120px;left:0px;right:0px;width:100%;background-color:#fff !important;"></div>'); }

                        console.log("Level: " + status.level + " isPlugged: " + status.isPlugged);
                        updatebatterymatter(status); 
                    }, false);
                } catch(e) { console.log('couldnt run onload'); }

            })

        </script><?php
    }

}