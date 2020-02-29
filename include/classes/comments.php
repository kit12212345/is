<?php



// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
if(!class_exists("Lang")) include_once($root_dir.'/include/classes/lang.php');
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class Comments extends AdmCommon{

  function __construct($argument){

  }

  public static function remove_html($str){
    $search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i",
    "'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
    $replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
    return preg_replace($search, $replace, $str);
  }

  private static function clear_str($str){
    $str = prc_to_str($str);
    // $str = htmlentities($str);
    // $str = nl2br($str);
    $str = mysql_real_escape_string($str);
    return $str;
  }
  public static function check_capcha($data){
    GLOBAl $l;
    // Проверим гугл каптчу, отправим POST запрос гуглу и получим результат
    $gipadress=$_SERVER['REMOTE_ADDR'];
    $grecaptcha=$data['g-recaptcha-response'];
    $postdata = http_build_query(
      array(
        'secret' => '6LdBDj4UAAAAAEc-2RiAhFxx2mYDGSJG5PPq4E3w',
        'response' => $grecaptcha,
        'remoteip' => $gipadress
      )
    );
    $opts = array('http' =>
    array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => $postdata
    )
  );



  $gcontents = stream_context_create($opts);

  $gresults = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $gcontents);

  $jsonresults = json_decode($gresults);

  if ($jsonresults->success === false) {
    generate_exception($l->didnt_confirm_robot);
  }
}

  public function add_comment($data){
    $user_session = $_SESSION['logged_user'];
    $comments_table_name = self::$comments_table_name;
    $posts_table_name = self::$posts_table_name;

    self::check_capcha($data);

    $post_count_comments = 1;
    $comment_status = 'approved';
    $user_id = (int)$user_session['id'];

    $user_ip = getRealIP();
    $comment_date_gmt = gmdate('Y-m-d H:i:s');
    $comment_create_date = strtotime($comment_date_gmt);
    $comment_date = gmdate('Y-m-d H:i:s',strtotime('+'.$_SESSION['count_time_offset'].' hours'));

    $post_id = (int)$data['post_id'];
    $user_name = $data['user_name'];
    $user_email = $data['user_email'];
    $user_site = $data['user_site'];
    $content = $data['content'];

    if($user_id > 0){
      $user_name = $user_session['name'];
      $user_email = $user_session['email'];
    }

    $parent_comment = (int)$data['parent_comment'];

    $q_enable_comments = ("SELECT * FROM `".$posts_table_name."` WHERE `".$posts_table_name."`.`id` = '".$post_id."'");
    $r_enable_comments = mysql_query($q_enable_comments) or die("cant execute enable_comments");
    $n_enable_comments = mysql_num_rows($r_enable_comments);
    if($n_enable_comments > 0){
      $count_comments = (int)htmlspecialchars(mysql_result($r_enable_comments, 0, $posts_table_name.".count_comments"));
      $comments_status = htmlspecialchars(mysql_result($r_enable_comments, 0, $posts_table_name.".comments_status"));

      if($comments_status == 'close') generate_exception($l->cant_comment);


      $count_comments++;

      $post_count_comments = $count_comments;

    } else generate_exception($p->post_not_found);

    // $content = self::remove_html($content);

    $origin_content = $content;

    if(empty($user_name)) generate_exception($l->enter_name);
    if(empty($user_email)) generate_exception($l->j_enter_email);
    if(empty($content)) generate_exception($l->empty_comment);

    $user_name = self::clear_str($user_name);
    $user_email = self::clear_str($user_email);
    $content = self::clear_str($content);



    $comment_id = self::insert_data(array(
      'table_name' => $comments_table_name,
      'fields' => array(
        'post_id' => $post_id,
        'user_id' => $user_id,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_site' => $user_site,
        'user_ip' => $user_ip,
        'parent_comment' => $parent_comment,
        'content' => $content,
        'status' => $comment_status,
        'comment_date' => $comment_date,
        'comment_date_gmt' => $comment_date_gmt,
        'create_date' => $comment_create_date
      )
    ));

    self::update_data(array(
      'table_name' => $posts_table_name,
      'fields' => array(
        'count_comments' => $post_count_comments
      ),
      'where' => array(
        'id' => $post_id
      )
    ));

    $q_count_comments = ("SELECT * FROM `".$comments_table_name."`
    WHERE `".$comments_table_name."`.`post_id` = '".$post_id."' AND `".$comments_table_name."`.`status` = 'approved'");
    $r_count_comments = mysql_query($q_count_comments) or die("cant execute posts");
    $count_comments = mysql_num_rows($r_count_comments);


    $comment_create_date = strtotime($comment_date_gmt);


    $html .= '<div class="comment_item">';
      $html .= '<div class="float_l ci_image">';
        $html .= '<img src="http://history55.ru/sites/default/files/noavatar.png" alt="">';
      $html .= '</div>';
      $html .= '<div class="ci_cont">';
        $html .= '<div class="ci_user_name">';
          $html .= '<strong>'.$user_name.'</strong>';
        $html .= '</div>';
        $html .= '<div class="ci_desc">'.$origin_content.'</div>';
        $html .= '<div class="ci_date text_right">';
          $html .= '<small>Отправлен '.date('d.m.Y в H:i',$comment_create_date + $_SESSION['time_offset']).'</small>';
        $html .= '</div>';
      $html .= '</div>';
    $html .= '</div>';


    return array(
      'html' => $html,
      'count_comments' => $count_comments
    );

  }

  public static function post_minus_comment($comment_id){

  }


  public static function comment_get_user_name($comment_id){
    $comments_table_name = static::$comments_table_name;
    $q_user_name = ("SELECT * FROM `".$comments_table_name."` WHERE `".$comments_table_name."`.`id` = '".$comment_id."'");
    $r_user_name = mysql_query($q_user_name) or die("cant execute posts");
    $n_user_name = mysql_num_rows($r_user_name);
    if($n_user_name > 0){
      $user_name = htmlspecialchars(mysql_result($r_user_name, 0, $comments_table_name.".user_name"));
      return $user_name;
    }
    return 'Без имени';
  }

  public static function get_comments($data){
    $posts_table_name = static::$posts_table_name;
    $comments_table_name = static::$comments_table_name;
    $users_table_name = static::$users_table_name;

    $sort_by_status = $data['status'];
    $post_id = (int)$data['post_id'];

    $sort_by_status = $sort_by_status == 'basket' ? 'deleted' : $sort_by_status;
    $query_sort_post = '';

    $query_sort_by_status = ' AND `'.$comments_table_name.'`.`status` = \''.$sort_by_status.'\'';
    $query_sort_by_status = $sort_by_status == 'all' || empty($sort_by_status) ?
    ' AND `'.$comments_table_name.'`.`status` <> \'deleted\'' : $query_sort_by_status;

    if($post_id > 0){
      $query_sort_post = ' AND `'.$comments_table_name.'`.`post_id` = \''.$post_id.'\'';
    }

    $arr_comments = array();

    $q_comments = ("SELECT * FROM `".$comments_table_name."`
    INNER JOIN `".$posts_table_name."` ON (`".$posts_table_name."`.`id` = `".$comments_table_name."`.`post_id`)
    LEFT JOIN `".$users_table_name."` ON (`".$users_table_name."`.`id` = `".$comments_table_name."`.`user_id`)
    WHERE `".$comments_table_name."`.`create_date` > '0'
    ".$query_sort_by_status.$query_sort_post."
    ORDER BY `".$comments_table_name."`.`comment_date_gmt` DESC");
    $r_comments = mysql_query($q_comments) or die("cant execute posts");
    $n_comments = mysql_num_rows($r_comments);
    if($n_comments > 0){
      for ($i = 0; $i < $n_comments; $i++) {
        $comment_parent_user_name = '';
        $comment_id = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".id"));
        $comment_status = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".status"));
        $comment_content = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".content"));
        $comment_parent = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".parent_comment"));

        $comment_user_id = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".user_id"));
        $comment_user_name = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".user_name"));
        $comment_user_email = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".user_email"));
        $comment_user_site = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".user_site"));
        $comment_user_ip = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".user_ip"));

        $comment_post_id = htmlspecialchars(mysql_result($r_comments, $i, $posts_table_name.".id"));
        $comment_post_name = htmlspecialchars(mysql_result($r_comments, $i, $posts_table_name.".title"));
        $comment_post_count_comments = htmlspecialchars(mysql_result($r_comments, $i, $posts_table_name.".count_comments"));
        $comment_create_date = htmlspecialchars(mysql_result($r_comments, $i, $comments_table_name.".comment_date_gmt"));

        if($comment_parent > 0){
          $comment_parent_user_name = static::comment_get_user_name($comment_parent);
        }

        array_push($arr_comments,array(
          'id' => $comment_id,
          'status' => $comment_status,
          'content' => $comment_content,
          'parent_comment' => $comment_parent,
          'parent_comment_user_name' => $comment_parent_user_name,
          'user_id' => $comment_user_id,
          'user_name' => $comment_user_name,
          'user_email' => $comment_user_email,
          'user_site' => $comment_user_site,
          'user_ip' => $comment_user_ip,
          'post_id' => $comment_post_id,
          'post_title' => $comment_post_name,
          'post_count_comments' => $comment_post_count_comments,
          'create_date' => $comment_create_date
        ));

      }
    }

    return $arr_comments;

  }



}


?>
