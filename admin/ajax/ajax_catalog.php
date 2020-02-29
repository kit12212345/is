<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/catalog.php');

if((int)$LOGGED_USER['id'] == 0) generate_exception('Вы не авторизированы на сайте');
$action = $_POST['action'];
$catalog = new Catalog();

if($action == 'save_product'){
  $catalog->save_product($_POST);
  echo json_encode(array('result' => 'true'));
} else if($action == 'save_catalog'){
  $data = $catalog->save_catalog($_POST);
  echo json_encode(array('result' => 'true'));
} else if($action == 'switch_status'){
  $data = $catalog->switch_status($_POST);
  echo json_encode(array('result' => 'true'));
} else if($action == 'get_catalog'){

  $data = $catalog->get_catalog($_POST);
  echo json_encode(array(
    'result' => 'true',
    'html' => $data['html'],
    'cat_path_html' => $data['cat_path_html'],
    'pages_html' => $data['pages_html']
  ));

} else if($action == 'delete_product'){
  $product_id = (int)$_POST['product_id'];
  $catalog->delete_product($product_id);
  echo json_encode(array('result' => 'true'));
}


?>
