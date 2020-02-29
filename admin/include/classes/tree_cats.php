<?php

class Create_cats_tree extends AdmCommon{

  private static $current_parent_id;

  public static function create($info){

    self::$current_parent_id = isset($info['parent_id']) ? $info['parent_id'] : 0;
    $in_table = $info['in_table'];
    $selected_cat = (int)$info['selected_cat'];
    $hide_childrens = isset($info['hide_childrens']) ? $info['hide_childrens'] : false;

    $level = -1;
    $cats = self::get_cats();

    return is_bool($in_table) && $in_table === true
    ? self::create_tree_table(array(
      'cats' => $cats,
      'parent_id' => 0,
      'level' => $level,
      'hide_childrens' => $hide_childrens
    ))
    : self::create_tree(array(
      'cats' => $cats,
      'parent_id' => 0,
      'selected_cat' => $selected_cat,
      'level' => $level,
      'hide_childrens' => $hide_childrens
    ));
  }

  public static function create_tree_post_cats($data){
    $cats = self::get_cats();

    $checked_cats = $data['checked_cats'];

    return self::_create_tree_post_cats(array(
      'cats' => $cats,
      'parent_id' => 0,
      'level' => $level,
      'checked_cats' => $checked_cats,
      'hide_childrens' => $hide_childrens
    ));

  }

  public static function _create_tree_post_cats($data){
    $cats = $data['cats'];
    $parent_id = $data['parent_id'];
    $level = $data['level'];
    $hide_childrens = $data['hide_childrens'];
    $checked_cats = $data['checked_cats'];

    $level++;

    $tree = '';

    $str_indent = '';
    for ($i=0; $i < $level; $i++) {
      $str_indent .= '&nbsp;&nbsp;&nbsp;';
    }

    if(is_array($cats) && isset($cats[$parent_id])){

        $tree .= '<ul id="cats_list_'.$parent_id.'" class="cats_list">';

        foreach($cats[$parent_id] as $cat){

          if(is_array($hide_childrens) && in_array($cat['parent_id'],$hide_childrens)) continue;

          $selected = (self::$current_parent_id > 0 && self::$current_parent_id == $cat['id']) ? 'selected' : '';

          $checked = in_array($cat['id'],$checked_cats) ? 'checked="checked"' : '';

          $tree .= '<li id="category_'.$cat['id'].'">';
            $tree .= '<label class="selectit">';
              $tree .= '<input value="'.$cat['id'].'" type="checkbox" class="select_tree_cat" '.$checked.' id="in_category_'.$cat['id'].'">';
              $tree .= '<div class="relative bd_check_ci"><div class="absolute all_null checked_ci"></div></div>';
              $tree .= '<span class="v_align_middle">'.$cat['name'].'</span>';
            $tree .= '</label>';
            $tree .= self::_create_tree_post_cats(array(
              'cats' => $cats,
              'parent_id' => $cat['id'],
              'level' => $level,
              'checked_cats' => $checked_cats,
              'hide_childrens' => $hide_childrens
            ));
          $tree .= '</li>';

        }

        $tree .= '</ul>';

    } else return null;

    return $tree;
  }

