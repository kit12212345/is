<?php
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$admin_id = (int)$LOGGED_USER['id'];
if($LOGGED_USER['id'] <= 0) $admin_id = 0;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
require($root_dir.'/db_connect.php');

$body_class = $admin_id == 0 ? 'login-container' : '';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta charset="utf-8">
    <title>Панель администратора</title>
    <link href='/fonts/fonts.css?family=Roboto:400,300,500' rel='stylesheet' type='text/css'>

    <!-- Global stylesheets -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
    <link href="css/icon_styles.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="css/core.css" rel="stylesheet" type="text/css">
    <link href="css/components.css" rel="stylesheet" type="text/css">
    <link href="css/colors.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="/css/main.css">
    <!-- /global stylesheets -->



    <link rel="stylesheet" href="/css/font-awesome.min.css">
    <!-- <script src="/js/core/dom.js" charset="utf-8"></script> -->
    <!-- <script src="/js/core/ajax.js" charset="utf-8"></script> -->
    <!-- <script src="/js/core/events.js" charset="utf-8"></script> -->
    <script src="/js/functions.js" charset="utf-8"></script>


    <script src="../admin/js/pace.js" charset="utf-8"></script>
    <script src="../admin/js/jquery.js" charset="utf-8"></script>
    <script src="../admin/js/bootstrap.js" charset="utf-8"></script>
    <script src="../admin/js/blockui.js" charset="utf-8"></script>

    <script src="../admin/js/bootbox.js" charset="utf-8"></script>
    <script src="../admin/js/sweet_alert.js" charset="utf-8"></script>
    <script src="../admin/js/select.js" charset="utf-8"></script>
    <script src="../admin/js/modals.js" charset="utf-8"></script>

    <script src="../admin/js/app.js" charset="utf-8"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <style media="screen">
      .navbar-nav>.dropdown-user img{
        width: 30px;
        height: 30px;
        object-fit: cover;
      }
    </style>
  </head>
  <body class="<?php echo $body_class; ?>">

    <style media="screen">
      .disable_content_w{
        display: none;
        z-index: 3000;
      }
      .disable_content{
        background: #000;
        opacity: 0.3;
      }
      .spinner_load{
        width: 22px;
        height: 22px;
        margin: auto;
      }
      .spinner_load > i{
        color: #fff;
      }
    </style>
      <div id="disable_body" class="fixed all_null disable_content_w">
        <div class="fixed all_null disable_content"></div>
        <div class="absolute all_null spinner_load">
          <i class="icon-spinner2 spinner"></i>
        </div>
      </div>
      <script type="text/javascript">
        function preload_page(remove){
          if(typeof remove !== 'undefined' && remove == false){
            $('body').removeClass('hide_scroll');
            $('#disable_body').hide();
          } else{
            $('body').addClass('hide_scroll');
            $('#disable_body').show();
          }
        }
      </script>

    <!-- Main navbar -->
    <div class="navbar navbar-inverse">
      <div class="navbar-header">
        <a class="navbar-brand" href="#"><h2 style="line-height: 18px; margin: 0px;" class="w_color">Site</h2></a>

        <ul class="nav navbar-nav visible-xs-block">
          <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
          <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
      </div>

      <?php

      if($admin_id > 0){
        ?>

        <div class="navbar-collapse collapse" id="navbar-mobile">
          <ul class="nav navbar-nav">
            <li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>

            <li class="dropdown">

              <div class="dropdown-menu dropdown-content">
                <div class="dropdown-content-heading">
                  Git updates
                  <ul class="icons-list">
                    <li><a href="#"><i class="icon-sync"></i></a></li>
                  </ul>
                </div>

                <ul class="media-list dropdown-content-body width-350">
                  <li class="media">
                    <div class="media-left">
                      <a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-pull-request"></i></a>
                    </div>

                    <div class="media-body">
                      Drop the IE <a href="#">specific hacks</a> for temporal inputs
                      <div class="media-annotation">4 minutes ago</div>
                    </div>
                  </li>

                  <li class="media">
                    <div class="media-left">
                      <a href="#" class="btn border-warning text-warning btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-commit"></i></a>
                    </div>

                    <div class="media-body">
                      Add full font overrides for popovers and tooltips
                      <div class="media-annotation">36 minutes ago</div>
                    </div>
                  </li>

                  <li class="media">
                    <div class="media-left">
                      <a href="#" class="btn border-info text-info btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-branch"></i></a>
                    </div>

                    <div class="media-body">
                      <a href="#">Chris Arney</a> created a new <span class="text-semibold">Design</span> branch
                      <div class="media-annotation">2 hours ago</div>
                    </div>
                  </li>

                  <li class="media">
                    <div class="media-left">
                      <a href="#" class="btn border-success text-success btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-merge"></i></a>
                    </div>

                    <div class="media-body">
                      <a href="#">Eugene Kopyov</a> merged <span class="text-semibold">Master</span> and <span class="text-semibold">Dev</span> branches
                      <div class="media-annotation">Dec 18, 18:36</div>
                    </div>
                  </li>

                  <li class="media">
                    <div class="media-left">
                      <a href="#" class="btn border-primary text-primary btn-flat btn-rounded btn-icon btn-sm"><i class="icon-git-pull-request"></i></a>
                    </div>

                    <div class="media-body">
                      Have Carousel ignore keyboard events
                      <div class="media-annotation">Dec 12, 05:46</div>
                    </div>
                  </li>
                </ul>

                <div class="dropdown-content-footer">
                  <a href="#" data-popup="tooltip" title="All activity"><i class="icon-menu display-block"></i></a>
                </div>
              </div>
            </li>
          </ul>

          <p class="navbar-text">
            <a href="/" target="_blank">
              <span class="label bg-success">Перейти на сайт</span>
            </a>
          </p>

          <ul class="nav navbar-nav navbar-right">

            <li class="dropdown dropdown-user">
              <a class="dropdown-toggle" data-toggle="dropdown">
                <img src="https://hluble.com/images/post_images/thumbnail_140/1564575908-b17f169ae11dd4aa636bfdf3dadba703.jpg" alt="">
                <span><?php echo $LOGGED_USER['name']; ?></span>
                <i class="caret"></i>
              </a>

              <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="/logout.php"><i class="icon-switch2"></i> Выйти</a></li>
              </ul>
            </li>
          </ul>
        </div>
        <?php
      }

      ?>

    </div>
    <!-- /main navbar -->

    <!-- Page container -->
    <div class="page-container">

      <!-- Page content -->
      <div class="page-content">

      <?php
      if($admin_id > 0){
        include($root_dir.'/admin/blocks/left_menu.php');
      }
      ?>
