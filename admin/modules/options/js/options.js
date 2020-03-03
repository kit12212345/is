var __options__ = {
  current_item_id: 0,
  show_options_modal: function(){
    this.rewrite_add_properts();
    $('#opt_child_count').val('');
    $('#btn_save_options').text('Добавить выбраные');
    $('#btn_save_options').attr('data-event','add');
    return this.show_modal('modal_eto');
  },
  show_faster_select_options: function(){
    var result = this.rewrite_fast_options();
    if(result !== true) return false;
    return this.show_modal('modal_seto',function(){
      $('#modal_scroll_seto').animate({scrollTop:0},0);
    });
  },
  init_options: function(){
    var options = $('#properts_object').attr('data-info');
    this.options = JSON.parse(options).options;
    this.allowed_properts = JSON.parse(options).allowed_properts;
  },
  save_product_value: function(element){
    var _this = this;
    var value = $(element).val();
    var _event = $(element).attr('data-event');
    var product_id = $(element).attr('data-productid');

    var type_operation = typeof window.type_operation !== 'undefined' ? window.type_operation : false;
    var document_id = typeof current_document_id !== "undefined" ? current_document_id : 0;

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'save_product_value',
        type_operation: type_operation,
        document_id: document_id,
        product_id: this.current_product_id,
        'event': _event,
        product_id: product_id,
        value: value
      },
      success: function(data){
        if(data.result == 'true'){

          if(_this.options.hasOwnProperty(product_id) && _this.options[product_id].hasOwnProperty(_event)){
            _this.options[product_id][_event] = value;
          }

          if(type_operation == 'inventory'){
            $('#ep_count').val(data.product_quantity);
          }


        } else{
          message.show(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  },
  get_selected_properts: function(){
    var items = []
    var length = $('.select_propert').length;

    for (var i = 0; i < length; i++) {
      items.push($('.select_propert').eq(i).val());
    }

    return items;

  },
  save_options: function(is_faster){
    var _this = this;
    var selected_items = this.get_selected_properts();
    var count = $('#opt_child_count').val();
    var _event = $('#btn_save_options').attr('data-event');
    _event = _event != 'add' && _event != 'save' ? 'add' : _event;

    var type_operation = typeof window.type_operation !== 'undefined' ? window.type_operation : false;

    var values = typeof is_faster !== 'undefined' && is_faster === true ? this.get_sto_values() : [];
    $('#preloader_body').show();
    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'save_options',
        'event': _event,
        type_operation: type_operation,
        product_id: this.current_product_id,
        selected_items: selected_items,
        item_id: _this.current_item_id,
        count: count,
        values: values
      },
      success: function(data){
        $('#preloader_body').hide();
        if(data.result == 'true'){

          if(!data.hasOwnProperty('allowed_properts')) return _this.handle_add_faster_option(data,function(){
            _this.replace_html_thead();
            if(type_operation == 'inventory'){
              $('#ep_count').val(data.product_quantity);
            }
            _this.rewrite_options();
            return _this.close_modal('modal_seto');
          });


          _this.allowed_properts = data.allowed_properts;
          _this.replace_html_thead();

          if(_event == 'add') _this.handle_add_option(data);
          if(_event == 'save') _this.handle_change_option(data);

          if(type_operation == 'inventory'){
            $('#ep_count').val(data.product_quantity);
          }

          _this.rewrite_options();
          return _this.close_modal('modal_eto');
        } else{
          message.show(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  },
  handle_add_faster_option: function(data,_call){

    for (var key in data) {
      if (data.hasOwnProperty(key)) {
        if(typeof data[key] !== 'object') continue;
        var item = data[key];

        this.handle_add_option(item);
      }
    }


    this.allowed_properts = item.allowed_properts;

    if(typeof _call !== 'undefined') return _call();

  },
  show_edit_option: function(item_id){
    this.current_item_id = item_id;
    this.rewrite_add_properts(item_id);

    var count = this.options.hasOwnProperty(item_id) ? this.options[item_id].quantity : '';

    $('#opt_child_count').val(count);
    $('#btn_save_options').text('Сохранить изменения');
    $('#btn_save_options').attr('data-event','save');
    return this.show_modal('modal_eto');
  },
  handle_change_option: function(data){
    if(!this.options.hasOwnProperty(data.child_product)) return message.show('Ошибка при изменении');

    this.options[data.child_product].price = data.price;
    this.options[data.child_product].quantity = data.quantity;
    this.options[data.child_product].items = {};

    var items = data.add_items;

    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      this.options[data.child_product].items[it.parent_propert_id] = {
        id: it.propert,
        name: it.propert_name
      };
    }

    return true;
  },
  handle_add_option: function(data){
    this.options[data.child_product] = {
      price: data.price,
      quantity: data.quantity,
      items: {}
    };
    var items = data.add_items;

    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      this.options[data.child_product].items[it.parent_propert_id] = {
        id: it.propert,
        name: it.propert_name,
        color: it.propert_color
      };
    }

    return true;
  },
  create_html_option: function(data){
    var html = '<tr id="opt_child_' + data.child_product + '">';
    var items = data.items;

    for(var i = 0; i < items.length; i++){
      html += '<td>' + items[i] +' </td>';
    }

    html += '<td><input placeholder="0" class="i_po_val" type="text" onkeyup="return products_options.save_product_value(this)" data-event="price" data-productid="' + data.child_product + '" value="' + data.price + '"></td>';
    html += '<td><input placeholder="0" class="i_po_val" type="text" onkeyup="return products_options.save_product_value(this)" data-event="quantity" data-productid="' + data.child_product + '" value="' + data.quantity + '"></td>';
    html += '<td class="text_center"><i onclick="return products_options.show_edit_option(' + data.child_product + ')" class="cursor_p g_color icon-pencil"></i></td>';
    html += '<td class="text_center"><i onclick="return products_options.delete_option(' + data.child_product + ')" class="cursor_p r_color icon-cross2"></i></td>';
    html += '</tr>';
    return html;
  },
  delete_option: function(item_id){
    var _this = this;
    if(!confirm('Вы действительно хотите удалить это?')) return false;

    var type_operation = typeof window.type_operation !== 'undefined' ? window.type_operation : false;
    var document_id = typeof current_document_id !== "undefined" ? current_document_id : 0;

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'delete_option',
        type_operation: type_operation,
        document_id: document_id,
        item_id: item_id
      },
      success: function(data){
        if(data.result == 'true'){
          _this.allowed_properts = data.allowed_properts;

          if(_this.options.hasOwnProperty(item_id)) delete _this.options[item_id];

          _this.replace_html_thead();
          _this.rewrite_options();

          if(type_operation == 'inventory'){
            $('#ep_count').val(data.product_quantity);
          }

        } else{
          message.show(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  }
};
