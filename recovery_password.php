<?php
$hide_left_panel = true;
$page_name = 'rec_password';
$main_page = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
$hide_right_panel = true;
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/components/alias/alias.php');
if(!class_exists("User")) include_once($root_dir.'/include/classes/user.php');
$s = $_GET['s'];
$r_user_id = isset($_GET['u']) ? (int)$_GET['u'] : 0;
$hash = isset($_GET['h']) ? $_GET['h'] : '';
$init_user = new User();

include_once($root_dir.'/blocks/header.php');
?>

<div class="login_grid">

  <div class="box post_content">

    <div class="text_center post_title">
      <h1><?php echo $l->recovering_password ?></h1>
    </div>

    <div class="about_content">

      <?php
      if($s == 'true'){
        echo $l->info_rec_password;
      } else if($s == 'done'){
        echo '<div class="g_color">'.($l->rec_password_done).'</div>';
        ?>
        <div class="text_right ask_post">
          <a href="/login">
            <div class="btn cursor_p w_color i_block btn_top_login">
              <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; <?php echo $l->auth ?>
            </div>
          </a>
        </div>
        <?php
      } else if($r_user_id > 0 && !empty($hash)){

        $user_info = $init_user->get_user_info($r_user_id);
        if($user_info === false || $user_info['recovery_hash'] != $hash){
          echo '<div class="r_color">'.($l->recovering_error).'</div>';
          ?>
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
          <script type="text/javascript">
            var user_id = '<?php echo (int)$r_user_id; ?>';
            var hash = '<?php echo $hash; ?>';
          </script>
          <div class="it_add_comment">
            <label><?php echo $l->new_password ?></label>
            <input class="full_w" id="r_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
          </div>

          <div class="it_add_comment">
            <label><?php echo $l->repeat_new_password ?></label>
            <input class="full_w" id="r_repeat_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
          </div>

          <div class="text_right">
            <div class="btn cursor_p w_color i_block btn_top_login" onclick="auth.save_new_password();">
              <?php echo $l->save ?>
            </div>
          </div>

          <?php
        }


      } else{
        ?>


        <div class="it_add_comment">
          <label>Email</label>
          <input class="full_w" id="l_email" placeholder="<?php echo $l->l_enter_email ?>" onkeyup="auth.listen_submit(event,'rec_pass');" type="text">
        </div>

        <div class="it_add_comment">
          <div class="g-recaptcha" data-sitekey="6LdBDj4UAAAAAHCR8E3b-NqfO7rgHnwN4Kc_tx09"></div>
        </div>

        <div class="text_right">
          <div class="btn cursor_p w_color i_block btn_top_login" onclick="auth.recovery_password();">
            <?php echo $l->send ?>
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
