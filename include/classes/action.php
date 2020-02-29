<?php
date_default_timezone_set("UTC");

if(!class_exists("Lang")) include_once($root_dir.'/include/classes/lang.php');
// 21:00 - 24:00 - 4:00
// 12:00 - 15:00 - 21:00
class Action{
  public $dates = array(
    'from' => '25.08.1019 21:00:00',
    'to' => '27.09.1019 21:00:00'
  );
  public $name = 'Blank';
  public $name_ru = 'Blank';
  public $name_en = 'Blank';
  public $not_enough_users = false;
  public $count_users = 0;
  public $description = 'Blank';
  public $competition_id = 0;
  protected $create_date;

  function __construct(Array $data = array()){
    $this->create_date = gmdate('Y-m-d H:i:s');

    // $status = isset($data['status']) ? $data['status'] : 'active';  // remove this

    $q_date = ("SELECT * FROM `competitions` WHERE `status` = 'active' ORDER BY `create_date` DESC LIMIT 1");
    // $q_date = ("SELECT * FROM `competitions` WHERE `status` = '".$status."' ORDER BY `create_date` DESC LIMIT 1");
    $r_date = mysql_query($q_date) or die(generate_exception(DB_ERROR));
    $n_date = mysql_num_rows($r_date); // or die("cant get numrows search_company");
    if($n_date > 0){
      $id = htmlspecialchars(mysql_result($r_date, 0, "id"));
      $name = htmlspecialchars(mysql_result($r_date, 0, "name_".LANG));
      $name_ru = htmlspecialchars(mysql_result($r_date, 0, "name_ru"));
      $name_en = htmlspecialchars(mysql_result($r_date, 0, "name_en"));
      $description = htmlspecialchars(mysql_result($r_date, 0, "description_".LANG));
      $date_from = htmlspecialchars(mysql_result($r_date, 0, "date_from"));
      $date_to = htmlspecialchars(mysql_result($r_date, 0, "date_to"));

      $this->competition_id = $id;
      $this->name = $name;
      $this->name_ru = $name_ru;
      $this->name_en = $name_en;
      $this->description = $description;
      $this->dates['from'] = $date_from;
      $this->dates['to'] = $date_to;

      $users = $this->get_users();
      $this->count_users = count($users);

      if($this->count_users < 10){
        $this->not_enough_users = true;
      }

    }

  }

