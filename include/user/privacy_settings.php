<div class="box p-3">
  <div class="page_title">
    <h1><?php echo $l->privacy_settings ?></h1>
  </div>
  <hr>
  <div class="post_cn">
      <div class="form-group">
        <label><?php echo $l->old_password ?></label>
        <input class="form-control" id="u_old_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="form-group">
        <label><?php echo $l->new_password ?></label>
        <input class="form-control" id="u_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="form-group">
        <label><?php echo $l->repeat_new_password ?></label>
        <input class="form-control" id="u_repeat_new_password" placeholder="******" onkeyup="auth.listen_submit(event,'new_pass');" type="password">
      </div>

      <div class="d-flex justify-content-end">
        <div class="btn btn-default" onclick="user.save_new_password();">
          <?php echo $l->save ?>
        </div>
      </div>

    </div>
  </div>
