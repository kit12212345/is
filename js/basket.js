var basket = {
  url: '/ajax/ajax_basket.php',
  timer: null,
  l_change_quan: function(element){
    clearTimeout(this.timer);
    this.timer = setTimeout(function(){
      return basket.change_quan(element);
    },300);
  },
  add: function(element){
    var id = $(element).data('itemid');
    var quan = $('.bsk_quan_' + id).eq(0).val();
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
        quan: quan
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
  change_quan: function(element){
    var id = $(element).data('itemid'), dir = $(element).data('dir');
    var quan = $('.bsk_quan_' + id).eq(0).val();
    dir = typeof dir !== 'undefined' ? dir != 'm' && dir != 'p' ? 'p' : dir : false;

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
