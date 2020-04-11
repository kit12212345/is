var catalog = {
  current_product_id: 0,
  current_cat_id: 0,
  parent_id: 0,
  search_timer: null,
  current_search_by: 'by_name',
  current_page: 1,
  goods_per_page: 25,
  init_product_edit: function(){
    var data = $("#products_info").data('info');
    this.current_product_id = data.product_id;

    uploader_recipe_image = new Uploader({
      value_item: this.current_product_id > 0 ? this.current_product_id : $('#md5_hash').val(),
      _event: this.current_product_id > 0 ? 'edit_product' : 'add_product',
      max_files: 10
    });

  },
  init_cat_edit: function(){
    var data = $("#products_info").data('info');
    this.current_cat_id = data.cat_id;
  },
  set_search_by: function(search_by){
    this.current_search_by = search_by;
    if(search_by == 'by_name') remove_url_param('search_by');
    else set_url_param('search_by',search_by);
    return this.get_catalog();
  },
  search: function(){
    clearTimeout(this.search_timer);
    return this.search_timer = setTimeout(function(){
      catalog.current_page = 1;
      remove_url_param('page');
      var search_str = $('#search_value').val();
      if(search_str) set_url_param('search_str',search_str);
      else remove_url_param('search_str');
      $('html').animate({scrollTop:0}, 450);
      return catalog.get_catalog();
    },400);
  },
  switch_page: function(page){
    this.current_page = page;
    if(page > 1) set_url_param('page',page);
    else remove_url_param('page');
    $('html').animate({scrollTop:0}, 450);
    return this.get_catalog();
  },
  set_gpp: function(goods_per_page){
    this.current_page = 1;
    remove_url_param('page');
    this.goods_per_page = goods_per_page;
    if(goods_per_page == 25) remove_url_param('goods_per_page');
    else set_url_param('goods_per_page',goods_per_page);
    return this.get_catalog();
  },
  set_parent_id: function(id){
    this.parent_id = id;
    if(id > 0) set_url_param('parent_id',id);
    else remove_url_param('parent_id');
    $('.sp_select_cat').val(id);
    return this.get_catalog();
  },
  get_catalog: function(){
    preload_page();
    var search_str = $('#search_value').val();
    $.ajax({
      url: '/admin/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'get_catalog',
        parent_id: this.parent_id,
        page: this.current_page,
        goods_per_page: this.goods_per_page,
        search_str: search_str,
        search_by: this.current_search_by
      },
      success: function(data){
        preload_page(false);
        if(data.result == 'true'){
          $('#catalog_content').html(data.html);
          $('#swith_pages_wrap').html(data.pages_html);
          $('#catalog_map_cats').html(data.cat_path_html);
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        preload_page(false);
        console.log(err);
      }
    });
  },
  save_product: function(){
    preload_page();
    var name = $('#product_name').val(),
    description = $('#product_description').val(),
    price = $('#product_price').val(),
    quan = $('#product_quan').val(),
    parent_id = $('#parent_id').val(),
    image_hash = $('#md5_hash').val();

    $.ajax({
      url: '/admin/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'save_product',
        name: name,
        product_id: this.current_product_id,
        description: description,
        price: price,
        parent_id: parent_id,
        image_hash: image_hash,
        quan: quan
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          preload_page(false);
          alert(data.string);
        }
      },
      error: function(err){
        preload_page(false);
        console.log(err);
      }
    });

  },
  delete_product: function(item_id){
    preload_page();
    $.ajax({
      url: '/admin/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_product',
        product_id: item_id
      },
      success: function(data){
        preload_page(false);
        if(data.result == 'true'){
          return catalog.get_catalog();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        preload_page(false);
        console.log(err);
      }
    });
  },
  save_catalog: function(){
    var name = $('#cat_name').val(),
    parent_id = $('#parent_id').val(),
    description = $('#cat_description').val();

    preload_page();
    $.ajax({
      url: '/admin/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'save_catalog',
        name: name,
        parent_id: parent_id,
        cat_id: this.current_cat_id,
        description: description
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
  switch_status: function(element){
    var item_id = $(element).data('itemid');
    var status = $(element).data('status');

    preload_page();
    $.ajax({
      url: '/admin/ajax/ajax_catalog.php',
      method: 'post',
      dataType: 'json',
      data: {
        action: 'switch_status',
        status: status,
        item_id: item_id
      },
      success: function(data){
        preload_page(false);
        if(data.result == 'true'){
          if(status == 'disabled'){
            $(element).data('status','enabled');
            $(element).addClass('g_color');
            $(element).removeClass('r_color');
            $(element).html('<i class="icon-circle-down2"></i>Опубликовать');
            $('#item_status_' + item_id).text('Отключен');
            $('#item_status_' + item_id).addClass('label-danger');
            $('#item_status_' + item_id).removeClass('label-success');
          } else if(status == 'enabled'){
            $(element).data('status','disabled');
            $(element).addClass('r_color');
            $(element).removeClass('g_color');
            $(element).html('<i class="icon-blocked"></i>Снять с продажи');
            $('#item_status_' + item_id).text('Активен');
            $('#item_status_' + item_id).removeClass('label-danger');
            $('#item_status_' + item_id).addClass('label-success');
          }
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        preload_page(false);
        console.log(err);
      }
    });



  }
};
