<?php
if(!isset($LOGGED_USER)){
  session_start();
  $LOGGED_USER=$_SESSION['logged_user'];
  session_write_close();
}
$var_create_date=time();
$var_user_id = (int)$LOGGED_USER['id'];
if($var_user_id == 0) generate_exception('Вы не авторизированы');


class Uploader{
  protected $user_id;
  protected $allowed_file_size;
  protected $allowed_file_types;
  protected $allowed_exts;
  protected $temporary_dir;
  protected $table_name;
  protected $where_item;
  protected $where_table_field;
  protected $root_dir;
  protected $image_table_name;
  protected $item_id;
  protected $path;
  protected $max_files;

  function __construct($info = array()){
    $this->allowed_file_size = isset($info['file_size']) ? $info['file_size']
    : 10000000;

    $this->allowed_file_types = isset($info['file_types']) ? $info['file_types']
    : array("image/png", "image/jpeg", "image/pjpeg");

    $this->allowed_exts = isset($info['file_types_text']) ? $info['file_types_text']
    : array("jpg", "jpeg", "png");

    $this->image_table_name = isset($info['image_table_name']) ? $info['image_table_name'] : false;
    $this->max_files = isset($info['max_files']) ? $info['max_files'] : 1;

    $this->temporary_dir = $info['temporary_dir'];
    $this->table_name = $info['table_name'];
    $this->where_item = $info['where_item'];
    $this->where_table_field = $info['where_table_field'];
    $this->root_dir = $_SERVER['DOCUMENT_ROOT'];

    $this->item_id = isset($info['item_id']) ? (int)$info['item_id'] : 0;
    $this->path = isset($info['path']) ? $info['path'] : '';

  }

  public function create_html(){
    GLOBAL $l;

    $mi = 'Главное';

    echo '<div class="form-group">';
    echo '<div class="col-lg-10">';
    echo '<div id="load_images" class="i_block">';

    $n_images = 0;
    if($this->item_id > 0){
      $q_images = ("SELECT * FROM `".$this->table_name."` WHERE `item_id` = '".$this->item_id."' ORDER BY `position` ASC");
      $r_images = mysql_query($q_images) or die(DB_ERROR);
      $n_images = mysql_numrows($r_images); // or die("cant get numrows query");
      if ($n_images > 0){
        for ($i = 0; $i < $n_images; $i++) {
          $image_id = htmlspecialchars(mysql_result($r_images, $i, "id"));
          $image_src = htmlspecialchars(mysql_result($r_images, $i, "image"));
          $image_position = htmlspecialchars(mysql_result($r_images, $i, "position"));

          echo '<div class="relative i_block l_img_item" data-imageid="'.$image_id.'">';
          echo '<div class="l__image">';
          echo '<img class="cover_img" src="'.($this->path).$image_src.'">';
          echo '</div>';
          echo '<button type="button" class="absolute close upbtn_delete_image cursor_p" data-image="'.$image_id.'">×</button>';
          if($image_position == 1) echo '<div class="absolute full_w main_img" style="display: block;">'.$mi.'</div>';
          echo '</div>';

        }
      }
    }

    $display_upload_images = $n_images >= $this->max_files ? 'display: none;' : '';

    echo '</div>';
    echo '<div id="btn_upload" class="i_block" style="'.$display_upload_images.'">';
    echo '<div id="btn_load_img" class="cursor_p icon_add_image">';
    echo '<img class="full_w" src="/images/add_image_icon.png" alt="">';
    echo '</div>';
    echo '<input type="file" name="files[]" class="display_none" id="file_list">';
    echo '<input type="hidden" id="md5_hash" value="'.md5(rand(1,10000).time()).'">';
    echo '</div>';
    echo '</div>';
    echo '<div class="clear"></div>';
    echo '</div>';




  }

  public function replace_extension($filename, $new_extension){
      $info = pathinfo($filename);
      return $info['filename'].'.'.$new_extension;
  }

