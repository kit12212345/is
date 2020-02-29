<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
$var_admin_id = (int)$LOGGED_USER['admin_id'];
$var_create_date = time();

if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');

$var_order_id = (int)$_POST['order_id'];

function get_total_summa($order_id){
  global $db_error;
  $total_summa = 0;
  $q_order_info = sprintf("SELECT * FROM `orders_items` WHERE `orders_items`.`order_id` = '".$order_id."'");
  $r_order_info = mysql_query($q_order_info) or die(generate_exception($db_error));
  $n_order_info = mysql_numrows($r_order_info); // or die("cant get numrows query_path");
  if($n_order_info > 0){
    for ($i=0; $i < $n_order_info; $i++) {
      $order_item_total_summa = htmlspecialchars(mysql_result($r_order_info, $i, "orders_items.total_summa"));
      $total_summa += $order_item_total_summa;
    }
  }
  return $total_summa;
}


if($_POST['action'] == 'show_order'){

  $q_orders_items = sprintf("SELECT * FROM `orders_items`
  INNER JOIN `products` ON (`products`.`id` = `orders_items`.`product_id`)
  WHERE `orders_items`.`order_id` = '".$var_order_id."'
  ORDER BY `orders_items`.`id` ASC");
  $r_orders_items = mysql_query($q_orders_items) or die("cant execute query_path");
  $n_orders_items = mysql_numrows($r_orders_items); // or die("cant get numrows query_path");
  if($n_orders_items > 0){
    for ($i=0; $i < $n_orders_items; $i++) {
      $order_id = htmlspecialchars(mysql_result($r_orders_items, $i, "orders_items.id"));
      $order_product_code = htmlspecialchars(mysql_result($r_orders_items, $i, "products.code"));
      $order_product_name = htmlspecialchars(mysql_result($r_orders_items, $i, "products.name"));
      $order_product_price = htmlspecialchars(mysql_result($r_orders_items, $i, "orders_items.one_price"));
      $order_count_products = htmlspecialchars(mysql_result($r_orders_items, $i, "orders_items.count_products"));
      $order_total_summa = htmlspecialchars(mysql_result($r_orders_items, $i, "orders_items.total_summa"));
      $order_create_date = htmlspecialchars(mysql_result($r_orders_items, $i, "orders_items.create_date"));

      $order_product_price = number_format($order_product_price, 2, ',', ' ');
      $order_total_summa = number_format($order_total_summa, 2, ',', ' ');


      $html .= '<tr role="row" id="o_item_'.$order_id.'" class="odd">'; //selected
      //$html .= '<td class=" select-checkbox"></td>';
      $html .= '<td class="sorting_1">'.$order_id.'</td>';
      $html .= '<td>'.$order_product_code.'</td>';
      $html .= '<td>'.$order_product_name.'</td>';
      $html .= '<td class="text_center"><span class="label label-danger" id="o_count_'.$order_id.'">'.$order_count_products.'</span></td>';
      $html .= '<td><span class="label label-info">'.$order_product_price.'&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></span></td>';
      $html .= '<td><span class="label label-success"><span class="o_i_t_summa_'.$var_order_id.'_'.$order_id.'">'.$order_total_summa.'</span>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></span></td>';
      $html .= '<td class="text-center">';
      $html .= '<ul class="icons-list">';
      $html .= '<li class="dropdown">';
      $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a>';
      $html .= '<ul class="dropdown-menu dropdown-menu-right">';
      $html .= '<li><a <a class="g_color" data-itemid="'.$order_id.'" data-count="'.$order_count_products.'" onclick="orders.edit_count_products(this)"><i class="icon-cog3"></i>Изменить кол-во</a></li>';
      $html .= '<li><a data-itemid="'.$order_id.'" data-orderid="'.$var_order_id.'" onclick="orders.delete_product(this);" class="r_color"><i class="icon-folder-remove"></i>Удалить</a></li>';
      $html .= '</ul>';
      $html .= '</li>';
      $html .= '</ul>';
      $html .= '</td>';
      $html .= '</tr>';
    }
  }



  echo json_encode(array('result' => 'true','html' => $html));

} else if($_POST['action'] == 'edit_count'){
  $var_item_id = (int)$_POST['item_id'];
  $var_count = (int)$_POST['count'];

  $var_count = $var_count <= 0 ? 1 : $var_count;

  $q_order_info = sprintf("SELECT
  `orders_items`.`order_id`,
  `orders_items`.`one_price`,
  `products`.`count`
   FROM `orders_items`
  INNER JOIN `orders` ON (`orders_items`.`order_id` = `orders`.`id`)
  INNER JOIN `products` ON (`orders_items`.`product_id` = `products`.`id`)
  WHERE `orders_items`.`id` = '".$var_item_id."'");
  $r_order_info = mysql_query($q_order_info) or die(generate_exception($db_error));
  $n_order_info = mysql_numrows($r_order_info); // or die("cant get numrows query_path");
  if($n_order_info > 0){
    $order_id = htmlspecialchars(mysql_result($r_order_info, 0, "orders_items.order_id"));
    $product_one_price = htmlspecialchars(mysql_result($r_order_info, 0, "orders_items.one_price"));
    $product_count = htmlspecialchars(mysql_result($r_order_info, 0, "products.count"));

    if($var_count > $product_count) generate_exception('На вашем складе только '.$product_count.' товаров ???????????????????');

    $order_item_total_summa = $product_one_price * $var_count;

    $q_update = sprintf("UPDATE `orders_items`
    SET `orders_items`.`count_products`='".$var_count."',
    `orders_items`.`total_summa`='".$order_item_total_summa."'"." WHERE `orders_items`.`id`='".$var_item_id."'");
    mysql_query($q_update) or die(generate_exception($db_error));

    $order_total_summa = get_total_summa($order_id);

    $q_update = sprintf("UPDATE `orders`
    SET `orders`.`total_summa`='".$order_total_summa."'"." WHERE `orders`.`id`='".$order_id."'");
    mysql_query($q_update) or die(generate_exception($db_error));

  } else generate_exception('Заказ не найден');

  $order_item_total_summa = number_format($order_item_total_summa, 2, ',', ' ');
  $order_total_summa = number_format($order_total_summa, 2, ',', ' ');


  echo json_encode(array(
    'result' => 'true',
    'order_id' => $order_id,
    'order_total_summa' => $order_total_summa,
    'order_item_total_summa' => $order_item_total_summa
  ));


} else if($_POST['action'] == 'delete_product'){
  $var_item_id = (int)$_POST['item_id'];

  $q_order_info = sprintf("SELECT
  `orders_items`.`order_id`
   FROM `orders_items`
  INNER JOIN `orders` ON (`orders_items`.`order_id` = `orders`.`id`)
  WHERE `orders_items`.`id` = '".$var_item_id."'");
  $r_order_info = mysql_query($q_order_info) or die(generate_exception($db_error));
  $n_order_info = mysql_numrows($r_order_info); // or die("cant get numrows query_path");
  if($n_order_info > 0){
    $order_id = htmlspecialchars(mysql_result($r_order_info, 0, "orders_items.order_id"));

    $q_delete = sprintf("DELETE FROM `orders_items` WHERE `orders_items`.`id`='".$var_item_id."'");
    mysql_query($q_delete) or die(generate_exception($db_error));

    $order_total_summa = get_total_summa($order_id);

    $q_update = sprintf("UPDATE `orders`
    SET `orders`.`total_summa`='".$order_total_summa."'"." WHERE `orders`.`id`='".$order_id."'");
    mysql_query($q_update) or die(generate_exception($db_error));


  } else generate_exception('Заказ не найден');

  $order_total_summa = number_format($order_total_summa, 2, ',', ' ');

  echo json_encode(array(
    'result' => 'true',
    'order_id' => $order_id,
    'order_total_summa' => $order_total_summa
  ));
} else if($_POST['action'] == 'search_product'){
  $var_search_str = $_POST['search_str'];
  $var_search_by = $_POST['search_by'];

  $var_search_str = mysql_real_escape_string($var_search_str);

  $sql_search_like = '';

  if(!empty($var_search_str)){
    $search_field_name = $var_search_by == 'by_code' ? 'code' : 'name';
    $sql_search_like = ' AND `products`.`'.$search_field_name.'` LIKE \''.$var_search_str.'%\'';

    $q_products = ("SELECT * FROM `products` WHERE `products`.`deleted` = '0' ".$sql_search_like."
    ORDER BY `products`.`id` DESC");
    $r_products = mysql_query($q_products) or die("cant execute query_path4".$sql_search_like);
    $n_products = mysql_numrows($r_products); // or die("cant get numrows query_path");
    if ($n_products > 0) {
      for ($i = 0; $i < $n_products; $i++) {
        $product_id = htmlspecialchars(mysql_result($r_products, $i, "products.id"));
        $product_code = htmlspecialchars(mysql_result($r_products, $i, "products.code"));
        $product_name = htmlspecialchars(mysql_result($r_products, $i, "products.name"));

        $html .= '<div data-productname="'.$product_name.'" data-productcode="'.$product_code.'" data-productid="'.$product_id.'" onclick="orders.select_search_product(this);" class="padd_ten s_pr_item cursor_p">'.$product_code.' - '.$product_name.'</div>';

      }
    } else{
      $html .= '<div class="padd_ten s_pr_item">Ничего не найдено</div>';
    }
  }

  echo json_encode(array('result' => 'true','html' => $html));
} else if($_POST['action'] == 'check_product_in_order'){
  $var_product_id = (int)$_POST['product_id'];

  $q_check_product = sprintf("SELECT * FROM `products`WHERE `products`.`id` = '".$var_product_id."'");
  $r_check_product = mysql_query($q_check_product) or die("cant execute query_path");
  $n_check_product = mysql_numrows($r_check_product); // or die("cant get numrows query_path");
  if($n_check_product == 0) generate_exception('Товар не найден');

  $q_orders_items = sprintf("SELECT * FROM `orders_items`
  WHERE `orders_items`.`order_id` = '".$var_order_id."' AND `orders_items`.`product_id` = '".$var_product_id."'
  ORDER BY `orders_items`.`id` ASC");
  $r_orders_items = mysql_query($q_orders_items) or die("cant execute query_path");
  $n_orders_items = mysql_numrows($r_orders_items); // or die("cant get numrows query_path");
  if($n_orders_items > 0) generate_exception('Этот товар уже находится в заказе');



  echo json_encode(array('result' => 'true'));
} else if($_POST['action'] == 'add_products'){
  $products = $_POST['products'];

  $q_order_info = sprintf("SELECT `orders`.`user_id` FROM `orders` WHERE `orders`.`id` = '".$var_order_id."'");
  $r_order_info = mysql_query($q_order_info) or die("cant execute query_path");
  $n_order_info = mysql_numrows($r_order_info); // or die("cant get numrows query_path");
  if($n_order_info > 0){
    $order_user_id = htmlspecialchars(mysql_result($r_order_info, 0, "orders.user_id"));

    for ($i = 0; $i < count($products); $i++) {

      $var_product_id = (int)$products[$i]['id'];
      $var_product_count = (int)$products[$i]['count'];

      $q_check_product = sprintf("SELECT `products`.`price` FROM `products`WHERE `products`.`id` = '".$var_product_id."'");
      $r_check_product = mysql_query($q_check_product) or die("cant execute query_path");
      $n_check_product = mysql_numrows($r_check_product); // or die("cant get numrows query_path");
      if($n_check_product > 0){
        $product_price = htmlspecialchars(mysql_result($r_check_product, 0, "products.price"));
      } else continue;

      $total_price = $product_price * $var_product_count;

      $q_orders_items = sprintf("SELECT * FROM `orders_items`
      WHERE `orders_items`.`order_id` = '".$var_order_id."' AND `orders_items`.`product_id` = '".$var_product_id."'
      ORDER BY `orders_items`.`id` ASC");
      $r_orders_items = mysql_query($q_orders_items) or die("cant execute query_path");
      $n_orders_items = mysql_numrows($r_orders_items); // or die("cant get numrows query_path");
      if($n_orders_items == 0){
        $q_insert_order_item = sprintf("INSERT INTO `orders_items` (
        `order_id`,
        `user_id`,
        `product_id`,
        `one_price`,
        `count_products`,
        `total_summa`,
        `create_date`
        ) values(
        '".$var_order_id."',
        '".$order_user_id."',
        '".$var_product_id."',
        '".$product_price."',
        '".$var_product_count."',
        '".$total_price."',
        '".$var_create_date."'".")");
        mysql_query($q_insert_order_item) or die(generate_exception($db_error));

      }
    }

    $new_total_summa = get_total_summa($var_order_id);

    $q_update = sprintf("UPDATE `orders`
    SET `orders`.`total_summa`='".$new_total_summa."'"." WHERE `orders`.`id`='".$var_order_id."'");
    mysql_query($q_update) or die(generate_exception($db_error));

    $new_total_summa = number_format($new_total_summa, 2, ',', ' ');

  }

  echo json_encode(array('result' => 'true','total_summa' => $new_total_summa));

} else if($_POST['action'] == 'handle_order'){
  $var_handle = $_POST['handle'];
  $var_handle = mysql_real_escape_string($var_handle);

  if($var_handle != 'apply' && $var_handle != 'cancel'
  && $var_handle != 'in_way' && $var_handle != 'done') generate_exception('Для заказа выбрано неизвестное событие');

  $q_update = sprintf("UPDATE `orders`
  SET `orders`.`status`='".$var_handle."'"." WHERE `orders`.`id`='".$var_order_id."'");
  mysql_query($q_update) or die(generate_exception($db_error));

  echo json_encode(array('result' => 'true'));

}


?>
