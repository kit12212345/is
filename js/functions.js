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
