var catalog = {
  parent_id: 6,
  init: function(){

  },
  get_active_options: function(){
    var options = [];
    var active_param_items = $('.active_param_item');
    for (var i = 0; i < active_param_items.length; i++) {
      var item = $(active_param_items).eq(i);
      var option_id = $(item).data('optionid');
      options.push(option_id);
    }
    return options;
  },
  pick_option: function(element){
    var option_id = $(element).data('optionid');
    if($(element).hasClass('active_param_item')){
      $(element).removeClass('active_param_item');
    } else{
      $(element).addClass('active_param_item');
    }
    var active_options = this.get_active_options();
    if(active_options.length == 0) remove_url_param('options');
    else set_url_param('options',active_options.toString());
    return this.get_products();
  },
  pick_sort_by: function(sort_by){
    if(sort_by != 'default'){
      set_url_param('sort_by',sort_by);
    } else remove_url_param('sort_by');
    return this.get_products();
  },
  get_products: function(){
    var active_options = this.get_active_options();
    var sort_by = $('#catalog_sort_by').val();
    $.ajax({
      url: '/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'get_products',
        parent_id: this.parent_id,
        sort_by: sort_by,
        options: active_options.toString()
      },
      success: function(data){
        if(data.result == 'true'){
          $('#products_wrap').html(data.products_html);
          console.log(data);
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
