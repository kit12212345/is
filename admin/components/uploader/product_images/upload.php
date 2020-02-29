<?php
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
header('Content-Type: application/json');
set_time_limit(0);
error_reporting(E_ERROR);
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
$var_create_date=time();
$var_user_id = (int)$LOGGED_USER['admin_id'];
if($var_user_id == 0) generate_exception('Вы не авторизированы');

$allowed_file_size = 10000000;
$allowed_File_Types = array("image/png", "image/jpeg", "image/pjpeg");
$allowed_Exts = array("jpg", "jpeg", "png");
$temporary_dir = $root_dir.'/images/products/';

function replace_extension($filename, $new_extension){
    $info = pathinfo($filename);
    return $info['filename'].'.'.$new_extension;
}

function reposition_images($item_id,$position){

  $table_name = is_numeric($item_id) ? 'products_images' : 'temp_images';
  $sql_where = is_numeric($item_id)
  ? '`products_images`.`product_id` = \''.$item_id.'\''
  : '`temp_images`.`md5_hash` = \''.$item_id.'\'';

  $q_images = sprintf("SELECT * FROM `".$table_name."` WHERE ".$sql_where."
  AND `".$table_name."`.`position` > '".$position."'");
  $r_images = mysql_query($q_images) or die("cant execute query");
  $n_images = mysql_numrows($r_images); // or die("cant get numrows query");
  if ($n_images > 0){
    for ($i = 0; $i < $n_images; $i++) {
      $image_id = htmlspecialchars(mysql_result($r_images, $i, $table_name.".id"));
      $image_name = htmlspecialchars(mysql_result($r_images, $i, $table_name.".image"));
      $image_position = htmlspecialchars(mysql_result($r_images, $i, $table_name.".position"));
      $image_position--;

      if($position == 1 && $image_position == 1 && is_numeric($item_id)){
        $q_update_main_image = sprintf("UPDATE `products` SET `products`.`main_image`='".$image_name."'"."
        WHERE `products`.`id`='".$item_id."'");
        mysql_query($q_update_main_image) or die(generate_exception($db_error));
      }

      $q_update_image = sprintf("UPDATE `".$table_name."` SET `".$table_name."`.`position`='".$image_position."'"."
      WHERE `".$table_name."`.`id`='".$image_id."'");
      mysql_query($q_update_image) or die(generate_exception($db_error));

    }
  }
}


function create_thumb($src_image, $folder_output, $maxwidth, $maxheight, $new_extension = 'current', $resize_to_max_size = false)
{
    $arr_image_details = getimagesize($src_image);

    if ($arr_image_details[2] == 1) {
        $imgt = 'ImageGIF';
        $imgcreatefrom = 'ImageCreateFromGIF';
    }
    if ($arr_image_details[2] == 2) {
        $imgt = 'ImageJPEG';
        $imgcreatefrom = 'ImageCreateFromJPEG';
    }
    if ($arr_image_details[2] == 3) {
        $imgt = 'ImagePNG';
        $imgcreatefrom = 'ImageCreateFromPNG';
    }

    $img = $imgcreatefrom($src_image);
    $width = imagesx($img);
    $height = imagesy($img);

    if ($height > $width) {
        $ratio = $maxheight / $height;
        $newheight = $maxheight;
        $newwidth = $width * $ratio;
    } else {
        $ratio = $maxwidth / $width;
        $newwidth = $maxwidth;
        $newheight = $height * $ratio;
    }

    if (($ratio > 1) && ($resize_to_max_size==false)) {
        $newheight = $height;
        $newwidth = $width;
    }

    $newimg = imagecreatetruecolor($newwidth, $newheight);

    imagealphablending($newimg, false);

    imagesavealpha($newimg, true);

    $palsize = ImageColorsTotal($img);

    for ($i = 0; $i < $palsize; ++$i) {
        $colors = ImageColorsForIndex($img, $i);
        ImageColorAllocate($newimg, $colors['red'], $colors['green'], $colors['blue']);
    }

    imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    if (!file_exists($folder_output)) {
        mkdir($folder_output, 0777, true);
    }

    if (strtolower($new_extension) == 'jpg') {
        $src_image = replace_extension($src_image, 'jpg');
        ImageJPEG($newimg, $folder_output.'/'.$src_image, 100);
    } elseif (strtolower($new_extension) == 'png') {
        $src_image = replace_extension($src_image, 'png');
        ImagePNG($newimg, $folder_output.'/'.$src_image, 0);
    } elseif (strtolower($new_extension) == 'gif') {
        $src_image = replace_extension($src_image, 'gif');
        ImageGIF($newimg, $folder_output.'/'.$src_image, 100);
    } else {
        $imgt($newimg, $folder_output.'/'.$src_image);
    }

    $out['width'] = $newwidth;
    $out['height'] = $newheight;
    return $out;

}


if ($_GET['action']=='upload_files'){
  $error_code=0;
  $error_string='';

  $file_size=$_FILES["file"]["size"];
  $file_name=$_FILES["file"]["name"];
  $file_type=$_FILES["file"]["type"];
  $file_error=$_FILES["file"]["error"];
  $file_tmp_name=$_FILES["file"]["tmp_name"];

  $var_product_id = (int)$_GET['product_id'];
  $var_hash = $_GET['hash'];
  $var_hash = mysql_real_escape_string($var_hash);

  $table_name = $var_product_id > 0 ? 'products_images' : 'temp_images';

  if(empty($var_hash)) generate_exception('Хеш изображения не найден, перезаргузите страницу и повторите действие');

  if ($file_error > 0){
    generate_exception('Произошла ошибка при загрузке файла '.$file_name.', повторите попытку позже');
  }

  $exploded_arr=explode(".", $file_name);

  $extension = end($exploded_arr);

  $extension = strtolower($extension);


  if($file_size > $allowed_file_size){
    generate_exception('Ошибка: файл '.$file_name.' слишком большой');
  }
  if(!in_array($file_type, $allowed_File_Types)){
    generate_exception('Ошибка: файл '.$file_name.' имеет недопустимое формат');
  }
  if(!in_array($extension, $allowed_Exts)){
    generate_exception('Ошибка: файл '.$file_name.' имеет недопустимое формат');
  }

  $var_new_file_name=$var_create_date.'_'.md5(uniqid(rand(1,10000000), true)).'.'.$extension;

  $copy_result=move_uploaded_file($file_tmp_name,$temporary_dir.$var_new_file_name);

  if ($copy_result==false) {
    generate_exception('Произошла ошибка при загрузке файла '.$file_name.', повторите попытку позже');
  }

  create_thumb($temporary_dir.$var_new_file_name,$temporary_dir.'thumbnail_140',140,140,$extension);
  create_thumb($temporary_dir.$var_new_file_name,$temporary_dir.'thumbnail_480',480,480,$extension);
  create_thumb($temporary_dir.$var_new_file_name,$temporary_dir.'thumbnail_1024',1024,1024,$extension);

  if($var_product_id > 0){
    $q_last_position = sprintf("SELECT * FROM `products_images`
      WHERE `products_images`.`product_id` = '".$var_product_id."' ORDER BY `products_images`.`position` DESC LIMIT 1");
  } else{
    $q_last_position = sprintf("SELECT * FROM `temp_images`
      WHERE `temp_images`.`md5_hash` = '".$var_hash."' ORDER BY `temp_images`.`position` DESC LIMIT 1");
  }
  $r_last_position = mysql_query($q_last_position) or die("cant execute query");
  $n_last_position = mysql_numrows($r_last_position); // or die("cant get numrows query");
  if ($n_last_position > 0){
    $last_position = htmlspecialchars(mysql_result($r_last_position, 0, $table_name.".position"));
    $last_position++;
  } else{
    $last_position = 1;
  }

  if($var_product_id > 0){
    $q_insert_from = sprintf("INSERT INTO `products_images` (
      `product_id`,
      `image`,
      `position`,
      `create_date`
    ) values(
      '".$var_product_id."',
      '".$var_new_file_name."',
      '".$last_position."',
      '".$var_create_date."'
      ".")");
      mysql_query($q_insert_from) or die(generate_exception('Произошла ошибка,повторите попытку позже'));
  } else{
    $q_insert_from = sprintf("INSERT INTO `temp_images` (
      `image`,
      `md5_hash`,
      `position`,
      `create_date`
    ) values(
      '".$var_new_file_name."',
      '".$var_hash."',
      '".$last_position."',
      '".$var_create_date."'
      ".")");
      mysql_query($q_insert_from) or die(generate_exception('Произошла ошибка,повторите попытку позже'));

  }

  $last_id = mysql_insert_id();

  echo json_encode(array('result' => 'true','image_id' => $last_id,'image' => '/images/products/thumbnail_480/'.$var_new_file_name));

} else if($_GET['action'] == 'delete_image'){
  $var_product_id = (int)$_POST['product_id'];
  $var_item_id = (int)$_POST['item_id'];
  $var_hash = $_POST['hash'];
  $var_hash = mysql_real_escape_string($var_hash);

  $table_name = $var_product_id > 0 ? 'products_images' : 'temp_images';

  if($var_product_id > 0){
    $q_check_exist = sprintf("SELECT * FROM `products_images` WHERE `products_images`.`id` = '".$var_item_id."'
    AND `products_images`.`product_id` = '".$var_product_id."'");
  } else{
    $q_check_exist = sprintf("SELECT * FROM `temp_images` WHERE `temp_images`.`id` = '".$var_item_id."'
    AND `temp_images`.`md5_hash` = '".$var_hash."'");
  }
  $r_check_exist = mysql_query($q_check_exist) or die("cant execute query");
  $n_check_exist = mysql_numrows($r_check_exist); // or die("cant get numrows query");
  if ($n_check_exist > 0){
    $image_name = htmlspecialchars(mysql_result($r_check_exist, 0, $table_name.".image"));
    $image_position = htmlspecialchars(mysql_result($r_check_exist, 0, $table_name.".position"));

    $q_delete_temp_image = sprintf("DELETE FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$var_item_id."'");
    mysql_query($q_delete_temp_image) or die(generate_exception('Произошла ошибка,повторите попытку позже'));

    $f_item_id = $var_product_id > 0 ? $var_product_id : $var_hash;
    reposition_images($f_item_id,$image_position);

    unlink($temporary_dir.'thumbnail_140/'.$image_name);
    unlink($temporary_dir.'thumbnail_480/'.$image_name);
    unlink($temporary_dir.'thumbnail_1024/'.$image_name);
    unlink($temporary_dir.$image_name);

  } else generate_exception('Изображение не найдено');


  echo json_encode(array('result' => 'true'));

} else if($_GET['action'] == 'change_positions'){
  $arr_positions = $_POST['positions'];
  $var_product_id = (int)$_POST['product_id'];
  $table_name = $var_product_id > 0 ? 'products_images' : 'temp_images';

  for ($i = 0, $position = 1; $i < count($arr_positions); $i++, $position++) {
    $var_image_id = (int)$arr_positions[$i]['image_id'];

    if($position == 1 && $var_product_id > 0){

      $q_image = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$var_image_id."'
      AND `".$table_name."`.`product_id` = '".$var_product_id."'");
      $r_image = mysql_query($q_image) or die(generate_exception($db_error));
      $n_image = mysql_numrows($r_image); // or die("cant get numrows query");
      if ($n_image > 0){
        $image = htmlspecialchars(mysql_result($r_image, 0, $table_name.".image"));

        $q_update_main_image = sprintf("UPDATE `products`
        SET `products`.`main_image`='".$image."'"."
        WHERE `products`.`id`='".$var_product_id."'");
        mysql_query($q_update_main_image) or die(generate_exception($db_error));
      }

    }

    $q_update_position = sprintf("UPDATE `".$table_name."`
    SET `".$table_name."`.`position`='".$position."'"."
    WHERE `".$table_name."`.`id`='".$var_image_id."'");
    mysql_query($q_update_position) or die(generate_exception($db_error));

  }

  echo json_encode(array('result' => 'true'));

} else if($_POST['action'] == 'get_md5_hash'){
  echo json_encode(array('result' => 'true','hash' => md5(rand(0,10000000))));
}







?>
