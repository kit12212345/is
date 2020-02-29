<?php
if(!class_exists('User')) require_once($root_dir.'/include/classes/user.php');


class UserRecipes extends User{
  public $recipe_id;
  public $count_show = 20;
  public $page = 1;
  public $count_all_recipes;
  public $statuses = array('in_moderation','published','rejected');

  function __construct(Array $data = array()){
    parent::__construct($data);
    $this->recipe_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
  }

  public function add_draft(Array $data = array()){
    $hash = $data['h'];
    $lang = LANG;

    $q_recipes = ("SELECT `id` FROM `user_recipes`
    WHERE `deleted` = '0' AND `hash` = '".$hash."' AND `user_id` = '".$this->user_id."'");
    $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    $n_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");
    if($n_recipes == 0){

      $q_query = ("INSERT INTO
      `user_recipes`
      (
      `user_id`,
      `status`,
      `hash`,
      `lang`,
      `create_date`)
      values(
      '".$this->user_id."',
      'draft',
      '".$hash."',
      '".$lang."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $recipe_id = mysql_insert_id();

    } else {
      $recipe_id = htmlspecialchars(mysql_result($r_recipes, 0, "id"));
    }


    return $recipe_id;
  }


  public function get_recipes(Array $data = array(),$admin = false){
    GLOBAL $l;

    $recipes = array();
    $recipes_html = array(
      'mobile' => '',
      'desktop' => ''
    );

    $sort_user = " AND `user_recipes`.`user_id` = '".$this->user_id."'";

    if($admin === true){
      if(isset($data['sort_user']) && $data['sort_user'] === false) $sort_user = "";
    }


    $status = isset($data['status']) ? $data['status'] : 'all';
    $page = isset($data['page']) ? (int)$data['page'] : 1;
    $search_str = isset($data['search_str']) ? $data['search_str'] : '';
    $page = $page <= 0 ? 1 : $page;
    $this->page = $page;

    $offset = ($page - 1) * $this->count_show;
    $sql_limit = " LIMIT " . $offset . "," .  $this->count_show;

    $status = mysql_real_escape_string($status);
    $search_str = mysql_real_escape_string($search_str);

    $sort_status = in_array($status,$this->statuses) ? " AND `user_recipes`.`status` = '".$status."' " : "";

    $query_search = !empty($search_str) ? " AND `user_recipes`.`title` LIKE '%".$search_str."%' " : "";

    $q_recipes = ("SELECT * FROM `user_recipes`
    INNER JOIN `users` ON (`users`.`id` = `user_recipes`.`user_id`)
    LEFT JOIN `posts` ON (`posts`.`id` = `user_recipes`.`parent_id`)
    WHERE `user_recipes`.`deleted` = '0' ".$sort_status.$sort_user.$query_search."
     ORDER BY `user_recipes`.`create_date` DESC ".$sql_limit);
    $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    $n_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");
    if($n_recipes > 0){
      for ($i = 0; $i < $n_recipes; $i++) {
        $id = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.id"));
        $title = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.title"));
        $content = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.content"));
        $user_id = htmlspecialchars(mysql_result($r_recipes, $i, "users.id"));
        $user_name = htmlspecialchars(mysql_result($r_recipes, $i, "users.name"));
        $status = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.status"));
        $rejection_reason = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.rejection_reason"));
        $main_image = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.main_image"));
        $count_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_like"));
        $count_not_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_not_like"));
        $lang = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.lang"));
        $alias = htmlspecialchars(mysql_result($r_recipes, $i, "posts.alias_".LANG));
        $create_date = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.create_date"));

        $data = array(
          'id' => $id,
          'title' => $title,
          'user_id' => $user_id,
          'lang' => $lang,
          'user_name' => $user_name,
          'status' => $status,
          'rejection_reason' => $rejection_reason,
          'alias' => $alias,
          'content' => $content,
          'count_like' => $count_like,
          'count_not_like' => $count_not_like,
          'main_image' => $main_image,
          'create_date' => $create_date
        );

        $html = $this->create_recipe_html($data);

        $recipes_html['desktop'] .= $html['desktop'];
        $recipes_html['mobile'] .= $html['mobile'];

        array_push($recipes,$data);

      }
    } else{
      $recipes_html['desktop'] = '<tr>';
        $recipes_html['desktop'] .= '<td colspan="5" class="text_center">'.($l->no_recipes).'</td>';
      $recipes_html['desktop'] .= '</tr>';


      $recipes_html['mobile'] = '<div class="text_center no_recipes">'.($l->no_recipes).'</div>';
    }

    $all_recipes_sql = str_replace($sql_limit,'',$q_recipes);
    $all_recipes_sql = str_replace('`user_recipes`.`id`','*',$all_recipes_sql);
    $r_recipes = mysql_query($all_recipes_sql) or die(generate_exception(DB_ERROR));
    $this->count_all_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");

    $btns = $this->create_btns_switch_pages();


    if($sort_status){
      $all_recipes_sql = str_replace($sort_status,'',$all_recipes_sql);
    }
    $r_recipes = mysql_query($all_recipes_sql) or die(generate_exception(DB_ERROR));
    $count_all = mysql_numrows($r_recipes); // or die("cant get numrows search_company");

    $statuses = $this->get_recipes_statuses(array(
      'search_str' => $search_str
    ));

    return array(
      'count_all' => $count_all,
      'recipes' => $recipes,
      'html' => $recipes_html,
      'btns' => $btns,
      'statuses' => $statuses
    );
  }

  public function get_recipes_statuses(Array $data = array(),$admin = false){
    $statuses = array();

    $sort_user = " AND `user_recipes`.`user_id` = '".$this->user_id."'";

    if($admin === true){
      if(isset($data['sort_user']) && $data['sort_user'] === false) $sort_user = "";
    }

    $search_str = isset($data['search_str']) ? $data['search_str'] : '';
    $search_str = mysql_real_escape_string($search_str);
    $query_search = !empty($search_str) ? " AND `user_recipes`.`title` LIKE '%".$search_str."%' " : "";

    $q_recipes = ("SELECT COUNT(`id`) AS count,`status` FROM `user_recipes` WHERE `deleted` = '0' ".$sort_user.$query_search."
    GROUP BY `status` ORDER BY `status` ASC ");
    $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    $n_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");
    if($n_recipes > 0){
      for ($i = 0; $i < $n_recipes; $i++) {
        $count = htmlspecialchars(mysql_result($r_recipes, $i, "count"));
        $status = htmlspecialchars(mysql_result($r_recipes, $i, "status"));
        $statuses[$status] = $count;
      }
    }


    return $statuses;

  }

  public function get_ingredients($item_id){
    $ingredients = array();

    $q_ings = ("SELECT * FROM `user_recipes_ingredients` WHERE `item_id` = '".$item_id."'");
    $r_ings = mysql_query($q_ings) or die("cant execute query_path");
    $n_ings = mysql_num_rows($r_ings);
    if($n_ings > 0){
      for ($i = 0; $i < $n_ings; $i++) {
        $name = htmlspecialchars(mysql_result($r_ings, $i, "content"));
        $lang = htmlspecialchars(mysql_result($r_ings, $i, "lang"));

        array_push($ingredients,$name);

      }
    }
    return $ingredients;
  }

  public function get_cats($item_id){
    $cats = array();

    $q_ings = ("SELECT * FROM `user_recipes_categories_items`
    INNER JOIN `posts_categories` ON (`posts_categories`.`id` = `user_recipes_categories_items`.`cat_id`)
    WHERE `user_recipes_categories_items`.`item_id` = '".$item_id."'");
    $r_ings = mysql_query($q_ings) or die("cant execute query_path");
    $n_ings = mysql_num_rows($r_ings);
    if($n_ings > 0){
      for ($i = 0; $i < $n_ings; $i++) {
        $id = htmlspecialchars(mysql_result($r_ings, $i, "posts_categories.id"));
        $name = htmlspecialchars(mysql_result($r_ings, $i, "posts_categories.name_".LANG));
        $alias = htmlspecialchars(mysql_result($r_ings, $i, "posts_categories.alias_".LANG));


        array_push($cats,array(
          'id' => $id,
          'name' => $name,
          'alias' => $alias
        ));

      }
    }
    return $cats;
  }

  public function get_recipe_info($data = array(),$admin){

    $sort_user = " AND `user_recipes`.`user_id` = '".$this->user_id."'";

    if($admin === true){
      if(isset($data['sort_user']) && $data['sort_user'] === false) $sort_user = "";
    }

    $info = array();

    $q_info = ("SELECT * FROM `user_recipes` WHERE `id` = '".$this->recipe_id."'
    ".$sort_user." AND `deleted` = '0'");
    $r_info = mysql_query($q_info) or die(generate_exception(DB_ERROR));
    $n_info = mysql_numrows($r_info); // or die("cant get numrows search_company");
    if($n_info > 0){
      $id = htmlspecialchars(mysql_result($r_info, 0, "id"));
      $title = htmlspecialchars(mysql_result($r_info, 0, "title"));
      $content = htmlspecialchars_decode(mysql_result($r_info, 0, "content"));
      $lang = htmlspecialchars_decode(mysql_result($r_info, 0, "lang"));
      $user_id = htmlspecialchars_decode(mysql_result($r_info, 0, "user_id"));
      $main_image = htmlspecialchars(mysql_result($r_info, 0, "main_image"));
      $status = htmlspecialchars(mysql_result($r_info, 0, "status"));
      $parent_id = htmlspecialchars(mysql_result($r_info, 0, "parent_id"));
      $create_date = htmlspecialchars(mysql_result($r_info, 0, "create_date"));

      $ingredients = $this->get_ingredients($this->recipe_id);
      $cats = $this->get_cats($this->recipe_id);

      $info = array(
        'id' => $id,
        'title' => $title,
        'content' => $content,
        'main_image' => $main_image,
        'user_id' => $user_id,
        'parent_id' => $parent_id,
        'lang' => $lang,
        'status' => $status,
        'ingredients' => $ingredients,
        'cats' => $cats,
        'create_date' => $create_date
      );

    } else return false;

    return $info;
  }

  public function save_recipe_changes(Array $data = array()){
    $recipe_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $title = $data['title'];
    $content = $data['content'];
    $ingredients = $data['ingredients'];
    $cats = $data['cats'];
    $lang = LANG;

    $title = mysql_real_escape_string($title);
    $content = mysql_real_escape_string($content);

    $title = trim($title);
    $content = trim($content);

    $q_user_info = ("UPDATE `user_recipes` SET
    `title` = '".$title."',
    `content` = '".$content."'
    WHERE `id` = '".$recipe_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    $this->add_recipe_cats($recipe_id,$cats);
    $this->add_ingredients($recipe_id,$ingredients);

    return array(
      'id' => $recipe_id
    );

  }

  public function save_recipe(Array $data = array()){
    $event = $data['_event'];
    $recipe_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $title = $data['title'];
    $content = $data['content'];
    $ingredients = $data['ingredients'];
    $image_hash = $data['hash'];
    $cats = $data['cats'];
    $lang = LANG;


    $title = mysql_real_escape_string($title);
    $content = mysql_real_escape_string($content);
    $image_hash = mysql_real_escape_string($image_hash);

    $title = trim($title);
    $content = trim($content);

    if($event == 'update'){

      $q_user_info = ("UPDATE `user_recipes` SET
      `title` = '".$title."',
      `content` = '".$content."',
      `status` = 'in_moderation'
      WHERE `id` = '".$recipe_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));



    } else {

      $q_query = ("INSERT INTO
      `user_recipes`
      (
      `user_id`,
      `title`,
      `content`,
      `lang`,
      `create_date`)
      values(
      '".$this->user_id."',
      '".$title."',
      '".$content."',
      '".$lang."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $recipe_id = mysql_insert_id();

      $q_temp_images = ("SELECT * FROM `temp_images` WHERE `temp_images`.`md5_hash` = '".$image_hash."'");
      $r_temp_images = mysql_query($q_temp_images) or die(generate_exception($db_error));
      $n_temp_images = mysql_numrows($r_temp_images); // or die("cant get numrows query");
      if ($n_temp_images > 0){
        for ($i = 0; $i < $n_temp_images; $i++) {
          $image_id = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.id"));
          $image_name = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.image"));
          $image_position = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.position"));

          if($image_position == 1){

            $q_user_info = ("UPDATE `user_recipes` SET `main_image` = '".$image_name."' WHERE `id` = '".$recipe_id."'");
            mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

          }

          $q_query = ("INSERT INTO
          `user_recipes_images`
          (
          `user_id`,
          `item_id`,
          `image`,
          `position`,
          `create_date`)
          values(
          '".$this->user_id."',
          '".$recipe_id."',
          '".$image_name."',
          '".$image_position."',
          '".time()."')");
          mysql_query($q_query) or die(generate_exception(DB_ERROR));


          $q_delete_temp_image = ("DELETE FROM `temp_images`
          WHERE `temp_images`.`id`='".$image_id."'");
          mysql_query($q_delete_temp_image) or die(generate_exception($db_error));


        }
      }

    }

    $this->add_recipe_cats($recipe_id,$cats);
    $this->add_ingredients($recipe_id,$ingredients);

    return array(
      'id' => $recipe_id
    );
  }

  public function delete_recipe($data){
    GLOBAL $l;
    $item_id = $data['item_id'];
    $this->recipe_id = $item_id;
    $info = $this->get_recipe_info();
    if($info === false) generate_exception($l->recipe_not_found);

    $q_user_info = ("UPDATE `user_recipes` SET `deleted` = '1' WHERE `id` = '".$this->recipe_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    $this->add_ingredients($this->recipe_id,array());
    $this->add_recipe_cats($this->recipe_id,array());

    return true;
  }

  public function add_ingredients($item_id,$ingredients){
    $lang = LANG;

    $q_delete = ("DELETE FROM `user_recipes_ingredients`
    WHERE `item_id`='".$item_id."'");
    mysql_query($q_delete) or die(generate_exception($db_error));

    for ($i=0; $i < count($ingredients); $i++) {
      $name = $ingredients[$i];

      if(empty($name)) continue;

      $name = mysql_real_escape_string($name);

      $q_query = ("INSERT INTO
      `user_recipes_ingredients`
      (
      `item_id`,
      `content`,
      `lang`,
      `create_date`)
      values(
      '".$item_id."',
      '".$name."',
      '".$lang."',
      '".time()."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

    }

  }

  public static function add_recipe_cats($item_id,$cats){

    $q_delete = ("DELETE FROM `user_recipes_categories_items` WHERE `item_id`='".$item_id."'");
    mysql_query($q_delete) or die(generate_exception($db_error));

    for ($i = 0; $i < count($cats); $i++) {
      $cat_id = (int)$cats[$i];

      $q_check_exist_cat = sprintf("SELECT * FROM `posts_categories` WHERE `id`='".$cat_id."'");
      $r_check_exist_cat = mysql_query($q_check_exist_cat) or die("cant execute query_path");
      $n_check_exist_cat = mysql_num_rows($r_check_exist_cat);
      if($n_check_exist_cat == 0) continue;

      $q_check_exist = ("SELECT * FROM `user_recipes_categories_items` WHERE `cat_id`='".$cat_id."'
      AND `item_id`='".$item_id."'");
      $r_check_exist = mysql_query($q_check_exist) or die("cant execute query_path");
      $n_check_exist = mysql_num_rows($r_check_exist); // or die("cant get numrows query_path");
      if ($n_check_exist == 0){

        $q_query = ("INSERT INTO
        `user_recipes_categories_items`
        (
        `item_id`,
        `cat_id`,
        `create_date`)
        values(
        '".$item_id."',
        '".$cat_id."',
        '".time()."')");
        mysql_query($q_query) or die(generate_exception(DB_ERROR));


      }


    }


  }


  public function create_recipe_html($data){
    GLOBAL $l,$time_offset;

    $id = $data['id'];
    $title = $data['title'];
    $content = $data['content'];
    $status = $data['status'];
    $rejection_reason = $data['rejection_reason'];
    $main_image = $data['main_image'];
    $published = $data['published'];
    $create_date = $data['create_date'];
    $count_like = (int)$data['count_like'];
    $count_not_like = (int)$data['count_not_like'];
    $alias = $data['alias'];

    $cats = $this->get_cats($id);
    $count_cats = count($cats);

    $html_status = '<span>'.($l->in_moderation).'</span>';

    if($status == 'published'){
      $html_status = '<span class="g_color">'.($l->published).'</span>';
    } else if($status == 'rejected'){
      $html_status = '<span class="r_color">'.($l->rejected).'</span>';
      $html_status .= '<div>'.($l->reason_for_rejection).': '.$rejection_reason.'</div>';
    } else if($status == 'draft'){
      $html_status = '<span>'.($l->draft).'</span>';
    }

    $date = date('d.m.Y H:i',strtotime($create_date) + $time_offset);

    if(LANG == 'en'){
      $date = date('m/d/Y g:i A',strtotime($create_date) + $time_offset);
    }

    $image_src = empty($main_image) ? '/images/no_image.jpg' : '/images/post_images/thumbnail_480/'.$main_image;

    $html = '';
    $html .= '<tr>';
    $html .= '<td style="min-width: 230px;">';
    $html .= '<div>';
    $html .= '<div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div>'.$title.'</div>';
    $html .= '<div class="hidden_actions">';
    $html .= '<a href="/user?p=my_recipes&action=edit&item_id='.$id.'">';
    $html .= '<div class="ha_item cursor_p float_l">'.($l->edit).' | </div>';
    $html .= '</a>';
    $html .= '<div class="ha_item cursor_p float_l" onclick="user_recipes.delete_recipe('.$id.');" style="color: #a00;">'.($l->delete).' </div>';
    if($status == 'published'){
      $html .= '<a href="/'.$alias.'" target="_blank">';
      $html .= '<div class="ha_item cursor_p float_l">| '.($l->go).'</div>';
      $html .= '</a>';
      $html .= '<div class="clear"></div>';
    }
    $html .= '</div>';
    $html .= '</td>';
    $html .= '<td class="text_center"><span class="g_color">'.$count_like.'</span> / <span class="r_color">'.$count_not_like.'</span></td>';
    $html .= '<td class="text_left" style="max-width: 120px;word-break: break-word;">';
    if($count_cats > 0){
      for ($i = 0; $i < $count_cats; $i++) {
        $cat_id = $cats[$i]['id'];
        $cat_name = $cats[$i]['name'];
        $cat_alias = $cats[$i]['alias'];
        $cat_link = !empty($cat_alias) ? '/'.$cat_alias : '/posts.php?cat='.$cat_id;

        $z = $i == $count_cats - 1 ? '' : ',&nbsp;';

        $html .= '<a href="'.$cat_link.'" target="_blank">'.$cat_name.'</a>'.$z;
      }
    } else{
      $html .= '----';
    }
    $html .= '</td>';
    $html .= '<td class="text_left">'.$html_status.'</td>';
    $html .= '<td class="text_left">'.$date.'</td>';
    $html .= '</tr>';

    $mobile_html = '';

    $mobile_html .= '<div class="m_user_recipe">';
    $mobile_html .= '<table class="full_w">';
    $mobile_html .= '<tbody>';
    $mobile_html .= '<tr>';
    $mobile_html .= '<td>';
    $mobile_html .= '<div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div>'.$title.'</div>';
    $mobile_html .= '</td>';
    $mobile_html .= '<td class="text_right" style="width: 55px;">';
    if($status == 'published'){
      $mobile_html .= '<a href="/'.$alias.'" target="_blank">';
      $mobile_html .= '<i class="fa fa-share" aria-hidden="true"></i>';
      $mobile_html .= '</a>&nbsp;&nbsp;';
    }
    $mobile_html .= '<a href="/user?p=my_recipes&action=edit&item_id='.$id.'">';
    $mobile_html .= '<i class="fa fa-cog g_color cursor_p" title="'.($l->edit).'"></i>';
    $mobile_html .= '</a>&nbsp;&nbsp;';
    $mobile_html .= '<i class="fa fa-close r_color cursor_p" title="'.($l->delete).'" onclick="user_recipes.delete_recipe('.$id.');"></i>';
    $mobile_html .= '</td>';
    $mobile_html .= '</tr>';
    $mobile_html .= '</tbody>';
    $mobile_html .= '</table>';
      $mobile_html .= '<div class="m_info_ur">';
        $mobile_html .= '<table class="full_w">';
          $mobile_html .= '<tbody>';
            $mobile_html .= '<tr>';
              $mobile_html .= '<td>'.($l->categories).': </td>';
              $mobile_html .= '<td class="text_right" style="word-break: break-word;">';
              if($count_cats > 0){
                for ($i = 0; $i < $count_cats; $i++) {
                  $cat_id = $cats[$i]['id'];
                  $cat_name = $cats[$i]['name'];
                  $cat_alias = $cats[$i]['alias'];
                  $cat_link = !empty($cat_alias) ? '/'.$cat_alias : '/posts.php?cat='.$cat_id;

                  $z = $i == $count_cats - 1 ? '' : ',&nbsp;';

                  $mobile_html .= '<a href="'.$cat_link.'" target="_blank">'.$cat_name.'</a>'.$z;
                }
              } else{
                $mobile_html .= '----';
              }
              $mobile_html .= '</td>';
            $mobile_html .= '</tr>';
            $mobile_html .= '<tr>';
              $mobile_html .= '<td>'.($l->status).': </td>';
              $mobile_html .= '<td class="text_right">'.$html_status.'</td>';
            $mobile_html .= '</tr>';
            $mobile_html .= '<tr>';
              $mobile_html .= '<td>'.($l->create_date).': </td>';
              $mobile_html .= '<td class="text_right">'.$date.'</td>';
            $mobile_html .= '</tr>';

          $mobile_html .= '</tbody>';
        $mobile_html .= '</table>';
      $mobile_html .= '</div>';
    $mobile_html .= '</div>';



    return array(
      'desktop' => $html,
      'mobile' => $mobile_html
    );
  }

  public function create_btns_switch_pages(){
    $btns = '';
    $page_count = 0;
    if (0 === $this->count_all_recipes) {
    } else {
      $page_count = (int)ceil($this->count_all_recipes / $this->count_show);
      if($this->page > $page_count) {
        $this->page = 1;
      }
    }


    $max_pages_nav = 10;
    $center_pos=ceil($max_pages_nav/2);
    $center_offset=round($max_pages_nav/2);

    if($this->page > $center_pos) $start_page_count= $this->page - 2;
    else  $start_page_count=1;
    $end_page_count=$start_page_count+($max_pages_nav-1);
    if($end_page_count>$page_count){
      $end_page_count=$page_count;
      $start_page_count=$page_count-($max_pages_nav-1);
    }

    if ($start_page_count<1) $start_page_count=1;
    $btns = '';
    // if ($this->page != 1) $btns .= '<a href="?q=posts&page='.($this->page-1).'" class="paginate_button previous" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_previous">←</a>';
    $btns .= '<span id="nums_pages_pr">';
    for ($i = $start_page_count; $i <= $end_page_count; $i++) {
      if($page_count <= 1) continue;
      if ($i === $this->page) {
        $btns .= '<a><div class="cursor_p float_l text_center btn_sw_page current_page">'.$i.'</div></a>';
      } else {
        $btns .= '<a onclick="user_recipes.switch_page('.$i.');"><div class="cursor_p float_l text_center btn_sw_page">'.$i.'</div></a>';
      }
    }
    $btns .= '</span>';
    // if ($this->page!=$page_count) $btns .= '<a href="?q=posts&page='.($this->page+1).'" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" id="btn_sw_next">→</a>';

    return $btns;
  }


}


?>
