<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/admin/include/classes/comments.php');
$var_admin_id = (int)$LOGGED_USER['id'];
$var_create_date = time();


if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');

if($_POST['action'] == 'add_comment'){

  $comment_id = Comments::add_comment($_POST);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'update_comment'){

  $result = Comments::update_comment($_POST);

  if($result !== true) generate_exception($result);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'delete_comment'){

  $result = Comments::delete_comment($_POST);

  if($result !== true) generate_exception($result);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'change_status'){

  $result = Comments::change_status($_POST);

  if($result !== true) generate_exception($result);

  echo json_encode(array('result' => 'true'));

}

?>
