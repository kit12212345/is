<?php
$main_page = true;
$is_capcha_page = true;
$hide_right_panel = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/include/classes/lang.php');
header('HTTP/1.1 403 Forbidden');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title><?php echo $l->stinky_bot ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.min.css?ver=4" />
    <link rel="stylesheet" href="/css/main.min.css?ver=144" />
    <link rel="stylesheet" href="/css/posts.min.css?ver=135" />
    <link rel="stylesheet" href="/css/mobile.min.css?ver=444" media="screen" />
  </head>
  <body>

    <header class="header">
      <div class="_center">
        <div class="i_block logo">
          <a href="/">
            <img src="/images/logo.png" title="<?php echo $l->on_main ?>" alt="logo">
          </a>
        </div>
      </div>
    </header>

    <header class="mobile_header" id="mobile_header">
      <div class="relative _center">

          <div class="text_center logo">
            <a href="/">
              <img src="/images/logo.png" title="<?php echo $l->on_main ?>" alt="logo">
            </a>
          </div>

      </div>

    </header>
    <div class="zg_header"></div>
    <div class="wrapper _center">

      <main class="content">

        <div class="box post_content def_lists text_center">
          <h2 style="line-height: 28px;"><?php echo $l->stinky_bot ?>.</h2>
          <div style="text-align: right; display: inline-block; margin-top: 30px;" class="relative lgn_item">
            <div class="g-recaptcha" data-sitekey="6LdBDj4UAAAAAHCR8E3b-NqfO7rgHnwN4Kc_tx09"></div>
            <div class=""></div>
            <button onclick="return check_bot_capcha();" style="cursor: pointer; margin-top: 30px; color: #fff;  border-radius: 0px; padding: 8px 25px;   background: #f44336; border: 1px solid #e53935;" type="button" name="button"><?php echo $l->send ?></button>
          </div>
        </div>
        <script type="text/javascript">
          function check_bot_capcha() {
            var recaptcha = gClass('g-recaptcha-response')[0].value;
            ajx({
               url: '/ajax/ajax_capcha.php',
               method: 'post',
               dataType: 'json',
               data: {
                 action: 'check',
                 recaptcha: recaptcha
               },
               error: function(e){
                 message.show(e);
               },
               success: function(data){
                 if(data.result == 'true'){
                   return window.location.reload();
                 } else {
                   message.show(data.string);
                 }
               }
             });
          }

        </script>

      </main>

<?php
include($root_dir.'/blocks/footer.php');

?>
