<?php
if(!class_exists('User')) require_once($root_dir.'/include/classes/user.php');


class UserFavRecipes extends User{
  public $recipe_id;
  public $count_show = 20;
  public $page = 1;
  public $count_all_recipes;
  public $cats = array();

  function __construct(Array $data = array()){
    parent::__construct($data);
    $this->recipe_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
  }

  public function get_recipes(Array $data = array(),$admin = false){
    GLOBAL $l,$user_name;

    $recipes = array();
    $recipes_html = array(
      'mobile' => '',
      'desktop' => ''
    );

    $sort_user = " AND `user_fav_recipes`.`user_id` = '".$this->user_id."'";

    if($admin === true){
      if(isset($data['sort_user']) && $data['sort_user'] === false) $sort_user = "";
    }

    $parent_id = isset($data['parent_id']) ? (int)$data['parent_id'] : 0;
    $show = isset($data['show']) ? (int)$data['show'] : 0;
    $page = isset($data['page']) ? (int)$data['page'] : 1;
    $search_str = isset($data['search_str']) ? $data['search_str'] : '';
    $page = $page <= 0 ? 1 : $page;
    $this->page = $page;

    $show = $show < 20 ? 20 : $show;
    $show = $show > 100 ? 100 : $show;
    $this->count_show = $show;

    $offset = ($page - 1) * $this->count_show;
    $sql_limit = " LIMIT " . $offset . "," .  $this->count_show;

    $search_str = mysql_real_escape_string($search_str);

    $query_search = !empty($search_str) ? " AND (`posts`.`title_".LANG."` LIKE '%".$search_str."%' OR `user_fav_recipes`.`title` LIKE '%".$search_str."%')" : "";
    $query_parent_id = " AND `user_fav_recipes`.`parent_id` = '".$parent_id."' ";


    $order_by = " ORDER BY
    CASE WHEN `user_fav_recipes`.`type` = 'dir' THEN `user_fav_recipes`.`create_date` END DESC,
    CASE WHEN `user_fav_recipes`.`type` = 'recipe' THEN `posts`.`title_".LANG."` END ASC ";

    $q_recipes = ("SELECT * FROM `user_fav_recipes`
    LEFT JOIN `posts` ON (`posts`.`id` = `user_fav_recipes`.`recipe_id`)
    LEFT JOIN `users` ON (`users`.`id` = `posts`.`user_id`)
    WHERE `user_fav_recipes`.`id` <> '0' ".$sort_user.$query_parent_id.$query_search."
     ".$order_by." ".$sql_limit);
    $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    $n_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");
    if($n_recipes > 0){
      for ($i = 0; $i < $n_recipes; $i++) {
        $id = htmlspecialchars(mysql_result($r_recipes, $i, "user_fav_recipes.id"));
        $type = htmlspecialchars(mysql_result($r_recipes, $i, "user_fav_recipes.type"));
        $parent_id = htmlspecialchars(mysql_result($r_recipes, $i, "user_fav_recipes.parent_id"));
        $title = htmlspecialchars(mysql_result($r_recipes, $i, "posts.title_".LANG));
        $content = htmlspecialchars(mysql_result($r_recipes, $i, "posts.content_".LANG));
        $user_id = htmlspecialchars(mysql_result($r_recipes, $i, "users.id"));
        $user__name = htmlspecialchars(mysql_result($r_recipes, $i, "users.name"));
        $main_image = htmlspecialchars(mysql_result($r_recipes, $i, "posts.main_image"));
        $count_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_like"));
        $count_not_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_not_like"));
        $alias = htmlspecialchars(mysql_result($r_recipes, $i, "posts.alias_".LANG));
        $create_date = htmlspecialchars(mysql_result($r_recipes, $i, "user_fav_recipes.create_date"));

        $user_name = empty($user__name) ? $user_name : $user__name;

        if($type == 'dir'){
          $title = htmlspecialchars(mysql_result($r_recipes, $i, "user_fav_recipes.title"));
        }



        $data = array(
          'id' => $id,
          'type' => $type,
          'parent_id' => $parent_id,
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

      $recipes_html['mobile'] = '<tr>';
        $recipes_html['mobile'] .= '<td colspan="3" class="text_center">'.($l->no_recipes).'</td>';
      $recipes_html['mobile'] .= '</tr>';

    }

    $all_recipes_sql = str_replace($sql_limit,'',$q_recipes);
    $all_recipes_sql = str_replace('`fav_recipes`.`id`','*',$all_recipes_sql);
    $r_recipes = mysql_query($all_recipes_sql) or die(generate_exception(DB_ERROR));
    $this->count_all_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");

    $btns = $this->create_btns_switch_pages();
    $cat_path = array();
    $cat_path = $this->create_cat_path($parent_id,$cat_path);

    $count_cp = count($cat_path);

    $cp_html = '';
    if($count_cp > 0){

      $cp_html .= '<div class="ur_sort_items ask_post">';
        $cp_html .= '<ul id="ul_sort_recipes">';
          $cp_html .= '<li class="i_block v_align_middle">';
            $cp_html .= '<a class="<?php echo $acitve_all_class; ?>" id="lnk_status_all" onclick="fav_recipes.set_parent_id(0)">'.($l->all).'</a>';
          $cp_html .= '</li>';
          foreach ($cat_path as $key => $value) {
            $active = $value['id'] == $parent_id ? 'active_lnk' : '';
            $cp_html .= '<li class="i_block v_align_middle addns_r_status">';
              $cp_html .= '/ <a class="'.$active.'" onclick="fav_recipes.set_parent_id('.$value['id'].','.$parent_id.')"> '.$value['title'].'</a>';
            $cp_html .= '</li>';
          }
        $cp_html .= '</ul>';
      $cp_html .= '</div>';

    }

    $tree_cats = $this->get_tree_cats();


    // $q_recipes = ("SELECT * FROM `posts`");
    // $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    // $n_recipes = mysql_numrows($r_recipes); // or die("cant get numrows search_company");
    // if($n_recipes > 0){
    //   for ($i = 0; $i < $n_recipes; $i++) {
    //     $id = htmlspecialchars(mysql_result($r_recipes, $i, "id"));
    //
    //     $q_query = ("INSERT INTO
    //     `user_fav_recipes`
    //     (
    //     `user_id`,
    //     `recipe_id`,
    //     `parent_id`,
    //     `type`,
    //     `create_date`)
    //     values(
    //     '".$this->user_id."',
    //     '".$id."',
    //     '0',
    //     'recipe',
    //     '".$this->create_date."')");
    //     mysql_query($q_query) or die(generate_exception(DB_ERROR));
    //
    //   }
    // }

    return array(
      'recipes' => $recipes,
      'html' => $recipes_html,
      'btns' => $btns,
      'tree_cats' => $tree_cats,
      'cat_path' => $cat_path,
      'cp_html' => $cp_html
    );
  }

  public function get_tree_cats($item_id,&$tree){
    $cats = array();
    $html = '';
    $q_cats = ("SELECT * FROM `user_fav_recipes` WHERE `user_id` = '".$this->user_id."' AND `type` = 'dir' ORDER BY `create_date` DESC");
    $r_cats = mysql_query($q_cats) or die("cant execute query_path");
    $n_cats = mysql_num_rows($r_cats);
    if($n_cats > 0){
      for ($i = 0; $i < $n_cats; $i++) {
        $id = htmlspecialchars(mysql_result($r_cats, $i, "id"));
        $title = htmlspecialchars(mysql_result($r_cats, $i, "title"));
        $parent_id = htmlspecialchars(mysql_result($r_cats, $i, "parent_id"));

        $cats[$parent_id][$id] = array(
          'id' => $id,
          'title' => $title,
          'parent_id' => $parent_id
        );
      }
    }

    $this->cats = $cats;
    $html = $this->create_tree(0,0,$html);
    return $html;
  }

  public function create_tree($parent_id,$level,&$html){
    $line = '-';
    $cats = $this->cats;
    if(isset($cats[$parent_id])) {
      foreach ($cats[$parent_id] as $value) {

        $gline = '';

        for ($i = 0; $i < $level; $i++) {
          $gline .= $line;
        }
        $html .= '<option value="'.$value["id"].'">'.$gline.' '.$value["title"].'</option>';
        $level++;
        $this->create_tree($value["id"], $level,$html);
        $level--;
      }
    }
    return $html;
  }


  public function bookmark(Array $data = array(),$c_user_id = 0){
    GLOBAL $l;
    $item_id = isset($data['id']) ? (int)$data['id'] : 0;
    $type = isset($data['type']) ? $data['type'] : 'add';
    if($item_id == 0) generate_exception($l->post_not_found);
    $user_id = $c_user_id > 0 ? $c_user_id : $this->user_id;

    $q_c = ("SELECT * FROM `posts` WHERE `id` = '".$item_id."'");
    $r_c = mysql_query($q_c) or die(generate_exception(DB_ERROR));
    $n_c = mysql_num_rows($r_c);
    if($n_c == 0) generate_exception($l->post_not_found);

    if($type == 'delete'){
      if($this->exist_in_bookmark($item_id) === false) return true;

      $q_delete = ("DELETE FROM `user_fav_recipes` WHERE `recipe_id` = '".$item_id."' AND `user_id` = '".$this->user_id."'");
      mysql_query($q_delete) or die(generate_exception(DB_ERROR));

    } else{
      if($this->exist_in_bookmark($item_id) === true) return true;

      $q_query = ("INSERT INTO
      `user_fav_recipes`
      (
      `user_id`,
      `recipe_id`,
      `type`,
      `create_date`)
      values(
      '".$user_id."',
      '".$item_id."',
      'recipe',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));
    }

    return true;
  }


  public function save_dir(Array $data = array()){
    GLOBAL $l;
    $event = $data['_event'];
    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $items = isset($data['items']) ? $data['items'] : array();
    $title = $data['title'];
    $parent_id = $data['parent_id'];
    $type = $data['type'];

    $title = mysql_real_escape_string($title);

    $title = trim($title);

    if($type == 'dir' && empty($title)) generate_exception($l->name_dir_ent);

    if($event == 'update'){


      if($type == 'group'){

        for ($i = 0; $i < count($items); $i++) {
          $item_id = $items[$i];

          $prnt = $item_id == $parent_id ? 0 : $parent_id;

          $q_user_info = ("UPDATE `user_fav_recipes` SET `parent_id` = '".$prnt."' WHERE `id` = '".$item_id."'");
          mysql_query($q_user_info) or die(generate_exception(DB_ERROR));
        }

      } else {

        if($item_id == $parent_id) $parent_id = 0;

        $q_user_info = ("UPDATE `user_fav_recipes` SET
        `title` = '".$title."',
        `parent_id` = '".$parent_id."'
        WHERE `id` = '".$item_id."'");
        mysql_query($q_user_info) or die(generate_exception(DB_ERROR));
      }

    } else {

      $q_query = ("INSERT INTO
      `user_fav_recipes`
      (
      `user_id`,
      `title`,
      `parent_id`,
      `type`,
      `create_date`)
      values(
      '".$this->user_id."',
      '".$title."',
      '".$parent_id."',
      'dir',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $item_id = mysql_insert_id();
    }

    return array(
      'id' => $item_id
    );
  }


  public function exist_item($item_id){
    if($this->user_id == 0) return false;
    $q_c = ("SELECT * FROM `user_fav_recipes` WHERE `id` = '".$item_id."' AND `user_id` = '".$this->user_id."'");
    $r_c = mysql_query($q_c) or die(generate_exception(DB_ERROR));
    $n_c = mysql_num_rows($r_c);
    if($n_c == 0) return false;
    return true;
  }

  public function exist_in_bookmark($item_id){
    if($this->user_id == 0) return false;
    $q_c = ("SELECT * FROM `user_fav_recipes` WHERE `recipe_id` = '".$item_id."' AND `user_id` = '".$this->user_id."'");
    $r_c = mysql_query($q_c) or die(generate_exception(DB_ERROR));
    $n_c = mysql_num_rows($r_c);
    if($n_c == 0) return false;
    return true;
  }


  public function delete_item($data){
    GLOBAL $l;
    $item_id = $data['item_id'];
    $items = isset($data['items']) ? $data['items'] : array();
    $count_items = count($items);

    if($count_items > 0){

      for ($i = 0; $i < $count_items; $i ++) {
        $item_id = $items[$i];

        $info = $this->exist_item($item_id);
        if($info === false) continue;

        $q_user_info = ("DELETE FROM `user_fav_recipes` WHERE `id` = '".$item_id."'");
        mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

      }

    } else{

      $info = $this->exist_item($item_id);
      if($info === false) generate_exception($l->not_found);

      $q_user_info = ("DELETE FROM `user_fav_recipes` WHERE `id` = '".$item_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    }


    return true;
  }

  public function create_cat_path($item_id,&$path){
    $q_query_path = ("SELECT * FROM `user_fav_recipes` WHERE `id` = '".$item_id."' AND `type`='dir'");
    $r_query_path = mysql_query($q_query_path) or die(generate_exception(DB_ERROR));
    $n_query_path = mysql_numrows($r_query_path); // or die("cant get numrows query_path");
    if ($n_query_path > 0) {
      for ($i = 0; $i < $n_query_path; $i++) {
        $id = htmlspecialchars(mysql_result($r_query_path, $i, "id"));
        $title = htmlspecialchars(mysql_result($r_query_path, $i, "title"));
        $parent_id = htmlspecialchars(mysql_result($r_query_path, $i, "parent_id"));
        array_unshift($path, array('id' => $id,'title' => $title));
        $this->create_cat_path($parent_id,$path);
      }
    }
    return $path;
  }

  public function create_recipe_html($data){
    GLOBAL $l,$time_offset;

    $id = $data['id'];
    $title = $data['title'];
    $content = $data['content'];
    $user_name = $data['user_name'];
    $type = $data['type'];
    $parent_id = $data['parent_id'];
    $main_image = $data['main_image'];
    $create_date = $data['create_date'];
    $alias = $data['alias'];

    $date = date('d.m.Y H:i',strtotime($create_date) + $time_offset);

    if(LANG == 'en'){
      $date = date('m/d/Y g:i A',strtotime($create_date) + $time_offset);
    }

    if($type == 'dir'){
      $image_src = '/images/dir.png';
    } else{
      $image_src = empty($main_image) ? '/images/no_image.jpg' : '/images/post_images/thumbnail_480/'.$main_image;
    }


    $html = '';
    $html .= '<tr id="fr_item_'.$id.'" data-parentid="'.$parent_id.'">';
    $html .= '<td class="fr_lp">';
    $html .= '<input type="checkbox" class="chf_a chf_d" value="'.$id.'">';
    $html .= '</td>';
    $html .= '<td style="min-width: 230px;">';
    $html .= '<div>';
    if($type == 'dir'){
      $html .= '<a class="cursor_p" onclick="fav_recipes.set_parent_id('.$id.')"><div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div><span id="dir_title_'.$id.'">'.$title.'</span></div></a>';
    } else{
      $html .= '<a href="/'.$alias.'" target="_blank"><div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div>'.$title.'</div></a>';
    }
    $html .= '<div class="hidden_actions">';
    $html .= '<a onclick="fav_recipes.show_edit_item(this,'.$id.')" data-type="'.$type.'" class="cursor_p">';
    $html .= '<div class="ha_item cursor_p float_l">'.($l->edit).' | </div>';
    $html .= '</a>';
    $html .= '<div class="ha_item cursor_p float_l" onclick="fav_recipes.delete_item('.$id.');" style="color: #a00;">'.($l->delete).' </div>';
    $html .= '</div>';
    $html .= '</td>';
    $html .= '<td>'.$user_name.'</td>';
    $html .= '<td class="text_right">'.$date.'</td>';
    $html .= '</tr>';


    $mobile_html = '';


    $mobile_html .= '<div class="m_user_recipe">';
    $mobile_html .= '<table class="full_w">';
    $mobile_html .= '<tbody>';
    $mobile_html .= '<tr>';
    $mobile_html .= '<td style="width: 30px;">';
    $mobile_html .= '<input type="checkbox" class="chf_a chf_m" value="'.$id.'">';
    $mobile_html .= '</td>';
    $mobile_html .= '<td>';
    if($type == 'dir'){
      $mobile_html .= '<a class="cursor_p" onclick="fav_recipes.set_parent_id('.$id.')"><div class="float_l ur_mini_img" style="width: 25px; height: 25px; margin-right: 10px;"><img class="cover_img" src="'.$image_src.'"></div><span>'.$title.'</span></div></a>';
    } else{
      $mobile_html .= '<a href="/'.$alias.'" target="_blank"><div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div>'.$title.'</div></a>';
    }
    $mobile_html .= '</td>';
    $mobile_html .= '<td class="text_right" style="width: 55px;">';
    $mobile_html .= '<a onclick="fav_recipes.show_edit_item(this,'.$id.')" data-type="'.$type.'">';
    $mobile_html .= '<i class="fa fa-cog g_color cursor_p" title="'.($l->edit).'"></i>';
    $mobile_html .= '</a>&nbsp;&nbsp;';
    $mobile_html .= '<i class="fa fa-close r_color cursor_p" title="'.($l->delete).'" onclick="fav_recipes.delete_item('.$id.');"></i>';
    $mobile_html .= '</td>';
    $mobile_html .= '</tr>';
    $mobile_html .= '</tbody>';
    $mobile_html .= '</table>';
    $mobile_html .= '</div>';






    /////////////////////



    $mobile_html = '';


    $mobile_html .= '<tr>';
    $mobile_html .= '<td class="fr_lp">';
    $mobile_html .= '<input type="checkbox" class="chf_a chf_m" value="'.$id.'">';
    $mobile_html .= '</td>';
    $mobile_html .= '<td>';
    if($type == 'dir'){
      $mobile_html .= '<a class="cursor_p" onclick="fav_recipes.set_parent_id('.$id.')"><div class="float_l ur_mini_img" style="width: 25px; height: 25px; margin-right: 10px;"><img class="cover_img" src="'.$image_src.'"></div><span>'.$title.'</span></div></a>';
    } else{
      $mobile_html .= '<a href="/'.$alias.'" target="_blank"><div class="float_l ur_mini_img"><img class="cover_img" src="'.$image_src.'"></div>'.$title.'</div></a>';
    }
    $mobile_html .= '</td>';
    $mobile_html .= '<td class="text_right" style="width: 55px;">';
    $mobile_html .= '<a onclick="fav_recipes.show_edit_item(this,'.$id.')" data-type="'.$type.'">';
    $mobile_html .= '<i class="fa fa-cog g_color cursor_p" title="'.($l->edit).'"></i>';
    $mobile_html .= '</a>&nbsp;&nbsp;';
    $mobile_html .= '<i class="fa fa-close r_color cursor_p" title="'.($l->delete).'" onclick="fav_recipes.delete_item('.$id.');"></i>';
    $mobile_html .= '</td>';
    $mobile_html .= '</tr>';



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
        $btns .= '<a onclick="fav_recipes.switch_page('.$i.');"><div class="cursor_p float_l text_center btn_sw_page">'.$i.'</div></a>';
      }
    }
    $btns .= '</span>';
    // if ($this->page!=$page_count) $btns .= '<a href="?q=posts&page='.($this->page+1).'" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" id="btn_sw_next">→</a>';

    return $btns;
  }


}


?>
