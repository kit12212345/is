var auth = {
  url: '/ajax/ajax_auth.php',
  after_auth_action: false,
  registration: function(){

    var first_name = $('#r_first_name').val(),
    last_name = $('#r_last_name').val(),
    email = $('#r_email').val(),
    password = $('#r_password').val(),
    repeat_password = $('#r_repeat_password').val();

    // var capcha_response = $('#g-recaptcha-response').val();

    if(!first_name) return message.show(l.enter_first_name);
    if(!last_name) return message.show(l.enter_last_name);
    if(!email) return message.show(l.enter_email);
    if(!password) return message.show(l.enter_password);
    if(password.length < 4) return message.show(l.short_password);
    if(!repeat_password) return message.show(l.enter_re_password);
    if(password != repeat_password) return message.show(l.pass_dont_match);

    $.ajax({
      url: this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'registration',
        first_name: first_name,
        last_name: last_name,
        email: email,
        password: password,
        repeat_password: repeat_password
        // 'g-recaptcha-response': capcha_response
      },
      success: function(data){
        if(data.result == 'true'){
          if(auth.after_auth_action !== false) return auth.after_auth_action();
          return window.location.href = "/registration.php?s=true";
        } else{
          message.show(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  login: function(prexi){
    var prexi = typeof prexi !== 'undefined' && prexi ? prexi + '_' : '';

    var email = $('#' + prexi + 'l_email').val(),
    password = $('#' + prexi + 'l_password').val();

    if(!email) return message.show(l.enter_email);
    if(!password) return message.show(l.enter_password);

    $.ajax({
      url: this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'login',
        email: email,
        password: password
      },
      success: function(data){
        if(data.result == 'true'){
          if(auth.after_auth_action !== false) return auth.after_auth_action();
          window.location.href = '/user.php';
        } else{
          message.show(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  recovery_password: function(){

    var email = $('#l_email').val();
    // var capcha_response = $('#g-recaptcha-response').val();

    if(!email) return message.show(l.j_enter_email);


    $.ajax({
      url: this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'recovery_password',
        email: email
        // 'g-recaptcha-response': capcha_response
      },
      success: function(data){
        if(data.result == 'true'){
          return window.location.href = "/recovery_password.php?s=true";
        } else{
          message.show(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  save_new_password: function(){

    var new_password = $('#r_new_password').val();
    var repeat_new_password = $('#r_repeat_new_password').val();

    if(!new_password) return message.show(l.enter_new_password);
    if(new_password.length < 4) return message.show(l.short_password);
    if(!repeat_new_password) return message.show(l.enter_re_new_password);
    if(new_password != repeat_new_password) return message.show(l.pass_dont_match);


    $.ajax({
      url: this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'save_new_password',
        new_password: new_password,
        repeat_new_password: repeat_new_password,
        user_id: user_id,
        hash: hash
      },
      success: function(data){
        if(data.result == 'true'){
          return window.location.href = "/recovery_password.php?s=done";
        } else{
          message.show(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  listen_submit: function(event,type,prexi){
    var prexi = typeof prexi === 'undefined' ? '' : prexi;

    var e = event;
    var keyCode = e.keyCode;

    if(typeof keyCode === "undefined") return true;

    if(keyCode == 13){

      if(type == 'login') return this.login(prexi);
      if(type == 'reg') return this.registration();
      if(type == 'rec_pass') return this.recovery_password();
      if(type == 'new_pass') return this.save_new_password();

    }

  }
};
