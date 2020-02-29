<?php
if(!class_exists("Auth")) include_once($root_dir.'/include/classes/auth.php');

class User{
  public $user_id;
  public $sex_names;
  protected $create_date;


  function __construct(Array $data = array()){
    GLOBAL $l;
    $this->create_date = gmdate('Y-m-d H:i:s');
    $this->user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
    $this->sex_names = array(
      '1' => $l->male,
      '2' => $l->female
    );

  }


  public function get_user_info($user_id = 0){
    $user_info = array();
    $user_id = $user_id == 0 ? $this->user_id : $user_id;

    $q_user = ("SELECT * FROM `users` WHERE `id` = '".$user_id."'");
    $r_user = mysql_query($q_user) or die(generate_exception(DB_ERROR));
    $n_user = mysql_numrows($r_user); // or die("cant get numrows search_company");
    if($n_user > 0){
      $login = htmlspecialchars(mysql_result($r_user, 0, "login"));
      $name = htmlspecialchars(mysql_result($r_user, 0, "name"));
      $last_name = htmlspecialchars(mysql_result($r_user, 0, "last_name"));
      $middle_name = htmlspecialchars(mysql_result($r_user, 0, "middle_name"));
      $main_image = htmlspecialchars(mysql_result($r_user, 0, "main_image"));
      $email = htmlspecialchars(mysql_result($r_user, 0, "email"));
      $salt = htmlspecialchars(mysql_result($r_user, 0, "salt"));
      $password = htmlspecialchars(mysql_result($r_user, 0, "password"));
      $birth_date = htmlspecialchars(mysql_result($r_user, 0, "birth_date"));
      $about_me = htmlspecialchars(mysql_result($r_user, 0, "about_me"));
      $sex = htmlspecialchars(mysql_result($r_user, 0, "sex"));
      $recovery_hash = htmlspecialchars(mysql_result($r_user, 0, "recovery_hash"));
      $last_login = htmlspecialchars(mysql_result($r_user, 0, "last_login"));
      $admin = (int)htmlspecialchars(mysql_result($r_user, 0, "admin"));


      $user_info = array(
        'login' => $login,
        'name' => $name,
        'last_name' => $last_name,
        'middle_name' => $middle_name,
        'main_image' => $main_image,
        'email' => $email,
        'salt' => $salt,
        'birth_date' => $birth_date,
        'about_me' => $about_me,
        'sex' => $sex,
        'admin' => $admin,
        'password' => $password,
        'recovery_hash' => $recovery_hash,
        'last_login' => $last_login
      );

    } else return false;

    return $user_info;

  }

  public function save_profile(Array $data = array()){
    GLOBAL $l;
    $name = $data['name'];
    $last_name = $data['last_name'];
    $middle_name = $data['middle_name'];
    $about_me = $data['about_me'];
    $b_day = $data['b_day'];
    $b_month = $data['b_month'];
    $b_year = $data['b_year'];
    $sex = $data['sex'];


    if(empty($name)) generate_exception($l->enter_name);

    if($sex == 'female') $sex = 2;
    else if($sex == 'male') $sex = 1;
    else $sex = 0;

    $birth_date = '0000-00-00';

    if((int)$b_day > 0 && (int)$b_month > 0 && (int)$b_year > 0){
      $birth_date = date('Y-m-d',strtotime($b_day.'-'.$b_month.'-'.$b_year));
    }

    $name = mysql_real_escape_string($name);
    $about_me = mysql_real_escape_string($about_me);
    $last_name = mysql_real_escape_string($last_name);
    $middle_name = mysql_real_escape_string($middle_name);



    $q_user_info = ("UPDATE `users` SET
      `name` = '".$name."',
      `last_name` = '".$last_name."',
      `middle_name` = '".$middle_name."',
      `about_me` = '".$about_me."',
      `birth_date` = '".$birth_date."',
      `sex` = '".$sex."'
     WHERE `id` = '".$this->user_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));





    return true;

  }

  public function save_new_password($data){
    GLOBAL $l;
    $r_user_id = isset($data['user_id']) ? $data['user_id'] : 0;
    $hash = isset($data['hash']) ? $data['hash'] : '';
    $is_recovering = $r_user_id > 0 && !empty($hash);

    $old_password = isset($data['old_password']) ? $data['old_password'] : '';
    $new_password = $data['new_password'];
    $repeat_new_password = $data['repeat_new_password'];


    if($is_recovering === false) if(empty($old_password)) generate_exception($l->enter_old_password);
    if(empty($new_password)) generate_exception($l->enter_new_password);
    if(strlen($new_password) < 4) generate_exception($l->short_password);
    if(empty($repeat_new_password)) generate_exception($l->enter_re_new_password);
    if($new_password != $repeat_new_password) generate_exception($l->pass_dont_match);

    $user_id = $is_recovering === true ? $r_user_id : $this->user_id;

    $user_info = $this->get_user_info($user_id);
    if($user_info === false) generate_exception($l->user_not_found);

    if($is_recovering === true){

      if($user_info['recovery_hash'] != $hash) generate_exception($l->recovering_error);

      $q_user_info = ("UPDATE `users` SET `recovery_hash` = '' WHERE `id` = '".$r_user_id."'");
      mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    } else {

      if($user_info['password'] != md5($user_info['salt'].$old_password.(Auth::$salt)))
        generate_exception($l->invalid_old_password);

    }

    $salt = Auth::generate_salt();
    $md5_password = md5($salt.$new_password.(Auth::$salt));


    $q_user_info = ("UPDATE `users` SET
    `password` = '".$md5_password."',
    `salt` = '".$salt."'
    WHERE `id` = '".$user_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    return true;

  }

  public function check_capcha($data){
    GLOBAL $l;

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


}

?>
