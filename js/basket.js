var basket = {
  url: '/ajax/ajax_basket.php',
  timer: null,
  l_change_quan: function(element){
    clearTimeout(this.timer);
    this.timer = setTimeout(function(){
      return basket.change_quan(element);
    },300);
  },
  pick_option: function(element){
    var propert_id = $(element).data('propertid');
    var option_id = $(element).data('optid');
    show_p(propert_id,option_id);

    $('#vp_propert_' + propert_id).children('.opt_item').removeClass('active_opt');
    return $(element).addClass('active_opt');
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
