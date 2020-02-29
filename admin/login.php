
  <div class="panel panel-body login-form">
    <div class="text-center">
      <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
      <h5 class="content-group">Вход в аккаунт</h5>
    </div>

    <div class="form-group has-feedback has-feedback-left">
      <input type="text" id="login" class="form-control" placeholder="Email">
      <div class="form-control-feedback">
        <i class="icon-user text-muted"></i>
      </div>
    </div>

    <div class="form-group has-feedback has-feedback-left">
      <input type="password" id="password" class="form-control" placeholder="Пароль">
      <div class="form-control-feedback">
        <i class="icon-lock2 text-muted"></i>
      </div>
    </div>

    <div class="form-group login-options">
      <div class="row">
        <div class="col-sm-6">
          <label class="checkbox-inline">
            <div class="checker"><span class="checked"><input type="checkbox" class="styled" checked="checked"></span></div>
            Запомнить
          </label>
        </div>

        <div class="col-sm-6 text-right">
          <a href="login_password_recover.html">Забыли пароль?</a>
        </div>
      </div>
    </div>

    <div class="form-group">
      <button onclick="login();" type="submit" class="btn bg-blue btn-block">Войти <i class="icon-arrow-right14 position-right"></i></button>
    </div>

  </div>

<script type="text/javascript">
  function login(){
    var login = $('#login').val(),
    password = $('#password').val();

    if(!login){
      alert('Введите логин');
      return false;
    }

    if(!password){
      alert('Введите пароль');
      return false;
    }

    $.ajax({
      url: '../admin/ajax/ajax_auth.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'login',
        login: login,
        password: password
      },
      success: function(data){
        if(data.result == 'true'){
          location.reload();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });


  }
</script>