  public static function get_cats(){
    $table_name = self::$categories_table_name;

    $arr_tree_cats = array();

    $q_query_path = sprintf("SELECT * FROM `".$table_name."` GROUP BY `".$table_name."`.`parent_id`");
    $r_query_path = mysql_query($q_query_path) or die("cant execute query_path");
    $n_query_path = mysql_num_rows($r_query_path); // or die("cant get numrows query_path");
    if ($n_query_path > 0) {
      for ($i = 0; $i < $n_query_path; $i++){
        $query_path_id = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".id"));
        $query_path_name = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".name_ru"));
        $query_path_parent_id = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".parent_id"));

        $arr_tree_cats[$query_path_parent_id] = array();

      }
    }

    $count_parents = count($arr_tree_cats);
    $arr_parents_keys = array_keys($arr_tree_cats);

    for ($c = 0; $c < $count_parents; $c++) {

      $parent_id = $arr_parents_keys[$c];

      $q_query_path = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`parent_id` = '".$parent_id."' ORDER BY `".$table_name."`.`position` ASC");
      $r_query_path = mysql_query($q_query_path) or die("cant execute query_path");
      $n_query_path = mysql_num_rows($r_query_path); // or die("cant get numrows query_path");
      if ($n_query_path > 0) {
        for ($i = 0; $i < $n_query_path; $i++){
          $query_path_id = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".id"));
          $query_path_name = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".name_ru"));
          $query_path_description = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".description_ru"));
          $query_path_alias = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".alias_ru"));
          $query_path_parent_id = htmlspecialchars(mysql_result($r_query_path, $i, $table_name.".parent_id"));




          $arr_tree_cats[$parent_id][$query_path_id] = array(
            'id' => $query_path_id,
            'name' => $query_path_name,
            'description' => $query_path_description,
            'alias' => $query_path_alias,
            'parent_id' => $query_path_parent_id
          );

        }
      }
    }


    return $arr_tree_cats;
  }


  public static function create_tree($data){

    $cats = $data['cats'];
    $parent_id = $data['parent_id'];
    $selected_cat = $data['selected_cat'];
    $level = $data['level'];
    $hide_childrens = $data['hide_childrens'];

    $level++;

    $tree = '';

    $str_indent = '';
    for ($i=0; $i < $level; $i++) {
      $str_indent .= '&nbsp;&nbsp;&nbsp;';
    }

    if(is_array($cats) && isset($cats[$parent_id])){

        foreach($cats[$parent_id] as $cat){

          if(is_array($hide_childrens) && in_array($cat['parent_id'],$hide_childrens)) continue;

          $selected = (self::$current_parent_id > 0 && self::$current_parent_id == $cat['id']) || $selected_cat == $cat['id']
          ? 'selected' : '';

          $tree .= '<option '.$selected.' value="'.$cat['id'].'" data-level="'.$level.'">'.$str_indent.$cat['name'];
          $tree .= self::create_tree(array(
            'cats' => $cats,
            'parent_id' => $cat['id'],
            'level' => $level,
            'selected_cat' => $selected_cat,
            'hide_childrens' => $hide_childrens
          ));
          $tree .= '</option>';
        }

    } else return null;

    return $tree;
  }

  public static function create_tree_table($data){
    $cats = $data['cats'];
    $parent_id = $data['parent_id'];
    $level = $data['level'];

    $level++;

    $tree = '';

    $str_indent = '';
    for ($i=0; $i < $level; $i++) {
      $str_indent .= '— ';
    }

    if(is_array($cats) && isset($cats[$parent_id])){

        foreach($cats[$parent_id] as $cat){
          $tree .= '<tr id="cat_item_'.$cat['id'].'">';
            $tree .= '<td>';
              $tree .= '<input class="checked_cat_item" type="checkbox" value="'.$cat['id'].'">';
            $tree .= '</td>';
            $tree .= '<td style="min-width: 224px;">';
              $tree .= $str_indent;
              $tree .= '<span id="t_cat_name_'.$cat['id'].'">'.$cat['name'].'</span>';
              $tree .= '<div class="hidden_actions_cat">';
              $tree .= '<a href="?q=edit_cat&edit_item='.$cat['id'].'">';
              $tree .= '<div class="ha_cat_item">Изменить | </div>';
              $tree .= '</a>';
              $tree .= '<div class="ha_cat_item" onclick="categories.show_edit('.$cat['id'].');">Свойства | </div>';
              $tree .= '<div class="ha_cat_item" onclick="categories._delete('.$cat['id'].');" style="color: #a00;">Удалить | </div>';
              $tree .= '<div class="ha_cat_item">Перейти</div>';
              $tree .= '<div class="clear"></div>';
              $tree .= '</div>';
              $tree .= '</div>';
            $tree .= '</td>';
            $tree .= '<td>';
              $tree .= $cat['description'];
            $tree .= '</td>';
            $tree .= '<td>';
            $tree .= '<span id="t_cat_alias_'.$cat['id'].'">'.$cat['alias'].'</span>';
            $tree .= '</td>';
            $tree .= '<td>';
              $tree .= '0';
            $tree .= '</td>';
          $tree .= '</tr>';

          $tree .= '<tr id="c_hide_edit_'.$cat['id'].'" class="et_cat_cont">';
          $tree .= '<td colspan="5">';
          $tree .= '<div><h5>Свойства</h5></div>';

          $tree .= '<fieldset class="content-group">';


            $tree .= '<div class="form-group">';
              $tree .= '<label class="control-label col-lg-2">Навзвание:</label>';
              $tree .= '<div class="col-lg-10">';
                $tree .= '<input type="text" id="cat_name_'.$cat['id'].'" value="'.$cat['name'].'" placeholder="Название" class="form-control">';
              $tree .= '</div>';
              $tree .= '<div class="clear"></div>';
            $tree .= '</div>';

            $tree .= '<div class="form-group">';
              $tree .= '<label class="control-label col-lg-2 ">Алиас:</label>';
              $tree .= '<div class="col-lg-10">';
                $tree .= '<input type="text" id="cat_alias_'.$cat['id'].'" value="'.$cat['alias'].'" placeholder="Алиас" class="form-control">';
              $tree .= '</div>';
              $tree .= '<div class="clear"></div>';
            $tree .= '</div>';

            $tree .= '<div class="text_right">';
            $tree .= '<div class="float_l btn btn-xs btn-default" onclick="categories.show_edit('.$cat['id'].');">Отмена</div>';
            $tree .= '<div class="btn btn-xs btn-primary" onclick="categories.small_update('.$cat['id'].');">Обновить рубрику</div>';
            $tree .= '<div class="clear"></div>';
            $tree .= '</div>';


          $tree .= '</fieldset>';

          $tree .= '</td>';
          $tree .= '</tr>';



          $tree .= self::create_tree_table(array(
            'cats' => $cats,
            'parent_id' => $cat['id'],
            'level' => $level
          ));
        }

    } else return null;

    return $tree;
  }


}


?>
