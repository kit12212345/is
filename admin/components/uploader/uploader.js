var Uploader = function(data){
  const self = this;
  this.upload_url = '/admin/components/uploader/uploader.php';
  this.item_id = '';
  this._event = '';
  this.accepted_ext =  ['.jpeg','.jpg', '.png'];
  this.accepted_file_size = 10000000;
  this.current_load_files = 0;
  this.arr_load_files = [];
  this.max_files = 1;
  this.files = [];
  this.init = function(){
    if(data.hasOwnProperty('url')){
      this.upload_url = data.url;
    }

    if(data.hasOwnProperty('value_item')){
      this.item_id = data.value_item;
    } else return console.log('set value_item');

    if(data.hasOwnProperty('_event')){
      this._event = data._event;
    } else return console.log('set event');

    if(data.hasOwnProperty('max_files')){
      this.max_files = data.max_files;
    }

    if(data.hasOwnProperty('max_file_size')){
      this.accepted_file_size = data.max_file_size;
    }

    if(data.hasOwnProperty('file_types')){
      this.accepted_ext = data.file_types;
    }

    if(this.max_files > 1){
      $('#file_list').attr('multiple','multiple');
    }

    $('#btn_load_img').on('click',this.select_images);

    var images_dlt = $('.upbtn_delete_image');

    for (var i = 0; i < images_dlt.length; i++) {
      $(images_dlt[i]).on('click',this.delete_image);
    }

    $("#load_images").sortable({
      update: function(){
        self.change_positions();
      }
    });
    $("#load_images").disableSelection();

  };
  this.hasExtension = function(str){
    var test_str=str.toLowerCase();
    return (new RegExp('(' + this.accepted_ext.join('|').replace(/\./g, '\\.') + ')$')).test(test_str);
  };
  this.select_images = function(){
    var i_file = $('#file_list');
    i_file.click();
    $(i_file).on('change',self.check_files);
  };
  this.check_files = function(){
    if (window.FileReader){
      var files = this.files;
      self.current_load_files += files.length;
      var count_files = files.length;
      for (var i = 0; i < count_files; i++){
        var fd = new FormData();
        if(!self.hasExtension(files[i].name)){
          message.show('Ошибка: файл' + ' ' + files[i].name + ' ' + 'имеет недопустимый формат');
          self.current_load_files--;
          count_files--;
          continue;
        }
        if(files[i].size > self.accepted_file_size){
          message.show('Ошибка: файл'  + ' ' + files[i].name + ' ' + 'слишком большой');
          self.current_load_files--;
          count_files--;
          continue;
        }
        fd.append('file',files[i]);
        var is_last = i == count_files - 1 ? true : false;
        self.upload(fd,is_last);
      }
    }
  };
  this._abort =function(){
    var index = this.id.split('_')[2];
    for (var i = 0; i < self.arr_load_files.length; i++) {
      if(self.arr_load_files[i].index == index){
        self.arr_load_files[i].request.abort();
      }
    }
    self.delete_ajx_request(index);
    $(this.parentNode).remove();
    document.getElementById('btn_upload').style.display = 'inline-block';
  };
  this.delete_image = function(e){

    if(!confirm('Вы действительно хотите удалить этот файл?')) return false;

    var btn = this;
    var image_id = this.parentNode.getAttribute('data-imageid');

    $.ajax({
      url: self.upload_url,
      dataType: 'json',
      method: 'post',
      data: {
        _event: self._event,
        action: 'delete',
        image_id: image_id,
        item_id: self.item_id
      },
      error: function (jqXHR, exception, errorThrown){
        var msg = '';
        if (jqXHR.status === 0) {
          msg = 'Not connect.\n Verify Network.';
        } else if (jqXHR.status == 404) {
          msg = 'Requested page not found. [404]';
        } else if (jqXHR.status == 500) {
          msg = 'Internal Server Error [500].';
        } else if (exception === 'parsererror') {
          msg = 'Requested JSON parse failed.';
        } else if (exception === 'timeout') {
          msg = 'Time out error.';
        } else if (exception === 'abort') {
          msg = 'Ajax request aborted.';
        } else {
          msg = 'Uncaught Error.\n' + jqXHR.responseText;
        }
        console.log(msg);
      },
      success: function(data) {
        if(data.result == 'true'){
          $(btn.parentNode).remove();
          self.get_image_positions();
          document.getElementById('btn_upload').style.display = 'inline-block';
        } else{
          message.show(data.string);
        }
      }
    });
  };
  this.get_image_positions = function() {
    var mi = 'Главное';
    var length = $('.l_img_item').length;
    var positions = [];
    if(length > 0) $('.main_img').remove();
    for (var i = 0; i < length; i++) {
      var item = $('.l_img_item')[i];
      if(i == 0){
        item.innerHTML += '<div class="absolute full_w main_img" style="display: block;">' + mi + '</div>';
      }
      var image_id = item.getAttribute('data-imageid');
      positions.push({
        image_id: image_id
      });
    }
    return positions;
  };
  this.change_positions = function(e){
    var positions = self.get_image_positions();

    $.ajax({
        url: self.upload_url,
        dataType: 'json',
        method: 'post',
        data: {
          _event: this._event,
          action: 'change_positions',
          positions: positions,
          item_id: self.item_id
        },
        success: function(data) {
          if(data.result == 'true'){
            console.log('change success');
          } else{
            message.show(data.string);
          }
        }
      });
  };
  this.create_image_item =function(){

    var length = $('.l_img_item').length;

    var item = document.createElement('div');
    item.setAttribute('class','relative i_block l_img_item');

    var item_image_body = document.createElement('div');
    item_image_body.setAttribute('class','l__image');

    var item_image = document.createElement('img');
    item_image.setAttribute('class','cover_img');
    item_image.src = '/images/add_image_icon.png';

    var btn_delete_image = document.createElement('button');
    btn_delete_image.type = 'button';
    btn_delete_image.id = 'dt_img_' + length;
    btn_delete_image.setAttribute('class','absolute close cursor_p');
    btn_delete_image.appendChild(document.createTextNode('×'));
    $(btn_delete_image).on('click',self._abort);


    var status_load_body = document.createElement('div');
    status_load_body.setAttribute('class','absolute full_w status_load');

    var status_load = document.createElement('div');
    status_load.setAttribute('class','prc_load_st');

    item_image_body.appendChild(item_image);
    status_load_body.appendChild(status_load);

    item.appendChild(item_image_body);
    item.appendChild(btn_delete_image);
    item.appendChild(status_load_body);

    var main_img_info = null;

    var is_main_img = $('.l_img_item').length == 0 ? true : false;

    if(is_main_img === true){
      var mi = typeof l !== 'undefined' ? l.main_image : 'Главное';

      main_img_info = document.createElement('div');
      main_img_info.setAttribute('class','absolute full_w main_img');
      main_img_info.appendChild(document.createTextNode(mi));
      main_img_info.style.display = 'none';

      item.appendChild(main_img_info);
    }

    return {
      index: length,
      item: item,
      status_load: status_load,
      hidden_info: main_img_info,
      image: item_image,
      btn_delete: btn_delete_image
    };
  };
  this.delete_empty_images = function(){
    var length = $('.l_img_item').length;
    for (var i = 0; i < length; i++) {
      var item = $('.l_img_item')[i];
      if(item.getAttribute('data-imageid') == ""){
        item.parentNode.removeChild(item);
        length--;
      }
    }
  };
  this.delete_ajx_request = function(index){
    for (var i = 0; i < this.arr_load_files.length; i++) {
      if(this.arr_load_files[i].index == index){
        this.arr_load_files.splice(i,1);
      }
    }
  };
  this.upload = function(fd,is_last){
    var alias = $('#post_alias_ru') === null ? '' : $('#post_alias_ru').value;

    var create_item = this.create_image_item();
    document.getElementById('load_images').appendChild(create_item.item);

    fd.append('_event',self._event);
    fd.append('action','upload');
    fd.append('item_id',self.item_id);
    fd.append('alias',alias);

    self.arr_load_files.push({
      index: create_item.index,
      request: $.ajax({
        xhr: function(){
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", function(evt) {
            if (evt.lengthComputable){
              var percentComplete = evt.loaded / evt.total;
              percentComplete = parseInt(percentComplete * 100);
              create_item.status_load.style.width = percentComplete + '%';
            }
          }, false);
          return xhr;
        },
        url: self.upload_url,
        dataType: 'json',
        method: 'post',
        data: fd,
        processData: false,
        contentType: false,
        error: function (jqXHR, exception, errorThrown){
          var msg = '';
          if (jqXHR.status === 0) {
            msg = 'Not connect.\n Verify Network.';
          } else if (jqXHR.status == 404) {
            msg = 'Requested page not found. [404]';
          } else if (jqXHR.status == 500) {
            msg = 'Internal Server Error [500].';
          } else if (exception === 'parsererror') {
            msg = 'Requested JSON parse failed.';
          } else if (exception === 'timeout') {
            msg = 'Time out error.';
          } else if (exception === 'abort') {
            msg = 'Ajax request aborted.';
          } else {
            msg = 'Uncaught Error.\n' + jqXHR.responseText;
          }
          console.log(msg);
        },
        success: function(data){
          if(data.result == 'true'){
            create_item.image.src = data.image;
            create_item.item.setAttribute('data-imageid',data.image_id);
            if(create_item.hidden_info) create_item.hidden_info.style.display = 'block';
            $(create_item.status_load.parentNode).remove();

            $(create_item.btn_delete).on('click',self.delete_image);

            self.delete_ajx_request(create_item.index);

          } else{
            self.delete_empty_images();
            self.current_load_files--;
            message.show(data.string);
          }
        }
      })});

      if(self.current_load_files >= self.max_files){
        $('#btn_upload').hide();
      }

      if(is_last === true){
        $('#file_list').val('');
      }

  };
  this.init();
};
