<?php
session_start();
// $_SESSION['logged_user']['id'] = 22;
$LOGGED_USER = isset($_SESSION['logged_user']) ? $_SESSION['logged_user'] : false;
$user_id = $LOGGED_USER['id'];
if($user_id != '22') exit;
// exit;

?>
<html><head>
  <meta charset="utf-8">
  <title>Happy birthday to you!</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta http-equiv="X-UA-compatible" content="IE=edge">
  <link rel="icon" href="img/favicon.ico" />
  <link rel="shortcut icon" href="img/favicon.ico" />
  <link href="img/favicon.ico" rel="shortcut icon" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="format-detection" content="telephone=no">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/main.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="js/script.js"></script>
  <link rel="stylesheet" href="/css/font-awesome.min.css">
</head>

<body>
<script>
  var articleLink = '/scripts/other/479-otkrytka-s-novym-godom.html';
</script>
<style media="screen">
  #preload_page{
    height: 100%;
    position: relative;
  }
  .pbg{
    height: 100%;
    background-image: url("img/pbg.png");;
    background-size: 11%;
  }
  .pbg_2{
    position: absolute;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    width: 100%;
    height: 100%;
    background-position: 127px 121px;
    background-image: url("img/pbg_2.png");
    background-size: 14%;
  }
  .pbg_o{
    position: absolute;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    width: 100%;
    height: 100%;
    opacity: 0.4;
    background: #000;
  }
  .preloader_page{
    width: 90px;
    height: 65px;
    margin: auto;
    position: absolute;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    text-align: center;
    color: #fff;
  }
  .preloader_page_btn{
    display: none;
    width: 240px;
    height: 37px;
    margin: auto;
    position: absolute;
    left: 0px;
    top: 0px;
    right: 0px;
    bottom: 0px;
    text-align: center;
    color: #fff;
  }
  .btn_click{
    background: #eb722e;
    padding: 7px 10px;
    border: 1px solid #eb722e;
    font-weight: 600;
    box-shadow: 1px 1px 4px #00000073;
    cursor: pointer;
  }
</style>
<div id="preload_page">
  <div class="pbg"></div>
  <div class="pbg_2"></div>
  <div class="pbg_o"></div>
  <div id="preloader_page_btn" class="preloader_page_btn absolute all_null">
    <div>
      <div class="btn_click" onclick="show_full_screen();show_content();">CLICK TO CONTINUE</div>
    </div>
  </div>
  <div id="preloader_page_text" class="preloader_page absolute all_null">
    <i class="fa fa-spinner fa-spin fa-2x fa-fw" style="color: #fff;"></i>
    <div style="margin-top: 10px;">
      <strong>LOADING..</strong>
    </div>
  </div>
  <!-- <img style="width: 100%; height: 100%;" src="http://megaport.hu/media/king-include/uploads/happy-birthday-animation-5820794613.gif" alt=""> -->
</div>
<div id="bd_page" style="display: none;">
  <div class="card">
    <div class="card-page cart-page-front">
      <div class="card-page cart-page-outside"></div>
      <div class="card-page cart-page-inside">
        <span class="merry-christmas">
          <img src="img/wife.png" alt="">
        </span>
      </div>
    </div>
    <div class="card-page cart-page-bottom">
      <h3 class="card-page__title">Happy birthday to you!</h3>
      <div class="card-page__text">
        Happy birthday to you<br>
        And I'll tell you, loving you dearly:<br>

        You're not just a wife to me,<br>
        And native, expensive man.<br>
        Met each other once<br>
        And now inseparable forever.<br>

        Let your dreams come true, there will be joy, health, wealth.<br>
        And eyes canvassed happiness are ablaze.<br>

      </div>
    </div>
  </div>

  <span class="click-icon">
    <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><path fill="#fff" d="M31.6 17.7V26c0 1.9-.7 3.7-2 5.1v.9c0 1.6-1.3 3-3 3h-8.4c-1.6 0-3-1.3-3-3 0-.6.5-1 1-1s1 .4 1 1c0 .5.4 1 1 1h8.4c.5 0 1-.4 1-1v-1.2-.3-.1c0-.1.1-.2.2-.3 1.1-1.1 1.7-2.5 1.7-4v-8.3c0-.3-.1-.5-.3-.7-.1-.1-.5-.4-1-.3-.4.1-.8.6-.8 1.1v2.4c0 .6-.5 1-1 1s-1-.4-1-1v-5.5c0-.3-.1-.5-.3-.7s-.4-.3-.7-.3c-.5 0-1 .5-1 1v5.5c0 .6-.5 1-1 1s-1-.4-1-1v-8.5c0-.3-.1-.5-.3-.7s-.4-.3-.7-.3c-.5 0-1 .5-1 1v8.5c0 .6-.5 1-1 1s-1-.4-1-1V7.7c0-.3-.1-.5-.3-.7-.1-.1-.5-.4-1-.3-.4.1-.8.6-.8 1.1V20c0 .4-.2.8-.6.9-.4.2-.8.1-1.1-.2L11 18.1c-.6-.6-1.6-.6-2.2.1-.5.6-.4 1.5.2 2.1l7 7c.4.4.4 1 0 1.4-.2.2-.5.3-.7.3-.3 0-.5-.1-.7-.3l-7-7.1c-1.3-1.3-1.5-3.5-.3-4.8C8 16 9 15.5 10 15.5c.9 0 1.8.4 2.5 1l.9.9V7.9c0-1.4.9-2.7 2.3-3 1-.3 2.1 0 2.8.8.6.6.9 1.3.9 2.1V9c.3-.1.7-.2 1-.2.8 0 1.5.3 2.1.9s.9 1.3.9 2.1v.2c.3-.1.7-.2 1-.2.8 0 1.5.3 2.1.9s.9 1.3.9 2.1v.2c.1 0 .2-.1.3-.1 1-.3 2.1 0 2.8.8.8.5 1.1 1.3 1.1 2z"></path></svg>
  </span>
</div>



<script type="text/javascript">
  window.onload = function(){
    setTimeout(function(){
      $('#preloader_page_text').fadeOut();
      $('#preloader_page_btn').fadeIn();
    },500);
  };
  function show_content(){
    setTimeout(function(){
      $('#preload_page').fadeOut();
      $('#bd_page').fadeIn();
    },800);
  }
</script>



<!-- <audio src="hb_m.mp3" id="hb_m"></audio> -->
</body>
</html>
