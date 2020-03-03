var orders = {
  current_order_id: 23,
  count_show: 10,
  max_pages_btns: 10,
  count_orders: 0,
  current_page: 1,
  current_sort: 'active',
  url: '/admin/ajax/ajax_orders.php',
  init: function(){
    var info_orders = $('#info_orders').attr('data-info');
    info_orders = JSON.parse(info_orders);
    this.count_orders = info_orders.count_orders;
    this.count_show = info_orders.count_show;
    this.current_sort = info_orders.current_sort;
    this.current_page = info_orders.current_page;
  },
  load_orders: function(l_data){
    var _this = orders;

    $('#disable_products_content').show();
    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'load_orders',
        count_show: _this.count_show,
        sort: _this.current_sort,
        page: _this.current_page
      },
      success: function(data){
        if(data.result == 'true'){

          $('.paginate_button').removeClass('disabled');

          _this.count_orders = data.count_orders;

          $('#orders_content').html(data.html);

          _this.reindex_btns_switch_pages();

        } else{
          console.log(data.string);
        }
        $('#disable_products_content').hide();
      },
      error: function(err){
        console.log(err);
        $('#disable_products_content').hide();
      }
    });
  },
  reindex_btns_switch_pages: function() {

    var max_pages_nav = this.max_pages_btns;

    var items_per_page = this.count_show;

    var page = this.current_page;


    var page_count = 0;
    if (0 === this.count_orders) {
    } else {
       page_count = parseInt(Math.ceil(this.count_orders / items_per_page));
       if(page > page_count) {
          page = 1;
       }
    }

    var center_pos=Math.ceil(max_pages_nav/2);
    var center_offset=Math.round(max_pages_nav/2);

    $('#nums_pages_pr').empty();

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

    if(page != 1) $('#nums_pages_pr').append('<li><a data-page="' + (page-1) + '" onclick="orders.switch_pages(this);">‹</a></li>');

      for (var i = start_page_count; i <= end_page_count; i++) {

         if (i == page) {
           $('#nums_pages_pr').append('<li class="active"><a>' + i + '</a></li>');
         } else{
           $('#nums_pages_pr').append('<li><a data-page="' + i + '" onclick="orders.switch_pages(this);">' + i + '</a></li>');
         }
      }

      if (page != page_count) $('#nums_pages_pr').append('<li><a data-page="' + (page+1) + '" onclick="orders.switch_pages(this);">›</a></li>');

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

    return this.load_orders();
  },
  delete_order: function(order_id){
    var _this = orders;

    if(!confirm('Вы дейсвительно хотите удалить этот заказ?')) return false;

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_order',
        order_id: order_id
      },
      success: function(data){
        if(data.result == 'true'){
          // var count_active_orders = data.count_active_orders;
          // $('#count_active_orders').text(data.count_active_orders);
          // if(count_active_orders <= 0){
          //   $('#count_active_orders').hide();
          // }
          // return _this.load_orders();
        } else{
          console.log(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  sort: function(element){
    var value = $(element).val();
    var now_get = window.location.href.split('?')[1];
    if(now_get.indexOf('view_order') >= 0 && value.indexOf('view_order') == -1){
      var view_order = now_get.split('view_order=')[1];
      view_order = view_order.split('&')[0];
      value += '&view_order=' + view_order;
    }
    window.location.href=value;
  },
  handle_order: function(element){
    var _this = orders;
    var order_id = $(element).attr('data-orderid');
    var handle = $(element).attr('data-handle');
    if(typeof handle === 'undefined') return false;

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'handle_order',
        order_id: order_id,
        handle: handle
      },
      success: function(data){
        if(data.result == 'true'){
          window.location.reload();
        } else{
          console.log(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });

  },
  refresh_order: function(order_id) {
    var _this = orders;
    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'show_order',
        order_id: order_id
      },
      success: function(data){
        if(data.result == 'true'){
          $('#order_conent_' + order_id).html(data.html);
        } else{
          console.log(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  show_add_product: function(order_id){
    this.current_order_id = order_id;
    $('#o_cont_add_products').empty();
    $('#i_search_product').val('');
    $('#s_pr_res').hide();
    $('#show_modal_add_product').trigger('click');
  },
  search_product: function(element){
    var _this = orders;
    var search_str = $(element).val();
    if($.trim(search_str) == ''){
      $('#s_pr_res').hide();
      return false;
    }

    var search_by = $('.i_by_search:checked').val();

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'search_product',
        search_by: search_by,
        search_str: search_str
      },
      success: function(data){
        if(data.result == 'true'){
          $('#s_pr_res').show();
          $('#s_pr_res').html(data.html);
        } else{
          $('#s_pr_res').hide();
          console.log(data.string);
        }
      },
      error: function(err){
        $('#s_pr_res').hide();
        console.log(err);
      }
    });
  },
  add_products: function() {
    $('#disable_s_product').show();
    var _this = orders;

    var products = [];
    var length = $('.s_product_item').length;

    for (var i = 0; i < length; i++) {
      var product_id = $('.s_product_item').eq(i).attr('data-productid');
      var product_count = $('#cp_' + product_id).text();
      product_count = !isNaN(product_count) && product_count > 0 ? product_count : 1;
      products.push({
        id: product_id,
        count: product_count
      });
    }

    if(products.length == 0) return false;

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'add_products',
        products: products,
        order_id: _this.current_order_id
      },
      success: function(data){
        $('#disable_s_product').hide();
        if(data.result == 'true'){
          $('#close_add_products').trigger('click');
          $('.o_t_summa_' + _this.current_order_id).text(data.total_summa);
          return _this.refresh_order(_this.current_order_id);
        } else{
          alert(data.string);
        }
      },
      error: function(err){
        console.log(err);
        $('#disable_s_product').hide();
      }
    });

  },
  delete_search_product: function(element){
    $(element).parent().remove();
  },
  select_search_product: function(element){
    var _this = orders;
    var html = '';
    var product_id = $(element).attr('data-productid');
    var product_name = $(element).attr('data-productname');
    var product_code = $(element).attr('data-productcode');

    if($('#o_add_pr_' + product_id)[0]){
      alert('Этот товар уже выбран');
      return false;;
    }

    $('#s_pr_res').hide();
    $('#disable_s_product').show();

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'check_product_in_order',
        product_id: product_id,
        order_id: _this.current_order_id
      },
      success: function(data){
        if(data.result == 'true'){
          html += '<div id="o_add_pr_' + product_id + '" data-productid="' + product_id + '" class="relative i_block s_product_item padd_ten">';
            html += '<div class="s_pr_desc">' + product_code + ' - ' + product_name + '</div>';
            html += '<div class="s_pr_desc" style="margin-top: 5px;"><small>Колличество: <span id="cp_' + product_id + '">1</span> <a style="text-decoration: underline;" onclick="orders.show_ect(' + product_id + ');">Изменить</a></small></div>';
            html += '<div onclick="orders.delete_search_product(this)" class="absolute __close cursor_p">';
              html += '<i class="fa fa-close" aria-hidden="true"></i>';
            html += '</div>';
          html += '</div>';
          $('#o_cont_add_products').append(html);
          $('#s_pr_res').hide();
        } else{
          alert(data.string);
        }
        $('#disable_s_product').hide();
      },
      error: function(err){
        console.log(err);
        $('#disable_s_product').hide();
      }
    });
  },
  show_ect: function(product_id) {
    var now_count = parseInt($('#cp_' + product_id).text());
    now_count = !isNaN(now_count) && now_count > 0 ? now_count : 1;
    var result = prompt('Введите новое количетсво',now_count);
    result = parseInt(result);
    if(!isNaN(result) && result > 0){
      $('#cp_' + product_id).text(result);
    }
  },
  edit_count_products: function(element){
    var _this = orders;
    var item_id = $(element).attr('data-itemid');
    var now_count = $(element).attr('data-count');
    var result = prompt('Введите новое количетсво',now_count);
    result = parseInt(result);
    if(!isNaN(result) && result > 0){
      $.ajax({
        url: _this.url,
        method: 'post',
        dataType: 'json',
        data: {
          action: 'edit_count',
          item_id: item_id,
          count: result
        },
        success: function(data){
          if(data.result == 'true'){
            $('#o_count_' + item_id).text(result);
            $('.o_t_summa_' + data.order_id).text(data.order_total_summa);
            $('.o_i_t_summa_' + data.order_id + '_' + item_id).text(data.order_item_total_summa);
            $(element).attr('data-count',result);
          } else{
            console.log(data.string);
          }
        },
        error: function(err){
          console.log(err);
        }
      });
    }
  },
  delete_product: function(element) {
    var _this = orders;
    var item_id = $(element).attr('data-itemid');
    console.log(item_id);
    $.ajax({
      url: _this.url,
      method: 'post',
      dataType: 'json',
      data: {
        action: 'delete_product',
        item_id: item_id
      },
      success: function(data){
        if(data.result == 'true'){
          $('#o_item_' + item_id).remove();
          $('.o_t_summa_' + data.order_id).text(data.order_total_summa);
        } else{
          console.log(data.string);
        }
      },
      error: function(err){
        console.log(err);
      }
    });
  },
  set_get_param: function(name,value){
    var now_url = window.location.href;
    var new_url = '';
    now_url = now_url.split('?')[1];

    if(now_url.indexOf(name) >= 0){
      var param = now_url.split(name + '=')[1];
      param = param.split('&')[0];
      var g_param = name + '=' + param;
      new_url = now_url.replace(g_param, '');
      new_url = new_url.replace('&' + g_param, '');
      new_url += '&' + name + '=' + value;
      if(new_url.charAt(0) == '&') new_url = new_url.slice(1);
    } else{
      new_url = now_url + '&' + name + '=' + value;
    }
    new_url = '?' + new_url;

    window.history.pushState(null, null, new_url);
  },
  remove_get_param: function(name){
    var now_url = window.location.href;
    if(now_url.indexOf(name) >= 0){
      var param = now_url.split(name + '=')[1];
      param = param.split('&')[0];
      var g_param = name + '=' + param;
      var new_url = now_url.replace('&' + g_param, '');
      new_url = new_url.replace(g_param, '');
      window.history.pushState(null, null, new_url);
    }
  },
  show_order: function(element,order_id){
    var _this = orders;
    if($('#order_' + order_id).css('display') == 'none'){
      if($('#order_conent_' + order_id).html() == ''){
        $('body').addClass('pr__load_');
        $.ajax({
          url: _this.url,
          method: 'post',
          dataType: 'json',
          data: {
            action: 'show_order',
            order_id: order_id
          },
          success: function(data){
            if(data.result == 'true'){
              $('#order_conent_' + order_id).html(data.html);
              $('#order_' + order_id).animate({'height':'show'},300);
              $(element).removeClass('collapsed');
            } else{
              console.log(data.string);
            }
            $('body').removeClass('pr__load_');
          },
          error: function(err){
            console.log(err);
            $('body').removeClass('pr__load_');
          }
        });

      } else{
        $(element).removeClass('collapsed');
        $('#order_' + order_id).animate({'height':'show'},300);
      }
      _this.set_get_param('view_order',order_id);
    } else{
      $(element).addClass('collapsed');
      $('#order_' + order_id).animate({'height':'hide'},300);
      _this.remove_get_param('view_order');
    }
  }
};
