
<!DOCTYPE html>

<html>

<head>
<title>sticky javascript: Если высота окна браузера меньше высоты боковой колонки, то прилипать будет либо нижний, либо верхний край колонки, иначе только верхний</title>
<style>
body {
  background: #aba;
}
header, main, footer {
  display: block;
  max-width: 740px;
  margin: 0 auto;
}
header {
  margin-bottom: 15px;
  font-size: 200%;
  background: #456;
  text-align: center;
}
header a {
  color: #fff;
}
article {
  display: block;
  float: left;
  width: 485px;
  height: 1000px;
  background: #fff;
}
aside {
  display: block;
  margin-left: 500px;
  box-shadow: 0 0 0 1px #cff inset;
  background: #cdc;
}
footer {
  position: relative;
  top: 15px;
  margin-bottom: 15px;
  clear: both;
  height: 800px;
  background: #456;
}
</style>
</head>

<body>
  <header>
    <a href="http://shpargalkablog.ru/2013/09/scroll-block.html">sticky javascript</a>
  </header>


      <div style="clear:both;"></div><main>
  <article id="article"><h1>Если высота окна браузера меньше высоты боковой колонки, то прилипать будет либо нижний, либо верхний край колонки, иначе только верхний</h1>1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9<br>10<br>11<br>12<br>13<br>14<br>15<br>16<br>17<br>18<br>19<br>20<br>21<br>22<br>23<br>24<br>25<br>26<br>27<br>28<br>29<br>30<br>41<br>42<br>43<br>44<br>45<br>46<br>47<br>48<br>49<br>50<br>51<br>52<br>53<br>54<br>55<br>66<br>67<br>68<br>69<br>70<br>71<br>72<br>73<br>74<br>75<br>76<br>77<br>78<br>79<br>80<br>81<br>82<br>83<br>84<br>85<br>86<br>87<br>88<br>89<br>90<br>91<br>92<br>93<br>94<br>95<br>96<br>97<br>98<br>99<br>100<br>101<br>102<br>103<br>104<br>105<br>106<br>107<br>108<br>109<br>110<br>111<br>112<br>113<br>114<br>115<br>116<br>117<br>118<br>119<br>120<br>121<br>122<br>123<br>124<br>125<br>126<br>127<br>128<br>129<br>130<br>141<br>142<br>143<br>144<br>145<br>146<br>147<br>148<br>149<br>150<br>151<br>152<br>153<br>154<br>155<br>166<br>167<br>168<br>169<br>170<br>171<br>172<br>173<br>174<br>175<br>176<br>177<br>178<br>179<br>180<br>181<br>182<br>183<br>184<br>185<br>186<br>187<br>188<br>189<br>190<br>191<br>192<br>193<br>194<br>195<br>196<br>197<br>198<br>199<br>200</article>
  <aside id="aside1">1<br>2<br>3<br>4<br>5<br>6<br>7<br>8<br>9<br>10<br>11<br>12<br>13<br>14<br>15<br>16<br>17<br>18<br>19<br>20<br>21<br>22<br>23<br>24<br>25<br>26<br>27<br>28<br>29<br>30<br>41<br>42<br>43<br>44<br>45<br>46<br>47<br>48<br>49<br>50<br>51<br>52<br>53<br>54<br>55<br>66<br>67<br>68<br>69<br>70<br>71<br>72<br>73<br>74<br>75<br>76<br>77<br>78<br>79<br>80</aside>
</main>
<footer></footer>

<style>
article {
  height: auto;
}
.sticky {
  position: fixed;
  z-index: 101;
}
.stop {
  position: relative;
  z-index: 101;
}
</style>

<script>
(function(){
var a = document.querySelector('#aside1'), b = null, K = null, Z = 0, P = 0, N = 0;  // если у P ноль заменить на число, то блок будет прилипать до того, как верхний край окна браузера дойдёт до верхнего края элемента, если у N — нижний край дойдёт до нижнего края элемента. Может быть отрицательным числом
window.addEventListener('scroll', Ascroll, false);
document.body.addEventListener('scroll', Ascroll, false);
function Ascroll() {
  var Ra = a.getBoundingClientRect(),
      R1bottom = document.querySelector('#article').getBoundingClientRect().bottom;

  if (Ra.bottom < R1bottom) {
    if (b == null) {
      var Sa = getComputedStyle(a, ''), s = '';
      for (var i = 0; i < Sa.length; i++) {
        if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
          s += Sa[i] + ': ' +Sa.getPropertyValue(Sa[i]) + '; '
        }
      }
      b = document.createElement('div');
      b.className = "stop";
      b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
      a.insertBefore(b, a.firstChild);
      var l = a.childNodes.length;
      for (var i = 1; i < l; i++) {
        b.appendChild(a.childNodes[1]);
      }
      a.style.height = b.getBoundingClientRect().height + 'px';
      a.style.padding = '0';
      a.style.border = '0';
    }
    var Rb = b.getBoundingClientRect(),
        Rh = Ra.top + Rb.height,
        W = document.documentElement.clientHeight,
        R1 = Math.round(Rh - R1bottom),
        R2 = Math.round(Rh - W);
    if (Rb.height > W) {
      if (Ra.top < K) {  // скролл вниз
        if (R2 + N > R1) {  // не дойти до низа
          if (Rb.bottom - W + N <= 0) {  // подцепиться
            b.className = 'sticky';
            b.style.top = W - Rb.height - N + 'px';
            Z = N + Ra.top + Rb.height - W;
          } else {
            b.className = 'stop';
            b.style.top = - Z + 'px';
          }
        } else {
          b.className = 'stop';
          b.style.top = - R1 +'px';
          Z = R1;
        }
      } else {  // скролл вверх
        if (Ra.top - P < 0) {  // не дойти до верха
          if (Rb.top - P >= 0) {  // подцепиться
            b.className = 'sticky';
            b.style.top = P + 'px';
            Z = Ra.top - P;
          } else {
            b.className = 'stop';
            b.style.top = - Z + 'px';
          }
        } else {
          b.className = '';
          b.style.top = '';
          Z = 0;
        }
      }
      K = Ra.top;
    } else {
      if ((Ra.top - P) <= 0) {
        if ((Ra.top - P) <= R1) {
          b.className = 'stop';
          b.style.top = - R1 +'px';
        } else {
          b.className = 'sticky';
          b.style.top = P + 'px';
        }
      } else {
        b.className = '';
        b.style.top = '';
      }
    }
    window.addEventListener('resize', function() {
      a.children[0].style.width = getComputedStyle(a, '').width
    }, false);
  }
}
})()
</script><div style="clear:both; padding-bottom:0.25em"></div>


</body>

</html>
