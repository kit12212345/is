<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class Posts extends AdmCommon{

  function __construct($argument){

  }

  public static function check_user_add_review($post_id){
    $review_table_name = self::$posts_review_table_name;
    $user_ip = getRealIP();
    $q_check_exit_review = ("SELECT *
    FROM `".$review_table_name."` WHERE `".$review_table_name."`.`user_ip` = '".$user_ip."'
    AND `".$review_table_name."`.`post_id` = '".$post_id."'");
    $r_check_exit_review = mysql_query($q_check_exit_review) or die("cant execute query");
    $n_check_exit_review = mysql_num_rows($r_check_exit_review);
    if($n_check_exit_review > 0) return true;
    return false;
  }

  final public static function add_review_post($data){
    $review_table_name = self::$posts_review_table_name;
    $posts_table_name = self::$posts_table_name;
    $user_ip = getRealIP();
    $post_id = (int)$data['post_id'];
    $review = $data['review'];
    $review = $review != 'yes' && $review != 'no' ? 'yes' : $review;

    $review_field_name = $review == 'no' ? 'count_not_like' : 'count_like';

    if(self::check_user_add_review($post_id) === true) return 'Вы уже оставляли отзыв об этом рецепте';

    $q_count = ("SELECT `".$posts_table_name."`.`".$review_field_name."`
    FROM `".$posts_table_name."` WHERE `".$posts_table_name."`.`id` = '".$post_id."'");
    $r_count = mysql_query($q_count) or die("cant execute query_path");
    $n_count = mysql_num_rows($r_count);
    if($n_count > 0){
      $count = htmlspecialchars(mysql_result($r_count, 0, $posts_table_name.".".$review_field_name));
      $count++;

      self::update_data(array(
        'table_name' => $posts_table_name,
        'fields' => array(
          $review_field_name => $count
        ),
        'where' => array(
          'id' => $post_id
        )
      ));

      self::insert_data(array(
        'table_name' => $review_table_name,
        'fields' => array(
          'post_id' => $post_id,
          'user_ip' => $user_ip,
          'create_date' => time()
        ),
        'where' => array(
          'id' => $post_id
        )
      ));

    }

    return true;

  }


  final public function get_new_posts($post_id){
    $arr_posts = array();
    $table_name = static::$posts_table_name;
    $post_cats_table_name = static::$posts_cats_item_table_name;

    $q_posts = ("SELECT
    DISTINCT(`".$table_name."`.`id`),
    `".$table_name."`.`alias_ru`,
    `".$table_name."`.`alias_en`,
    `".$table_name."`.`title_ru`,
    `".$table_name."`.`title_en`,
    `".$table_name."`.`content_ru`,
    `".$table_name."`.`content_en`,
    `".$table_name."`.`main_image`
    FROM `".$table_name."`
    WHERE `".$table_name."`.`id` <> '".$post_id."' AND `".$table_name."`.`status` <> 'deleted' ORDER BY `create_date` DESC LIMIT 4");
    $r_posts = mysql_query($q_posts) or die("cant execute posts1");
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      for ($i = 0; $i < $n_posts; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
        $post_alias = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_".LANG));
        $post_title = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_".LANG));
        $post_content = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_".LANG));
        $post_image = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".main_image"));

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'title' => $post_title,
          'content' => $post_content,
          'image' => $post_image
        ));


      }
    }

    return $arr_posts;

  }

  final public function get_similar_posts($post_id){
    $table_name = static::$posts_table_name;
    $post_cats_table_name = static::$posts_cats_item_table_name;
    $post_cats_info = $this->get_post_cats($post_id);
    $post_cats = '';

    for ($i=0; $i < count($post_cats_info); $i++) {
      $cat_id = $post_cats_info[$i]['id'];
      $post_cats .= $post_cats == '' ? $cat_id : ', '.$cat_id;
    }

    $arr_posts = array();

    $query_sort_cats = !empty($post_cats) ? " AND `".$post_cats_table_name."`.`cat_id` IN (".$post_cats.") " : "";

    $q_posts = ("SELECT
    DISTINCT(`".$table_name."`.`id`),
    `".$table_name."`.`alias_ru`,
    `".$table_name."`.`alias_en`,
    `".$table_name."`.`title_ru`,
    `".$table_name."`.`title_en`,
    `".$table_name."`.`content_ru`,
    `".$table_name."`.`content_en`,
    `".$table_name."`.`main_image`
    FROM `".$post_cats_table_name."`
    INNER JOIN `".$table_name."` ON (`".$table_name."`.`id` = `".$post_cats_table_name."`.`post_id`)
    WHERE `".$post_cats_table_name."`.`post_id` <> '".$post_id."' AND `".$table_name."`.`status` <> 'deleted' ".$query_sort_cats."
    ORDER BY RAND() LIMIT 4");
    $r_posts = mysql_query($q_posts) or die("cant execute posts1");
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      for ($i = 0; $i < $n_posts; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
        $post_alias = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_".LANG));
        $post_title = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_".LANG));
        $post_content = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_".LANG));
        $post_image = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".main_image"));

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'title' => $post_title,
          'content' => $post_content,
          'image' => $post_image
        ));


      }
    }


    return $arr_posts;

  }

  public static function get_best_posts($data){

    $count_posts = isset($data['count_posts']) ? (int)$data['count_posts'] : 4;

    $table_name = static::$posts_table_name;
    $arr_posts = array();

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` <> 'deleted'
    ORDER BY `".$table_name."`.`count_view` DESC LIMIT ".$count_posts);
    $r_posts = mysql_query($q_posts) or die("cant execute posts2");
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      for ($i = 0; $i < $n_posts; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
        $post_alias = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_".LANG));
        $post_title = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_".LANG));
        $post_content = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_".LANG));
        $post_image = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".main_image"));

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'title' => $post_title,
          'content' => $post_content,
          'image' => $post_image
        ));


      }
    }


    return $arr_posts;

  }

  public function add_count_view($post_id){
    $table_name = static::$posts_table_name;
    $q_count_view = ("SELECT `".$table_name."`.`count_view`
    FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$post_id."'");
    $r_count_view = mysql_query($q_count_view) or die("cant execute query_path");
    $n_count_view = mysql_num_rows($r_count_view);
    if($n_count_view > 0){
      $count_view = htmlspecialchars(mysql_result($r_count_view, 0, $table_name.".count_view"));
      $count_view++;

      self::update_data(array(
        'table_name' => $table_name,
        'fields' => array(
          'count_view' => $count_view
        ),
        'where' => array(
          'id' => $post_id
        )
      ));
    }
    return true;
  }



  public function get_ingredients($post_id){
    $ingredients_ru = array();
    $ingredients_en = array();

    $q_ings = ("SELECT * FROM `ingredients` WHERE `ingredients`.`post_id` = '".$post_id."'");
    $r_ings = mysql_query($q_ings) or die("cant execute query_path");
    $n_ings = mysql_num_rows($r_ings);
    if($n_ings > 0){
      for ($i = 0; $i < $n_ings; $i++) {
        $name = htmlspecialchars(mysql_result($r_ings, $i, "ingredients.content"));
        $lang = htmlspecialchars(mysql_result($r_ings, $i, "ingredients.lang"));

        if($lang == 'ru'){
          array_push($ingredients_ru,$name);
        } else if($lang == 'en'){
          array_push($ingredients_en,$name);
        }

      }
    }
    return array(
      'ingredients' => $ingredients_ru,
      'ingredients_ru' => $ingredients_ru,
      'ingredients_en' => $ingredients_en
    );
  }

  public function get_post_info($post_id){
    $table_name = static::$posts_table_name;
    $review_table_name = self::$posts_review_table_name;

    $post_info = false;

    $q_posts = ("SELECT * FROM `".$table_name."`
      LEFT JOIN `users` ON (`users`.`id` = `".$table_name."`.`user_id`)
      WHERE `".$table_name."`.`id` = '".$post_id."' AND `".$table_name."`.`status` <> 'deleted'");
    $r_posts = mysql_query($q_posts) or die("cant execute query_path");
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      $post_id = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".id"));
      $post_status = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".status"));
      $post_title = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".title_".LANG));
      $post_title_ru = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".title_ru"));
      $post_title_en = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".title_en"));
      $post_image = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".main_image"));
      $post_alias = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".alias_".LANG));
      $post_alias_ru = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".alias_ru"));
      $post_alias_en = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".alias_en"));
      $post_content = htmlspecialchars_decode(mysql_result($r_posts, 0, $table_name.".content_".LANG));
      $post_content_ru = htmlspecialchars_decode(mysql_result($r_posts, 0, $table_name.".content_ru"));
      $post_content_en = htmlspecialchars_decode(mysql_result($r_posts, 0, $table_name.".content_en"));
      $post_create_date = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".post_date_gmt"));
      $post_update_date = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".update_date"));
      $post_count_comments = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".count_comments"));
      $post_count_view = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".count_view"));
      $post_keywords = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".keywords_".LANG));
      $post_keywords_ru = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".keywords_ru"));
      $post_keywords_en = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".keywords_en"));
      $post_description = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".description_".LANG));
      $post_description_ru = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".description_ru"));
      $post_description_en = htmlspecialchars(mysql_result($r_posts, 0, $table_name.".description_en"));
      $post_user_id = htmlspecialchars(mysql_result($r_posts, 0, "users.id"));
      $post_user_name = htmlspecialchars(mysql_result($r_posts, 0, "users.login"));

      $post_cat_items = $this->get_post_cats($post_id);
      $post_ht_item = $this->get_post_hash_tags($post_id);
      $post_ingredients_info = $this->get_ingredients($post_id);

      $post_ingredients = $post_ingredients_info['ingredients_'.LANG];

      $post_ingredients_ru = $post_ingredients_info['ingredients_ru'];
      $post_ingredients_en = $post_ingredients_info['ingredients_en'];

      $allow_add_review = !self::check_user_add_review($post_id);

      $post_info = array(
        'id' => $post_id,
        'user_id' => $post_user_id,
        'user_name' => $post_user_name,
        'title' => $post_title,
        'title_ru' => $post_title_ru,
        'title_en' => $post_title_en,
        'content' => $post_content,
        'content_ru' => $post_content_ru,
        'content_en' => $post_content_en,
        'alias' => $post_alias,
        'alias_ru' => $post_alias_ru,
        'alias_en' => $post_alias_en,
        'image' => $post_image,
        'cats' => $post_cat_items,
        'ingredients' => $post_ingredients,
        'ingredients_ru' => $post_ingredients_ru,
        'ingredients_en' => $post_ingredients_en,
        'status' => $post_status,
        'hash_tags' => $post_ht_item,
        'create_date' => $post_create_date,
        'update_date' => $post_update_date,
        'keywords' => $post_keywords,
        'keywords_ru' => $post_keywords_ru,
        'keywords_en' => $post_keywords_en,
        'description' => $post_description,
        'description_ru' => $post_description_ru,
        'description_en' => $post_description_en,
        'count_comments' => $post_count_comments,
        'count_view' => $post_count_view,
        'allow_add_review' => $allow_add_review
      );


    }


    return $post_info;
  }


  public function get_post_hash_tags($post_id){
    $ht_table_name = static::$hash_tags_table_name;
    $post_ht_table_name = static::$hash_tags_items_table_name;

    $post_ht_items = array();

    $q_posts_ht = ("SELECT * FROM `".$post_ht_table_name."`
    INNER JOIN `".$ht_table_name."` ON (`".$ht_table_name."`.`id` = `".$post_ht_table_name."`.`ht_id`)
    WHERE `".$post_ht_table_name."`.`post_id` = '".$post_id."' ");
    $r_posts_ht = mysql_query($q_posts_ht) or die("cant execute hash_tags");
    $n_posts_ht = mysql_num_rows($r_posts_ht);
    if($n_posts_ht > 0){
      for ($i = 0; $i < $n_posts_ht; $i++) {
        $ht_id = htmlspecialchars(mysql_result($r_posts_ht, $i, $ht_table_name.".id"));
        $ht_name = htmlspecialchars(mysql_result($r_posts_ht, $i, $ht_table_name.".name"));

        array_push($post_ht_items,array(
          'id' => $ht_id,
          'name' => $ht_name
        ));

      }
    }

    return $post_ht_items;
  }

  public function get_post_cats($post_id){
    $cats_table_name = static::$categories_table_name;
    $post_cats_table_name = static::$posts_cats_item_table_name;

    $post_cat_items = array();

    $q_posts_cats = ("SELECT * FROM `".$post_cats_table_name."`
    INNER JOIN `".$cats_table_name."` ON (`".$cats_table_name."`.`id` = `".$post_cats_table_name."`.`cat_id`)
    WHERE `".$post_cats_table_name."`.`post_id` = '".$post_id."' ");
    $r_posts_cats = mysql_query($q_posts_cats) or die("cant execute query_path");
    $n_posts_cats = mysql_num_rows($r_posts_cats);
    if($n_posts_cats > 0){
      for ($i = 0; $i < $n_posts_cats; $i++) {
        $cat_id = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".id"));
        $cat_name = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".name_".LANG));
        $cat_name_ru = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".name_ru"));
        $cat_name_en = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".name_en"));
        $cat_alias = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".alias_".LANG));
        $cat_alias_ru = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".alias_ru"));
        $cat_alias_en = htmlspecialchars(mysql_result($r_posts_cats, $i, $cats_table_name.".alias_en"));

        array_push($post_cat_items,array(
          'id' => $cat_id,
          'name' => $cat_name,
          'name_ru' => $cat_name_ru,
          'name_en' => $cat_name_en,
          'alias' => $cat_alias,
          'alias_ru' => $cat_alias_ru,
          'alias_en' => $cat_alias_en
        ));

      }
    }

    return $post_cat_items;

  }

  public function get_posts($data){
    $table_name = self::$posts_table_name;
    $posts_cats_item_table_name = self::$posts_cats_item_table_name;
    $hash_tags_items_table_name = self::$hash_tags_items_table_name;
    $users_table_name = self::$users_table_name;

    $search_str = $data['search_str'];
    $cat_id = $data['cat_id'];
    $sort_date = $data['date'];
    $hash_tag = $data['hash_tag'];
    $page = (int)$data['page'];

    $page = $page == 0 ? 1 : $page;

    $items_per_page = isset($data['items_per_page']) ? $data['items_per_page'] : 20;
    $offset = ($page - 1) * $items_per_page;
    $sql_limit = " LIMIT " . $offset . "," . $items_per_page;

    $sort_by_status = $data['status'];


    $sort_by_status = mysql_real_escape_string($sort_by_status);
    $search_str = mysql_real_escape_string($search_str);

    $sort_by_status = $sort_by_status == 'basket' ? 'deleted' : $sort_by_status;

    $query_sort_by_status = ' AND `'.$table_name.'`.`status` = \''.$sort_by_status.'\'';

    $query_sort_by_status = $sort_by_status == 'all' || empty($sort_by_status) ?
      ' AND `'.$table_name.'`.`status` <> \'deleted\'' : $query_sort_by_status;


    $inner_cats = '';
    $inner_hash_tags = '';
    $query_sort_cats = '';
    $query_search = '';
    $query_sort_date = '';
    $query_sort_hash_tags = '';

    if($cat_id > 0){
      $inner_cats = ' INNER JOIN `'.$posts_cats_item_table_name.'` ON (`'.$posts_cats_item_table_name.'`.`post_id` = `'.$table_name.'`.`id`) ';
      $query_sort_cats = ' AND `'.$posts_cats_item_table_name.'`.`cat_id` = \''.$cat_id.'\'';
    }


    if(!empty($sort_date)){
      $arr_date = explode('-',$sort_date);
      $sort_year = (int)$arr_date[0];
      $sort_month = (int)$arr_date[1];

      $query_sort_date = ' AND YEAR(`'.$table_name.'`.`post_date`) = \''.$sort_year.'\'';
      $query_sort_date .= ' AND MONTH(`'.$table_name.'`.`post_date`) = \''.$sort_month.'\'';

    }

    if(!empty($search_str)){
      $query_search = ' AND `'.$table_name.'`.`title_ru` LIKE \''.$search_str.'%\'';
    }

    if($hash_tag > 0){
      $inner_hash_tags = ' INNER JOIN `'.$hash_tags_items_table_name.'` ON (`'.$hash_tags_items_table_name.'`.`post_id` = `'.$table_name.'`.`id`) ';
      $query_sort_hash_tags = ' AND `'.$hash_tags_items_table_name.'`.`ht_id` = \''.$hash_tag.'\'';
    }


    $arr_posts = array();


    $q_posts = ("SELECT * FROM `".$table_name."`
    ".$inner_cats.$inner_hash_tags."
    LEFT JOIN `".$users_table_name."` ON (`".$users_table_name."`.`id` = `".$table_name."`.`user_id`)
    WHERE `".$table_name."`.`id` <> '0'
    ".$query_sort_by_status.$query_sort_cats.$query_sort_date.$query_search.$query_sort_hash_tags."
    GROUP BY `".$table_name."`.`id`
    ORDER BY `".$table_name."`.`post_date_gmt` DESC ".$sql_limit);
    // echo $q_posts;
    $r_posts = mysql_query($q_posts) or die($q_posts);
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      for ($i = 0; $i < $n_posts; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
        $post_status = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".status"));
        $post_alias = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_".LANG));
        $post_alias_ru = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_ru"));
        $post_alias_en = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias_en"));
        $post_title = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_".LANG));
        $post_title_ru = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_ru"));
        $post_title_en = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title_en"));
        $post_content = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_".LANG));
        $post_content_ru = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_ru"));
        $post_content_en = htmlspecialchars_decode(mysql_result($r_posts, $i, $table_name.".content_en"));
        $post_image = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".main_image"));
        $post_user_id = htmlspecialchars(mysql_result($r_posts, $i, $users_table_name.".id"));
        $post_user_name = htmlspecialchars(mysql_result($r_posts, $i, $users_table_name.".name"));
        $post_count_comments = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_comments"));
        $post_create_date = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".post_date_gmt"));
        $post_update_date = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".update_date"));
        $post_keywords = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".keywords"));
        $post_description = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".description"));
        $post_count_view = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_view"));
        $post_count_like = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_like"));

        $post_cat_items = $this->get_post_cats($post_id);
        $post_ht_item = $this->get_post_hash_tags($post_id);

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'alias_ru' => $post_alias_en,
          'alias_en' => $post_alias_en,
          'title' => $post_title,
          'title_ru' => $post_title_ru,
          'title_en' => $post_title_en,
          'content' => $post_content,
          'content_ru' => $post_content_ru,
          'content_en' => $post_content_en,
          'image' => $post_image,
          'user_id' => $post_user_id,
          'user_name' => $post_user_name,
          'cats' => $post_cat_items,
          'status' => $post_status,
          'count_comments' => $post_count_comments,
          'hash_tags' => $post_ht_item,
          'create_date' => $post_create_date,
          'update_date' => $post_update_date,
          'keywords' => $post_keywords,
          'count_view' => $post_count_view,
          'count_like' => $post_count_like,
          'description' => $post_description
        ));


      }
    }

    // $q_posts = ("SELECT * FROM `".$table_name."`
    // WHERE `".$table_name."`.`count_like` = '0'");
    // $r_posts = mysql_query($q_posts) or die($q_posts);
    // $n_posts = mysql_num_rows($r_posts);
    // if($n_posts > 0){
    //   for ($i = 0; $i < $n_posts; $i++) {
    //     $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
    //
    //     self::update_data(array(
    //       'table_name' => self::$posts_table_name,
    //       'fields' => array(
    //         'count_like' => rand(5,50),
    //       ),
    //       'where' => array(
    //         'id' => $post_id
    //       )
    //     ));
    //
    //
    //   }
    // }


    $q_count_posts = ("SELECT * FROM `".$table_name."`
    ".$inner_cats.$inner_hash_tags."
    LEFT JOIN `".$users_table_name."` ON (`".$users_table_name."`.`id` = `".$table_name."`.`user_id`)
    WHERE `".$table_name."`.`status` <> 'deleted'
    ".$query_sort_by_status.$query_sort_cats.$query_sort_date.$query_search.$query_sort_hash_tags."
    GROUP BY `".$table_name."`.`id`
    ORDER BY `".$table_name."`.`post_date_gmt` DESC");
    $r_count_posts = mysql_query($q_count_posts) or die("cant execute posts4");
    $n_count_posts = mysql_num_rows($r_count_posts);


    return array(
      'posts' => $arr_posts,
      'count_posts' => $n_count_posts
    );

  }



  public static function add_cats_count_posts(){
    $cats_table_name = self::$categories_table_name;
    $cats_items_table_name = self::$posts_cats_item_table_name;

    $q_cats = ("SELECT * FROM `".$cats_table_name."`");
    $r_cats = mysql_query($q_cats) or die("cant execute all");
    $n_cats = mysql_num_rows($r_cats);
    if($n_cats > 0){
      for ($i=0; $i < $n_cats; $i++) {
        $cat_id = htmlspecialchars(mysql_result($r_cats, $i, $cats_table_name.".id"));

        $q_count = ("SELECT * FROM `".$cats_items_table_name."` WHERE `".$cats_items_table_name."`.`cat_id` = '".$cat_id."'");
        $r_count = mysql_query($q_count) or die("cant execute all");
        $n_count = mysql_num_rows($r_count);

        static::update_data(array(
          'table_name' => $cats_table_name,
          'fields' => array(
            'count_posts' => $n_count
          ),
          'where' => array(
            'id' => $cat_id
          )
        ));

      }
    }

  }


  public static function restore_post($data){
    $table_name = static::$posts_table_name;
    $post_id = $data['post_id'];

    $now_time = time();

    $post_new_status = 'published';

    $q_post_info = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`id` = '".$post_id."'");
    $r_post_info = mysql_query($q_post_info) or die("cant execute all");
    $n_post_info = mysql_num_rows($r_post_info);
    if($n_post_info > 0){
      $post_public_date = htmlspecialchars(mysql_result($r_post_info, 0, $table_name.".post_date_gmt"));

      $time_pd = strtotime($post_public_date);


      $post_new_status = $time_pd > $now_time ? 'future' : $post_new_status;

      self::update_data(array(
        'table_name' => $table_name,
        'fields' => array(
          'status' => $post_new_status
        ),
        'where' => array(
          'id' => $post_id
        )
      ));

    } else return 'Запись не найдена';

    return true;

  }


  public static function get_statuses_posts(){
    $table_name = static::$posts_table_name;

    $arr_statuses = array();

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` <> 'deleted'");
    $r_posts = mysql_query($q_posts) or die("cant execute all");
    $count_all_posts = mysql_num_rows($r_posts);

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` = 'published'");
    $r_posts = mysql_query($q_posts) or die("cant execute published");
    $count_published_posts = mysql_num_rows($r_posts);

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` = 'future'");
    $r_posts = mysql_query($q_posts) or die("cant execute future");
    $count_future_posts = mysql_num_rows($r_posts);

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` = 'approval'");
    $r_posts = mysql_query($q_posts) or die("cant execute approval");
    $count_approval_posts = mysql_num_rows($r_posts);

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` = 'draft'");
    $r_posts = mysql_query($q_posts) or die("cant execute draft");
    $count_draft_posts = mysql_num_rows($r_posts);

    $q_posts = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`status` = 'deleted'");
    $r_posts = mysql_query($q_posts) or die("cant execute draft");
    $count_deleted_posts = mysql_num_rows($r_posts);

    if($count_all_posts > 0){
      array_push($arr_statuses,array(
        'translate' => 'Все',
        'name' => 'all',
        'count' => $count_all_posts
      ));

      if($count_published_posts > 0){
        array_push($arr_statuses,array(
          'translate' => 'Опубликованные',
          'name' => 'published',
          'count' => $count_published_posts
        ));
      }

      if($count_future_posts > 0){
        array_push($arr_statuses,array(
          'translate' => 'Запланированные',
          'name' => 'future',
          'count' => $count_future_posts
        ));
      }

      if($count_approval_posts > 0){
        array_push($arr_statuses,array(
          'translate' => 'На утверждении',
          'name' => 'approval',
          'count' => $count_approval_posts
        ));
      }

      if($count_draft_posts > 0){
        array_push($arr_statuses,array(
          'translate' => 'Черновики',
          'name' => 'draft',
          'count' => $count_draft_posts
        ));
      }

      if($count_deleted_posts > 0){
        array_push($arr_statuses,array(
          'translate' => 'Корзина',
          'name' => 'basket',
          'count' => $count_deleted_posts
        ));
      }

    }

    return array(
      'statuses' => $arr_statuses,
      'counts' => array(
        'all' => $count_all_posts,
        'published' => $count_published_posts,
        'future' => $count_future_posts,
        'approval' => $count_approval_posts,
        'draft' => $count_draft_posts,
        'basket' => $count_deleted_posts
      )
    );
  }


  public static function delete_post($data){
    $post_id = $data['post_id'];
    $posts = $data['posts'];
    $delete_forever = (int)$data['forever'];

    $table_name = self::$posts_table_name;

    if($delete_forever > 0){

      if(is_array($posts)){

        for ($i = 0; $i < count($posts); $i++) {
          $post_id = (int)$posts[$i];

          $q_delete = ("DELETE FROM `".$table_name."`
          WHERE `".$table_name."`.`id`='".$post_id."'");
          mysql_query($q_delete) or die(generate_exception($db_error));

          $q_delete = ("DELETE FROM `aliases` WHERE `item_id` = '".$post_id."'");
          mysql_query($q_delete) or die(generate_exception($db_error));

        }

      } else {
        $q_delete = ("DELETE FROM `".$table_name."`
        WHERE `".$table_name."`.`id`='".$post_id."'");
        mysql_query($q_delete) or die(generate_exception($db_error));

        $q_delete = ("DELETE FROM `aliases` WHERE `item_id` = '".$post_id."'");
        mysql_query($q_delete) or die(generate_exception($db_error));
      }

    } else{

      if(is_array($posts)){

        for ($i = 0; $i < count($posts); $i++) {
          $post_id = (int)$posts[$i];

          self::update_data(array(
            'table_name' => $table_name,
            'fields' => array(
              'status' => 'deleted'
            ),
            'where' => array(
              'id' => $post_id
            )
          ));


          $q_delete = ("DELETE FROM `aliases` WHERE `item_id` = '".$post_id."'");
          mysql_query($q_delete) or die(generate_exception($db_error));

        }

      } else{

        self::update_data(array(
          'table_name' => $table_name,
          'fields' => array(
            'status' => 'deleted'
          ),
          'where' => array(
            'id' => $post_id
          )
        ));

        $q_delete = ("DELETE FROM `aliases` WHERE `item_id` = '".$post_id."'");
        mysql_query($q_delete) or die(generate_exception($db_error));

      }

    }

    return true;
  }


  public static function exist_hash_tag($name){

    $table_name = self::$hash_tags_table_name;

    $q_check_exist_ht = ("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`name`='".$name."'");
    $r_check_exist_ht = mysql_query($q_check_exist_ht) or die("cant execute query_path");
    $n_check_exist_ht = mysql_num_rows($r_check_exist_ht);
    if($n_check_exist_ht > 0){
      $ht_id = htmlspecialchars(mysql_result($r_check_exist_ht, 0, $table_name.".id"));
      return $ht_id;
    }
    return false;
  }

  public static function add_hash_tag($data){
    $post_id = $data['post_id'];
    $name = $data['name'];

    if(empty($name)) return 'Введите название хеш-тега';

    $table_name = self::$hash_tags_table_name;
    $items_table_name = self::$hash_tags_items_table_name;

    $ht_id = self::exist_hash_tag($name);

    if($ht_id === false){

      $ht_id = self::insert_data(array(
        'table_name' => $table_name,
        'fields' => array(
          'name' => $name,
          'create_date' => time()
        )
      ));

    }

    self::insert_data(array(
      'table_name' => $items_table_name,
      'fields' => array(
        'post_id' => $post_id,
        'ht_id' => $ht_id,
        'create_date' => time()
      )
    ));

  }

  public static function set_post_timer($data){
    $post_id = (int)$data['post_id'];
    $date = (int)$data['date'];

    $table_name = self::$posts_timers_table_name;

    self::insert_data(array(
      'table_name' => $table_name,
      'fields' => array(
        'post_id' => $post_id,
        'time' => $date
      )
    ));

  }

  public static function delete_extra_cat_items($post_id){




  }

  public static function add_post_cat($data){
    $post_id = (int)$data['post_id'];
    $cat_id = (int)$data['cat_id'];

    $cat_table_name = self::$categories_table_name;
    $cat_items_table_name = self::$posts_cats_item_table_name;

    $q_check_exist_cat = ("SELECT * FROM `".$cat_table_name."` WHERE `".$cat_table_name."`.`id`='".$cat_id."'");
    $r_check_exist_cat = mysql_query($q_check_exist_cat) or die("cant execute query_path");
    $n_check_exist_cat = mysql_num_rows($r_check_exist_cat);
    if($n_check_exist_cat == 0) return false;

    $q_check_exist = ("SELECT * FROM `".$cat_items_table_name."` WHERE
    `".$cat_items_table_name."`.`cat_id`='".$cat_id."' AND `".$cat_items_table_name."`.`post_id`='".$post_id."'");
    $r_check_exist = mysql_query($q_check_exist) or die("cant execute query_path");
    $n_check_exist = mysql_num_rows($r_check_exist); // or die("cant get numrows query_path");
    if ($n_check_exist == 0){

      self::insert_data(array(
        'table_name' => $cat_items_table_name,
        'fields' => array(
          'post_id' => $post_id,
          'cat_id' => $cat_id,
          'create_date' => time()
        )
      ));

    }


  }

  public static function add_ingredients($data){
    $post_id = (int)$data['post_id'];
    $ingredients = $data['ingredients'];
    $lang = $data['lang'];

    $q_delete = ("DELETE FROM `ingredients`
    WHERE `post_id`='".$post_id."' AND `lang` = '".$lang."'");
    mysql_query($q_delete) or die(generate_exception($db_error));

    for ($i=0; $i < count($ingredients); $i++) {
      $name = $ingredients[$i];

      if(empty($name)) continue;

      $name = mysql_real_escape_string($name);

      self::insert_data(array(
        'table_name' => 'ingredients',
        'fields' => array(
          'post_id' => $post_id,
          'content' => $name,
          'lang' => $lang,
          'create_date' => time()
        )
      ));


    }

  }

  public static function add_post($data){
    $root_dir = $_SERVER['DOCUMENT_ROOT'];
    $now_time = time();
    $user_id = (int)$_SESSION['logged_user']['id'];
    $title_ru = $data['title_ru'];
    $title_en = $data['title_en'];
    $content_ru = $data['content_ru'];
    $content_en = $data['content_en'];
    $alias_ru = $data['alias_ru'];
    $alias_en = $data['alias_en'];
    $keywords_ru = $data['keywords_ru'];
    $keywords_en = $data['keywords_en'];
    $ingredients_ru = $data['ingredients_ru'];
    $ingredients_en = $data['ingredients_en'];
    $description_ru = $data['description_ru'];
    $description_en = $data['description_en'];
    $status = $data['status'];
    $image_hash = $data['image_hash'];
    $published_date = $data['published_date'];
    $post_cats = $data['cats'];
    $hash_tags = $data['hash_tags'];
    $user_recipe_id = $data['user_recipe_id'];



    $content_ru = mysql_real_escape_string($content_ru);
    $content_en = mysql_real_escape_string($content_en);

    $keywords_ru = mysql_real_escape_string($keywords_ru);
    $keywords_en = mysql_real_escape_string($keywords_en);

    $description_ru = mysql_real_escape_string($description_ru);
    $description_en = mysql_real_escape_string($description_en);

    if(empty($title_ru) && empty($title_en)) generate_exception('Введите заголовок записи');
    if(empty($content_ru) && empty($content_en)) generate_exception('Введите текси записи');

    $alias_ru = mysql_real_escape_string($alias_ru);
    $alias_en = mysql_real_escape_string($alias_en);

    if(Alias::check_exist_alias(array('alias' => $alias_ru,'lang' => 'ru')) !== false)
      generate_exception('Алиас с таким именем уже существует, придумайте другой');

    if(Alias::check_exist_alias(array('alias' => $alias_en,'lang' => 'en')) !== false)
      generate_exception('This url already exists');

    $status = $status != 'published' && $status != 'approval' && $status != 'draft'
    ? 'published' : $status;

    $time_pd = strtotime($published_date);

    $post_date_gmt = gmdate('Y-m-d H:i:s');
    $post_date = gmdate('Y-m-d H:i:s',strtotime('+'.$_SESSION['count_time_offset'].' hours'));

    if($time_pd > $now_time){
      $status = 'future';

      $post_date_gmt = date('Y-m-d H:i:s', $time_pd - $_SESSION['time_offset']);
      $post_date = date('Y-m-d H:i:s', $time_pd);

    }

    if($user_recipe_id > 0){

      require_once($root_dir.'/include/classes/user_recipes.php');

      $init_user_recipes = new UserRecipes(array(
        'item_id' => $user_recipe_id
      ));

      $recipe_info = $init_user_recipes->get_recipe_info(array(
        'sort_user' => false
      ),true);

      if($recipe_info === false) generate_exception('Рецепт не найден');

      $user_id = $recipe_info['user_id'];

    }


    $post_id = self::insert_data(array(
      'table_name' => self::$posts_table_name,
      'fields' => array(
        'user_id' => $user_id,
        'title_ru' => $title_ru,
        'title_en' => $title_en,
        'content_ru' => $content_ru,
        'content_en' => $content_en,
        'alias_ru' => $alias_ru,
        'alias_en' => $alias_en,
        'status' => $status,
        'create_date' => time(),
        'post_date' => $post_date,
        'post_date_gmt' => $post_date_gmt,
        'keywords_ru' => $keywords_ru,
        'keywords_en' => $keywords_en,
        'description_ru' => $description_ru,
        'description_en' => $description_en,
        'user_recipe_id' => $user_recipe_id
      )
    ));

    if($user_recipe_id > 0){

      require_once($root_dir.'/include/classes/fav_recipes.php');

      $init_fav_recipes = new UserFavRecipes(array(
        'recipe_id' => $post_id
      ));

      $init_fav_recipes->bookmark(array(
        'id' => $post_id,
        'type' => 'add'
      ),$user_id);


      self::update_data(array(
        'table_name' => 'user_recipes',
        'fields' => array(
          'parent_id' => $post_id,
          'status' => 'published'
        ),
        'where' => array(
          'id' => $user_recipe_id
        )
      ));



    }

    $add_alias_ru = Alias::add_alias(array(
      'item_id' => $post_id,
      'type' => 'post',
      'alias' => $alias_ru,
      'lang' => 'ru',
      'table_name' => self::$posts_table_name
    ));

    $add_alias_en = Alias::add_alias(array(
      'item_id' => $post_id,
      'type' => 'post',
      'alias' => $alias_en,
      'lang' => 'en',
      'table_name' => self::$posts_table_name
    ));


    $q_temp_images = ("SELECT * FROM `temp_images` WHERE `temp_images`.`md5_hash` = '".$image_hash."'");
    $r_temp_images = mysql_query($q_temp_images) or die(generate_exception($db_error));
    $n_temp_images = mysql_numrows($r_temp_images); // or die("cant get numrows query");
    if ($n_temp_images > 0){
      for ($i = 0; $i < $n_temp_images; $i++) {
        $image_id = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.id"));
        $image_name = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.image"));
        $image_position = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.position"));

        if($image_position == 1){

          self::update_data(array(
            'table_name' => self::$posts_table_name,
            'fields' => array(
              'main_image' => $image_name,
            ),
            'where' => array(
              'id' => $post_id
            )
          ));

        }

        self::insert_data(array(
          'table_name' => 'posts_images',
          'fields' => array(
            'user_id' => $user_id,
            'item_id' => $post_id,
            'image' => $image_name,
            'position' => $image_position,
            'create_date' => time()
          )
        ));

        $q_delete_temp_image = ("DELETE FROM `temp_images`
        WHERE `temp_images`.`id`='".$image_id."'");
        mysql_query($q_delete_temp_image) or die(generate_exception($db_error));


      }
    } else if($user_recipe_id > 0){

      $main_image = $recipe_info['main_image'];

      $exploded_arr=explode(".", $file_name);
      $extension = end($exploded_arr);
      $extension = strtolower($extension);

      $new_image_name = !empty($alias_ru) ? $alias_ru.'.'.$extension : time().'-'.md5(uniqid(rand(1,10000000), true)).'.'.$extension;

      $img_dir = $_SERVER['DOCUMENT_ROOT'].'/images/post_images/';

      copy($img_dir.$main_image,$img_dir.$new_image_name);
      copy($img_dir.'thumbnail_1024/'.$main_image,$img_dir.'thumbnail_1024/'.$new_image_name);
      copy($img_dir.'thumbnail_480/'.$main_image,$img_dir.'thumbnail_480/'.$new_image_name);
      copy($img_dir.'thumbnail_140/'.$main_image,$img_dir.'thumbnail_140/'.$new_image_name);

      self::update_data(array(
        'table_name' => self::$posts_table_name,
        'fields' => array(
          'main_image' => $new_image_name,
        ),
        'where' => array(
          'id' => $post_id
        )
      ));

      self::insert_data(array(
        'table_name' => 'posts_images',
        'fields' => array(
          'user_id' => $user_id,
          'item_id' => $post_id,
          'image' => $new_image_name,
          'position' => 1,
          'create_date' => time()
        )
      ));

    }



    for ($i = 0; $i < count($post_cats); $i++) {
      $cat_id = (int)$post_cats[$i];
      self::add_post_cat(array(
        'post_id' => $post_id,
        'cat_id' => $cat_id
      ));
    }

    self::delete_extra_cat_items($post_id);

    for ($i = 0; $i < count($hash_tags); $i++) {
      $name = $hash_tags[$i];
      self::add_hash_tag(array(
        'name' => $name,
        'post_id' => $post_id
      ));
    }

    self::add_ingredients(array(
      'post_id' => $post_id,
      'ingredients' => $ingredients_ru,
      'lang' => 'ru'
    ));

    self::add_ingredients(array(
      'post_id' => $post_id,
      'ingredients' => $ingredients_en,
      'lang' => 'en'
    ));


    if($status == 'future'){

      self::set_post_timer(array(
        'post_id' => $post_id,
        'date' => $time_pd
      ));

    }

    self::add_cats_count_posts();

    return $post_id;

  }

  public static function get_posts_date($date){

    $arr_month_translate = array('1' => 'Январь','Февраль','Март','Апрель','Май',
    'Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');

    $table_name = self::$posts_table_name;

    $q_date = ("SELECT
    YEAR(`".$table_name."`.`post_date_gmt`) AS `Year`,
    MONTH(`".$table_name."`.`post_date_gmt`) AS `Month`
    FROM `".$table_name."` GROUP BY Year,Month ORDER BY Year DESC,Month DESC");
    $r_date = mysql_query($q_date) or die("cant execute date");
    $n_date = mysql_num_rows($r_date);
    if($n_date > 0){
      for ($i = 0; $i < $n_date; $i++) {
        $year = htmlspecialchars(mysql_result($r_date, $i, "Year"));
        $month = htmlspecialchars(mysql_result($r_date, $i, "Month"));

        $value_date = $year.'-'.$month;

        $selected = $value_date == $date ? 'selected="selected"' : '';

        $html .= '<option '.$selected.' value="'.$year.'-'.$month.'">'.$arr_month_translate[$month].' '.$year.'</option>';

      }
    }


    return $html;


  }

  public static function update_post($data){
    $post_id = (int)$data['post_id'];
    $now_time = time();
    $user_id = (int)$_SESSION['logged_user']['id'];
    $title_ru = $data['title_ru'];
    $title_en = $data['title_en'];
    $content_ru = $data['content_ru'];
    $content_en = $data['content_en'];
    $alias_ru = $data['alias_ru'];
    $alias_en = $data['alias_en'];
    $keywords_ru = $data['keywords_ru'];
    $keywords_en = $data['keywords_en'];
    $ingredients_ru = $data['ingredients_ru'];
    $ingredients_en = $data['ingredients_en'];
    $description_ru = $data['description_ru'];
    $description_en = $data['description_en'];
    $status = $data['status'];
    $published_date = $data['published_date'];
    $post_cats = $data['cats'];
    $hash_tags = $data['hash_tags'];

    $content_ru = mysql_real_escape_string($content_ru);
    $content_en = mysql_real_escape_string($content_en);

    $keywords_ru = mysql_real_escape_string($keywords_ru);
    $keywords_en = mysql_real_escape_string($keywords_en);

    $description_ru = mysql_real_escape_string($description_ru);
    $description_en = mysql_real_escape_string($description_en);

    if(empty($title_ru) && empty($title_en)) generate_exception('Введите заголовок записи');
    if(empty($content_ru) && empty($content_en)) generate_exception('Введите текси записи');

    $add_alias_ru = Alias::add_alias(array(
      'item_id' => $post_id,
      'type' => 'post',
      'alias' => $alias_ru,
      'lang' => 'ru',
      'table_name' => self::$posts_table_name
    ));

    $add_alias_en = Alias::add_alias(array(
      'item_id' => $post_id,
      'type' => 'post',
      'alias' => $alias_en,
      'lang' => 'en',
      'table_name' => self::$posts_table_name
    ));


    if($add_alias_ru === false) generate_exception('Алиас с таким именем уже существует, придумайте другой');
    if($add_alias_en === false) generate_exception('This url already exists');

    $status = $status != 'published' && $status != 'approval' && $status != 'draft'
    ? 'published' : $status;

    $time_pd = strtotime($published_date);

    $post_date_gmt = gmdate('Y-m-d H:i:s');
    $post_date = gmdate('Y-m-d H:i:s',strtotime('+'.$_SESSION['count_time_offset'].' hours'));

    $post_update_date_gmt = gmdate('Y-m-d H:i:s');
    $post_update_date = gmdate('Y-m-d H:i:s',strtotime('+'.$_SESSION['count_time_offset'].' hours'));

    if($time_pd > $now_time){
      $status = $status == 'published' ? 'future' : $status;

      $post_date_gmt = date('Y-m-d H:i:s', $time_pd - $_SESSION['time_offset']);
      $post_date = date('Y-m-d H:i:s', $time_pd);

    }

    self::update_data(array(
      'table_name' => self::$posts_table_name,
      'fields' => array(
        'title_ru' => $title_ru,
        'title_en' => $title_en,
        'content_ru' => $content_ru,
        'content_en' => $content_en,
        'alias_ru' => $alias_ru,
        'alias_en' => $alias_en,
        'status' => $status,
        'update_date' => $now_time,
        'post_date' => $post_date,
        'post_date_gmt' => $post_date_gmt,
        'post_date_update' => $post_update_date,
        'post_date_update_gmt' => $post_update_date_gmt,
        'keywords_ru' => $keywords_ru,
        'keywords_en' => $keywords_en,
        'description_ru' => $description_ru,
        'description_en' => $description_en
      ),
      'where' => array(
        'id' => $post_id
      )
    ));

    $cat_items_table_name = self::$posts_cats_item_table_name;

    $q_delete = ("DELETE FROM `".$cat_items_table_name."`
    WHERE `".$cat_items_table_name."`.`post_id`='".$post_id."'");
    mysql_query($q_delete) or die(generate_exception($db_error));


    for ($i = 0; $i < count($post_cats); $i++) {
      $cat_id = (int)$post_cats[$i];
      self::add_post_cat(array(
        'post_id' => $post_id,
        'cat_id' => $cat_id
      ));
    }

    self::delete_extra_cat_items($post_id);

    $hash_tags_items_table_name = self::$hash_tags_items_table_name;

    $q_delete = ("DELETE FROM `".$hash_tags_items_table_name."`
    WHERE `".$hash_tags_items_table_name."`.`post_id`='".$post_id."'");
    mysql_query($q_delete) or die(generate_exception($db_error));

    for ($i = 0; $i < count($hash_tags); $i++) {
      $name = $hash_tags[$i];
      self::add_hash_tag(array(
        'name' => $name,
        'post_id' => $post_id
      ));
    }


    if($status == 'future'){

      self::set_post_timer(array(
        'post_id' => $post_id,
        'date' => $time_pd
      ));

    }

    self::add_ingredients(array(
      'post_id' => $post_id,
      'ingredients' => $ingredients_ru,
      'lang' => 'ru'
    ));

    self::add_ingredients(array(
      'post_id' => $post_id,
      'ingredients' => $ingredients_en,
      'lang' => 'en'
    ));

    self::add_cats_count_posts();

    return $post_id;

  }


}



?>
