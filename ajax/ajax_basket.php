<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER = $_SESSION['logged_user'];
$time_offset = $_SESSION['time_offset'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/basket.php');
$user_id = (int)$LOGGED_USER['id'];
$var_create_date = time();
$action = $_POST['action'];

$basket = new Basket(array(
  'user_id' => $LOGGED_USER['id']
));


if($action == 'add'){
  $basket->add($_POST);
  echo json_encode(array('result' => 'true'));
} else if($_POST['action'] == 'change_quan'){

  $result = $basket->change_quan($_POST);
  echo json_encode(array(
    'result' => 'true',
    'quan' => $result['quan']
  ));

} else if($_POST['action'] == 'remove'){

  $id = (int)$_POST['item_id'];
  $basket->remove($id);
  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'checkout'){
  $basket->checkout($_POST);
  echo json_encode(array('result' => 'true'));
}


?>
