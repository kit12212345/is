var l = {};
var hb = {};
// hb.lang = getCookie('lang');
hb.lang = 'ru';
hb.translate = json_lang;
for (var variable in hb.translate) {
  if (hb.translate.hasOwnProperty(variable)) {
    l[variable] = hb.translate[variable][hb.lang];
  }
}
