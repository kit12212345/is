var basket = {
  url: '/ajax/ajax_basket.php',
  timer: null,
  checkout: function(){
    var first_name = $('#co_first_name').val(),
    last_name = $('#co_last_name').val(),
    phone = $('#co_phone').val(),
    country = $('#co_country').val(),
    state = $('#co_state').val(),
    city = $('#co_city').val(),
    address = $('#co_address').val(),
    zip = $('#co_zip').val(),
    comment = $('#co_comment').val(),
    dm_id = $('input[name="dm_item"]:checked').val();

    if(!first_name) return message.show('Введите имя');
    if(!last_name) return message.show('Введите фамилию');
    if(!phone) return message.show('Введите телефон');
    if(!city) return message.show('Укажите город');
    if(!address) return message.show('Укажите адрес');
    if(!zip) return message.show('Укажите почтовый индекс');

    $.ajax({
      url: this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'checkout',
        first_name: first_name,
        last_name: last_name,
        phone: phone,
        country: country,
        state: state,
        city: city,
        address: address,
        zip: zip,
        comment: comment,
        dm_id: dm_id
      },
      success: function(data){
        if(data.result == 'true'){

        } else{
          alert(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });


  },
  l_change_quan: function(element){
    clearTimeout(this.timer);
    this.timer = setTimeout(function(){
      return basket.change_quan(element);
    },300);
  },
  pick_option: function(element){
    var propert_id = $(element).data('propertid');
    var option_id = $(element).data('optid');

    var is_remove = $(element).hasClass('active_opt');

    $('.opt_item').addClass('disabled_opt_item');

    if(!$(element).hasClass('active_opt')){
      $('#opt_' + propert_id + '_' + option_id).removeClass('disabled_opt_item');

      for (var prop in allowed[propert_id][option_id]) {
        for (var i = 0; i < allowed[propert_id][option_id][prop].length; i++) {
          $('#opt_' + prop + '_' + allowed[propert_id][option_id][prop][i]).removeClass('disabled_opt_item');
        }
      }

      $('#vp_propert_' + propert_id).children('.opt_item').removeClass('active_opt');
      $(element).addClass('active_opt');

    } else{
      $(element).removeClass('active_opt');

      var count_active_options = $('.active_opt').length;

      if(count_active_options > 0){

        for (var i = 0; i < $('.active_opt').length; i++) {
          var item = $('.active_opt').eq(i);
          var i_propert_id = $(item).data('propertid');
          var i_option_id = $(item).data('optid');

          for (var prop in allowed[i_propert_id][i_option_id]) {
            for (var i = 0; i < allowed[i_propert_id][i_option_id][prop].length; i++) {
              console.log($('#opt_' + prop + '_' + allowed[i_propert_id][i_option_id][prop][i]));
              $('#opt_' + prop + '_' + allowed[i_propert_id][i_option_id][prop][i]).removeClass('disabled_opt_item');

            }
          }

        }

        $('.active_opt').removeClass('disabled_opt_item');

      } else{
        for (var val in options) {

          for (var item__id in options[val]['items']) {
            $('.item_opt_' + options[val]['items'][item__id]['id']).removeClass('disabled_opt_item');
          }

        }
      }


    }

  },
  get_selected_options: function(){
    var selected_options = [];
    var active_items = $('.active_opt');
    var length = active_items.length;
    for (var i = 0; i < active_items.length; i++) {
      var item = active_items[i];
      selected_options.push({
        propert_id: $(item).data('propertid'),
        option_id: $(item).data('optid')
      });
    }

    return selected_options;
  },
  add: function(element){
    var id = $(element).data('itemid');
    var quan = $('.bsk_quan_' + id).eq(0).val();
    var selected_options = this.get_selected_options();
    quan = parseInt(quan);
    quan = isNaN(quan) ? 1 : quan;
    quan = quan <= 0 ? 1 : quan;

    $.ajax({
      url: this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'add',
        item_id: id,
        quan: quan,
        selected_options: selected_options
      },
      success: function(data){
        if(data.result == 'true'){

        } else{
          alert(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  },
  get_basket: function(){
    var basket = getCookie('basket');
    return basket == 0 ? [] : basket;
  },
  spl_update_quan: function(element){
    var id = $(element).data('itemid'), dir = $(element).data('dir');
    var quan = $('.bsk_quan_' + id).eq(0).val();
    dir = typeof dir !== 'undefined' ? dir != 'm' && dir != 'p' ? 'p' : dir : false;
    if(dir == 'm') quan--;
    else if(dir == 'p') quan++;
    quan = quan < 1 ? 1 : quan;
    $('.bsk_quan_' + id).val(quan);
  },
  change_quan: function(element){
    var id = $(element).data('itemid'), dir = $(element).data('dir');
    var quan = $('.bsk_quan_' + id).eq(0).val();
    dir = typeof dir !== 'undefined' ? dir != 'm' && dir != 'p' ? 'p' : dir : false;

    this.spl_update_quan(element);

    $.ajax({
      url: this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'change_quan',
        item_id: id,
        quan: quan,
        dir: dir
      },
      success: function(data){
        if(data.result == 'true'){
          $('.bsk_quan_' + id).val(data.quan);
        } else{
          alert(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  },
  remove: function(id){
    var id = typeof id === 'undefined' || !id ? 0 : id;
    $.ajax({
      url: this.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'remove',
        item_id: id
      },
      success: function(data){
        if(data.result == 'true'){

        } else{
          alert(data.string);
        }
      },
      error: function(e){
        console.log(e);
      }
    });
  }
};
