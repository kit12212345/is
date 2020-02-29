<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');

if(isset($LOGGED_USER['id'])) generate_exception('Вы уже авторизированы на сайте');


if($_POST['action'] == 'login'){
  $var_login = $_POST['login'];
  $var_password = $_POST['password'];

  if(empty($var_login)) generate_exception('Введите логин');
  if(empty($var_password)) generate_exception('Введите пароль');

  $var_login = mysql_real_escape_string($var_login);
  $var_password = mysql_real_escape_string($var_password);

  $var_md5_password = md5($var_password);


  $q_check_user = ("SELECT * FROM `users` WHERE `users`.`email`='".$var_login."'
  AND `users`.`password` = '".$var_md5_password."' AND `users`.`admin` > '0'");
  $r_check_user = mysql_query($q_check_user) or die("cant execute query");
  $n_check_user = mysql_numrows($r_check_user); // or die("cant get numrows query");
  if($n_check_user > 0){
    $admin_id = htmlspecialchars(mysql_result($r_check_user, 0, "users.id"));
    $admin_name = htmlspecialchars(mysql_result($r_check_user, 0, "users.name"));
    $admin_email = htmlspecialchars(mysql_result($r_check_user, 0, "users.email"));
    $admin_post = htmlspecialchars(mysql_result($r_check_user, 0, "users.admin"));

    $_SESSION['logged_user']['id'] = $admin_id;
    $_SESSION['logged_user']['email'] = $admin_email;


  } else generate_exception('Логин или пароль введены неверно');

  echo json_encode(array('result' => 'true'));
}



?>
