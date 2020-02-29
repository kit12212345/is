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
include_once($root_dir.'/include/classes/categories.php');
$var_admin_id = (int)$LOGGED_USER['id'];
$var_create_date = time();

require_once($root_dir.'/components/alias/alias.php');

if($LOGGED_USER['admin'] <= 0) generate_exception('Вы не авторизированы');

function create_path($item_id,&$path){
  $q_query_path = sprintf("SELECT * FROM `categories` WHERE `categories`.`id`='".$item_id."'");
  $r_query_path = mysql_query($q_query_path) or die("cant execute query_path");
  $n_query_path = mysql_numrows($r_query_path); // or die("cant get numrows query_path");
  if ($n_query_path > 0) {
    for ($i = 0; $i < $n_query_path; $i++) {
      $query_path_id = htmlspecialchars(mysql_result($r_query_path, $i, "categories.id"));
      $query_path_name = htmlspecialchars(mysql_result($r_query_path, $i, "categories.name"));
      $query_path_parent_id = htmlspecialchars(mysql_result($r_query_path, $i, "categories.parent_id"));
      array_unshift($path, array('id' => $query_path_id,'name' => $query_path_name));
      create_path($query_path_parent_id,$path);
    }
  }
  return $path;
}

function is_parent_cat($item_id,$check_item){
  $path = array();
  $path = create_path($item_id,$path);
  if(in_array($check_item,$path)) return true;
  return false;
}

function set_last_items($item_id,$items){
  $q_last_items = sprintf("SELECT * FROM `categories` WHERE `categories`.`id` = '".$item_id."'");
  $r_last_items = mysql_query($q_last_items) or die("cant execute query7");
  $n_last_items = mysql_numrows($r_last_items); // or die("cant get numrows query");
  if ($n_last_items > 0) {
    $last_items = htmlspecialchars(mysql_result($r_last_items, 0, "categories.last_items"));

    for ($i = 0; $i < count($items); $i++) {
      $check_parent = is_parent_cat($item_id,$items[$i]);
      if(strpos($last_items,$items[$i]) === false && $check_parent === false){
        $last_items .= $last_items ? ','.$items[$i] : $items[$i];
      }
    }

    $q_update = sprintf("UPDATE `categories` SET `categories`.`last_items`='".$last_items."'"." WHERE `categories`.`id`='".$item_id."'");
    mysql_query($q_update) or die("cant execute update posters_link");

  }
}

function is_last_cat($item_id){
  $q_check = sprintf("SELECT * FROM `categories` WHERE `categories`.`parent_id`='".$item_id."'");
  $r_check = mysql_query($q_check) or die("cant execute query_path");
  $n_check = mysql_numrows($r_check); // or die("cant get numrows query_path");
  if ($n_check > 0) return false;
  return true;
}

