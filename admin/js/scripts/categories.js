var categories = {
  save: function(element){

    var action = $(element).attr('data-action') == 'edit_cat' ? 'edit_cat' : 'add_cat';


    var edit_item = action == 'edit_cat' ? current_edit_item : 0;

    var name_ru = $('#cat_name_ru').val();
    var name_en = $('#cat_name_en').val();
    var description_ru = $('#cat_description_ru').val();
    var description_en = $('#cat_description_en').val();
    var alias_ru = $('#cat_alias_ru').val();
    var alias_en = $('#cat_alias_en').val();
    var hash = $('#md5_hash').val();
    var keywords_ru = $('#cat_keywords_ru').val();
    var keywords_en = $('#cat_keywords_en').val();
    var translate = $('#cat_translate').val();
    var parent_id = $('#cat_parent_id').val();

    if(!name_ru && !name_en) return alert('Введите название категории');


    ajx({
      url: '/admin/ajax/ajax_categories.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: action,
        name_ru: name_ru,
        name_en: name_en,
        description_ru: description_ru,
        description_en: description_en,
        parent_id: parent_id,
        keywords_ru: keywords_ru,
        keywords_en: keywords_en,
        translate: translate,
        alias_ru: alias_ru,
        alias_en: alias_en,
        cat_id: edit_item,
        hash: hash
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
  _delete: function(cat_id){
    ajx({
      url: '/admin/ajax/ajax_categories.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_cat',
        cat_id: cat_id
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
  show_edit: function(cat_id){

    if($('#cat_item_' + cat_id).css('display') == 'none'){
      $('#cat_item_' + cat_id).show();
      $('#c_hide_edit_' + cat_id).hide();
    } else{
      $('#cat_item_' + cat_id).hide();
      $('#c_hide_edit_' + cat_id).show();
      $('#cat_name_' + cat_id).focus();

      var name = $('#cat_name_' + cat_id), nameVal = name.val();
      name.val('').focus().val(nameVal);

    }

  },
  small_update: function(cat_id){
    var name = $('#cat_name_' + cat_id).val();
    var alias = $('#cat_alias_' + cat_id).val();

    if($.trim(name) == '') return alert('Введите название рубрики');

    ajx({
      url: '/admin/ajax/ajax_categories.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'small_update_cat',
        cat_id: cat_id,
        name: name,
        alias: alias
      },
      success: function(data){
        if(data.result == 'true'){

          $('#t_cat_name_' + cat_id).text(name);
          $('#t_cat_alias_' + cat_id).text(alias);

          return categories.show_edit(cat_id);
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  apply_cats_action: function(){
    var action = $('#s_action_cats').val();

    var checked_items = [];

    for (var i = 0; i < $('.checked_cat_item:checked').length; i++) {
      var item_id = $('.checked_cat_item:checked').eq(i).val();
      checked_items.push(item_id);
    }

    if(checked_items.length == 0) $('#s_action_cats').val('null');

    ajx({
      url: '/admin/ajax/ajax_categories.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: action,
        items: checked_items
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



  }
};
