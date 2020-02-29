<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class Posts extends AdmCommon{

  function __construct($argument){

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

      $query_sort_date = ' AND YEAR(`'.$table_name.'`.`post_date_gmt`) = \''.$sort_year.'\'';
      $query_sort_date .= ' AND MONTH(`'.$table_name.'`.`post_date_gmt`) = \''.$sort_month.'\'';

    }

    if(!empty($search_str)){
      $query_search = ' AND `'.$table_name.'`.`title` LIKE \''.$search_str.'%\'';
    }

    if($hash_tag > 0){
      $inner_hash_tags = ' INNER JOIN `'.$hash_tags_items_table_name.'` ON (`'.$hash_tags_items_table_name.'`.`post_id` = `'.$table_name.'`.`id`) ';
      $query_sort_hash_tags = ' AND `'.$hash_tags_items_table_name.'`.`ht_id` = \''.$hash_tag.'\'';
    }


    $arr_posts = array();

    $q_posts = ("SELECT * FROM `".$table_name."`
    ".$inner_cats.$inner_hash_tags."
    LEFT JOIN `".$users_table_name."` ON (`".$users_table_name."`.`id` = `".$table_name."`.`user_id`)
    WHERE `".$table_name."`.`create_date` > '0'
    ".$query_sort_by_status.$query_sort_cats.$query_sort_date.$query_search.$query_sort_hash_tags."
    GROUP BY `".$table_name."`.`id`
    ORDER BY `".$table_name."`.`id` DESC");
    $r_posts = mysql_query($q_posts) or die("cant execute posts");
    $n_posts = mysql_num_rows($r_posts);
    if($n_posts > 0){
      for ($i = 0; $i < $n_posts; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".id"));
        $post_status = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".status"));
        $post_alias = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".alias"));
        $post_title = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".title"));
        $post_content = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".content"));
        $post_user_id = htmlspecialchars(mysql_result($r_posts, $i, $users_table_name.".id"));
        $post_user_name = htmlspecialchars(mysql_result($r_posts, $i, $users_table_name.".name"));
        $post_count_comments = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_comments"));
        $post_create_date = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".post_date_gmt"));
        $post_update_date = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".update_date"));
        $post_keywords = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".keywords"));
        $post_description = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".description"));
        $post_count_likes = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_like"));
        $post_count_not_likes = htmlspecialchars(mysql_result($r_posts, $i, $table_name.".count_not_like"));

        $post_cat_items = $this->get_post_cats($post_id);
        $post_ht_item = $this->get_post_hash_tags($post_id);

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'title' => $post_title,
          'content' => $post_content,
          'user_id' => $post_user_id,
          'user_name' => $post_user_name,
          'cats' => $post_cat_items,
          'status' => $post_status,
          'count_comments' => $post_count_comments,
          'hash_tags' => $post_ht_item,
          'create_date' => $post_create_date,
          'update_date' => $post_update_date,
          'keywords' => $post_keywords,
          'description' => $post_description,
          'count_likes' => $post_count_likes,
          'count_not_likes' => $post_count_not_likes
        ));


      }
    }


    return $arr_posts;

  }





}



?>
