var upload_image = {
  upload_url: '',
  item_id: '',
  page: '',
  accepted_ext:  ['.jpeg','.jpg', '.png'],
  accepted_file_size: 10000000,
  current_load_files: 0,
  arr_load_files: [],
  max_files: 1,
  files: [],
  init: function(data){
    if(data.hasOwnProperty('url')){
      this.upload_url = data.url;
    } else{
      console.log('set url upload');
      return false;
    }
    if(data.hasOwnProperty('value_item')){
      this.item_id = data.value_item;
    } else{
      console.log('set value_item');
      return false;
    }
    if(data.hasOwnProperty('page')){
      this.page = data.page;
    } else{
      console.log('set action');
      return false;
    }

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
      gId('file_list').setAttribute('multiple','multiple');
    }

    this.current_load_files = gClass('l_img_item').length;
    if(this.current_load_files == this.max_files){
      gId('btn_upload').style.display = 'none';
    }

    _e.addEvent(gId('btn_load_img'),'click',this.select_images);
  },
  hasExtension: function(str){
    var test_str=str.toLowerCase();
    return (new RegExp('(' + this.accepted_ext.join('|').replace(/\./g, '\\.') + ')$')).test(test_str);
  },
  select_images: function(){
    var i_file = gId('file_list');
    i_file.click();
    _e.addEvent(i_file,'change',upload_image.check_files);
  },
  check_files: function(){
    if (window.FileReader){
      var _this = upload_image;
      var files = this.files;
      _this.current_load_files += files.length;
      var count_files = files.length;
      for (var i = 0; i < count_files; i++){
        var fd = new FormData();
        if(!_this.hasExtension(files[i].name)){
          alert('Ошибка: файл ' + files[i].name + ' имеет недопустимый формат');
          _this.current_load_files--;
          count_files--;
          continue;
        }
        if(files[i].size > _this.accepted_file_size){
          alert('Ошибка: файл ' + files[i].name + ' слишком большой, максимум 10 мегабайт');
          _this.current_load_files--;
          count_files--;
          continue;
        }
        fd.append('file',files[i]);
        var is_last = i == count_files - 1 ? true : false;
        _this.upload(fd,is_last);
      }
    }
  },
  _abort:function(){
    var _this = upload_image;
    var index = this.id.split('_')[2];
    for (var i = 0; i < _this.arr_load_files.length; i++) {
      if(_this.arr_load_files[i].index == index){
        _this.arr_load_files[i].request.abort();
      }
    }
    _this.delete_ajx_request(index);
    removeElement(this.parentNode);
    gId('btn_upload').style.display = 'inline-block';
  },
  delete_image: function(e){

    if(!confirm('Вы действительно хотите удалить это изображение?')) return false;

    var _this = upload_image;
    var btn = this;
    var image_id = this.parentNode.getAttribute('data-imageid');

    ajx({
      url: _this.upload_url,
      dataType: 'json',
      method: 'post',
      data: {
        page: _this.page,
        action: 'delete',
        image_id: image_id,
        item_id: _this.item_id
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
          removeElement(btn.parentNode);
          upload_image.get_image_positions();
          gId('btn_upload').style.display = 'inline-block';
        } else{
          alert(data.string);
        }
      }
    });
  },
  get_image_positions: function() {
    var length = $('.l_img_item').length;
    var positions = [];
    if(length > 0) $('.main_img').remove();
    for (var i = 0; i < length; i++) {
      var item = $('.l_img_item').eq(i);
      if(i == 0){
        $(item).append('<div class="absolute full_w main_img" style="display: block;">Главное изображение</div>');
      }
      var image_id = $(item).attr('data-imageid');
      positions.push({
        image_id: image_id
      });
    }
    return positions;
  },
  change_positions: function(e){
    var _this = upload_image;
    var positions = upload_image.get_image_positions();

    ajx({
        url: _this.upload_url,
        dataType: 'json',
        method: 'post',
        data: {
          page: _this.page,
          action: 'change_positions',
          positions: positions,
          item_id: _this.item_id
        },
        success: function(data) {
          if(data.result == 'true'){
            console.log('change success');
          } else{
            alert(data.string);
          }
        }
      });
  },
  create_image_item:function(){

    var length = gClass('l_img_item').length;

    var item = document.createElement('div');
    item.setAttribute('class','relative i_block l_img_item');

    var item_image_body = document.createElement('div');
    item_image_body.setAttribute('class','l__image');

    var item_image = document.createElement('img');
    item_image.setAttribute('class','cover_img');
    item_image.src = '/admin/images/add_image.png';

    var btn_delete_image = document.createElement('button');
    btn_delete_image.type = 'button';
    btn_delete_image.id = 'dt_img_' + length;
    btn_delete_image.setAttribute('class','absolute close');
    btn_delete_image.appendChild(document.createTextNode('×'));
    _e.addEvent(btn_delete_image,'click',this._abort);

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

    var is_main_img = gClass('l_img_item').length == 0 ? true : false;

    if(is_main_img === true){

      main_img_info = document.createElement('div');
      main_img_info.setAttribute('class','absolute full_w main_img');
      main_img_info.appendChild(document.createTextNode('Главное изображение'));
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
  },
  delete_empty_images: function(){
    var length = $('.l_img_item').length;
    for (var i = 0; i < length; i++) {
      var item = $('.l_img_item').eq(i);
      if(!$(item).is('[data-imageid]')){
        $(item).remove();
        length--;
      }
    }
  },
  delete_ajx_request: function(index){
    for (var i = 0; i < this.arr_load_files.length; i++) {
      if(this.arr_load_files[i].index == index){
        this.arr_load_files.splice(i,1);
      }
    }
  },
  upload: function(fd,is_last){
    var _this = upload_image;
    var alias = gId('post_alias_ru').value;

    var create_item = this.create_image_item();
    gId('load_images').appendChild(create_item.item);

    fd.append('page',_this.page);
    fd.append('action','upload');
    fd.append('item_id',_this.item_id);
    fd.append('alias',alias);

    _this.arr_load_files.push({
      index: create_item.index,
      request: ajx({
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
        url: _this.upload_url,
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
            removeElement(create_item.status_load.parentNode);

            _e.removeEvent(create_item.btn_delete,'click',_this._abort);
            _e.addEvent(create_item.btn_delete,'click',_this.delete_image);

            _this.delete_ajx_request(create_item.index);

          } else{
            _this.delete_empty_images();
            _this.current_load_files--;
            alert(data.string);
          }
        }
      })});

      if(_this.current_load_files >= _this.max_files){
        $('#btn_upload').hide();
      }

      if(is_last === true){
        $('#file_list').val('');
      }

  }
};
