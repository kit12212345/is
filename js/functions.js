function getCookie(name) {
  var cookie = " " + document.cookie;
  var search = " " + name + "=";
  var setStr = 0;
  var offset = 0;
  var end = 0;
  if (cookie.length > 0) {
    offset = cookie.indexOf(search);
    if (offset != -1) {
      offset += search.length;
      end = cookie.indexOf(";", offset)
      if (end == -1) {
        end = cookie.length;
      }
      setStr = unescape(cookie.substring(offset, end));
    }
  }
  return (setStr);
}

function setCookie(name,value){
  var date = new Date;
  date.setDate(date.getDate() + 1000);
  document.cookie = name + "=" + value + "; path=/; expires=" + date.toUTCString();
}

function delete_cookie(cookie_name){
  var cookie_date = new Date ( );  // Текущая дата и время
  cookie_date.setTime ( cookie_date.getTime() - 1 );
  document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}

function set_url_param(name,value){
  var current_url = window.location.href;
  var new_url = '';
  current_url = current_url.split('?')[1];

  value = encodeURIComponent(value);

  if(current_url.indexOf(name) >= 0){
    var param = current_url.split(name + '=')[1];
    param = param.split('&')[0];
    var g_param = name + '=' + param;
    new_url = current_url.replace('&' + g_param, '');
    new_url = new_url.replace(g_param, '');
    if(new_url.charAt(new_url.length - 1) == '&'){
      new_url += name + '=' + value;
    } else{
      new_url += '&' + name + '=' + value;
    }
    if(new_url.charAt(0) == '&') new_url = new_url.slice(1);
  } else{
    if(current_url.charAt(current_url.length - 1) == '&'){
      new_url = current_url + name + '=' + value;
    } else{
      new_url = current_url + '&' + name + '=' + value;
    }
  }
  new_url = '?' + new_url;

  window.history.pushState(null, null, new_url);
}

function remove_url_param(name){
  var current_url = window.location.href;
  if(current_url.indexOf(name) >= 0){
    var param = current_url.split(name + '=')[1];
    param = param.split('&')[0];
    var g_param = name + '=' + param;
    var new_url = current_url.replace('&' + g_param, '');
    new_url = new_url.replace(g_param, '');
    window.history.pushState(null, null, new_url);
  }
}


function get_url_vars(){
  var vars = [], hash;
  var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
  for(var i = 0; i < hashes.length; i++){
    hash = hashes[i].split('=');
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars;
}


var message = {
  time_visible:  5000, // Время показа сообщения
  offset_top:  15, // Отступ сверху между сообщениями
  messages: [], // массив всех сообщений
  show: function(text,is_good){
    var class_name = (typeof is_good !== 'undefined' && is_good === true) ? 'msg_success' : 'msg_danger';
    var msg_offset_top = this.get_offset_top();
    var _message = this.create_html(text,class_name);
    _message.style.top = msg_offset_top + 'px';
    document.body.appendChild(_message);
    this.messages.push(_message);
    message.reindex_messages();
    setTimeout(function(){
      message.add_class(_message,'show_message');
    },0);
    setTimeout(function(){
      var message_index = message.messages.indexOf(_message);
      if(message_index == -1) return false;
      message.add_class(_message,'hide_message');
      _message.parentNode.removeChild(_message);
      message.reindex_messages();
      message.messages.splice(message_index,1);
    },message.time_visible);
  },
  close_message: function(e){
    e = e || window.event;
    var target = e.target || e.srcElement;
    var _message = target.parentNode.parentNode;
    var message_index = message.messages.indexOf(_message);
    if(message_index == -1) return false;
    message.add_class(_message,'hide_message');
    message.messages.splice(message_index,1);
    _message.parentNode.removeChild(_message);
    message.reindex_messages();
  },
  reindex_messages: function() {
    var count_messages = document.getElementsByClassName('message_item').length;
    var total_top = this.offset_top;
    for (var i = 0; i < count_messages; i++) {
      var item = document.getElementsByClassName('message_item')[i];
      var height = item.offsetHeight;
      item.style.top = total_top + 'px';
      total_top += this.offset_top + height;
    }
  },
  add_class: function(item,class_name){
    var item_classes = item.getAttribute('class');
    item.setAttribute('class',item_classes + ' ' + class_name);
  },
  create_html: function(text,class_name){
    var message = document.createElement('div');
    message.setAttribute('class','fixed message message_item ' + class_name);

    var content = document.createElement('div');
    content.setAttribute('class','msg__text w_color');
    content.appendChild(document.createTextNode(text));

    var close_message = document.createElement('div');
    close_message.setAttribute('class','absolute cursor_p close_message');

    var i_close = document.createElement('i');
    i_close.setAttribute('class','w_color fa fa-times');
    i_close.setAttribute('aria-hidden','true');

    i_close.onclick = this.close_message;

    close_message.appendChild(i_close);

    message.appendChild(content);
    message.appendChild(close_message);

    return message;
  },
  get_offset_top: function(){
    var count_messages = document.getElementsByClassName('message_item').length;
    var total_top = this.offset_top;
    for (var i = 0; i < count_messages; i++) {
      var item = document.getElementsByClassName('message_item')[i];
      var height = item.offsetHeight;
      total_top += this.offset_top + height;
    }
    return total_top;
  }
};
