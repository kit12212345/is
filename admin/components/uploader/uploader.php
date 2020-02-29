<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/db_connect.php');
include($root_dir.'/admin/components/uploader/index.php');

function set_action($uploader,$action){
  if($action == 'upload'){
    $uploader->upload();
  } else if($action == 'delete'){
    $uploader->delete_image();
  } else if($action == 'change_positions'){
    $uploader->change_positions();
  }
}

$var_action = $_POST['action'];

if($_POST['_event'] == 'add_product'){
  $var_hash = $_POST['item_id'];
  $var_hash = mysql_real_escape_string($var_hash);

  $uploader = new Uploader(array(
    'table_name' => 'temp_images',
    'where_item' => $var_hash,
    'where_table_field' => 'md5_hash',
    'temporary_dir' => '/images/catalog/',
    'max_files' => 10
  ));

  set_action($uploader,$var_action);

} else if($_POST['_event'] == 'edit_product'){
  $product_id = (int)$_POST['item_id'];

  $uploader = new Uploader(array(
    'table_name' => 'catalog_images',
    'where_item' => $product_id,
    'where_table_field' => 'item_id',
    'temporary_dir' => '/images/catalog/',
    'image_table_name' => 'catalog',
    'max_files' => 10
  ));

  set_action($uploader,$var_action);

} else if($_POST['page'] == 'add_cat'){
  $var_hash = $_POST['item_id'];
  $var_hash = mysql_real_escape_string($var_hash);

  $uploader = new Uploader(array(
    'table_name' => 'temp_images',
    'where_item' => $var_hash,
    'where_table_field' => 'md5_hash',
    'temporary_dir' => '/images/categories/'
  ));

  set_action($uploader,$var_action);
} else if($_POST['page'] == 'edit_cat'){
  $var_cat_id = (int)$_POST['item_id'];

  $uploader = new Uploader(array(
    'table_name' => 'categories_images',
    'where_item' => $var_cat_id,
    'where_table_field' => 'cat_id',
    'temporary_dir' => '/images/categories/',
    'image_table_name' => 'categories'
  ));

  set_action($uploader,$var_action);
} else if($_POST['page'] == 'add_post'){

  $var_hash = $_POST['item_id'];
  $var_hash = mysql_real_escape_string($var_hash);

  $uploader = new Uploader(array(
    'table_name' => 'temp_images',
    'where_item' => $var_hash,
    'where_table_field' => 'md5_hash',
    'temporary_dir' => '/images/post_images/'
  ));

  set_action($uploader,$var_action);

} else if($_POST['page'] == 'edit_post'){
  $var_post_id = (int)$_POST['item_id'];


  $uploader = new Uploader(array(
    'table_name' => 'posts_images',
    'where_item' => $var_post_id,
    'where_table_field' => 'post_id',
    'temporary_dir' => '/images/post_images/',
    'image_table_name' => 'posts'
  ));

  set_action($uploader,$var_action);
}
?>