function get_cat_position($parent_id){
  $q_last_position = sprintf("SELECT `categories`.`position` FROM `categories`
  WHERE `categories`.`parent_id` = '".$var_parent_id."' ORDER BY `categories`.`position` DESC LIMIT 1");
  $r_last_position = mysql_query($q_last_position) or die("cant execute query8");
  $n_last_position = mysql_numrows($r_last_position); // or die("cant get numrows query");
  if ($n_last_position > 0){
    $position = htmlspecialchars(mysql_result($r_last_position, 0, "categories.position"));
    $position++;
  } else $position = 1;

  return $position;
}

function reposition_cats($parent_id,$position){

  $q_items = sprintf("SELECT * FROM `categories` WHERE `categories`.`parent_id` = '".$parent_id."'
  AND `categories`.`position` > '".$position."'");
  $r_items = mysql_query($q_items) or die("cant execute query9");
  $n_items = mysql_numrows($r_items); // or die("cant get numrows query");
  if ($n_items > 0){
    for ($i = 0; $i < $n_items; $i++) {
      $item_id = htmlspecialchars(mysql_result($r_items, $i, "categories.id"));
      $item_position = htmlspecialchars(mysql_result($r_items, $i, "categories.position"));
      $item_position--;

      $q_update_position = sprintf("UPDATE `categories` SET `categories`.`position`='".$item_position."'"."
      WHERE `categories`.`id`='".$item_id."'");
      mysql_query($q_update_position) or die(generate_exception($db_error));
    }
  }
}

function create_path_items($item_id,&$path){
  //GLOBAL $path;
  $q_query_path = sprintf("SELECT * FROM `categories` WHERE `categories`.`parent_id`='".$item_id."'");
  $r_query_path = mysql_query($q_query_path) or die("cant execute query_path");
  $n_query_path = mysql_numrows($r_query_path); // or die("cant get numrows query_path");
  if ($n_query_path > 0) {
    for ($i = 0; $i < $n_query_path; $i++) {
      $query_path_id = htmlspecialchars(mysql_result($r_query_path, $i, "categories.id"));
      $query_path_is_last = htmlspecialchars(mysql_result($r_query_path, $i, "categories.is_last"));
      if ($query_path_is_last=='true'){
        $path=$path.$query_path_id.',';
      }
      create_path_items($query_path_id,$path);
    }
  }
  return $path;
}

function reindex_cats(){

  $q_query = sprintf("SELECT * FROM `categories` ORDER BY `categories`.`id`");
  $r_query = mysql_query($q_query) or die("cant execute query");
  $n_query = mysql_numrows($r_query); // or die("cant get numrows query");
  if ($n_query > 0) {
    for ($i = 0; $i < $n_query; $i++) {
      $query_id = htmlspecialchars(mysql_result($r_query, $i, "categories.id"));
      $q_check = sprintf("SELECT * FROM `categories` WHERE `categories`.`parent_id`='".$query_id."'");
      $r_check = mysql_query($q_check) or die("cant execute check");
      $n_check = mysql_numrows($r_check); // or die("cant get numrows check");
      if ($n_check > 0) {
        $var_is_last='false';
        $q_update = sprintf("UPDATE `categories` SET `categories`.`is_last`='".$var_is_last."'"." WHERE `categories`.`id`='".$query_id."'");
        mysql_query($q_update) or die("cant execute update update");
      } else {
        $var_is_last='true';
        $q_update = sprintf("UPDATE `categories` SET `categories`.`is_last`='".$var_is_last."'"." WHERE `categories`.`id`='".$query_id."'");
        mysql_query($q_update) or die("cant execute update update");
      }
    }
  }


  $q_query = sprintf("SELECT * FROM `categories` WHERE `categories`.`is_last` = 'true' ORDER BY `categories`.`id`");
  $r_query = mysql_query($q_query) or die("cant execute query");
  $n_query = mysql_numrows($r_query); // or die("cant get numrows query");
  if ($n_query > 0) {
    for ($i = 0; $i < $n_query; $i++) {
      $query_id = htmlspecialchars(mysql_result($r_query, $i, "categories.id"));
      $path = array();
      $path = create_path($query_id,$path);
      for ($x = 0; $x < count($path); $x++){
        if($path[$x] == $query_id) continue;
        set_last_items($path[$x],$path);
      }
    }
  }
}



if($_POST['action'] == 'save_positions'){
  $postitions = $_POST['positions'];
  $var_parent_id = (int)$_POST['parent_id'];

  for ($i = 0,$position = 1; $i < count($postitions); $i++,$position++) {
    $item_id = $postitions[$i];
    $q_update_child = sprintf("UPDATE `categories` SET `categories`.`position`='".$position."'"."
    WHERE `categories`.`id`='".$item_id."' AND `categories`.`parent_id` = '".$var_parent_id."'");
    mysql_query($q_update_child) or die(generate_exception($db_error));
  }

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'edit_cat_parent'){


} else if($_POST['action'] == 'add_cat'){

  $create_tree = (int)$_POST['create_tree'];
  $table_name = AdmConfig::$categories_table_name;
  $images_table_name = AdmConfig::$categories_images_table_name;

  $var_hash = $_POST['hash'];
  $var_hash = mysql_real_escape_string($var_hash);

  $cat_id = Categories::add_cat($_POST);

  $q_temp_images = sprintf("SELECT * FROM `temp_images` WHERE `temp_images`.`md5_hash` = '".$var_hash."'");
  $r_temp_images = mysql_query($q_temp_images) or die(generate_exception($db_error));
  $n_temp_images = mysql_numrows($r_temp_images); // or die("cant get numrows query");
  if ($n_temp_images > 0){
    for ($i = 0; $i < $n_temp_images; $i++) {
      $image_id = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.id"));
      $image_name = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.image"));
      $image_position = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.position"));

      if($image_position == 1){

        AdmCommon::update_data(array(
          'table_name' => $table_name,
          'fields' => array(
            'main_image' => $image_name
          ),
          'where' => array(
            'id' => $cat_id
          )
        ));

      }

      AdmCommon::insert_data(array(
        'table_name' => $images_table_name,
        'fields' => array(
          'user_id' => $var_user_id,
          'cat_id' => $cat_id,
          'image' => $image_name,
          'position' => $image_position,
          'create_date' => $var_create_date
        )
      ));

      $q_delete_temp_image = sprintf("DELETE FROM `temp_images`
      WHERE `temp_images`.`id`='".$image_id."'");
      mysql_query($q_delete_temp_image) or die(generate_exception($db_error));

    }
  }

  if($create_tree > 0){
    require_once($root_dir.'/admin/include/classes/tree_cats.php');
    $add_cat_tree_cats = Create_cats_tree::create(array(
      'in_table' => false
    ));
  }


  echo json_encode(array(
    'result' => 'true',
    'item_id' => $cat_id,
    'tree_cats' => $add_cat_tree_cats
  ));

} else if($_POST['action'] == 'edit_cat'){

  Categories::update_cat($_POST);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'small_update_cat'){

  $table_name = AdmConfig::$categories_table_name;

  $cat_id = (int)$_POST['cat_id'];
  $name = $_POST['name'];
  $alias = $_POST['alias'];

  $name = mysql_real_escape_string($name);
  $alias = mysql_real_escape_string($alias);

  if(empty($name)) generate_exception('Введите название рубрики');

  $add_alias = Alias::add_alias(array(
    'item_id' => $cat_id,
    'type' => 'cat',
    'alias' => $alias,
    'table_name' => $table_name
  ));

  if($add_alias === false) generate_exception('Алиас с таким именем уже существует, придумайте другой');

  AdmCommon::update_data(array(
    'table_name' => $table_name,
    'fields' => array(
      'name' => $name,
      'alias' => $alias
    ),
    'where' => array(
      'id' => $cat_id
    )
  ));

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'delete_cat'){

  $result = Categories::delete_cat($_POST);

  if($result !== true) generate_exception($result);

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'get_cats'){

  $is_select_product_cat = (int)$_POST['select_product'];
  $var_parent_id = (int)$_POST['parent_id'];

  $path = array();
  $path = create_path($var_parent_id,$path);
  $arrlength = count($path);


  $html_map .= '<ul>';
  if($arrlength > 0){
    $html_map .= '<li onclick="get_cats(0);">Все</li>';
    for($i = 0; $i < $arrlength; $i++){
      if($i==$arrlength-1){
        $html_map .= '<li data-catname="'.$path[$i]['name'].'" id="map_sl_cat" class="active_map_item"> / '.$path[$i]['name'].'</li>';
      } else {
        $html_map .= '<li onclick="get_cats('.$path[$i]['id'].');"> / '.$path[$i]['name'].'</li>';
      }
    }
  } else{
    $html_map .= '<li class="active_map_item">Все / </li>';
  }
  $html_map .= '</ul>';

  $q_cats = sprintf("SELECT * FROM `categories` WHERE `categories`.`parent_id` = '".$var_parent_id."'");
  $r_cats = mysql_query($q_cats) or die("cant execute query6");
  $n_cats = mysql_numrows($r_cats); // or die("cant get numrows query");
  if ($n_cats > 0){
    for ($i = 0; $i < $n_cats; $i++) {
      $cat_id = htmlspecialchars(mysql_result($r_cats, $i, "categories.id"));
      $cat_name = htmlspecialchars(mysql_result($r_cats, $i, "categories.name"));

      $html_cats .= '<div onclick="get_cats('.$cat_id.');" class="s_cat_item" id="s_cat_'.$cat_id.'">'.$cat_name.'</div>';

    }
  } else $html_cats = '<div class="sel_cat s_not_cats">Нет категорий</div>';

  echo json_encode(array('result' => 'true','html_map' => $html_map,'html_cats' => $html_cats));
} else if($_POST['action'] == 'create_chpy'){

  $var_str = $_POST['str'];
  $lang = $_POST['lang'];

  $var_chpy = Alias::create_chpy($var_str,$lang);

  echo json_encode(array('result' => 'true','str' => $var_chpy));
}




 ?>
