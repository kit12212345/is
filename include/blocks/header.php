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
  <link rel="stylesheet" href="/css/bootstrap.min.css">
  <link rel="stylesheet" href="/css/fonts.css">
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="/css/main.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css" />
  <link rel="stylesheet" href="/css/product.css">
  <link rel="stylesheet" href="/css/basket.css">
  <script src="/js/basket.js?ver=<?php echo rand(1,100000000); ?> " charset="utf-8"></script>
  <script src="/js/jquery.js" charset="utf-8"></script>
  <script src="/js/jquery.zoom.min.js" charset="utf-8"></script>
  <script src="/js/bootstrap.min.js"></script>
</head>
<body>
  <style media="screen">
    .header{
      background: #fff;
      box-shadow: 0 3px 3px 0 rgba(0, 0, 0, 0.2);
    }
    .header .navbar{
      padding: 0px;
    }
    .main_container{
      margin-top: 100px;
    }
    .header_content{
    }
    .container{
      max-width: 1280px;
    }
    .top_menu_item a{
      background: #fff!important;
      color: #2e3133!important;
      font-weight: 500;
    }
    .top_menu_item > a::after{
      content: '';
      display: block;
      visibility: hidden;
      height: 2px;
      width: 100%;
      background: #1e3352;
    }
    .top_menu_item:hover > a::after, .active_top_menu > a::after{
      visibility: visible;
    }
    .top_line{
      background: #1e3352;
      height: 30px;
    }
    .top_line .navbar-nav>li>a{
      color: #fff;
      padding: 5px 15px;
    }
    .top_line .navbar-nav>li>a:hover{
      color: #fbfbfb;
    }
    .top_basket{
      color: #1e3352!important;
      padding: 12px 0px;
    }
    .top_basket a{
      padding: 0px!important;
      color: #1e3352!important;
    }
    .top_basket .fa{
      font-size: 25px;
    }
    .top_basket .badge{
      top: 5px;
      left: 22px;
      background: #1e3352;
    }
    .top_info_string{
      padding: 6px 0px;
    }
    .top_info_string , .top_info_string a{
      color: #fff;
    }
    .top_info_string a{
      text-decoration: underline;
    }
  </style>
  <header class="fixed-top header">
      <nav class="navbar navbar-expand-lg navbar-fixed-top top_menu">
        <div class="top_line container-fluid">
          <div class="container">
              <span class="w_color top_info_string mr-auto">
                Some information
                <a href="#">
                  Some information
                </a>
              </span>
              <ul class="nav navbar-nav">
                <li><a href="#"><span class="glyphicon glyphicon-user"></span> Регистрация</a></li>
                <li><a href="#"><span class="glyphicon glyphicon-log-in"></span> Вход</a></li>
              </ul>
          </div>
        </div>
      </nav>
      <nav class="navbar navbar-expand-lg navbar-fixed-top">
        <div class="container">
          <div class="navbar-brand">
            Some Logo
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#dropdown" aria-controls="dropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="dropdown">
            <ul class="nav navbar-nav text-uppercase">
              <li class="nav-item top_menu_item active_top_menu">
                <a href="/" class="nav-link">Главная</a>
              </li>
              <li class="nav-item top_menu_item">
                <a href="/catalog.php" class="nav-link">Каталог</a>
              </li>
              <li class="nav-item top_menu_item">
                <a href="#" class="nav-link">Распродажа</a>
              </li>
              <li class="nav-item top_menu_item">
                <a href="#" class="nav-link">Контакты</a>
              </li>
            </ul>
            <form class="navbar-form mr-auto" action="/action_page.php">
              <div class="input-group">
                <input type="text" class="form-control form-control-sm" placeholder="Поиск...">
                <span class="input-group-append">
                  <button class="btn btn-sm btn-secondary" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
                </span>
              </div>
            </form>
            <ul class="nav navbar-nav">
              <li class="relative nav-item top_basket">
                <a href="/basket.php">
                  <i class="fa fa-shopping-bag" aria-hidden="true"></i><span class="absolute badge badge-dark badge-pill">4</span>
                </a>
              </li>
            </ul>
          </div>
      </nav>
  </header>
  <main class="container main_container">
