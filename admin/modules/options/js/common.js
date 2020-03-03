Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


var products_options = {
  url: '/admin/modules/options/ajax.php',
  default_properts: ['Цена','Количество','',''],
  allowed_properts: {},
  current_product_id: 0,
  init: function(){
    var commom_object = function(a,b){
        var obj3 = {};
        for (var attrname in a) { obj3[attrname] = a[attrname]; }
        for (var attrname in b) { obj3[attrname] = b[attrname]; }
        return obj3;
    }
    this.__proto__ = commom_object(__properts__,__options__);
    this.init_properts();
    this.init_options();
    console.log(catalog.current_product_id);
    this.current_product_id = catalog.current_product_id;
  },
  close_modal: function(name){
    $('#' + name).hide();

    var close_bg = true;
    for (var i = 0; i < $('._modal_body').length; i++) {
      if($('._modal_body').eq(i).css('display') == 'block') close_bg = false;
    }

    if(close_bg === true){
      $('body').removeClass('hide_scroll');
      $('body').removeClass('modal_options_show');
      document.body.style.paddingRight = '0px';
      $('#__modal_bg').hide();
    }

  },
  show_modal: function(name,_call){
    $('body').addClass('modal_options_show');
    var body_width = document.body.offsetWidth;
    $('body').addClass('hide_scroll');
    $('#__modal_bg').show();
    $('#' + name).show();
    var diff_body_width = document.body.offsetWidth - body_width;
    document.body.style.paddingRight = diff_body_width + 'px';

    if(typeof _call !== 'undefined') return _call();
  },
  replace_html_thead: function(){
    var html = '<tr>';


    var items = this.allowed_properts;
    var keys = Object.keys(items);

    for (var i = 0; i < keys.length; i++) {
      var id = keys[i];
      var name = items[id];
      html += '<th id="th_item_' + id + '">' + name + '</th>';
    }

    for (var i = 0; i < this.default_properts.length; i++) {
      var name = this.default_properts[i];
      html += '<th>' + name + '</th>';
    }

    html += '</tr>';

    $('#thead_options').html(html);

    return true;

  },
  rewrite_selected_properts: function(){
    var properts = this.properts;
    var html = '';
    for (var key in properts) {
      if (properts.hasOwnProperty(key)) {
        var propert_id = key;
        var propert_name = properts[key].name;

        var selected = this.selected_properts.indexOf(propert_id) >= 0 ? 'active_select_prop_item' : '';

        html += '<div data-itemid="' + propert_id + '" onclick="products_options.select_propert(this);" class="select_prop_item ' + selected + '">';
          html += '<span>' + propert_name + '</span>';
        html += '</div>';

      }
    }

    $('#body_select_properts').html(html);

  },
  rewrite_add_properts: function(item_id){
    var html = '';

    for (var value in this.properts) {
      if(this.properts.hasOwnProperty(value)){
        var propert_id = value;
        var propert_name = this.properts[value].name;
        var child = this.properts[value].items;

        if(this.selected_properts.indexOf(propert_id) == -1) continue;


        html += '<div class="form-group mb-10">';
        html += '<label class="control-label full_w">' + propert_name + ':</label>';
        html += '<div class="full_w">';
        html += '<select class="form-control select_propert">';
        html += '<option value="0">Не выбрано</option>';

        for (var child_id in child) {
          if(child.hasOwnProperty(child_id)){
            var selected = '';
            var _child_id = child[child_id].id;
            var child_name = child[child_id].name;


            if(typeof item_id !== "undefined"){
              if(this.options.hasOwnProperty(item_id)){
                var __items = this.options[item_id].items;
                selected = __items.hasOwnProperty(propert_id) && __items[propert_id].id == _child_id ? 'selected="selected"' : '';
              }
            }

            html += '<option ' + selected + ' value="' + _child_id + '">' + child_name + '</option>';
          }
        }

        html += '</select>';
        html += '</div>';
        html += '<div class="clear"></div>';
        html += '</div>';



      }
    }

    $('#wrap_add_properts').html(html);

  },
  rewrite_options: function(data){
    var html = '';
    var options = this.options;
    var allowed_properts = this.allowed_properts;

    if(Object.size(options)){

      for (var key in options) {
        if (options.hasOwnProperty(key)) {
          var item = options[key];
          var product_id = key;

          html += '<tr id="opt_child_' + product_id + '">';

          var items = item.items;

          var keys_properts = Object.keys(allowed_properts);

          for (i = 0; i < Object.size(allowed_properts); i++) {

            // var _nm_ = items[keys_properts[i]].hasOwnProperty('name') ?  items[keys_properts[i]].name : '';

            var _val = items.hasOwnProperty(keys_properts[i]) && items[keys_properts[i]].color ? '<div class="opt_backr" style="background: ' + items[keys_properts[i]].color + '"></div>' : items[keys_properts[i]].name;

            var val = items.hasOwnProperty(keys_properts[i]) ? _val : '———';

            html += '<td>' + val + '</td>';
          }

          item.quantity = item.quantity == 0 ? '' : item.quantity;
          item.price = item.price == 0 ? '' : item.price;

          html += '<td><input style="min-width: 70px;" placeholder="0" class="i_po_val" type="number" onkeyup="return products_options.save_product_value(this)" data-event="price" data-productid="' + product_id + '" value="' + item.price + '"></td>';
          html += '<td><input style="min-width: 70px;" placeholder="0" class="i_po_val" type="number" onkeyup="return products_options.save_product_value(this)" data-event="quantity" data-productid="' + product_id + '" value="' + item.quantity + '"></td>';
          html += '<td class="text_center"><i onclick="return products_options.show_edit_option(' + product_id + ')" class="cursor_p g_color icon-pencil"></i></td>';
          html += '<td class="text_center"><i onclick="return products_options.delete_option(' + product_id + ')" class="cursor_p r_color icon-cross2"></i></td>';
          html += '</tr>';


        }
      }

    } else{
      html += '<tr>';
      html += '<td colspan="20" class="text_center"><strong>Нет видов</strogn></td>';
      html += '</tr>';
    }

    $('#tbody_options').html(html);


  },
  rewrite_fast_options: function(data){
    var html = '';
    var properts = this.properts;
    var allowed_properts = this.allowed_properts;

    var _key = this.search_key_size();

    if(typeof _key === 'undefined') return message.show('Не выбрано свойство с размером');


    html += '<table class="table_options">';
      html += '<thead id="thead_spb_options">';
        html += '<tr>';
          html += '<th>';
            html += '<input type="checkbox" name="" value="">';
          html += '</th>';

          for (var key in properts) {
            if (properts.hasOwnProperty(key)) {


              if(this.selected_properts.indexOf(key) == -1) continue;


              html += '<th id="th_fast_item_' + properts[key].id + '">' + properts[key].name + '</th>';
            }
          }


        html += '</tr>';
      html += '</thead>';
      html += '<tbody id="tbody_spb_options">';


      var count_child_items = Object.size(properts[_key].items);

        if(key !== false && count_child_items > 0){


          for (var pp_id in properts[_key].items) {
            if (properts[_key].items.hasOwnProperty(pp_id)) {


              var item = properts[_key].items[pp_id];
              var size_id = item.id;
              var size_name = item.name;

              html += '<tr class="sopt_child_hidden" id="sopt_child_' + size_id + '">';

              html += '<td>';
              html += '<input class="sto_checked" type="checkbox" value="' + size_id + '">';
              html += '</td>';

              html += '<td>';
              html += '<div class="sto_name">' + size_name + '</div>';
              html += '</td>';


              for (var ch in properts) {
                if (properts.hasOwnProperty(ch)) {

                  if(ch == _key) continue;

                  if(this.selected_properts.indexOf(ch) == -1){
                    continue;
                  }



                  var other_id = ch;
                  var other = properts[ch].items;

                  var active = 'active_sto';

                  html += '<td>';


                  for (var k in other) {
                    if (other.hasOwnProperty(k)) {

                      var _id = other[k].id;
                      var _name = other[k].name;
                      var _color = other[k].color;

                      var cls_color = _color ? 'hide_opt_color' : '';

                      if(_color){
                        html += '<div style="background: ' + _color + ';" onclick="products_options.set_active_propert(this)" data-size="' + size_id + '" data-typeid="' + other_id + '" data-itemid="' + _id + '" class="show_opt_color opt_color sto_item prms_s_' + size_id + '_' + other_id + ' ' + active + ' val_active_' + size_id + '"></div>';
                      } else{
                        html += '<div title="' + _name + '" onclick="products_options.set_active_propert(this)" data-size="' + size_id + '" data-typeid="' + other_id + '" data-itemid="' + _id + '" class="' + cls_color + ' sto_item prms_s_' + size_id + '_' + other_id + ' ' + active + ' val_active_' + size_id + '">' + _name + '</div>';
                      }
                      active = '';

                    }
                  }
                  html += '</td>';


                }
              }



              html += '</tr>';


            }
          }



        } else{
          html += '<tr>';
            html += '<td colspan="20" class="text_center"><strong>Нет свойств</strogn></td>';
            html += '</tr>';
        }

      html += '</tbody>';
    html += '</table>';


    $('#fast_options_content').html(html);

    return true;
  }
};
