var __properts__ = {
  show_properts_modal: function(){
    $('#selected_propert').val(0);
    this.handle_toggle_properts();
    return this.show_modal('modal_etp');
  },
  init_properts: function(){
    var properts = $('#properts_object').attr('data-info');
    this.properts = JSON.parse(properts).properts;
    this.selected_properts = JSON.parse(properts).selected_properts;
  },
  show_selected_properts_modal: function(){
    this.rewrite_selected_properts();
    return this.show_modal('modal_set_properts');
  },
  select_propert: function(element){
    var item_id = $(element).attr('data-itemid');
    if(typeof item_id === 'undefined' || !item_id) return message.show('Ошибка совсем неожиданная');

    var current_selected_items = getCookie('selected_properts');
    current_selected_items = current_selected_items == 0 ? "[]" : current_selected_items;
    current_selected_items = JSON.parse(current_selected_items);
    var index = current_selected_items.indexOf(item_id);
    if(index >= 0){
      current_selected_items.splice(index,1);
      $(element).removeClass('active_select_prop_item');

      var obj_index = this.selected_properts.indexOf(item_id);
      if(obj_index >= 0) this.selected_properts.splice(obj_index,1);

    } else{
      current_selected_items.push(item_id);
      this.selected_properts.push(item_id);
      $(element).addClass('active_select_prop_item');
    }
    var items = JSON.stringify(current_selected_items);

    var date = new Date;
    date.setDate(date.getDate() + 100000);
    return document.cookie = "selected_properts" + "=" + items + "; path=/; expires=" + date.toUTCString();
  },
  handle_toggle_properts: function(element){
    var val = typeof element === 'undefined' ? 0 : $(element).val();
    if(val == 0){
      $('#modal_body_etp').removeClass('nw_selected_propert');
    } else {
      $('#modal_body_etp').addClass('nw_selected_propert');
      this.create_add_properts_html(val);
    }
  },
  add_main_propert: function(element){
    var result = prompt('Введите название свойства');
    if(!result) return false;

    return this.save_propert({
      is_parent: true,
      name: result
    });

  },
  save_propert: function(data){
    var _this = this;
    var data = typeof data === 'undefined' ? {} : data;
    var is_parent = data.hasOwnProperty('is_parent') ? data.is_parent : false;
    var item_id = data.hasOwnProperty('item_id') ? data.item_id : 0;

    var name = data.hasOwnProperty('name') ? data.name : $('#propert_name').val();
    var parent_id = data.hasOwnProperty('parent_id') ? data.parent_id : $('#selected_propert').val();
    parent_id = is_parent && !data.hasOwnProperty('parent_id') ? 0 : parent_id;

    if(!name) return message.show('Название не может быть пустым');
    if(!is_parent && parent_id == 0) return message.show('Выберите свойство');

    var _event = data.hasOwnProperty('_event') ? data._event : 'add';

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'save_propert',
        'event': _event,
        item_id: item_id,
        name: name,
        parent_id: parent_id
      },
      success: function(data){
        data.parent_id = parent_id;
        if(data.result == 'true'){

          if(_event == 'save' && !is_parent) return _this.handle_save_child_propert(data);
          if(_event == 'save' && is_parent) return _this.handle_save_parent_propert(data);
          if(!is_parent) return _this.handle_add_child_propert(data);

          return _this.handle_add_parent_propert(data);

        } else{
          message.show(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  },
  show_edit_propert: function(element){
    var is_parent = !element;
    var item_id = 0;
    var name = '';

    if(is_parent === true){
      item_id = $('#selected_propert').val();
      if(item_id == 0) return message.show('Выберите свойство');
      name = $('#parent_propert_' + item_id).text();
    } else{
      name = $(element).attr('data-name');
      item_id = $(element).attr('id');
    }

    var new_name = prompt('Изменение свойства',name);
    if(new_name == '') return message.show('Название не может быть пустым');

    return this.save_propert({
      _event: 'save',
      item_id: item_id,
      name: new_name,
      is_parent: is_parent
    });

  },
  handle_save_child_propert: function(data){
    if(this.properts.hasOwnProperty(data.parent_id))
      this.properts[data.parent_id].items[data.id].name = data.name;
    return $('#po_child_name_' + data.id).text(data.name);
  },
  handle_save_parent_propert: function(data){
    return $('#parent_propert_' + data.id).text(data.name);
  },
  handle_add_parent_propert:function(data){
    $('#propert_name').val('');
    $('#propert_name').focus();

    this.properts[data.id] = {};
    this.properts[data.id].name = data.name;
    this.properts[data.id].items = {};

    $('#selected_propert').append('<option id="parent_propert_' + data.id + '" value="' + data.id + '">' + data.name + '</option>');
    $('#selected_propert').val(data.id);

    var element = $('#parent_propert_' + data.id);

    return this.handle_toggle_properts(element);

  },
  handle_add_child_propert:function(data){
    $('#propert_name').val('');
    $('#propert_name').focus();


    if(this.properts.hasOwnProperty(data.parent_id)){
      var keys = Object.keys(this.properts[data.parent_id].items);
      this.properts[data.parent_id].items[parseInt(keys[keys.length - 1]) + 1] = {
        id: data.id,
        name: data.name
      };
    }

    return this.create_add_properts_html(data.parent_id);

  },
  create_add_properts_html: function(parent_id){
    var html = '';

    var child = this.properts.hasOwnProperty(parent_id) ? this.properts[parent_id] : false;
    if(child === false) return message.show('Произошла ошибка при отображении параметров');
    child = child.items;
    var keys = Object.keys(child);

    // for (var i in child) {
    //   if (child.hasOwnProperty(i)) {
    //
    //     var id = child[i].id;
    //     var name = child[i].name;
    //
    //     html += '<li id="child_prop_' + id + '" data-itemid="' + id + '">';
    //       html += '<div class="i_block v_align_middle __name" id="po_child_name_' + id + '">' + name + '</div>';
    //       html += '<div class="i_block v_align_middle mr-10">';
    //       html += '<input class="po_color_edit po_current_color current_po_color_' + id + '  mr-5" data-itemid="' + id + '" type="text">';
    //       html += '<div data-itemid="' + id + '" class="po_color_edit po_selecr_color"><i class="icon-eyedropper2"></i></div>';
    //       html += '</div>';
    //       html += '<span class="i_block v_align_middle">';
    //         html += '<i data-name="' + name + '" id="' + id + '" onclick="return products_options.show_edit_propert(this)" class="mr-5 cursor_p g_color icon-pencil"></i>&nbsp;&nbsp;';
    //         html += '<i class="cursor_p r_color icon-cross2" id="' + id + '" data-type="child" onclick="return products_options.delete_propert(this)"></i>';
    //       html += '</span>';
    //     html += '</li>';
    //
    //   }
    // }

    for (var i in child) {
      if (child.hasOwnProperty(i)) {

        var id = child[i].id;
        var name = child[i].name;

      html += '<li id="child_prop_' + id + '" data-itemid="' + id + '">';
        html += '<div class="i_block v_align_middle __name" id="po_child_name_' + id + '">' + name + '</div>';
        html += '<div class="i_block v_align_middle mr-10">';
        html += '<input class="po_color_edit po_current_color current_po_color_' + id + '  mr-5" data-itemid="' + id + '" type="text">';
        html += '<div data-itemid="' + id + '" class="po_color_edit po_selecr_color"><i class="icon-eyedropper2"></i></div>';
        html += '</div>';
        html += '<span class="i_block v_align_middle">';
          html += '<i data-name="' + name + '" id="' + id + '" onclick="return products_options.show_edit_propert(this)" class="mr-5 cursor_p g_color icon-pencil"></i>&nbsp;&nbsp;';
          html += '<i class="cursor_p r_color icon-cross2" id="' + id + '" data-type="child" onclick="return products_options.delete_propert(this)"></i>';
        html += '</span>';
      html += '</li>';
    }

    }

    if(keys.length == 0){
      html = '<li class="full_w padd_ten"><strong>Нет параметров</strong></li>';
    }



    $('#po_params_items').html(html);


    // $('.po_selecr_color').each(function(i, elem){
    //
    //
    //   var init = $(elem).colorpicker({
    //     useAlpha: false
    //   }).on('hide', function (e) {
    //     $('body').hide();
    //     console.log('asdasd');
    //   });
    //
    // });


    $( "#po_params_items" ).sortable({
      axis: "y",
      update: function(event, ui) {
        products_options.update_positions_properts();

      }
    });
    $( "#po_params_items" ).disableSelection();


    // $('.po_selecr_color').each(function(i, elem){
    //   var item_id = $(elem).attr('data-itemid');
    //   var hueb = new Huebee(elem,{
    //     setText: '.current_po_color_' + item_id,
    //     setBGColor: '.current_po_color_' + item_id
    //   });
    //   hueb.on('change', function(color,hue,sat,lum){
    //     // console.log(item_id + 'color changed to: ' + color)
    //   });
    //
    //   $('.current_po_color_' + item_id).on('change',function(){
    //     console.log($(this).val());
    //   });
    // });



  },
  update_positions_properts: function(){
    var _this = this;
    var positions = [];

    for (var i = 0; i < $('#po_params_items').children('li').length; i++) {
      var item_id = $('#po_params_items').children('li').eq(i).attr('data-itemid');

      positions.push(item_id);
    }


    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'update_properts_positions',
        positions: positions
      },
      success: function(data){
        if(data.result == 'true'){
        } else{
          message.show(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });


  },
  find_propert_index: function(parent_id,propert_id){
    for (var i = 0; i < this.properts[parent_id].length; i++) {
      if(_this.properts[parent_id][i]['id'] == propert_id) return i;
    }
    return false;
  },
  delete_propert: function(element){
    if(!confirm('Вы действительно хотите удалить это?')) return false;
    var _this = this;
    if(typeof element === 'undefined') return message.show('Свойство не найдено');

    var propert_id = element === false ? $('#selected_propert').val() : $(element).attr('id');
    var type = element === false ? 'parent' : 'child';

    $.ajax({
      url: _this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'delete_propert',
        propert_id: propert_id
      },
      success: function(data){
        if(data.result == 'true'){

          if(type == 'child'){
            var parent_id = data.parent_id;


            if(!_this.properts.hasOwnProperty(parent_id)) return message.show('Не найден родитель');

            var propert_index = _this.find_propert_index(parent_id,propert_id);

            delete _this.properts[parent_id].items[propert_index];

            return _this.create_add_properts_html(parent_id);

          } else{

            $('#parent_propert_' + propert_id).remove();

            delete _this.properts[propert_id];

            return _this.handle_toggle_properts();
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
  /*Все виды*/
  set_active_propert: function(element){
    var size_id = $(element).attr('data-size');
    var type_id = $(element).attr('data-typeid');

    $('.prms_s_' + size_id + '_' + type_id).removeClass('active_sto');
    $(element).addClass('active_sto');
  },
  hide_sto_other: function(){
    $('.sopt_child_hidden').hide();
    for (var i = 0; i < $('.sto_checked:checked').length; i++) {
      var item = $('.sto_checked:checked').eq(i);
      var item_id = $(item).val();
      $('#sopt_child_' + item_id).show();
    }
  },
  get_sto_values: function(){
    var values = [];

    for (var i = 0; i < $('.sto_checked:checked').length; i++){
      var item = $('.sto_checked:checked').eq(i);
      var item_id = $(item).val();
      values[i] = [];
      values[i].push(item_id);

      for (var c = 0; c < $('.val_active_' + item_id).length; c++) {
        if($('.val_active_' + item_id).eq(c).hasClass('active_sto')){

          values[i].push($('.val_active_' + item_id).eq(c).attr('data-itemid'));


        }
      }

    }

    console.log(values);
    return values;

  },
  search_key_size: function(){
    var key = false;
    var properts = this.properts;
    for (var _key in properts) {
      if (properts.hasOwnProperty(_key)){
        var name = properts[_key].name;

        if(this.selected_properts.indexOf(_key) == -1) continue;
        if(name == 'Размер' || name == 'размер' || name == 'Размеры' || name == 'размеры'){
          return _key;
        }

      }
    }
  }


  /**/
};
