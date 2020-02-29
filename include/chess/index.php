<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/chess/db_connect.php');

$action = $_GET['action'];
$last_id = (int)$_GET['last_id'];
$game_id = (int)$_GET['game_id'];
$sql_str = $_POST['sql_str'];

if($action == 'get_games'){
  $str_games = '';
  $q_date = ("SELECT * FROM `games` ORDER BY `id` DESC");
  $r_date = mysql_query($q_date) or die(generate_exception(DB_ERROR));
  $n_date = mysql_num_rows($r_date); // or die("cant get numrows search_company");
  if($n_date > 0){
    for ($i = 0; $i < $n_date; $i++) {
      $id = htmlspecialchars(mysql_result($r_date, $i, "id"));
      $str_games .= empty($str_games) ? $id : '-'.$id;
    }
  }
  echo $str_games;

} else if($action == 'create_game'){
  $create_date = gmdate('Y-m-d H:i:s');
  $game_name = $_POST['game_name'];
  $game_name = mysql_real_escape_string($game_name);

  $q_date = ("INSERT INTO `games`(`name`,`current_color`, `create_date`) VALUES ('".$game_name."','w','".$create_date."')");
  mysql_query($q_date) or die(generate_exception(DB_ERROR));
  exit;

} else if($action == 'get_color'){
  $q_date = ("SELECT * FROM `games` WHERE `id` =  '".$game_id."'");
  $r_date = mysql_query($q_date) or die(generate_exception(DB_ERROR));
  $n_date = mysql_num_rows($r_date); // or die("cant get numrows search_company");
  if($n_date > 0){
    $color = htmlspecialchars(mysql_result($r_date, 0, "current_color"));
  } else echo "w";
  echo $color;
  exit;

} else if(!empty($sql_str)){

  $arr_sql = explode(";",$sql_str);
  for ($i = 0; $i < count($arr_sql); $i++) {
    if(!$arr_sql[$i]) continue;
    mysql_query($arr_sql[$i]) or die(generate_exception(DB_ERROR));
  }

  exit;
}

$items = array();

$q_date = ("SELECT * FROM `game_items` WHERE `game_id` =  '".$game_id."' AND `id` > '".$last_id."' ORDER BY `id` ASC");
$r_date = mysql_query($q_date) or die(generate_exception(DB_ERROR));
$n_date = mysql_num_rows($r_date); // or die("cant get numrows search_company");
if($n_date > 0){
  for ($i = 0; $i < $n_date; $i++) {
    $id = htmlspecialchars(mysql_result($r_date, $i, "id"));
    $fx = htmlspecialchars(mysql_result($r_date, $i, "fx"));
    $fy = htmlspecialchars(mysql_result($r_date, $i, "fy"));
    $tx = htmlspecialchars(mysql_result($r_date, $i, "tx"));
    $ty = htmlspecialchars(mysql_result($r_date, $i, "ty"));
    $name = htmlspecialchars(mysql_result($r_date, $i, "name"));

    array_push($items,array(
      'id' => $id,
      'fx' => $fx,
      'fy' => $fy,
      'tx' => $tx,
      'ty' => $ty,
      'name' => $name
    ));

  }
}


foreach ($items as $key => $value) {
  echo "id-".$value['id'].";fx-".$value['fx'].";fy-".$value['fy'].";tx-".$value['tx'].";ty-".$value['ty'].";name-".$value['name'].";.";
}



?>
