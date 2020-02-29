<?php
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class Search extends AdmCommon{
  public $search_str;
  public $page;

  function __construct($data){
    $this->search_str = isset($data['search_str']) ? $data['search_str'] : '';
    $this->page = isset($data['page']) ? $data['page'] : 1;
  }

  public function search(){
    $posts_table_name = static::$posts_table_name;
    $search_string = $this->search_str;
    $arr_posts = array();

    if(!$search_string) return false;

    $page = $this->page;

    $items_per_page = 15;
    $offset = ($page - 1) * $items_per_page;
    $sql_limit = " LIMIT " . $offset . "," . $items_per_page;

    $q_search = ("
    SELECT * FROM `".$posts_table_name."`
    WHERE
    (
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '".$search_string."%' OR
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '%".$search_string."%' OR
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '%".$search_string."%'
    )
    AND `".$posts_table_name."`.`status` <> 'deleted' ".$sql_limit);
    $r_search = mysql_query($q_search) or die("cant execute search");
    $n_search = mysql_num_rows($r_search);
    if($n_search > 0){
      for ($i = 0; $i < $n_search; $i++) {
        $post_id = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".id"));
        $post_alias = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".alias_".LANG));
        $post_title = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".title_".LANG));
        $post_content = htmlspecialchars_decode(mysql_result($r_search, $i, $posts_table_name.".content_".LANG));
        $post_image = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".main_image"));
        $post_create_date = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".post_date_gmt"));
        $post_update_date = htmlspecialchars(mysql_result($r_search, $i, $posts_table_name.".update_date"));

        array_push($arr_posts,array(
          'id' => $post_id,
          'alias' => $post_alias,
          'title' => $post_title,
          'content' => $post_content,
          'image' => $post_image,
          'create_date' => $post_create_date,
          'update_date' => $post_update_date
        ));

      }

    }



    $q_search = ("
    SELECT * FROM `".$posts_table_name."`
    WHERE
    (
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '".$search_string."%' OR
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '%".$search_string."%' OR
    `".$posts_table_name."`.`title_".(LANG)."` LIKE '%".$search_string."%'
    )
    AND `".$posts_table_name."`.`status` <> 'deleted' ");
    $r_search = mysql_query($q_search) or die("cant execute search");
    $n_count = mysql_num_rows($r_search);

    return array(
      'posts' => $arr_posts,
      'count_posts' => $n_count
    );
    return $arr_posts;
  }

}


?>