  public function create_thumb($info){

    $image_src = $info['image_src'];
    $output_folder = $info['output_folder'];
    $max_width = $info['max_width'];
    $max_height = $info['max_height'];
    $new_extension = array_key_exists('new_extension',$info) ? $info['new_extension'] : 'current';
    $resize_to_max_size = array_key_exists('resize_to_max_size',$info) ? $info['resize_to_max_size'] : false;

    $arr_image_details = getimagesize($image_src);
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

    /*Water*/

    // $stamp = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/admin/components/uploader/lg_rgba.png');

    // $marge_right = 10;
    // $marge_bottom = 10;
    // $sx = imagesx($stamp);
    // $sy = imagesy($stamp);

    /*END Water*/

    $img = $imgcreatefrom($image_src);
    $width = imagesx($img);
    $height = imagesy($img);

    if ($height > $width) {
      $ratio = $max_height / $height;
      $newheight = $max_height;
      $newwidth = $width * $ratio;
    } else {
      $ratio = $max_width / $width;
      $newwidth = $max_width;
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

    imagecopy($img, $stamp, $width - $sx - $marge_right, $height - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));
    imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);



    // imagecopyresampled($newimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

    if (!file_exists($output_folder)) {
      mkdir($output_folder, 0777, true);
    }

    if (strtolower($new_extension) == 'jpg') {
      $image_src = $this->replace_extension($image_src, 'jpg');
      ImageJPEG($newimg, $output_folder.'/'.$image_src, 100);
    } elseif (strtolower($new_extension) == 'png') {
      $image_src = $this->replace_extension($image_src, 'png');
      ImagePNG($newimg, $output_folder.'/'.$image_src, 0);
    } elseif (strtolower($new_extension) == 'gif') {
      $image_src = $this->replace_extension($image_src, 'gif');
      ImageGIF($newimg, $output_folder.'/'.$image_src, 100);
    } else {
      $imgt($newimg, $output_folder.'/'.$image_src);
    }

