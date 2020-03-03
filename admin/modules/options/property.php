<?php

class Property{


  public function save_propert(Array $data = array()){
    $item_id = isset($data['item_id']) ? $data['item_id'] : 0;
    $name = $data['name'];
    $parent_id = (int)$data['parent_id'];
    $event = $data['event'];

    $name = mysql_real_escape_string($name);

    if(empty($name)) generate_exception('Название не может быть пустым');
    if($event == 'save'){

      $q_update = ("UPDATE `shop_options` SET
      `shop_options`.`name`='".$name."' WHERE `shop_options`.`id`='".$item_id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));

    } else if($event == 'add'){

      $q_query = ("INSERT INTO `shop_options`
      (`name`,
      `parent_id`,
      `create_date`)
       values
       ('".$name."',
        '".$parent_id."',
        '".$this->create_date."'".")");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $item_id = mysql_insert_id();
    }

    return array(
      'id' => $item_id,
      'name' => $name
    );

  }


  public function get_selected_properts(){
    $selected_properts = isset($_COOKIE['selected_properts']) ? $_COOKIE['selected_properts'] : "[]";
    $selected_properts = json_decode($selected_properts);
    return $selected_properts;
  }

  public function get_properts(){

    $properts = array();

    $q_properts = ("SELECT * FROM `shop_options` WHERE `parent_id` = '0'");
    $r_properts = mysql_query($q_properts) or die(DB_ERROR);
    $n_properts = mysql_numrows($r_properts); // or die("cant get numrows query");
    if($n_properts > 0){
      for ($i = 0; $i < $n_properts; $i++) {
        $propert_id = htmlspecialchars(mysql_result($r_properts, $i, "id"));
        $propert_name = htmlspecialchars(mysql_result($r_properts, $i, "name"));

        array_push($properts,array(
          'id' => $propert_id,
          'name' => $propert_name,
          'child' => array()
        ));

        $q_child = ("SELECT * FROM `shop_options` WHERE `parent_id` = '".$propert_id."' ORDER BY `position` DESC");
        $r_child = mysql_query($q_child) or die(DB_ERROR);
        $n_child = mysql_numrows($r_child); // or die("cant get numrows query");
        if($n_child > 0){
          for ($c = 0; $c < $n_child; $c++){
            $child_id = htmlspecialchars(mysql_result($r_child, $c, "id"));
            $child_name = htmlspecialchars(mysql_result($r_child, $c, "name"));
            $child_color = htmlspecialchars(mysql_result($r_child, $c, "color"));

            array_push($properts[$i]['child'],array(
              'id' => $child_id,
              'name' => $child_name,
              'color' => $child_color
            ));

          }
        }
      }
    }


    return $properts;
  }

  public function get_propert_name($id){
    $q_name = ("SELECT `name` FROM `shop_options` WHERE `id` = '".$id."'");
    $r_name = mysql_query($q_name) or die(DB_ERROR);
    $n_name = mysql_numrows($r_name); // or die("cant get numrows query");
    if($n_name > 0){
      $name = htmlspecialchars(mysql_result($r_name, 0, "name"));
    }
    return $name;
  }

  public function get_propert_color($id){
    $q_name = ("SELECT `color` FROM `shop_options` WHERE `id` = '".$id."'");
    $r_name = mysql_query($q_name) or die(DB_ERROR);
    $n_name = mysql_numrows($r_name); // or die("cant get numrows query");
    if($n_name > 0){
      $name = htmlspecialchars(mysql_result($r_name, 0, "color"));
    }
    return $name;
  }

  public function delete_propert(Array $data = array()){
    $propert_id = (int)$data['propert_id'];

    $q_exists = ("SELECT `parent_id` FROM `shop_options` WHERE `id` = '".$propert_id."'");
    $r_exists = mysql_query($q_exists) or die(DB_ERROR);
    $n_exists = mysql_numrows($r_exists); // or die("cant get numrows query");
    if($n_exists > 0){
      $parent_id = htmlspecialchars(mysql_result($r_exists, 0, "parent_id"));
    } else generate_exception('Свойство не найдено');


    $q_delete = ("DELETE FROM `shop_options` WHERE `id`='".$propert_id."'");
    mysql_query($q_delete) or die(generate_exception(DB_ERROR));

    return array(
      'parent_id' => $parent_id
    );

  }

  public function search_key_size($array,$properts){ // **Fuck
    $key = false;
    for ($i = 0; $i < count($array); $i++) {
      $name = $array[$i]['name'];
      if(!in_array($array[$i]['id'],$properts)) continue;
      if($name == 'Размер' || $name == 'размер' || $name == 'Размеры' || $name == 'размеры'){
        return $i;
      }
    }
    return $key;
  }


  public function update_properts_positions($data){
    $positions = $data['positions'];

    for ($i = 0,$pos = 1; $i < count($positions); $i++, $pos++) {
      $item_id = (int)$positions[$i];

      $q_properts = ("SELECT * FROM `shop_options` WHERE `id` = '".$item_id."'");
      $r_properts = mysql_query($q_properts) or die(DB_ERROR);
      $n_properts = mysql_numrows($r_properts); // or die("cant get numrows query");
      if($n_properts == 0){
        $pos--;
        continue;
      }

      $q_update = ("UPDATE `shop_options` SET
      `shop_options`.`position`='".$pos."' WHERE `shop_options`.`id`='".$item_id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));

    }

    return array();

  }


}





?>
