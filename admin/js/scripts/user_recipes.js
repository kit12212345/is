var user_recipes = {
  change_status: function(status){
    var rejected_reason = gId('rejected_reason').value;

    ajx({
      url: '/admin/ajax/ajax_user_recipes.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'change_status',
        user_recipe_id: user_recipe_id,
        status: status,
        rejected_reason: rejected_reason
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  delete_recipe: function(id){
    if(!confirm(l.really_delete_recipe)) return false;

    ajx({
      url: user.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_recipe',
        item_id: id
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
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
