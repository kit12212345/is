<?php

if(!class_exists('User')) include($root_dir.'/include/classes/user.php');


class Auth extends User{
  public static $salt = 'kit1228';
  protected $create_date;

  function __construct(Array $data = array()){
    $this->create_date = gmdate('Y-m-d H:i:s');
    parent::__construct();
  }

  public function login(Array $data = array()){
    GLOBAL $l;

    $login = $data['login'];
    $password = $data['password'];

    $login = trim($login);
    $password = trim($password);

    if(empty($login)) generate_exception($l->enter_username);
    if(empty($password)) generate_exception($l->enter_password);

    $login = mysql_real_escape_string($login);
    $password = mysql_real_escape_string($password);

    $q_user = ("SELECT `id`,`password`,`salt`,`confirmed` FROM `users` WHERE `login` = '".$login."'");
    $r_user = mysql_query($q_user) or die(generate_exception(DB_ERROR));
    $n_user = mysql_numrows($r_user); // or die("cant get numrows search_company");
    if($n_user > 0){
      $user_id = htmlspecialchars(mysql_result($r_user, 0, "id"));
      $user_password = htmlspecialchars(mysql_result($r_user, 0, "password"));
      $user_salt = htmlspecialchars(mysql_result($r_user, 0, "salt"));
      $confirmed = htmlspecialchars(mysql_result($r_user, 0, "confirmed"));

      $md5_password = md5($user_salt.$password.(self::$salt));

      if($md5_password != $user_password) generate_exception($l->wrong_password);

      return $this->start_session($user_id);

    } else generate_exception($l->u_nickname_not_found);

  }

