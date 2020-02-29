<div class="box post_content">
  <div class="post_title">
    <h1><?php echo $l->privacy_settings ?></h1>
  </div>
  <div class="post_cn">
    <div class="cont_post">
      <div class="it_add_comment">
        <label><?php echo $l->old_password ?></label>
        <input class="full_w" id="u_old_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="it_add_comment">
        <label><?php echo $l->new_password ?></label>
        <input class="full_w" id="u_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="it_add_comment">
        <label><?php echo $l->repeat_new_password ?></label>
        <input class="full_w" id="u_repeat_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="text_right">
        <div class="btn cursor_p w_color i_block btn_top_login" onclick="user.save_new_password();">
          <?php echo $l->save ?>
        </div>
      </div>

    </div>
    </div>
  </div>
