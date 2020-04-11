<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER = $_SESSION['logged_user'];
$time_offset = $_SESSION['time_offset'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/catalog.php');

$action = $_POST['action'];
$user_id = isset($LOGGED_USER['id']) ? (int)$LOGGED_USER['id'] : 0;

$init_catlog = new Catalog();

if($action == 'get_products'){
  $products_info = $init_catlog->get_products($_POST);

  $products = $products_info !== false ? $products_info['products'] : array();
  $count_products = count($products);
  $count_all_products = $products_info['count_all_products'];
  $products_html = $products_info['html'];
  $pages_html = $products_info['pages_html'];

  echo json_encode(array(
    'result' => 'true',
    'pages_html' => $pages_html,
    'count_products' => $count_products,
    'count_all_products' => $count_all_products,
    'products_html' => $products_html
  ));

}

?>
