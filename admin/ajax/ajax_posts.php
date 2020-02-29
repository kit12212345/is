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

require_once($root_dir.'/components/alias/alias.php');

if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');

if($_POST['action'] == 'add_post'){

  $post_id = Posts::add_post($_POST);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'update_post'){

  Posts::update_post($_POST);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'delete_post'){

  Posts::delete_post($_POST);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'restore_post'){

  $result = Posts::restore_post($_POST);
  if($result !== true) generate_exception($result);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'small_update'){

  $table_name = AdmConfig::$posts_table_name;

  $post_id = (int)$_POST['post_id'];
  $post_title = $_POST['title'];
  $post_alias = $_POST['alias'];

  if(empty($post_title)) generate_exception('Введите заголовок');

  $post_title = mysql_real_escape_string($post_title);
  $post_alias = mysql_real_escape_string($post_alias);

  $add_alias = Alias::add_alias(array(
    'item_id' => $post_id,
    'type' => 'post',
    'alias' => $post_alias,
    'table_name' => $table_name
  ));

  if($add_alias === false) generate_exception('Алиас с таким именем уже существует, придумайте другой');

  AdmCommon::update_data(array(
    'table_name' => $table_name,
    'fields' => array(
      'title' => $post_title,
      'alias' => $post_alias
    ),
    'where' => array(
      'id' => $post_id
    )
  ));

  echo json_encode(array('result' => 'true'));


} else if($_POST['action'] == 'search_ht'){
  $name = $_POST['name'];
  $exist_ht = false;

  $name = mysql_real_escape_string($name);

  $name = trim($name);

  if(!empty($name)){

    $table_name = AdmConfig::$hash_tags_table_name;


    $q_search_ht = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`name` LIKE '".$name."%'");
    $r_search_ht = mysql_query($q_search_ht) or die("cant execute query_path".$table_name);
    $n_search_ht = mysql_num_rows($r_search_ht);
    if($n_search_ht > 0){
      $exist_ht = true;
      for ($i = 0; $i < $n_search_ht; $i++) {
        $ht_id = htmlspecialchars(mysql_result($r_search_ht, $i, $table_name.".id"));
        $ht_name = htmlspecialchars(mysql_result($r_search_ht, $i, $table_name.".name"));

        $html .= '<div class="htsr_item" onclick="post.select_search_ht(this)" data-name="'.$ht_name.'">'.$ht_name.'</div>';

      }
    }
  }

  echo json_encode(array('result' => 'true','html' => $html,'exist_ht' => $exist_ht));

}


?>
