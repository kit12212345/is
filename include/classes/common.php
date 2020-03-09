<?php


class Common{

  public static function get_countries(){
    $countries = array();
    $q_c = ("SELECT * FROM `countries` ORDER BY `id` ASC");
    $r_c = mysql_query($q_c) or die(generate_exception(DB_ERROR));
    $n_c = mysql_num_rows($r_c); // or die("cant get numrows search_company");
    if($n_c > 0){
      for ($i = 0; $i < $n_c; $i++) {
        $id = htmlspecialchars(mysql_result($r_c, $i, "id"));
        $name = htmlspecialchars(mysql_result($r_c, $i, "name"));
        $suffix = htmlspecialchars(mysql_result($r_c, $i, "suffix"));

        array_push($countries,array(
          'id' => $id,
          'name' => $name,
          'suffix' => $suffix
        ));
      }
    }
    return $countries;
  }

  public static function get_states(){
    $states = array();
    $q_s = ("SELECT * FROM `states` ORDER BY `id` ASC");
    $r_s = mysql_query($q_s) or die(generate_exception(DB_ERROR));
    $n_s = mysql_numrows($r_s); // or die("cant get numrows search_company");
    if($n_s > 0){
      for ($i = 0; $i < $n_s; $i++) {
        $id = htmlspecialchars(mysql_result($r_s, $i, "id"));
        $name = htmlspecialchars(mysql_result($r_s, $i, "name"));
        $suffix = htmlspecialchars(mysql_result($r_s, $i, "suffix"));

        array_push($states,array(
          'id' => $id,
          'name' => $name,
          'suffix' => $suffix
        ));

      }
    }

    return $states;

  }


  public function get_loc_info($id,$type){
    $info = array();
    $types = array(
      'country' => 'countries',
      'state' => 'states'
    );

    if(!isset($types[$type])) generate_exception('Неизвесный тип');

    $table_name = $types[$type];

    $q_s = ("SELECT * FROM `".$table_name."` WHERE `id` = '".$id."'");
    $r_s = mysql_query($q_s) or die(generate_exception(DB_ERROR));
    $n_s = mysql_numrows($r_s); // or die("cant get numrows search_company");
    if($n_s > 0){
      $id = htmlspecialchars(mysql_result($r_s, 0, "id"));
      $name = htmlspecialchars(mysql_result($r_s, 0, "name"));
      $suffix = htmlspecialchars(mysql_result($r_s, 0, "suffix"));

      $info = array(
        'id' => $id,
        'name' => $name,
        'suffix' => $suffix
      );

    } else return fasle;

    return $info;
  }


  public function get_dm_info($id){
    $info = array();

    $q_dm = ("SELECT * FROM `delivery_methods` WHERE `id` = '".$id."'");
    $r_dm = mysql_query($q_dm) or die(generate_exception(DB_ERROR));
    $n_dm = mysql_numrows($r_dm); // or die("cant get numrows search_company");
    if($n_dm > 0){
      $id = htmlspecialchars(mysql_result($r_dm, $i, "id"));
      $name = htmlspecialchars(mysql_result($r_dm, $i, "name"));
      $cost = htmlspecialchars(mysql_result($r_dm, $i, "cost"));
      $days = htmlspecialchars(mysql_result($r_dm, $i, "days"));

      $info = array(
        'id' => $id,
        'name' => $name,
        'cost' => $cost,
        'days' => $days
      );

    } else return false;

    return $info;
  }

  public static function get_delivery_methods(){
    $dm = array();
    $q_dm = ("SELECT * FROM `delivery_methods` ORDER BY `id` ASC");
    $r_dm = mysql_query($q_dm) or die(generate_exception(DB_ERROR));
    $n_dm = mysql_numrows($r_dm); // or die("cant get numrows search_company");
    if($n_dm > 0){
      for ($i = 0; $i < $n_dm; $i++) {
        $id = htmlspecialchars(mysql_result($r_dm, $i, "id"));
        $name = htmlspecialchars(mysql_result($r_dm, $i, "name"));
        $cost = htmlspecialchars(mysql_result($r_dm, $i, "cost"));
        $days = htmlspecialchars(mysql_result($r_dm, $i, "days"));

        array_push($dm,array(
          'id' => $id,
          'name' => $name,
          'cost' => $cost,
          'days' => $days
        ));

      }
    }

    return $dm;

  }


}



?>
