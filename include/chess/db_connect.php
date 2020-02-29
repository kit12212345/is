<?php
$db_hostname="localhost";
$db_name="a0167398_chess";
$db_username="a0167398_chess";
$db_password="kit123";
defined(DB_ERROR,'Произошла неожиданная ошибка, повторите действие позже');
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

function prc_to_str($str){
  return str_replace('%', '_PRC_', $str);
}

function str_to_prc($str){
  return str_replace('_PRC_', '%', $str);
}
function valid_email($email) {
  return preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)*\.([a-zA-Z]{2,6})$/", $email);
}

function remove_html($str){
  $search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i",
  "'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
  $replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
  return preg_replace($search, $replace, $str);
}


$DB_CONNECTED = true;

function generate_exception($string){
  echo json_encode(array('result' => 'false','string' => $string));
  exit;
}

$db_error = 'Произошла неожиданная ошибка, повторите действие позже';

function check_mobile_device() {
  $mobile_agent_array = array('ipad', 'iphone', 'android', 'pocket', 'palm', 'windows ce', 'windowsce', 'cellphone', 'opera mobi', 'ipod', 'small', 'sharp', 'sonyericsson', 'symbian', 'opera mini', 'nokia', 'htc_', 'samsung', 'motorola', 'smartphone', 'blackberry', 'playstation portable', 'tablet browser');
  $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
  // var_dump($agent);exit;
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
?>
