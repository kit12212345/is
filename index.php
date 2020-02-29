<?php
session_start();
// $_SESSION['logged_user'] = array();
// $_SESSION['logged_user']['id'] = 5;
//unset($_SESSION['logged_user']);
//print_r(unserialize($_COOKIE['basket']));
?>
<!DOCTYPE html>
<html lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <script src="/js/jquery.js" charset="utf-8"></script>
    <script src="/js/functions.js" charset="utf-8"></script>
    <script src="/js/basket.js?ver=<?php echo rand(1,100000000); ?> " charset="utf-8"></script>
  </head>
  <body>
    <button type="button" name="button" onclick="basket.change_quan(this);" data-itemid="1" data-dir="p">+</button>
    <input type="number" name="" data-itemid="1" class="bsk_quan_1" value="" onkeyup="basket.l_change_quan(this);">
    <button type="button" name="button" onclick="basket.add(this);" data-itemid="1">ASD</button>
    <button type="button" name="button" onclick="basket.change_quan(this);" data-itemid="1" data-dir="m">-</button>
    <button type="button" name="button" onclick="basket.remove(1);">REMOVE</button>
  </body>
</html>
