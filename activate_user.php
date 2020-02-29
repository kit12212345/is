<?php
$hide_left_panel = true;
$main_page = true;
$page_name = 'reg_confirm';
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
$hide_right_panel = true;
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/blocks/header.php');
include_once($root_dir.'/include/classes/auth.php');

$user_id = isset($_GET['u']) ? (int)$_GET['u'] : '';
$activate_hash = isset($_GET['h']) ? $_GET['h'] : '';

$auth = new Auth();

$activate_result = $auth->activate_user($user_id,$activate_hash);
?>

<div class="login_grid">
  <div class="box post_content">
    <div class="text_center post_title">
      <h1><?php echo $l->reg_confirm ?></h1>
    </div>
    <div class="about_content" style="padding-bottom: 0px;">
      <div>
        <?php
        if($activate_result === true){
          echo '<div class="g_color">'.$l->suc_reg_confirm.'</div>';
        } else if($activate_result == 'confirmed'){
          echo '<div>'.$l->already_reg_confirm.'</div>';
        } else {
          echo '<div class="r_color">'.$l->error_reg_confirm.'</div>';
        }

        ?>
      </div>

      <div class="text_right ask_post">
        <a href="/login">
          <div class="btn cursor_p w_color i_block btn_top_login">
            <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; <?php echo $l->auth ?>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
<?php

include_once($root_dir.'/blocks/footer.php');
?>
