var thisisandroid = (navigator.userAgent.match(/Android/i)) == "Android" ? true : false;
var thisisiphone = (navigator.userAgent.match(/iPhone/i)) == "iPhone" ? true : false;

function getbarcode(onsuccess,onerror) {
  try { if(!thisisiphone) {
    cloudSky.zBar.scan({ text_title: "Identificando...", text_instructions: "Enquadre com o leitor no centro da câmera" },
      function (result) { console.log("Barcode: " +result);
        try { if(function_exists('onsuccess')) onsuccess(result); } catch(e) { console.log('no return function'); } },
      function (error) { console.log("Scanning failed: " + error);
      if(function_exists('onerror')) onerror(error); }
    );
  } else {
    cordova.plugins.barcodeScanner.scan(
      function (result) {
        console.log("Barcode: " +result);
        try { if(function_exists('onsuccess')) onsuccess(result.text);
        } catch(e) { console.log('no return function'); } },
      function (error) {
        console.log("Scanning failed: " + error);
        if(function_exists('onerror')) onerror(error);
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
    if(function_exists('onerror')) onerror('');
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
  } catch(error) { 
    if(function_exists('onerror')) onerror(error);
  }
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

  

function readfromfile(filename, callback) {
  try { window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function (fs) {
    var filedir = String(cordova.file.externalDataDirectory).replace(cordova.file.externalRootDirectory, '');
    fs.root.getFile(filedir+filename, { create: true, exclusive: false }, function (fileEntry) {
        fileEntry.file(function (file) {
          var reader = new FileReader();  
          reader.onloadend = function() {
            callback(this.result);
          };
          reader.readAsText(file);
        }, function(e){ console.log(e); callback(""); });
    }, function(e){ console.log(e); callback(""); });
  }, function(e){ console.log(e); callback(""); });
  } catch(e) { console.log(e); callback(""); }
}

function writetofile(filename, filedata, callback) {
  try { window.requestFileSystem(LocalFileSystem.PERSISTENT, 0, function (fs) {
    var filedir = String(cordova.file.externalDataDirectory).replace(cordova.file.externalRootDirectory, '');
    fs.root.getFile(filedir+filename, { create: true, exclusive: false }, function (fileEntry) {
        fileEntry.createWriter(function (fileWriter) {
            fileWriter.onwriteend = function() {
                var savevf = window.localStorage.getItem('storedb').split(',');
                if(!(savevf.includes(filename))) { 
                  savevf.push(filename); 
                  window.localStorage.setItem('storedb',savevf.join(',')); }
                callback(true);
            };
            fileWriter.onerror = function (e) {
                console.log("Failed to write file",filename,e.toString());
                callback(false);
            };
            fileWriter.write(filedata);
        });
    }, function(e){ console.log(e); callback(false); });
  }, function(e){ console.log(e); callback(false); });
  } catch(e) { console.log(e); callback(false); }
}


    

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
