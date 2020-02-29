<?php
if(!class_exists('AdmConfig')) require_once($root_dir.'/admin/config.php');
if(!class_exists('AdmCommon')) require_once($root_dir.'/admin/include/classes/adm_common.php');

class About extends AdmCommon{

  public function __construct($data){

  }

  public function get_questions(){
    $table_name = self::$questions_table_name;
    $arr_quests = array();

    $q_quests = sprintf("SELECT * FROM `".$table_name."` ORDER BY `".$table_name."`.`id` DESC");
    $r_quests = mysql_query($q_quests) or die("cant execute query_path");
    $n_quests = mysql_num_rows($r_quests); // or die("cant get numrows query_path");
    if ($n_quests > 0) {
      for ($i = 0; $i < $n_quests; $i++) {
        $quest_id = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".id"));
        $quest_user_name = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".user_name"));
        $quest_user_email = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".user_email"));
        $quest_user_ip = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".user_ip"));
        $quest_content = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".content"));
        $quest_create_date = htmlspecialchars(mysql_result($r_quests, $i, $table_name.".create_date"));

        array_push($arr_quests,array(
          'id' => $quest_id,
          'user_name' => $quest_user_name,
          'user_email' => $quest_user_email,
          'user_ip' => $quest_user_ip,
          'content' => $quest_content,
          'create_date' => $quest_create_date
        ));

      }
    }


    return $arr_quests;


  }

  public function get_not_readed_questions(){
    $table_name = self::$questions_table_name;

    $q_quests = sprintf("SELECT * FROM `".$table_name."` WHERE `".$table_name."`.`readed` = '0'");
    $r_quests = mysql_query($q_quests) or die("cant execute query_path");
    $n_quests = mysql_num_rows($r_quests); // or die("cant get numrows query_path");

    return $n_quests;

  }

  public static function set_read_questions($quest_id){
    $table_name = self::$questions_table_name;
    self::update_data(array(
      'table_name' => $table_name,
      'fields' => array(
        'readed' => 1
      ),
      'where' => array(
        'id' => $quest_id
      )
    ));

    return self::get_not_readed_questions();
  }



}


?>
