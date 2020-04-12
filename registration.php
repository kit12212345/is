<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/include/blocks/header.php');
$s = $_GET['s'];
?>
<div class="row justify-content-center">
  <div class="box col-md-6 login_box_content">
    <div class="text-center">
      <h1>Регистрация</h1>
    </div>
    <hr>
    <div class="login_content">
      <?php
      if($s == 'true'){
        ?>

        <div>
          <?php echo $l->suc_reg ?>
        </div>
        <hr>
        <div class="d-flex justify-content-end">
          <a href="/login.php">
            <div class="btn btn-default">
              <i class="fa fa-sign-in w_color" aria-hidden="true"></i> &nbsp; <?php echo $l->auth ?>
            </div>
          </a>
        </div>

        <?php

      } else{
        ?>

        <div class="form-row">
          <div class="form-group col-md-6">
            <label>Имя</label>
            <input class="form-control" id="r_first_name" placeholder="Введите имя" onkeyup="auth.listen_submit(event,'reg');" type="text">
          </div>

          <div class="form-group col-md-6">
            <label>Фамилия</label>
            <input class="form-control" id="r_last_name" placeholder="Введите фамилию" onkeyup="auth.listen_submit(event,'reg');" type="text">
          </div>

        </div>

        <div class="form-group">
          <label>Email</label>
          <input class="form-control" id="r_email" placeholder="example@example.com" onkeyup="auth.listen_submit(event,'reg');" type="text">
        </div>

        <div class="form-group">
          <label>Пароль</label>
          <input class="form-control" id="r_password" placeholder="Введите пароль" onkeyup="auth.listen_submit(event,'reg');" autocapitalize="off" type="password">
        </div>

        <div class="form-group">
          <label>Повтор пароля</label>
          <input class="form-control" id="r_repeat_password" placeholder="Повторите пароль" onkeyup="auth.listen_submit(event,'reg');" type="password">
        </div>

        <!-- <div class="form-group">
          <div class="g-recaptcha" data-sitekey="6LdBDj4UAAAAAHCR8E3b-NqfO7rgHnwN4Kc_tx09"></div>
        </div> -->

        <div class="d-flex flex-row">
          <div class="ml-auto btn btn-default" onclick="auth.registration();">
            Зарегистрироваться
          </div>
        </div>

        <?php
      }
      ?>
    </div>
  </div>
</div>
<?php

include_once($root_dir.'/include/blocks/footer.php');
?>
