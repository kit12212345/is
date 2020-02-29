<?php
$hide_left_panel = true;
$page_name = 'auth';
$main_page = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
$hide_right_panel = true;
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/components/alias/alias.php');
if(!class_exists("Lang")) include_once($root_dir.'/include/classes/lang.php');
$page = $_GET['p'];

include_once($root_dir.'/blocks/header.php');
?>
<div class="login_grid">
  <div class="box post_content">
    <div class="text_center post_title">
      <h1><?php echo $l->auth ?></h1>
    </div>
    <div class="about_content">

      <div class="it_add_comment">
        <label><?php echo $l->username ?></label>
        <input class="full_w" id="l_login" placeholder="<?php echo $l->enter_username ?>" onkeyup="auth.listen_submit(event,'login');" autofocus autocapitalize="off" autocorrect="off" type="text">
      </div>

      <div class="it_add_comment">
        <label><?php echo $l->password ?></label>
        <input class="full_w" id="l_password" placeholder="<?php echo $l->enter_password ?>" onkeyup="auth.listen_submit(event,'login');" type="password">
      </div>

      <div class="text_right">
        <div class="btn cursor_p w_color i_block btn_top_login" onclick="auth.login();">
          <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; <?php echo $l->sign_on ?>
        </div>
        <div class="float_l forgot_password">
          <a href="/recovery_password"><?php echo $l->forgot_password ?></a>
        </div>
      </div>


    </div>


  </div>

</div>


<?php

include_once($root_dir.'/blocks/footer.php');
?>
