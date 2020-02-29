<?php
// class DB{
//   protected static $db_connect;
//   private static $db_hostname="localhost";
//   private static $db_name="a0167398_hluble";
//   private static $db_username="a0167398_hluble";
//   private static $db_password="kit123";
//
//   public function __construct(){
//     try {
//       self::$db_connect = new PDO(
//         'mysql:dbname='.self::$db_name.';host='.self::$db_hostname,
//         self::$db_username,
//         self::$db_password,
//         array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
//         PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET 'utf8'",
//         PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION collation_connection = 'utf8_general_ci'")
//       );
//     }
//     catch (PDOException $e) {
//       die('Ошибка подключения: ' . $e->getMessage());
//     }
//   }
//
//
//   public static function run($sql, $params = [], $const = ''){
//     $query = self::$db_connect->prepare($sql);
//     if (array_key_exists(0, $params)) {
//       $i = 1;
//       foreach ($params as $value) {
//         $query->bindValue($i++, $value, self::type($value));
//       }
//     } else {
//       foreach ($params as $key => $value) {
//         $query->bindValue($key, $value, self::type($value));
//       }
//     }
//     $exec = $query->execute();
//
//     $const = empty($const) ? PDO::FETCH_ASSOC : $const;
//     if($exec){
//       $result = $query->fetchAll($const);
//       $num_rows = count($result);
//       return array(
//         'num_rows' => $num_rows,
//         'result' => $result
//       );
//     } else{
//       $error_info = $query->errorInfo();
//       die($error_info[2]);
//     }
//
//     return $query;
//   }
//
//   public static function type($value){
//     if (is_int($value)) {
//       $type = PDO::PARAM_INT;
//     } elseif (is_string($value) || is_float($value)) {
//       $type = PDO::PARAM_STR;
//     } elseif (is_bool($value)) {
//       $type = PDO::PARAM_BOOL;
//     } elseif (is_null($value)) {
//       $type = PDO::PARAM_NULL;
//     } else {
//       $type = false;
//     }
//     return $type;
//   }
//
//   public static final function select($sql,$const = ''){
//     $sql = self::$db_connect->quote($sql);
//     $sth = self::$db_connect->prepare($sql);
//     $exec = $sth->execute();
//     $const = empty($const) ? PDO::FETCH_ASSOC : $const;
//     if($exec){
//       $result = $sth->fetchAll($const);
//       $num_rows = count($result);
//       return array(
//         'num_rows' => $num_rows,
//         'result' => $result
//       );
//     } else{
//       $error_info = $sth->errorInfo();
//       die($error_info[2]);
//     }
//   }
//
//   public static final function update($sql){
//     $sth = self::$db_connect->prepare($sql);
//     $exec = $sth->execute();
//     if($exec){
//       return true;
//     } else{
//       $error_info = $sth->errorInfo();
//       die($error_info[2]);
//     }
//   }
//
//   public static final function insert($sql){
//     $sth = self::$db_connect->prepare($sql);
//     $exec = $sth->execute();
//     if($exec){
//       $insert_id = self::$db_connect->lastInsertId();
//       return $insert_id;
//     } else{
//       $error_info = $sth->errorInfo();
//       die($error_info[2]);
//     }
//   }
//
//   public static final function delete($sql){
//     $sth = self::$db_connect->prepare($sql);
//     $exec = $sth->execute();
//     if($exec){
//       return true;
//     } else{
//       $error_info = $sth->errorInfo();
//       die($error_info[2]);
//     }
//   }
//
// }
//
// new DB();


$db_hostname="localhost";
$db_name="is";
$db_username="kit";
$db_password="kit123";
define(DB_ERROR,'Произошла неожиданная ошибка, повторите действие позже');
//текущее время
$now=time();


$link = @mysql_connect($db_hostname, $db_username, $db_password);
if (!$link) {
    die('Ошибка соединения: ' . mysql_error());
}
//подключение к базе данных
$db=@mysql_connect($db_hostname,$db_username,$db_password);
if($db!=FALSE)
    $tabledb=mysql_select_db($db_name) or die("Can't select database");
else{
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1251\">";
    echo "<LINK href=\"style.css\" rel=\"stylesheet\" type=\"text/css\">";
    echo "<h3>Ошибка!</h3>";
    echo "<p>Невозможно подключиться к базе данных. Пожалуйста повторите попыку позже.</p>";
//    die();
    }
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");
mysql_query("SET SESSION collation_connection = 'utf8_general_ci'");

function valid_email($email) {
  return preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $email);
}

function remove_html($str){
  $search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i",
  "'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
  $replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
  return preg_replace($search, $replace, $str);
}

function generate_exception($string){
  echo json_encode(array('result' => 'false','string' => $string));
  exit;
}

define(DB_ERROR,'Произошла неожиданная ошибка, повторите действие позже');

function check_mobile_device() {
  $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
  $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
  foreach ($mobile_agent_array as $value) {
    if (strpos($agent, $value) !== false) return true;
  }
  return false;
}

function getRealIP(){
  if( $_SERVER['HTTP_X_FORWARDED_FOR'] != ''){
    $client_ip =
    ( !empty($_SERVER['REMOTE_ADDR']) ) ?
    $_SERVER['REMOTE_ADDR']
    :
    ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
    $_ENV['REMOTE_ADDR']
    :
    "unknown" );
    $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
    reset($entries);
    while (list(, $entry) = each($entries)){
      $entry = trim($entry);
      if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)){
        $private_ip = array(
          '/^0\./',
          '/^127\.0\.0\.1/',
          '/^192\.168\..*/',
          '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
          '/^10\..*/');

          $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

          if ($client_ip != $found_ip){
            $client_ip = $found_ip;
            break;
          }
        }
      }
    }
    else{
      $client_ip =
      ( !empty($_SERVER['REMOTE_ADDR']) ) ?
      $_SERVER['REMOTE_ADDR']
      :
      ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
      $_ENV['REMOTE_ADDR']
      :
      "unknown" );
    }

    return $client_ip;

  }




//$q_check_user = sprintf("SELECT users.first_name, users.last_name,users.avatar FROM `users` WHERE `users`.`id`='".$to_user_id."'");
//$r_check_user = mysql_query($q_check_user) or die("cant execute query");
//$n_check_user = mysql_numrows($r_check_user); // or die("cant get numrows query");
//if ($n_check_user > 0){
//$to_user_first_name = htmlspecialchars(mysql_result($r_check_user, 0, "users.first_name"));


//$q_set_deleted = sprintf("UPDATE messages SET messages.deleted='".$var_deleted."'".",messages.date_deleted='".$var_date_deleted."'"." WHERE messages.user_id='".$var_user_id."'"." AND messages.message_id='".$var_message_id."'");
//mysql_query($q_set_deleted) or die("cant execute update set_deleted");

/*
$q_insert_from = sprintf("INSERT INTO messages (
    date_create,
    date_deleted
  ) values(
    '".$var_date_create."',
    '".$var_user_id."'
    '".")");
  mysql_query($q_insert_from) or die('{"result":"false"}');
*/
