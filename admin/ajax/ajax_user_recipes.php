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
include_once($root_dir.'/include/classes/posts.php');
$var_admin_id = (int)$LOGGED_USER['id'];
$var_create_date = time();

require_once($root_dir.'/include/classes/posts.php');
require_once($root_dir.'/include/classes/user_recipes.php');

if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');


if($_POST['action'] == 'change_status'){
  $user_recipe_id = (int)$_POST['user_recipe_id'];

  $status = $_POST['status'];
  $rejected_reason = $_POST['rejected_reason'];

  $init_user_recipes = new UserRecipes(array(
    'item_id' => $user_recipe_id
  ));

  $recipe_info = $init_user_recipes->get_recipe_info(array(
    'sort_user' => false
  ),true);

  if($recipe_info === false) generate_exception('Рецепт не найден');

  $post_id = $recipe_info['parent_id'];

  $q_user_info = ("UPDATE `user_recipes` SET
  `status` = '".$status."',
  `rejection_reason` = '".$rejected_reason."',
  `parent_id` = '0'
  WHERE `id` = '".$user_recipe_id."'");
  mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

  if($post_id > 0){
    Posts::delete_post(array(
      'post_id' => $post_id,
      'forever' => 1
    ));
  }


  echo json_encode(array(
    'result' => 'true'
  ));

}
?>
