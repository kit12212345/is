<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/include/blocks/header.php');
?>
<div class="row justify-content-center">
  <div class="box col-md-6 login_box_content">
    <div class="text-center">
      <h1>Авторизация</h1>
    </div>
    <div class="login_content">

      <div class="form-group">
        <label>Email</label>
        <input class="form-control" id="l_email" placeholder="Введите email" onkeyup="auth.listen_submit(event,'login');" autofocus autocapitalize="off" autocorrect="off" type="text">
      </div>

      <div class="form-group">
        <label>Пароль</label>
        <input class="form-control" id="l_password" placeholder="Введите пароль" onkeyup="auth.listen_submit(event,'login');" type="password">
      </div>

      <div class="d-flex flex-row align-items-center">
        <div class="forgot_password">
          <a href="/recovery_password">Забыли пароль?</a>
        </div>
        <div class="ml-auto btn btn-default" onclick="auth.login();">
          <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; Войти
        </div>
      </div>


    </div>


  </div>

</div>


<?php

include_once($root_dir.'/include/blocks/footer.php');
?>
