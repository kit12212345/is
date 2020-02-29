<?php
$hide_left_panel = true;
$page_name = 'registration';
$main_page = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
$hide_right_panel = true;
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/blocks/header.php');
$s = $_GET['s'];
?>
<div class="c_center_grid">
  <div class="box post_content">
    <div class="post_title">
      <h1><?php echo $l->registration ?></h1>
    </div>
    <div class="about_content">
      <?php
      if($s == 'true'){

        ?>

        <div>

          <?php echo $l->suc_reg ?>
        </div>

        <div class="text_right ask_post">
          <a href="/login">
            <div class="btn cursor_p w_color i_block btn_top_login">
              <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; <?php echo $l->auth ?>
            </div>
          </a>
        </div>

        <?php

      } else{
        ?>
        <div class="it_add_comment">
          <label><?php echo $l->username ?></label>
          <input class="full_w" id="r_login" autofocus placeholder="<?php echo $l->enter_username ?>" onkeyup="auth.listen_submit(event,'reg');" autocapitalize="off" autocorrect="off" type="text">
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->name ?></label>
          <input class="full_w" id="r_name" placeholder="<?php echo $l->enter_name ?>" onkeyup="auth.listen_submit(event,'reg');" type="text">
        </div>

        <div class="it_add_comment">
          <label>Email</label>
          <input class="full_w" id="r_email" placeholder="example@example.com" onkeyup="auth.listen_submit(event,'reg');" type="text">
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->password ?></label>
          <input class="full_w" id="r_password" placeholder="<?php echo $l->enter_password ?>" onkeyup="auth.listen_submit(event,'reg');" autocapitalize="off" type="password">
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->re_password ?></label>
          <input class="full_w" id="r_repeat_password" placeholder="<?php echo $l->enter_re_password ?>" onkeyup="auth.listen_submit(event,'reg');" type="password">
        </div>

        <div class="it_add_comment">
          <div class="g-recaptcha" data-sitekey="6LdBDj4UAAAAAHCR8E3b-NqfO7rgHnwN4Kc_tx09"></div>
        </div>

        <div class="text_right">
          <div class="btn cursor_p w_color i_block btn_top_login" onclick="auth.registration();">
            <?php echo $l->sign_in ?>
          </div>
        </div>

        <?php
      }
      ?>
    </div>
  </div>
</div>
<?php

include_once($root_dir.'/blocks/footer.php');
?>
