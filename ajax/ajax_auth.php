<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
session_start();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/auth.php');
$var_create_date = time();
$action = $_POST['action'];

$auth = new Auth();

if($action == 'registration'){

  if(isset($LOGGED_USER['id']) && (int)$LOGGED_USER['id'] > 0) generate_exception($l->already_auth);

  $data = $auth->registration($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

} else if($action == 'login'){

  if(isset($LOGGED_USER['id']) && (int)$LOGGED_USER['id'] > 0) generate_exception($l->already_auth);

  $data = $auth->login($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

} else if($action == 'recovery_password'){

  if(isset($LOGGED_USER['id']) && (int)$LOGGED_USER['id'] > 0) generate_exception($l->already_auth);

  $data = $auth->recovery_password($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

} else if($action == 'save_new_password'){

  if(isset($LOGGED_USER['id']) && (int)$LOGGED_USER['id'] > 0) generate_exception($l->already_auth);

  $data = $auth->save_new_password($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

}

?>
