var user = {
  url: '/ajax/ajax_user.php',
  save_profile: function(){

    var first_name = $('#u_first_name').val(),
    last_name = $('#u_last_name').val(),
    birthday = $('#u_birthday').val(),
    sex = !$('.u_sex:checked')[0] ? '' : $('.u_sex:checked').val();

    if(!first_name) return message.show(l.enter_name);

    $.ajax({
      url: this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'save_profile',
        first_name: first_name,
        last_name: last_name,
        birthday: birthday,
        sex: sex
      },
      success: function(data){
        if(data.result == 'true'){
          message.show(l.saved_changes,true);
          setTimeout(function(){
            return window.location.href = "/user.php?p=profile";
          },1500);
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

    var old_password = $('#u_old_password').val();
    var new_password = $('#u_new_password').val();
    var repeat_new_password = $('#u_repeat_new_password').val();

    if(!old_password) return message.show(l.enter_old_password);
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
        old_password: old_password,
        new_password: new_password,
        repeat_new_password: repeat_new_password
      },
      success: function(data){
        if(data.result == 'true'){
          $('#u_old_password').value = '';
          $('#u_new_password').value = '';
          $('#u_repeat_new_password').value = '';
          message.show(l.saved_changes,true);
        } else{
          message.show(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  }
};
