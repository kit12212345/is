function set_get_param(name,value){
  var now_url = window.location.href;
  var new_url = '';
  now_url = now_url.split('?')[1];

  value = encodeURIComponent(value);

  if(now_url.indexOf(name) >= 0){
    var param = now_url.split(name + '=')[1];
    param = param.split('&')[0];
    var g_param = name + '=' + param;
    new_url = now_url.replace('&' + g_param, '');
    new_url = new_url.replace(g_param, '');
    if(new_url.charAt(new_url.length - 1) == '&'){
      new_url += name + '=' + value;
    } else{
      new_url += '&' + name + '=' + value;
    }
    if(new_url.charAt(0) == '&') new_url = new_url.slice(1);
  } else{
    if(now_url.charAt(now_url.length - 1) == '&'){
      new_url = now_url + name + '=' + value;
    } else{
      new_url = now_url + '&' + name + '=' + value;
    }
  }
  new_url = '?' + new_url;

  window.history.pushState(null, null, new_url);
}

function remove_get_param(name){
  var now_url = window.location.href;
  if(now_url.indexOf(name) >= 0){
    var param = now_url.split(name + '=')[1];
    param = param.split('&')[0];
    var g_param = name + '=' + param;
    var new_url = now_url.replace('&' + g_param, '');
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
