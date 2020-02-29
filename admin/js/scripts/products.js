var products = {
  count_show: 10,
  max_pages_btns: 10,
  count_products: 0,
  current_page: 1,
  offset_time_search: 800,
  timer_search: null,
  current_parent_id: 0,
  url: '/admin/ajax/ajax_products.php',
  init: function(){

    var info_products = $('#info_products').attr('data-info');
    info_products = JSON.parse(info_products);
    this.count_products = info_products.count_products;
    this.count_show = info_products.count_show;
    this.parent_id = info_products.parent_id;

    // $('#swith_page_product').on('click',function(e){
    //   e = e || window.event;
    //   var target = e.target || e.srcElement;
    //   if($(target).hasClass('paginate_button')){
    //     return products.click_switch_pages.apply(target);
    //   }
    // });

    $('#i_search_product').on('keyup',this.search_product);
    $('.i_by_search').on('change',this.search_product);

  },
  select_cat:function(element){
    var cat_id = typeof element === 'object' ? $(element).val() : element;

    this.current_parent_id = cat_id;

    if(cat_id > 0){
      set_get_param('cat_id',cat_id);
    } else remove_get_param('cat_id');

    this.current_page = 1;

    remove_get_param('page');

    return this.load_products();

  },
  search_product: function() {
    var _this = products;

    clearTimeout(_this.timer_search);

    _this.timer_search = setTimeout(function(){

      var search_str = $('#i_search_product').val();

      if(search_str){
        set_get_param('search_str',search_str);
      } else remove_get_param('search_str');

      _this.current_page = 1;

      remove_get_param('page');

      return _this.load_products();

    },_this.offset_time_search);

  },
  load_products: function(l_data){
    var _this = products;
    var search_str = $('#i_search_product').val();

    $('#disable_products_content').show();
    ajx({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'load_products',
        count_show: _this.count_show,
        page: _this.current_page,
        parent_id: _this.current_parent_id,
        search_str: search_str
      },
      success: function(data){
        if(data.result == 'true'){

          $('.paginate_button').removeClass('disabled');

          _this.count_products = data.count_products;

          $('#table_pr_content').html(data.html);

          _this.reindex_btns_switch_pages();

        } else{
          alert(data.string);
        }
        $('#disable_products_content').hide();
      },
      error: function(err){
        console.log(err);
        $('#disable_products_content').hide();
      }
    });
  },
  switch_status:function (element,product_id){
    var _this = products;
    var status = $(element).attr('data-status') == 'disable' ? 0 : 1;

    ajx({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'switch_status_product',
        status: status,
        product_id: product_id
      },
      success: function(data){
        if(data.result == 'true'){
          if(status == 0){
            $(element).attr('data-status','enable');
            $(element).text('Опубликовать');
            $(element).html('<i class="icon-spinner11"></i>Опубликовать');
            $(element).removeClass('r_color');
            $(element).addClass('g_color');
            $('#pr_status_' + product_id).text('Отключен');
            $('#pr_status_' + product_id).removeClass('label-success');
            $('#pr_status_' + product_id).addClass('label-danger');
          } else {
            $(element).attr('data-status','disable');
            $(element).text('Снять с продажи');
            $(element).html('<i class="icon-blocked"></i>Снять с продажи');
            $(element).removeClass('g_color');
            $(element).addClass('r_color');
            $('#pr_status_' + product_id).text('Активен');
            $('#pr_status_' + product_id).removeClass('label-danger');
            $('#pr_status_' + product_id).addClass('label-success');
          }
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  _delete:function(product_id){
    var _this = products;
    ajx({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_product',
        product_id: product_id
      },
      success: function(data){
        if(data.result == 'true'){
          return _this.load_products();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  show_edit: function(element){
    var text = $(element).text();
    var length = $('.cl_edit').length;
    var action = $(element).attr('data-action');
    var product_id = $(element).attr('data-productid');
    var input = '<input data-productid="' + product_id + '" data-action="' + action + '" onblur="products.hide_edit(this)" onkeydown="if(event.keyCode == 13) return products.hide_edit(this);" type="text" id="i_cd_edit_' + length + '" autofocus="autofocus" class="cl_edit" value="' + text + '">';
    if($(element).parent().children('.cl_edit').length == 0){
      $(element).hide();
      $(element).parent().append(input);
      var el = $('#i_cd_edit_' + length)[0];
      el.focus();
      el.setSelectionRange(el.value.length,el.value.length);
    }
  },
  hide_edit: function(element){
    var _this = products;
    var val = $(element).val();
    var action = $(element).attr('data-action');
    var product_id = $(element).attr('data-productid');
    var show_el = $(element).parent().children('.sh_cp_edit');
    ajx({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: action,
        value: val,
        product_id: product_id
      },
      success: function(data){
        if(data.result == 'true'){
          $(show_el).text(data.value);
          $(show_el).show();
          $(element).remove();
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  reindex_btns_switch_pages: function() {

    var max_pages_nav = this.max_pages_btns;

    var items_per_page = this.count_show;

    var page = this.current_page;


    var page_count = 0;
    if (0 === this.count_products) {
    } else {
       page_count = parseInt(Math.ceil(this.count_products / items_per_page));
       if(page > page_count) {
          page = 1;
       }
    }

    var center_pos=Math.ceil(max_pages_nav/2);
    var center_offset=Math.round(max_pages_nav/2);

    $('.paginate_button').remove();

    if(page_count > 1){
      if(page > center_pos) var start_page_count = page - 2;
      else  var start_page_count = 1;
      var end_page_count = start_page_count + (max_pages_nav - 1);
      if(end_page_count > page_count){
        end_page_count = page_count;
        start_page_count = page_count - (max_pages_nav - 1);
      }

    if (start_page_count < 1) start_page_count = 1;
    page = parseInt(page);

    if(page != 1) $('#nums_pages_pr').append('<a onclick="products.switch_pages(this);" data-page="' + ( page - 1) + '" data-direct="previous" class="paginate_button previous" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_previous">←</a>');

      for (var i = start_page_count; i <= end_page_count; i++) {

         if (i == page) {
           $('#nums_pages_pr').append('<a class="paginate_button page_btn_switch current" aria-controls="DataTables_Table_0" data-dt-idx="" tabindex="0">' + i + '</a>');
         } else{
           $('#nums_pages_pr').append('<a onclick="products.switch_pages(this);" data-page="' + i + '" class="paginate_button page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="" tabindex="0">' + i + '</a>');
         }
      }

      if (page != page_count) $('#nums_pages_pr').append('<a onclick="products.switch_pages(this);" data-page="' + (page + 1) + '" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_next">→</a>');

    }

  },
  switch_pages: function(element){

    var page = $(element).attr('data-page');
    page = page <= 0 ? 1 : page;

    this.current_page = page;

    if(page > 1){
      set_get_param('page',page);
    } else remove_get_param('page');

    $('html').animate({scrollTop:0}, 450);

    return this.load_products();
  },
  change_count_show: function(element){
    var val = $(element).val();
    val = val > 100 ? 100 : val;
    val = val < 10 ? 10 : val;

    this.count_show = val;

    this.current_page = 1;

    set_get_param('count_show',val);

    remove_get_param('page');

    return this.load_products();
  },
};


$(window).on('popstate', function(e) {
  var _this = products;
  var url_vars = get_url_vars();
  var cat_id = url_vars['cat_id'];
  var page = url_vars['page'];
  var count_show = url_vars['count_show'];
  page = typeof page === 'undefined' ? 1 : page;
  count_show = typeof count_show === 'undefined' ? 10 : count_show;

  _this.current_parent_id = cat_id;
  _this.current_page = page;
  _this.count_show = count_show;

  $('html').animate({scrollTop:0}, 0);
  console.log("ASd");

  return _this.load_products();
});
