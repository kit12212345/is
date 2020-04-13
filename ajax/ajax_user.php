<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
session_start();
$LOGGED_USER = $_SESSION['logged_user'];
$time_offset = $_SESSION['time_offset'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/user.php');
$action = $_POST['action'];
$user_id = isset($LOGGED_USER['id']) ? (int)$LOGGED_USER['id'] : 0;
$user_first_name = isset($LOGGED_USER['first_name']) ? $LOGGED_USER['first_name'] : '';

if($user_id == 0) generate_exception($l->not_auth);

$init_user = new User(array(
  'user_id' => $user_id
));

if($action == 'save_profile'){

  $result = $init_user->save_profile($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

} else if($action == 'save_new_password'){

  $result = $init_user->save_new_password($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
}
?>
