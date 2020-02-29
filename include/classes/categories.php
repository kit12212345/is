<?php
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class Categories extends AdmCommon{
  protected $parent_id;

  function __construct($data){
    $this->parent_id = isset($data['parent_id']) ? $data['parent_id'] : 0;
  }


  public function get_cat_info($cat_id){

    $table_name = self::$categories_table_name;

    $q_cat_info = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$cat_id."'");
    $r_cat_info = mysql_query($q_cat_info) or die("cant execute query_path");
    $n_cat_info = mysql_num_rows($r_cat_info); // or die("cant get numrows query_path");
    if ($n_cat_info > 0) {
        $cat_id = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".id"));
        $cat_name = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".name_".LANG));
        $cat_name_ru = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".name_ru"));
        $cat_name_en = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".name_en"));
        $cat_description = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".description_".LANG));
        $cat_description_ru = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".description_ru"));
        $cat_description_en = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".description_en"));
        $cat_image = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".main_image"));
        $cat_alias = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".alias_".LANG));
        $cat_alias_ru = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".alias_ru"));
        $cat_alias_en = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".alias_en"));
        $cat_parent_id = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".parent_id"));
        $cat_keywords = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".keywords_".LANG));
        $cat_keywords_ru = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".keywords_ru"));
        $cat_keywords_en = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".keywords_en"));
        $cat_translate = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".translate"));
        $cat_last_items = htmlspecialchars(mysql_result($r_cat_info, 0, $table_name.".last_items"));
        $arr_last_items = explode(',',$cat_last_items);


        return array(
          'id' => $cat_id,
          'name' => $cat_name,
          'name_ru' => $cat_name_ru,
          'name_en' => $cat_name_en,
          'description' => $cat_description,
          'description_ru' => $cat_description_ru,
          'description_en' => $cat_description_en,
          'image' => $cat_image,
          'alias' => $cat_alias,
          'alias_ru' => $cat_alias_ru,
          'alias_en' => $cat_alias_en,
          'parent_id' => $cat_parent_id,
          'keywords' => $cat_keywords,
          'keywords_ru' => $cat_keywords_ru,
          'keywords_en' => $cat_keywords_en,
          'translate' => $cat_translate,
          'arr_last_items' => $arr_last_items
        );

    }

    return false;
  }

  public function get_cats(){

    $categories = array();

    $table_name = self::$categories_table_name;

    $q_cats = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`parent_id` = '".$this->parent_id."'
    ORDER BY `".$table_name."`.`id` ASC");
    $r_cats = mysql_query($q_cats) or die("cant execute query_path");
    $n_cats = mysql_num_rows($r_cats); // or die("cant get numrows query_path");
    if ($n_cats > 0) {
      for ($i = 0; $i < $n_cats; $i++) {
        $cat_id = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".id"));
        $cat_name = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".name_".LANG));
        $cat_name_ru = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".name_ru"));
        $cat_name_en = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".name_en"));
        $cat_description = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".description_".LANG));
        $cat_description_ru = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".description_ru"));
        $cat_description_en = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".description_en"));
        $cat_image = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".main_image"));
        $cat_alias = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".alias_".LANG));
        $cat_count_posts = htmlspecialchars(mysql_result($r_cats, $i, $table_name.".count_posts"));

        array_push($categories,[
          'id' => $cat_id,
          'name' => $cat_name,
          'description' => $cat_description,
          'main_image' => $cat_image,
          'alias' => $cat_alias,
          'count_posts' => $cat_count_posts
        ]);

      }
    }
    return $categories;


  }


  public static function add_cat($data){

    $table_name = self::$categories_table_name;

    $name = $data['name'];
    $description = $data['description'];
    $alias = $data['alias'];
    $parent_id = (int)$data['parent_id'];

    if(Alias::check_exist_alias(array('alias' => $alias)) !== false)
      generate_exception('Алиас с таким именем уже существует, придумайте другой');

    $name = mysql_real_escape_string($name);
    $description = mysql_real_escape_string($description);

    if(empty($name)) generate_exception('Введите навзвание категории');

    $name = prc_to_str($name);
    $description = prc_to_str($description);

    $cat_id = self::insert_data(array(
      'table_name' => self::$categories_table_name,
      'fields' => array(
        'name' => $name,
        'description' => $description,
        'parent_id' => $parent_id,
        'create_date' => time()
      )
    ));

    $add_alias = Alias::add_alias(array(
      'item_id' => $cat_id,
      'type' => 'cat',
      'alias' => $alias,
      'table_name' => $table_name
    ));

    return $cat_id;

  }

  public static function is_parent_cat($item_id,$check_item){
    $path = array();
    $path = self::create_path($item_id,$path);
    if(in_array($check_item,$path)) return true;
    return false;
  }

  public static function set_last_items($item_id,$items){

    $table_name = self::$categories_table_name;

    $q_last_items = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$item_id."'");
    $r_last_items = mysql_query($q_last_items) or die("cant execute query1");
    $n_last_items = mysql_numrows($r_last_items); // or die("cant get numrows query");
    if ($n_last_items > 0) {
      $last_items = htmlspecialchars(mysql_result($r_last_items, 0, $table_name.".last_items"));

      for ($i = 0; $i < count($items); $i++) {
        $check_parent = self::is_parent_cat($item_id,$items[$i]['id']);
        if(strpos($last_items,$items[$i]['id']) === false && $check_parent === false){
          $last_items .= $last_items ? ','.$items[$i]['id'] : $items[$i]['id'];
        }
      }

      self::update_data(array(
        'table_name' => $table_name,
        'fields' => array(
          'last_items' => $last_items
        ),
        'where' => array(
          'id' => $item_id
        )
      ));


    }
  }

  public static function reindex_cats(){

    $table_name = self::$categories_table_name;

    $q_query = sprintf("SELECT * FROM `".$table_name."` ORDER BY `".$table_name."`.`id`");
    $r_query = mysql_query($q_query) or die("cant execute query2");
    $n_query = mysql_numrows($r_query); // or die("cant get numrows query");
    if ($n_query > 0) {
      for ($i = 0; $i < $n_query; $i++) {
        $query_id = htmlspecialchars(mysql_result($r_query, $i, $table_name.".id"));
        $q_check = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`parent_id`='".$query_id."'");
        $r_check = mysql_query($q_check) or die("cant execute check");
        $n_check = mysql_numrows($r_check); // or die("cant get numrows check");
        if ($n_check > 0) {
          $var_is_last='false';

          self::update_data(array(
            'table_name' => $table_name,
            'fields' => array(
              'is_last' => $var_is_last
            ),
            'where' => array(
              'id' => $query_id
            )
          ));
        } else {
          $var_is_last='true';

          self::update_data(array(
            'table_name' => $table_name,
            'fields' => array(
              'is_last' => $var_is_last
            ),
            'where' => array(
              'id' => $query_id
            )
          ));

        }
      }
    }


    $q_query = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`is_last` = 'true' ORDER BY `".$table_name."`.`id`");
    $r_query = mysql_query($q_query) or die("cant execute query3");
    $n_query = mysql_numrows($r_query); // or die("cant get numrows query");
    if ($n_query > 0) {
      for ($i = 0; $i < $n_query; $i++) {
        $query_id = htmlspecialchars(mysql_result($r_query, $i, $table_name.".id"));
        $path = array();
        $path = self::create_path($query_id,$path);
        for ($x = 0; $x < count($path); $x++){
          if($path[$x]['id'] == $query_id) continue;
          self::set_last_items($path[$x]['id'],$path);
        }
      }
    }
  }

  public static function reposition_cats($parent_id,$position){

    $table_name = self::$categories_table_name;

    $q_items = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`parent_id` = '".$parent_id."'
    AND `".$table_name."`.`position` > '".$position."'");
    $r_items = mysql_query($q_items) or die("cant execute query4");
    $n_items = mysql_numrows($r_items); // or die("cant get numrows query");
    if ($n_items > 0){
      for ($i = 0; $i < $n_items; $i++) {
        $item_id = htmlspecialchars(mysql_result($r_items, $i, $table_name.".id"));
        $item_position = htmlspecialchars(mysql_result($r_items, $i, $table_name.".position"));
        $item_position--;

        self::update_data(array(
          'table_name' => $table_name,
          'fields' => array(
            'position' => $item_position
          ),
          'where' => array(
            'id' => $item_id
          )
        ));

      }
    }
  }

  public static function get_cat_position($parent_id){

    $table_name = self::$categories_table_name;

    $q_last_position = sprintf("SELECT `".$table_name."`.`position` FROM `".$table_name."`
    WHERE `".$table_name."`.`parent_id` = '".$var_parent_id."' ORDER BY `".$table_name."`.`position` DESC LIMIT 1");
    $r_last_position = mysql_query($q_last_position) or die("cant execute query5");
    $n_last_position = mysql_numrows($r_last_position); // or die("cant get numrows query");
    if ($n_last_position > 0){
      $position = htmlspecialchars(mysql_result($r_last_position, 0, $table_name.".position"));
      $position++;
    } else $position = 1;

    return $position;
  }

  public static function update_cat_parent($data){

    $table_name = self::$categories_table_name;

    $var_parent_id = (int)$data['parent_id'];
    $var_item_id = (int)$data['item_id'];

    $q_cat_parent = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$var_item_id."'");
    $r_cat_parent = mysql_query($q_cat_parent) or die(generate_exception($db_error));
    $n_cat_parent = mysql_numrows($r_cat_parent); // or die("cant get numrows query");
    if ($n_cat_parent > 0){
      $cat_parent = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".parent_id"));
      $cat_position = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".position"));
      $cat_last_items = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".last_items"));

      if($cat_last_items){
        $arr_children = explode(',',$cat_last_items);
        if(in_array($var_parent_id,$arr_children)) generate_exception('Вы не можете переместить категорию внутрь текущей категории');
      }

      self::reposition_cats($cat_parent,$cat_position);
      $position = self::get_cat_position($var_parent_id);

      self::update_data(array(
        'table_name' => $table_name,
        'fields' => array(
          'parent_id' => $var_parent_id,
          'position' => $position
        ),
        'where' => array(
          'id' => $var_item_id
        )
      ));

      self::reindex_cats();

    } else return 'Категория не найдена';

    return true;
  }

  #Суп, супы, как, приготовить, рецепт, рецепт супа, какой

  public static function update_cat($data){
    $cat_id = (int)$data['cat_id'];
    $parent_id = (int)$data['parent_id'];
    $name_ru = $data['name_ru'];
    $name_en = $data['name_en'];
    $description_ru = $data['description_ru'];
    $description_en = $data['description_en'];
    $keywords_ru = $data['keywords_ru'];
    $keywords_en = $data['keywords_en'];
    $translate = $data['translate'];
    $alias_ru = $data['alias_ru'];
    $alias_en = $data['alias_en'];

    $name_ru = mysql_real_escape_string($name_ru);
    $name_en = mysql_real_escape_string($name_en);

    $description_ru = mysql_real_escape_string($description_ru);
    $description_en = mysql_real_escape_string($description_en);

    $alias_ru = mysql_real_escape_string($alias_ru);
    $alias_en = mysql_real_escape_string($alias_en);

    $keywords_ru = mysql_real_escape_string($keywords_ru);
    $keywords_en = mysql_real_escape_string($keywords_en);


    if(empty($name_ru) || empty($name_en)) generate_exception('Введите навзвание категории');

    $add_alias_ru = Alias::add_alias(array(
      'item_id' => $cat_id,
      'type' => 'cat',
      'lang' => 'ru',
      'alias' => $alias_ru,
      'table_name' => self::$categories_table_name
    ));

    $add_alias_en = Alias::add_alias(array(
      'item_id' => $cat_id,
      'type' => 'cat',
      'lang' => 'en',
      'alias' => $alias_en,
      'table_name' => self::$categories_table_name
    ));


    if($add_alias_ru === false) generate_exception('Алиас с таким именем уже существует, придумайте другой');
    if($add_alias_en === false) generate_exception('This url already exists');

    self::update_cat_parent(array(
      'item_id' => $cat_id,
      'parent_id' => $parent_id
    ));


    self::update_data(array(
      'table_name' => self::$categories_table_name,
      'fields' => array(
        'name_ru' => $name_ru,
        'name_en' => $name_en,
        'description_ru' => $description_ru,
        'description_en' => $description_en,
        'keywords_ru' => $keywords_ru,
        'keywords_en' => $keywords_en,
        'translate' => $translate,
        'parent_id' => $parent_id,
        'alias_ru' => $alias_ru,
        'alias_en' => $alias_en
      ),
      'where' => array(
        'id' => $cat_id
      )
    ));

  }

  public static function delete_cat($data){

    $cat_id = isset($data['cat_id']) ? (int)$data['cat_id'] : 0;

    $group_delete = isset($data['items']) ? $data['items'] : false;

    $table_name = self::$categories_table_name;


    function delete_item($item_id,$table_name){

      $q_cat_parent = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$item_id."'");
      $r_cat_parent = mysql_query($q_cat_parent) or die(generate_exception($db_error));
      $n_cat_parent = mysql_numrows($r_cat_parent); // or die("cant get numrows query");
      if ($n_cat_parent > 0){
        $cat_parent = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".parent_id"));
        $cat_position = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".position"));
        $cat_last_items = htmlspecialchars(mysql_result($r_cat_parent, 0, $table_name.".last_items"));

        AdmCommon::update_data(array(
          'table_name' => $table_name,
          'fields' => array(
            'parent_id' => $cat_parent
          ),
          'where' => array(
            'parent_id' => $item_id
          )
        ));

        $q_delete = sprintf("DELETE FROM `".$table_name."` WHERE `".$table_name."`.`id`='".$item_id."'");
        mysql_query($q_delete) or die(generate_exception($db_error));

      }

    }

    if(is_array($group_delete)){

      for ($i = 0; $i < count($group_delete); $i++) {
        $item_id = (int)$group_delete[$i];

        delete_item($item_id,$table_name);

      }
    } else {
      delete_item($cat_id,$table_name);
    }

    self::reindex_cats();

    return true;

  }

}


?>
