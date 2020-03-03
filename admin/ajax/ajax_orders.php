<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');

include_once($root_dir.'/include/classes/orders.php');

$orders = new Orders();

$action = $_POST['action'];


if($action == 'delete_order'){
  $order_id = $_POST['order_id'];
  $orders->delete_order($order_id);
  echo json_encode(array('result' => 'true'));
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
}


?>
