<?php
session_start();
$LOGGED_USER = $_SESSION['logged_user'];
$time_offset = $_SESSION['time_offset'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/db_connect.php');
include($root_dir.'/include/classes/basket.php');

$init_basket = new Basket(array(
  'user_id' => $LOGGED_USER['id']
));

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link rel="stylesheet" href="/css/fonts.css">
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="/css/main.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css" />
  <link rel="stylesheet" href="/css/product.css">
  <link rel="stylesheet" href="/css/basket.css">
  <script src="/js/basket.js?ver=<?php echo rand(1,100000000); ?> " charset="utf-8"></script>
  <script src="/js/jquery.js" charset="utf-8"></script>
</head>
<body>
  <style media="screen">
    .header{
      height: 120px;
      /* background: #f5f5f5; */
    }
    .header_img{
      background-image: url(https://image.shutterstock.com/image-vector/editable-vector-seamless-pattern-womens-260nw-716697793.jpg);
      background-position: center;
      background-size: 15%;
      opacity: 0.4;
      z-index: -1;
    }
    .header_content{
      height: 80px;
    }
    ._center {
      display: block;
      width: 1280px;
      margin: auto;
    }
    .top_menu{
      background: #474747;
      height: 40px;
    }
    .tm_items{

    }
    .tm_items li{
      float: left;
    }
    .tm_items li a{
      display: block;
      padding: 11px 15px;
      color: #fff;
      text-transform: uppercase;
    }
    .active_tm_item a{
      background: #30b387;
    }
    .logo{
      font-size: 38px;
      font-weight: bold;
      padding: 31px 0px;
    }
    .content{
      margin-top: 15px;
    }
  </style>
<header class="relative header">
  <div class="_center header_content">
    <div class="float_l logo">
      Some Logo
    </div>
    <div class="float_r">
      <ul>
        <li>
          <i class="fa fa-shopping-bag" aria-hidden="true"></i>
        </li>
      </ul>
    </div>
    <div class="clear"></div>
  </div>
  <div class="_center top_menu">
    <ul class="tm_items">
      <li class="active_tm_item">
        <a href="#">Магазин</a>
      </li>
      <li>
        <a href="#">Каталог</a>
      </li>
      <li>
        <a href="#">Распродажа</a>
      </li>
      <li>
        <a href="#">Контакты</a>
      </li>
    </ul>
  </div>
  <div class="header_img absolute all_null"></div>
</header>
<main class="content _center">
