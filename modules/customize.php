<?php
class customize {

  public static function appcss() { 
    ?><style>
      :root {
          --corlink: #0d4684;
          --corprincipal: #FFAF00;
          --cordotextohover: #FFFFFF;
          --cordotextoprincipal: #222222;
          --corprincipal2: #212420;
          --corprincipal3: #7f8a8e;
          --background: #f0f0f0;
          --darkbackground: #21242d;
      }

      /* materialize */
      .background, nav,.page-footer { background-color:var(--corprincipal); }
      a, .text, .collection a.collection-item { color:var(--corprincipal); }
      span.badge.new { background-color:var(--corprincipal); }
      .border { border-color:var(--corprincipal); }
      .btn, .btn-large, .btn-small, .btn-floating { background-color:var(--corprincipal); }
      .btn:hover, .btn-large:hover, .btn-small:hover { background-color: var(--corprincipal); }
      .btn-floating:hover { background-color: #ccc; }
      .btn:focus, .btn-large:focus, .btn-small:focus, .btn-floating:focus { background-color: var(--corprincipal); }
      .collection .collection-item.active { background-color:var(--corprincipal); color:#fff; }
      .pagination li.active { background-color:var(--corprincipal); color:#fff; }
      [type="checkbox"]:checked+span:not(.lever):before { border-right: 2px solid var(--corprincipal); border-bottom: 2px solid var(--corprincipal); }
      [type="checkbox"].filled-in:checked+span:not(.lever):after { border: 2px solid var(--corprincipal); background-color: var(--corprincipal); }
      .datepicker-date-display { background-color:var(--corprincipal); color:#fff; }
      .datepicker-table td.is-today { color:var(--corprincipal); }
      .datepicker-cancel, .datepicker-clear, .datepicker-today, .datepicker-done { color:var(--corprincipal); }
      .datepicker-table td.is-selected { background-color: var(--corprincipal); color: #fff; }
      [type="radio"]:checked+span:after, [type="radio"].with-gap:checked+span:after { background-color: var(--corprincipal); }
      [type="radio"]:checked+span:after, [type="radio"].with-gap:checked+span:before, [type="radio"].with-gap:checked+span:after { border: 2px solid var(--corprincipal); }
      .dropdown-content li>a, .dropdown-content li>span { color:var(--corprincipal); }
      .dropdown-content { background-color:var(--background); }
      .switch label input[type=checkbox]:checked+.lever { background-color:#ccc; }
      .switch label input[type=checkbox]:checked+.lever:after { background-color:var(--corprincipal); }
      input:not([type]):focus:not([readonly]), input[type=text]:not(.browser-default):focus:not([readonly]), input[type=password]:not(.browser-default):focus:not([readonly]), input[type=email]:not(.browser-default):focus:not([readonly]), input[type=url]:not(.browser-default):focus:not([readonly]), input[type=time]:not(.browser-default):focus:not([readonly]), input[type=date]:not(.browser-default):focus:not([readonly]), input[type=datetime]:not(.browser-default):focus:not([readonly]), input[type=datetime-local]:not(.browser-default):focus:not([readonly]), input[type=tel]:not(.browser-default):focus:not([readonly]), input[type=number]:not(.browser-default):focus:not([readonly]), input[type=search]:not(.browser-default):focus:not([readonly]), textarea.materialize-textarea:focus:not([readonly]) {
        border-bottom: 1px solid var(--corprincipal); -webkit-box-shadow: 0 1px 0 0 var(--corprincipal); box-shadow: 0 1px 0 0 var(--corprincipal); }
      input:not([type]):focus:not([readonly])+label, input[type=text]:not(.browser-default):focus:not([readonly])+label, input[type=password]:not(.browser-default):focus:not([readonly])+label, input[type=email]:not(.browser-default):focus:not([readonly])+label, input[type=url]:not(.browser-default):focus:not([readonly])+label, input[type=time]:not(.browser-default):focus:not([readonly])+label, input[type=date]:not(.browser-default):focus:not([readonly])+label, input[type=datetime]:not(.browser-default):focus:not([readonly])+label, input[type=datetime-local]:not(.browser-default):focus:not([readonly])+label, input[type=tel]:not(.browser-default):focus:not([readonly])+label, input[type=number]:not(.browser-default):focus:not([readonly])+label, input[type=search]:not(.browser-default):focus:not([readonly])+label, textarea.materialize-textarea:focus:not([readonly])+label {
        color:var(--corprincipal); }


      .darkthselradio { width:12px; height:12px; border:2px solid #777; border-radius:50%; vertical-align:middle; margin-right:16px; } 
      .darkthselradio.active { background-color:#555; border:2px solid #333; } 
      .darkthemetableconfig td, .darkthemetableconfig tr { padding:2px; margin:2px; padding-bottom:12px; font-size:14px; vertical-align:middle; border-bottom:0px solid transparent; }

      select.browser-default { color:var(--cordotextoprincipal, #333333); background-color:transparent !important; border:0px solid transparent !important; border-bottom:1px solid var(--cordotextoprincipal, #333333) !important; }

      html { background-color:var(--background, #f0f0f0); color:var(--cordotextoprincipal, #333333); }
      html[darkui="dark"] { overflow:visible; background-color:var(--darkbackground, #1e1e1e); }
      html[darkui="dark"] body { filter:invert(100%); min-width:100% !important; min-height:100% !important; min-width:100vw !important; min-height:100vh !important; }
      html[darkui="dark"] *:not(.fxcolor) img,
      html[darkui="dark"] *:not(.fxcolor) svg,
      html[darkui="dark"] *:not(.fxcolor) emoji,
      html[darkui="dark"] .fxcolor { filter:invert(100%); }
      html[darkui="dark"] .encolor { filter:invert(100%); -webkit-transform: translate3D(0, 0, 0); }

      @media (prefers-color-scheme: dark) {
      html[darkui="auto"] { overflow:visible; background-color:var(--darkbackground, #1e1e1e); }
      html[darkui="auto"] body { filter:invert(100%); min-width:100% !important; min-height:100% !important; min-width:100vw !important; min-height:100vh !important; }
      html[darkui="auto"] *:not(.fxcolor) img,
      html[darkui="auto"] *:not(.fxcolor) svg,
      html[darkui="auto"] *:not(.fxcolor) emoji,
      html[darkui="auto"] .fxcolor { filter:invert(100%); }
      html[darkui="auto"] .encolor { filter:invert(100%); -webkit-transform: translate3D(0, 0, 0); }
      }

      html[darkui="auto"] .dropdown-content,
      html[darkui="dark"] .dropdown-content { background-color:var(--darkbackground); }

      .disabled { opacity:0.5; pointer-events: none; }

      .checkboxball { width:14px; height:14px; border:1px solid #999; background-color:transparent !important; border-radius:50%; vertical-align:middle; display:inline-block; margin-right:0.5rem; }
      .checkboxball.active { background-color:var(--corlink) !important; }
      html[darkui="auto"] .checkboxball.active,
      html[darkui="dark"] .checkboxball.active {
          background-color:var(--corprincipal) !important; color:#111;
          border:1px solid #555 !important; } 

      .svg-inline--fa { display:inline-block; color:#111; width:20px; height:20px; vertical-align:middle; }
      .svg-inline--fa path { color:inherit !important; }

      .sidenav {
          will-change: initial;
          -webkit-backface-visibility: initial;
          backface-visibility: initial; }

      html[darkui="dark"] .sidenav-overlay, html[darkui="auto"] .sidenav-overlay { background-color:rgba(250,250,250,0.8) !important; }

      .btns, .btns2 {
          background-color:var(--corlink); color:#fff;
          padding:1rem 1.25rem;
          border:1px solid #999 !important; 
          border-radius:30px; width:100%; }

      .btns2 { background-color:#eee; color:#111; }

      html[darkui="auto"] .btns,
      html[darkui="dark"] .btns {
          background-color:var(--corprincipal) !important; color:#111;
          border:1px solid #555 !important; } 

      *:not(.encolor) a, *:not(.fxcolor) a { color:var(--corlink); }
      html[darkui="auto"] *:not(.encolor) a, html[darkui="auto"] *:not(.fxcolor) a,
      html[darkui="dark"] *:not(.encolor) a, html[darkui="dark"] *:not(.fxcolor) a { 
          color:var(--corlink) !important; }

      .e3d { -webkit-transform: translate3D(0, 0, 0); }

      .toparea {
          background-color:var(--corprincipal); 
          position:fixed; top:0px; left:0px; right:0px; width:100%; height:70px; overflow:hidden;
          -webkit-border-bottom-right-radius: 30px;
          -webkit-border-bottom-left-radius: 30px;
          -moz-border-radius-bottomright: 30px;
          -moz-border-radius-bottomleft: 30px;
          border-bottom-right-radius: 30px;
          border-bottom-left-radius: 30px;
          -webkit-backface-visibility: hidden; 
          -webkit-transform: translate3D(0, 0, 0);
          z-index:950;
      }
      
      #topbar { height:90px; }
      .toparea .tbelements { display:block; margin:1.25rem 0.5rem 0px 0.5rem; }
      .toparea .toplabel { display:inline-block; font-size:16px; color:#000 !important; filter:invert(0) !important; }
      .toparea .topimage { display:inline-block; background-color:#777; width:32px; height:32px; border:0px solid transparent; filter:invert(0) !important; border-radius:50%; margin:0.95rem 0.65rem 1.25rem 0.75rem; vertical-align:middle; overflow:hidden; }

      .heading { padding-bottom:5rem; }
      .bigtitulo { display:block; margin:0.5rem 1rem 1.8rem 1rem; font-size:28px; color:#111; text-align:center; }

      .backbtn { float:left; font-size:12px; color:#333; padding-left:0.5rem; }
      .backbtn i, .backbtn svg { font-size:14px; }
      .backbtn .vttitle { display:inline-block; margin-left:0.5rem; vertical-align:middle; color:#333; }

      .footing { position:fixed; bottom:0px; left:0px; right:0px; height:auto; }

      .activeitem { border:2px solid var(--corprincipal) !important; }
      .horizontalscroll { overflow: scroll; display: block; white-space: nowrap; justify-content: center; overflow-x: auto; }

      @media only screen and (min-width: 993px) {
        #app.fullscreen { margin-left: 100px; }
        #app.fullscreen .loginscreen .heading { margin-left: auto !important; transform: translate(-90px, 0px) !important; -webkit-transform: translate3D(0, 0, 0); }
        .sidenav { width:250px !important; background-color: transparent !important; z-index:999 !important; margin-top: 70px !important; }
        .socel { display:none !important; }
      }

      @media only screen and (max-width: 993px) {
        .sodesk { display:none !important; }
      }

      #topbar, .sidenav { display:none; }

      .loginscreen a { color:#0d4684; font-weight: bold; }
      .loginscreen { text-align:center; padding:2rem; }
      .loginscreen table { width:auto; margin:2rem auto 0px auto; }
      .loginscreen table tr { border-bottom:0px solid transparent; }
      .loginscreen .imagem, .loginscreen img { width:60px; height:auto; }
      .loginscreen .imagemtexto { padding-left:1rem; }
      .loginscreen .imagemtexto b { font-size:36px; font-weight:600; color:#21242D; }
      .loginscreen .imagemtexto span { display:block; font-size:18px; margin-top:-5px; color:#21242D; }

      .loginscreen .inputtexto { color:#333; background-color:#FFFFFF; overflow:hidden; }
      .loginscreen input[type=text],
      .loginscreen input[type=password] { 
          color:#111;
          border-bottom:0px solid transparent;
          background-color:transparent !important; 
          margin:2px 1.25rem; width:90%; }
      .loginscreen .inputtexto,
      .loginscreen input[type=submit] { 
          margin:0.5rem 0px;
          border:1px solid #999 !important; 
          border-radius:5px; width:100%; }

      html[darkui="auto"] .loginscreen .inputtexto,
      html[darkui="dark"] .loginscreen .inputtexto {
          background-color:transparent !important; }

      .loginscreen #forgotpassbtn { display:block; width:100%; margin-top:1rem; background-color:transparent; color:var(--corlink); padding:1.5rem; font-size:12px; }
      .loginscreen .footing #arealogin_create { display:block; width:100%; margin-top:1rem; background-color:#333; color:#fff; padding:1.5rem; font-size:12px; }
    </style><?php 
  }

  public static function apphtml() { 
    ?><ul id="slide-out" class="sidenav sidenav-fixed" style="padding-top:2rem;">
        <li><a class="menuhomebtn waves-effect sidenav-close" href="#" onclick="switchtab('#home',true);">Início</a></li>
        <li><a class="menuthemebtn waves-effect sidenav-close" href="#" onclick="darktheme();">Alterar tema</a></li>
    </ul>
    
    <div id="login_create" class="screen loginscreen">
        <a href="#" class="encolor" style="float:left;" onclick="switchtab('#login', true);"><i class="fa-solid fa-arrow-left" style="font-size:20px;"></i></a>
        <div class="heading">
            <table border="0"><tr>
                <td class="imagem"><img src="img/logo.png" class="encolor"></td>
            </tr></table>
            <label class="bigtitulo">Create new account</label>
            <div class="inputtexto"><input type="text" placeholder="Name" name="name"></div>
            <div class="inputtexto"><input type="text" placeholder="Lastname" name="lastname"></div><br>
            <div class="inputtexto"><input type="text" placeholder="Email" name="email"></div>
            <div class="inputtexto"><input type="password" placeholder="Password" name="pass"></div>
            <div class="inputtexto"><input type="password" placeholder="Confirm password" name="confirmpass"></div>
            <div style="padding:0.5rem;">
                <table border="0" style="margin:0px;padding:0px;">
                    <tr style="border-bottom:0px solid transparent;">
                        <td class="encolor" onclick="$('#login_create #create').toggleClass('active');"><div id="create" class="checkboxball"></div></td>
                        <td><label id="msgtermos"><span onclick="$('#login_create #create').toggleClass('active');">I agree with the creation of my account using the provided information above.</label></td>
                    </tr>
                </table>
            </div><br style="clear:both;">
            <input type="submit" class="encolor btnsubmit btns" value="CREATE" onclick="createaccount(this);">
        </div>
    </div>

    <div id="login" class="screen loginscreen">
        <div style="position:fixed;right:0.5rem;top:0.5rem;" onclick="darktheme();"><i style="color:#999;" class="fa-solid fa-circle-half-stroke"></i></div>
        <div class="heading">
            <table border="0"><tr>
                <td class="imagem"><img src="img/logo.png" class="encolor"></td>
            </tr></table>
            <label style="display:block;margin:1rem 1rem 2rem 1rem;font-size:18px;">Login to your account</label>
            <div class="inputtexto"><input type="text" placeholder="Login" name="login" class="docmask loginmask"></div>
            <div class="inputtexto"><input type="password" placeholder="Password" name="passw"></div>
            <input type="submit" class="encolor btnsubmit btns" value="LOGIN" onclick="performlogin(this);">
            <a id="forgotpassbtn" class="encolor" href="#">Forgot your password?</a>
        </div>
        <div class="autoremainingspace"><br></div>
        <div class="footing">
            <div id="arealogin_create" class="encolor" onclick="switchtab('#login_create');">Don't you have an account? <a href="#" style="color:initial;"><u>Create an account now</u></a></div>
        </div>
    </div><?php 
  }

  public static function appjs() {
    ?><script>
      $(window).on("onload",function(data){
          $(`<div id="topbar">
                <div class="toparea encolor">
                    <div class="tbelements">
                        <a href="#" data-target="slide-out" class="sidenav-trigger menubtn encolor socel" style="float:right;margin:1.4rem 0.75rem 0px 0px;"><i class="fa fa-bars"></i></a>
                        <img src="" border="0" class="topimage" onerror="$(this).attr('onerror',''); $(this).attr('src','img/nophoto.png');">
                        <label class="toplabel">Olá</label>
                    </div>
                </div>
            </div>`).insertBefore('#app');
        
          $('.sidenav').sidenav({
                'draggable':false,
                'onOpenEnd':function(e){ eventfire('menu_opened',e); },
                'onCloseEnd':function(e){ eventfire('menu_closed',e); },
                'preventScrolling':true
          });
      });

      /* auto positioning footer to the bottom for .footing clsses */
      $(window).on("screen_onload",function(state){

          if($(state.to+' .backbtn').length)
              $(state.to+' .backbtn').html('<span class="encolor"><i class="fa-solid fa-arrow-left"></i></span><span class="vttitle">Back</span>');

          if($(state.to+' .footing').length) {
              $(state.to+' .footing').attr('style','');
              resizetimer = setInterval(function(){
                  var foo = $(state.to+' .footing').offset().top;
                  if(!(parseInt(foo) > 0)) return;
                  $(state.to+' .footing').attr('style','top:'+(foo)+'px !important;bottom:auto;');
                  clearInterval(resizetimer);
              },100);
          }
      });
    </script><?php 
  }

}
?>