  public function get_last_competition(){
    $recipes = array();

    $q_date = ("SELECT * FROM `competitions`
      INNER JOIN `users` ON (`users`.`id` = `competitions`.`winner_id`)
     WHERE `competitions`.`status` = 'done' ORDER BY `competitions`.`create_date` DESC LIMIT 1");
    $r_date = mysql_query($q_date) or die(generate_exception(DB_ERROR));
    $n_date = mysql_num_rows($r_date); // or die("cant get numrows search_company");
    if($n_date > 0){
      $id = htmlspecialchars(mysql_result($r_date, 0, "competitions.id"));
      $name = htmlspecialchars(mysql_result($r_date, 0, "competitions.name_".LANG));
      $description = htmlspecialchars(mysql_result($r_date, 0, "competitions.description_".LANG));
      $winner_id = htmlspecialchars(mysql_result($r_date, 0, "users.id"));
      $winner_name = htmlspecialchars(mysql_result($r_date, 0, "users.login"));


      $q_recipes = ("SELECT * FROM `competitions_posts`
      INNER JOIN `posts` ON (`posts`.`id` = `competitions_posts`.`post_id`)
      WHERE `competitions_posts`.`competition_id` = '".$id."' ORDER BY `competitions_posts`.`count_like` DESC ");
      $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
      $n_recipes = mysql_num_rows($r_recipes); // or die("cant get numrows search_company");
      if($n_recipes > 0){
        for ($i = 0; $i < $n_recipes; $i++) {
          $id = htmlspecialchars(mysql_result($r_recipes, $i, "posts.id"));
          $title = htmlspecialchars(mysql_result($r_recipes, $i, "posts.title_".LANG));
          $content = htmlspecialchars(mysql_result($r_recipes, $i, "posts.content_".LANG));
          $main_image = htmlspecialchars(mysql_result($r_recipes, $i, "posts.main_image"));
          $count_like = htmlspecialchars(mysql_result($r_recipes, $i, "competitions_posts.count_like"));
          $alias = htmlspecialchars(mysql_result($r_recipes, $i, "posts.alias_".LANG));
          $create_date = htmlspecialchars(mysql_result($r_recipes, $i, "posts.create_date"));

          $content = remove_html($content);

          $data = array(
            'id' => $id,
            'title' => $title,
            'alias' => $alias,
            'content' => $content,
            'count_like' => $count_like,
            'image' => $main_image,
            'create_date' => $create_date
          );

          array_push($recipes,$data);

        }
      }

    } else return false;

    return array(
      'id' => $id,
      'name' => $name,
      'description' => $description,
      'winner_id' => $winner_id,
      'winner_name' => $winner_name,
      'recipes' => $recipes
    );

  }

  public function check_end(){
    $current_date = time();
    $end_date = strtotime($this->dates['to']);

    if($this->competition_id == 0) {
      echo "No active contest";
      return false;
    }

    if($current_date >= $end_date){

      $q_user_info = ("UPDATE `competitions` SET `status` = 'done'
       WHERE `id` = '".$this->competition_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

      if($this->not_enough_users === true) {
        echo "Not enough users";
        return false;
      }

      $win_users = $this->get_users(array(
        'get_win_user' => true
      ));

      if(count($win_users) > 0){
        $root_dir = $_SERVER['DOCUMENT_ROOT'];

        if(!class_exists('Noti')) include_once($root_dir.'/include/classes/noti.php');

        $init_noti = new Noti();

        for ($u = 0,$p = 1; $u < count($win_users); $u++,$p++) {
          $win_user = $win_users[$u];

          $winner_id = $win_user['id'];


          $q_query = ("INSERT INTO
          `competitions_winners`
          (`contest_id`,
          `user_id`,
          `place`,
          `create_date`)
          values(
          '".$this->competition_id."',
          '".$winner_id."',
          '".$p."',
          '".$this->create_date."')");
          mysql_query($q_query) or die(generate_exception(DB_ERROR));

          if($p == 1){

            $q_user_info = ("UPDATE `competitions` SET
              `winner_id` = '".$winner_id."'
             WHERE `id` = '".$this->competition_id."'");
            mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

            $winner_recipes = $this->get_user_recipes($winner_id);

            for ($i = 0; $i < count($winner_recipes); $i++) {
              $recipe_id = (int)$winner_recipes[$i]['id'];
              $count_like = (int)$winner_recipes[$i]['count_like'];

              $q_query = ("INSERT INTO
              `competitions_posts`
              (`competition_id`,
              `post_id`,
              `count_like`,
              `create_date`)
              values(
              '".$this->competition_id."',
              '".$recipe_id."',
              '".$count_like."',
              '".$this->create_date."')");
              mysql_query($q_query) or die(generate_exception(DB_ERROR));

            }

          }

          $init_noti->send(array(
            'user_id' => $winner_id,
            'content_ru' => 'Поздравляем '.$win_user['login'].', вы выиграли в конкурсе "'.$this->name_ru.'" и заняли '.$p.' место, в ближайшее время мы свяжемся с вами по email адресу который вы указали при регистрации для вручения приза.',
            'content_en' => 'Congratulations '.$win_user['login'].', you won the contest "'.$this->name_en.'" and took '.$p.' place, soon we will contact you by email address you provided during registration for the prize.',
          ));

        }

      }

    }
  }

  public function get_users(Array $data = array()){
    $users = array();

    $date_from = date('Y-m-d',strtotime($this->dates['from']));
    $date_to = date('Y-m-d',strtotime($this->dates['to']));
    $user_id = isset($data['user_id']) ? $data['user_id'] : 0;
    $get_win_user = isset($data['get_win_user']) ? $data['get_win_user'] : false;

    $get_win_user = $get_win_user === true ? ' LIMIT 3' : '';
    $sort_user = $user_id > 0 ? " AND `user_recipes`.`user_id` = '".$user_id."' " : '';

    $sort_date = " AND (DATE_FORMAT(FROM_UNIXTIME(`posts`.`create_date`),'%Y-%m-%d') >= '".$date_from."'
     AND DATE_FORMAT(FROM_UNIXTIME(`posts`.`create_date`),'%Y-%m-%d') <= '".$date_to."')";

    $q_users = ("SELECT
      `users`.`id`,
      `users`.`login`,
      `users`.`name`,
      `users`.`last_name`,
      `users`.`middle_name`,
      `users`.`main_image`,
      COUNT(`user_recipes`.`id`) AS count_recipes,
      SUM(`posts`.`count_like`) AS count_like
    FROM `users`
    INNER JOIN `user_recipes` ON (`user_recipes`.`user_id` = `users`.`id` AND `user_recipes`.`status` = 'published')
    INNER JOIN `posts` ON (`posts`.`id` = `user_recipes`.`parent_id`)
    WHERE `posts`.`status` <> 'deleted' ".$sort_date.$sort_user."
    GROUP BY `users`.`id` ORDER BY count_like DESC,count_recipes DESC,`users`.`last_name` DESC ".$get_win_user);
    $r_users = mysql_query($q_users) or die(generate_exception(DB_ERROR));
    $n_users = mysql_num_rows($r_users); // or die("cant get numrows search_company");
    if($n_users > 0){
      for ($i = 0; $i < $n_users; $i++) {
        $id = htmlspecialchars(mysql_result($r_users, $i, "users.id"));
        $login = htmlspecialchars(mysql_result($r_users, $i, "users.login"));
        $name = htmlspecialchars(mysql_result($r_users, $i, "users.name"));
        $last_name = htmlspecialchars(mysql_result($r_users, $i, "users.last_name"));
        $middle_name = htmlspecialchars(mysql_result($r_users, $i, "users.middle_name"));
        $main_image = htmlspecialchars(mysql_result($r_users, $i, "users.main_image"));
        $count_recipes = htmlspecialchars(mysql_result($r_users, $i, "count_recipes"));
        $count_like = (int)htmlspecialchars(mysql_result($r_users, $i, "count_like"));

        if($user_id == 0) if($count_recipes < 5) continue;

        $user_info = array(
          'id' => $id,
          'login' => $login,
          'name' => $name,
          'last_name' => $last_name,
          'middle_name' => $middle_name,
          'main_image' => $main_image,
          'count_recipes' => $count_recipes,
          'count_like' => $count_like
        );

        array_push($users,$user_info);

      }
    } else return false;

    return $users;

  }

  public function get_user_recipes($user_id){
    $recipes = array();

    $date_from = date('Y-m-d',strtotime($this->dates['from']));
    $date_to = date('Y-m-d',strtotime($this->dates['to']));

    $sort_date = " AND (DATE_FORMAT(FROM_UNIXTIME(`posts`.`create_date`),'%Y-%m-%d') >= '".$date_from."'
    AND DATE_FORMAT(FROM_UNIXTIME(`posts`.`create_date`),'%Y-%m-%d') <= '".$date_to."')";


    $q_recipes = ("SELECT * FROM `user_recipes`
    INNER JOIN `users` ON (`users`.`id` = `user_recipes`.`user_id`)
    INNER JOIN `posts` ON (`posts`.`id` = `user_recipes`.`parent_id`)
    WHERE `user_recipes`.`deleted` = '0' AND `users`.`id` = '".$user_id."'
    AND `user_recipes`.`status` = 'published' ".$sort_date."
     ORDER BY `posts`.`count_like` DESC ");
    $r_recipes = mysql_query($q_recipes) or die(generate_exception(DB_ERROR));
    $n_recipes = mysql_num_rows($r_recipes); // or die("cant get numrows search_company");
    if($n_recipes > 0){
      for ($i = 0; $i < $n_recipes; $i++) {
        $id = htmlspecialchars(mysql_result($r_recipes, $i, "posts.id"));
        $title = htmlspecialchars(mysql_result($r_recipes, $i, "posts.title_".LANG));
        $content = htmlspecialchars(mysql_result($r_recipes, $i, "posts.content_".LANG));
        $user_id = htmlspecialchars(mysql_result($r_recipes, $i, "users.id"));
        $user_name = htmlspecialchars(mysql_result($r_recipes, $i, "users.name"));
        $status = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.status"));
        $rejection_reason = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.rejection_reason"));
        $main_image = htmlspecialchars(mysql_result($r_recipes, $i, "posts.main_image"));
        $count_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_like"));
        $count_not_like = htmlspecialchars(mysql_result($r_recipes, $i, "posts.count_not_like"));
        $lang = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.lang"));
        $alias = htmlspecialchars(mysql_result($r_recipes, $i, "posts.alias_".LANG));
        $create_date = htmlspecialchars(mysql_result($r_recipes, $i, "user_recipes.create_date"));

        $content = remove_html($content);

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

        array_push($recipes,$data);

      }
    }
    return $recipes;
  }

}


?>