    $out['width'] = $newwidth;
    $out['height'] = $newheight;
    return $out;

  }


  public function upload(){
    GLOBAL $l,$var_user_id;
    $root_dir = $this->root_dir;
    $temporary_dir = $this->temporary_dir;
    $table_name = $this->table_name;
    $where_item = $this->where_item;
    $where_table_field = $this->where_table_field;
    $max_files = $this->max_files;
    $image_table_name = $this->image_table_name;
    $var_create_date = time();
    $alias = $_POST['alias'];

    $alias = mysql_real_escape_string($alias);

    $file_size=$_FILES["file"]["size"];
    $file_name=$_FILES["file"]["name"];
    $file_type=$_FILES["file"]["type"];
    $file_error=$_FILES["file"]["error"];
    $file_tmp_name=$_FILES["file"]["tmp_name"];


    $q_check_files = ("SELECT * FROM `".$table_name."`
      WHERE `".$table_name."`.`".$where_table_field."` = '".$where_item."'");
    $r_check_files = mysql_query($q_check_files) or die("cant execute query1");
    $n_check_files = mysql_numrows($r_check_files); // or die("cant get numrows query");
    if($n_check_files >= $max_files) generate_exception('Максимальное количество файлов уже загружено');
    $n_check_files++;


    if(empty($where_item)) generate_exception('Произошла неожиданная ошибка, повторите попыку позже');

    if ($file_error > 0){
      generate_exception(('Ошибка: ').' '.$file_name.', '.('повторите попытку позже'));
    }

    $exploded_arr=explode(".", $file_name);

    $extension = end($exploded_arr);

    $extension = strtolower($extension);


    if($file_size > $this->allowed_file_size){
      generate_exception(('Ошибка: ').' '.$file_name.' '.('слишком большой.'));
    }
    if(!in_array($file_type, $this->allowed_file_types)){
      generate_exception(('Ошибка: ').' '.$file_name.' '.('имеет некорректный формат'));
    }

    if(!in_array($extension, $this->allowed_exts)){
      generate_exception(('Ошибка: ').' '.$file_name.' '.('имеет некорректный формат'));
    }

    $var_new_file_name = rand(10,99).'-'.uniqid(rand(1,10000), true).'.'.$extension;

    $copy_result = move_uploaded_file($file_tmp_name,$root_dir.$temporary_dir.$var_new_file_name);

    if ($copy_result==false) {
      generate_exception(('Ошибка: ').' '.$file_name.', '.('повторите попытку позже'));
    }

    $this->create_thumb(array(
      'image_src' => $root_dir.$temporary_dir.$var_new_file_name,
      'output_folder' => $root_dir.$temporary_dir.'t_140',
      'max_width' => 140,
      'max_height' => 140,
      'new_extension' => $extension
    ));

    $this->create_thumb(array(
      'image_src' => $root_dir.$temporary_dir.$var_new_file_name,
      'output_folder' => $root_dir.$temporary_dir.'t_480',
      'max_width' => 480,
      'max_height' => 480,
      'new_extension' => $extension
    ));

    $this->create_thumb(array(
      'image_src' => $root_dir.$temporary_dir.$var_new_file_name,
      'output_folder' => $root_dir.$temporary_dir.'t_1024',
      'max_width' => 1024,
      'max_height' => 1024,
      'new_extension' => $extension
    ));

    $q_last_position = ("SELECT * FROM `".$table_name."`
      WHERE `".$table_name."`.`".$where_table_field."` = '".$where_item."' ORDER BY `".$table_name."`.`position` DESC LIMIT 1");
    $r_last_position = mysql_query($q_last_position) or die($q_last_position);
    $n_last_position = mysql_numrows($r_last_position); // or die("cant get numrows query");
    if ($n_last_position > 0){
      $last_position = htmlspecialchars(mysql_result($r_last_position, 0, $table_name.".position"));
      $last_position++;
    } else{
      $last_position = 1;
    }

    $q_insert_from = ("INSERT INTO `".$table_name."` (
      `".$where_table_field."`,
      `image`,
      `user_id`,
      `position`,
      `create_date`
    ) values(
      '".$where_item."',
      '".$var_new_file_name."',
      '".$var_user_id."',
      '".$last_position."',
      '".$var_create_date."'
      ".")");
      mysql_query($q_insert_from) or die(generate_exception(DB_ERROR));

    $last_id = mysql_insert_id();

    if($last_position == 1 && !empty($image_table_name)){
      $q_update_main_image = ("UPDATE `".$image_table_name."` SET `".$image_table_name."`.`main_image`='".$var_new_file_name."'"."
      WHERE `".$image_table_name."`.`id`='".$where_item."'");
      mysql_query($q_update_main_image) or die(generate_exception($db_error));
    }

    $is_last_file = $n_check_files == $max_files ? true : false;

    echo json_encode(array(
      'result' => 'true',
      'image_id' => $last_id,
      'image' => $temporary_dir.'t_480/'.$var_new_file_name,
      'is_last_file' => $is_last_file
    ));
  }

  private function reposition_images($position){
    $table_name = $this->table_name;
    $where_item = $this->where_item;
    $where_table_field = $this->where_table_field;
    $image_table_name = $this->image_table_name;

    $q_images = ("SELECT * FROM `".$table_name."` WHERE
    `".$table_name."`.`".$where_table_field."` = '".$where_item."'
    AND `".$table_name."`.`position` > '".$position."'");
    $r_images = mysql_query($q_images) or die("cant execute query2");
    $n_images = mysql_numrows($r_images); // or die("cant get numrows query");
    if ($n_images > 0){
      for ($i = 0; $i < $n_images; $i++) {
        $image_id = htmlspecialchars(mysql_result($r_images, $i, $table_name.".id"));
        $image_name = htmlspecialchars(mysql_result($r_images, $i, $table_name.".image"));
        $image_position = htmlspecialchars(mysql_result($r_images, $i, $table_name.".position"));
        $image_position--;

        if(($position == 1 && $image_position == 1) &&
        ($image_table_name !== false)){
          $q_update_main_image = ("UPDATE `".$image_table_name."` SET `".$image_table_name."`.`main_image`='".$image_name."'"."
          WHERE `".$image_table_name."`.`id`='".$where_item."'");
          mysql_query($q_update_main_image) or die(generate_exception($db_error));
        }

        $q_update_image = ("UPDATE `".$table_name."` SET `".$table_name."`.`position`='".$image_position."'"."
        WHERE `".$table_name."`.`id`='".$image_id."'");
        mysql_query($q_update_image) or die(generate_exception($db_error));

      }
    }
  }


  public function delete_image($image_id){
    GLOBAL $l;
    $root_dir = $this->root_dir;
    $temporary_dir = $this->temporary_dir;
    $table_name = $this->table_name;
    $where_item = $this->where_item;
    $where_table_field = $this->where_table_field;
    $image_id = (int)$_POST['image_id'];
    $image_table_name = $this->image_table_name;

    $q_check_exist = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$image_id."'
    AND `".$table_name."`.`".$where_table_field."` = '".$where_item."'");
    $r_check_exist = mysql_query($q_check_exist) or die("cant execute query3");
    $n_check_exist = mysql_numrows($r_check_exist); // or die("cant get numrows query");
    if ($n_check_exist > 0){
      $image_name = htmlspecialchars(mysql_result($r_check_exist, 0, $table_name.".image"));
      $image_position = htmlspecialchars(mysql_result($r_check_exist, 0, $table_name.".position"));
      $item_id = htmlspecialchars(mysql_result($r_check_exist, 0, $table_name.".item_id"));

      $q_delete_temp_image = ("DELETE FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$image_id."'");
      mysql_query($q_delete_temp_image) or die(generate_exception(DB_ERROR));

      $this->reposition_images($image_position);

      unlink($root_dir.$temporary_dir.'t_140/'.$image_name);
      unlink($root_dir.$temporary_dir.'t_480/'.$image_name);
      unlink($root_dir.$temporary_dir.'t_1024/'.$image_name);
      unlink($root_dir.$temporary_dir.$image_name);

    } else generate_exception('Изображение не найдено');

    if($n_check_exist == 1){

      $q_update_main_image = ("UPDATE `".$image_table_name."` SET `main_image`= '' WHERE `id` = '".$item_id."'");
      mysql_query($q_update_main_image) or die(generate_exception($db_error));

    }


    echo json_encode(array('result' => 'true'));
  }

  public function change_positions(){
    $arr_positions = $_POST['positions'];
    $table_name = $this->table_name;
    $where_item = $this->where_item;
    $where_table_field = $this->where_table_field;
    $image_table_name = $this->image_table_name;


    for ($i = 0, $position = 1; $i < count($arr_positions); $i++, $position++) {
      $var_image_id = (int)$arr_positions[$i]['image_id'];

      if($position == 1 && $image_table_name !== false){


        $q_image = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$var_image_id."'
        AND `".$table_name."`.`".$where_table_field."` = '".$where_item."'");
        $r_image = mysql_query($q_image) or die(generate_exception($db_error));
        $n_image = mysql_numrows($r_image); // or die("cant get numrows query");
        if ($n_image > 0){
          $image = htmlspecialchars(mysql_result($r_image, 0, $table_name.".image"));

          $q_update_main_image = ("UPDATE `".$image_table_name."`
          SET `".$image_table_name."`.`main_image`='".$image."'"."
          WHERE `".$image_table_name."`.`id`='".$where_item."'");
          mysql_query($q_update_main_image) or die(generate_exception($db_error));
        }

      }


      $q_update_position = ("UPDATE `".$table_name."`
      SET `".$table_name."`.`position`='".$position."'"."
      WHERE `".$table_name."`.`id`='".$var_image_id."'");
      mysql_query($q_update_position) or die(generate_exception($db_error));

    }

    echo json_encode(array('result' => 'true'));
  }

}
?>
