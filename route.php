<?php
session_start();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
require_once($root_dir.'/components/alias/alias.php');
include_once($root_dir.'/include/classes/lang.php');
$key1 = $_GET['key1'];

$key1 = mysql_real_escape_string($key1);

if(isset($_GET['post']) || isset($_GET['cat'])){
  $key2 = $_GET['key2'];
  $key1 = Alias::get_alias_name($key1,$key2,$lang->lang);
  if(!empty($key1)){
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://hluble.com/".$key1);
  }
} else {

}
$alias_info_key1 = Alias::get_page($key1,$lang->lang);


if(isset($alias_info_key1['name'])){
  header("HTTP/1.1 301 Moved Permanently");
  header("Location: https://hluble.com/".$alias_info_key1['name']);
}

if($alias_info_key1 === false){
  include_once($root_dir.'/error_404.php');
  exit;
}

$type_item = $alias_info_key1['type'];


$_GET[$type_item] = $alias_info_key1['id'];

if($type_item == 'recipess.php'){
  include_once($root_dir.'/recipess.php');
} else if($type_item == 'best_posts.php'){
  include_once($root_dir.'/best_posts.php');
} else if($type_item == 'about.php'){
  include_once($root_dir.'/about.php');
} else if($type_item == 'add_recipe.php'){
  include_once($root_dir.'/add_recipe.php');
} else if($type_item == 'post'){
  include_once($root_dir.'/post.php');
} else if($type_item == 'cat'){
  include_once($root_dir.'/posts.php');
} else {
  include_once($root_dir.'/error_404.php');
}





?>
