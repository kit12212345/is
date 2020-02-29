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
include_once($root_dir.'/admin/include/classes/about.php');
$var_admin_id = (int)$LOGGED_USER['id'];
$var_create_date = time();

if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');


if($_POST['action'] == 'set_read'){
  $question_id = (int)$_POST['question_id'];
  $count_questions = About::set_read_questions($question_id);

  echo json_encode(array(
    'result' => 'true',
    'count_not_readed' => $count_questions
  ));
}



?>