  public function registration(Array $data = array()){
    GLOBAL $l;

    $name = $data['name'];
    $dir = $data['dir'];
    $login = $data['login'];
    $email = $data['email'];
    $password = $data['password'];
    $repeat_password = $data['repeat_password'];

    $login = trim($login);
    $email = trim($email);
    $password = trim($password);
    $repeat_password = trim($repeat_password);

    if(empty($name)) generate_exception($l->enter_name);
    if(empty($login)) generate_exception($l->enter_username);
    if(strlen($login) < 4) generate_exception($l->short_login);
    if(!preg_match("/[A-Za-z0-9_-]+$/i", $login)) generate_exception($l->wrong_login);
    if(empty($email)) generate_exception($l->enter_email);
    if($this->valid_email($email) === 0) generate_exception($l->wrong_email);
    if(empty($password)) generate_exception($l->enter_password);
    if(strlen($password) < 4) generate_exception($l->short_password);
    if(empty($repeat_password)) generate_exception($l->enter_re_password);
    if($password != $repeat_password) generate_exception($l->pass_dont_match);
    if($this->busy_login($login) === true) generate_exception($l->busy_login);
    if($this->busy_email($email) === true) generate_exception($l->busy_email);

    /* RECAPCHA*/

    $this->check_capcha($data);

    /* END RECAPCHA*/

    $name = mysql_real_escape_string($name);
    $login = mysql_real_escape_string($login);
    $email = mysql_real_escape_string($email);
    $password = mysql_real_escape_string($password);

    $salt = self::generate_salt();

    $md5_password = md5($salt.$password.(self::$salt));

    $activate_hash = md5(rand(1,1000000).$email.time().rand(1.1000000));

    $ip = $this->get_ip();

    $q_query = ("INSERT INTO
    `users`
    (
    `login`,
    `name`,
    `email`,
    `password`,
    `salt`,
    `activate_hash`,
    `user_ip`,
    `create_date`)
    values(
    '".$login."',
    '".$name."',
    '".$email."',
    '".$md5_password."',
    '".$salt."',
    '".$activate_hash."',
    '".$ip."',
    '".$this->create_date."')");
    mysql_query($q_query) or die(generate_exception(DB_ERROR));


    $user_id = mysql_insert_id();

    $this->send_registration_email(array(
      'name' => $name,
      'email' => $email,
      'activate_hash' => $activate_hash,
      'id' => $user_id
    ));

    if($dir == 'from_contest'){
      $this->start_session($user_id);
    }

    return array(
    'user_id' => $user_id
    );

  }

  public function recovery_password(Array $data = array()){
    GLOBAL $l;
    $email = $data['email'];
    $email = trim($email);

    if(empty($email)) generate_exception($l->enter_email);
    if($this->valid_email($email) === 0) generate_exception($l->wrong_email);

    /* RECAPCHA*/
    $this->check_capcha($data);
    /* END RECAPCHA*/

    $email = mysql_real_escape_string($email);

    $q_user = ("SELECT `id`,`name` FROM `users` WHERE `email` = '".$email."'");
    $r_user = mysql_query($q_user) or die(generate_exception(DB_ERROR));
    $n_user = mysql_numrows($r_user); // or die("cant get numrows search_company");
    if($n_user > 0){
      $user_id = htmlspecialchars(mysql_result($r_user, 0, "id"));
      $user_name = htmlspecialchars(mysql_result($r_user, 0, "name"));

      $hash = md5(self::$salt.rand(1,10000000).time().rand(1,1000000));

      $q_user_info = ("UPDATE `users` SET `recovery_hash` = '".$hash."' WHERE `id` = '".$user_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

      return $this->send_recovery_password_email(array(
        'id' => $user_id,
        'name' => $user_name,
        'email' => $email,
        'hash' => $hash
      ));

    } else generate_exception($l->u_email_not_found);

    return true;


  }

  public function start_session($user_id){
    GLOBAL $l;

    $user_info = $this->get_user_info($user_id);

    if($user_info === false) generate_exception($l->user_not_found);

    /* ADD TEMPBOOKMARKS*/
    $temp_bookmarks = $_COOKIE['bookmarks'];
    if(!empty($temp_bookmarks)){

      if(!class_exists('UserFavRecipes')) include($_SERVER['DOCUMENT_ROOT'].'/include/classes/fav_recipes.php');

      $init_fav_recipes = new UserFavRecipes(array(
        'user_id' => $user_id
      ));

      $temp_bookmarks = json_decode($temp_bookmarks);
      for ($i = 0; $i < count($temp_bookmarks); $i++){
        $init_fav_recipes->bookmark(array(
          'id' => (int)$temp_bookmarks[$i],
          'type' => 'add'
        ));
      }
      // setcookie("bookmarks","",time()-3600,"/");
    }
    /*END ADD TEMPBOOKMARKS*/


    $last_login = $user_info['last_login'];

    $q_user_info = ("UPDATE `users` SET
    `prev_login` = '".$last_login."',
    `last_login` = '".$this->create_date."'
    WHERE `id` = '".$user_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    $_SESSION['logged_user'] = array(
      'id' => $user_id,
      'login' => $user_info['login'],
      'admin' => $user_info['admin'],
      'name' => $user_info['name'],
      'email' => $user_info['email']
    );

    return true;
  }

  public function activate_user($user_id,$hash){

    $q_user = ("SELECT `confirmed` FROM `users` WHERE `id` = '".$user_id."' AND `activate_hash` = '".$hash."'");
    $r_user = mysql_query($q_user) or die(generate_exception(DB_ERROR));
    $n_user = mysql_numrows($r_user); // or die("cant get numrows search_company");
    if($n_user > 0){
      $confirmed = htmlspecialchars(mysql_result($r_user, 0, "confirmed"));

      if($confirmed > 0) return 'confirmed';

      $q_user_info = ("UPDATE `users` SET `confirmed` = '1' WHERE `id` = '".$user_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    } else return false;

    return true;

  }


  public static function generate_salt(){
    $salt = '';
    $latters = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r',
    's','t','u','v','w','x','y','z');

    for ($i = 0; $i < 6; $i++) {
      $letter = $latters[rand(0,count($latters) - 1)];
      $salt .= rand(0,1) > 0 ? strtoupper($letter) : $letter;
      $salt .= rand(0,1) > 0 ? rand(1,9) : '';
    }

    return $salt;
  }

  public function send_registration_email($data = array()){
    GLOBAL $l;
    $html = '';

    $link = $_SERVER['HTTP_X_FORWARDED_PROTO'].'://'.$_SERVER['SERVER_NAME'].'/activate_user?u='.$data['id'].'&h='.$data['activate_hash'];

    $to = $data['email'];

    $subject = $l->reg_confirm;

    $html .= '<html lang="'.(LANG).'">';
    $html .= '<head>';
    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $html .= '<title>'.($l->reg_confirm).'</title>';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<table style="width: 100%;">';
    $html .= '<tbody>';
    $html .= '<tr>';
    $html .= '<td style="background: #f44336; padding: 20px 10px;">';
    $html .= '<span style="font-size: 26px; color: #fff;">Hluble.com</span>&nbsp;';
    $html .= '<div style="color: #fff;">'.($l->w_of_rec).'</div>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td>';
    $html .= ' <p>'.($l->hello).' '.$data['name'].', '.($l->signed_up_1).' <a href="'.$link.'">'.($l->signed_up_2).'</a> '.($l->signed_up_3).'</p> </br>';
    $html .= ' <p style="text-align: center;"><a href="'.$link.'" style="text-decoration: none; display: inline-block; background: #f44336; padding: 7px 10px; color: #fff; border: 1px solid #e53935;">'.($l->confirm_reg).'</a></p></br>';
    $html .= ' <p>'.($l->signed_up_4).'</p> </br>';
    $html .= ' <p></p> </br>';
    $html .= ' <p>Hluble.com © 2017 - '.date('Y').' '.($l->rights).'</p> </br>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body>';
    $html .= '</html>';
    // echo $html;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: Hluble.com <support@hluble.com>\r\n";
    $headers .= "Reply-To: support@hluble.com\r\n";

    mail($to, $subject, $html, $headers);

  }

  public function send_recovery_password_email($data = array()){
    GLOBAL $l;
    $html = '';

    $link = $_SERVER['HTTP_X_FORWARDED_PROTO'].'://'.$_SERVER['SERVER_NAME'].'/recovery_password?u='.$data['id'].'&h='.$data['hash'];

    $to = $data['email'];

    $subject = $l->recovering_password;

    $html .= '<html lang="'.(LANG).'">';
    $html .= '<head>';
    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $html .= '<title>'.($l->recovering_password).'</title>';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<table style="width: 100%;">';
    $html .= '<tbody>';
    $html .= '<tr>';
    $html .= '<td style="background: #f44336; padding: 20px 10px;">';
    $html .= '<span style="font-size: 26px; color: #fff;">Hluble.com</span>&nbsp;';
    $html .= '<div style="color: #fff;">'.($l->w_of_rec).'</div>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td>';
    $html .= ' <p>'.($l->hello).' '.$data['name'].', '.($l->rec_pass_1).' <a href="'.$link.'">'.($l->rec_pass_2).'</a> '.($l->rec_pass_3).'</p> </br>';
    $html .= ' <p style="text-align: center;"><a href="'.$link.'" style="text-decoration: none; display: inline-block; background: #f44336; padding: 7px 10px; color: #fff; border: 1px solid #e53935;">'.($l->rec_the_pass).'</a></p></br>';
    $html .= ' <p>'.($l->rec_pass_4).'</p> </br>';
    $html .= ' <p></p> </br>';
    $html .= ' <p>Hluble.com © 2017 - '.date('Y').' '.($l->rights).'</p> </br>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body>';
    $html .= '</html>';
    // echo $html;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: Hluble.com <support@hluble.com>\r\n";
    $headers .= "Reply-To: support@hluble.com\r\n";

    mail($to, $subject, $html, $headers);

  }

  public function valid_email($email){
    return preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email);
  }

  public function busy_email($email){
    $email = mysql_real_escape_string($email);
    $q_check = ("SELECT `email` FROM  `users` WHERE `email` = '".$email."'");
    $r_check = mysql_query($q_check) or die(generate_exception(DB_ERROR));
    $n_check = mysql_num_rows($r_check); // or die("cant get numrows query");
    if($n_check > 0) return true;
    return false;
  }

  public function busy_login($login){
    $login = mysql_real_escape_string($login);
    $q_check = ("SELECT `login` FROM  `users` WHERE `login` = '".$login."'");
    $r_check = mysql_query($q_check) or die(generate_exception(DB_ERROR));
    $n_check = mysql_num_rows($r_check); // or die("cant get numrows query");
    if($n_check > 0) return true;
    return false;
  }


  public function get_ip(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

}

?>
