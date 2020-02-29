<?php

if(!class_exists("Auth")) include_once($root_dir.'/include/classes/auth.php');

class Basket extends User{

  function __construct(Array $data = array()){
    parent::__construct($data);
  }

  public function add(Array $data = array()){
    if($this->user_id > 0) return $this->add_in_db($data);
    return $this->add_in_cookies($data);
  }

  public function change_quan(Array $data = array()){
    if($this->user_id > 0) return $this->change_quan_in_db($data);
    return $this->change_quan_in_cookies($data);
  }

  public function remove($item_id){
    if($this->user_id > 0) return $this->remove_in_db($item_id);
    return $this->remove_in_cookies($item_id);
  }

  public function c_get_basket(){
    $basket = isset($_COOKIE['basket']) ? unserialize($_COOKIE['basket']) : array();
    return is_array($basket) ? $basket : array();
  }

  private function c_get_item($id){
    $basket = $this->c_get_basket();
    for ($i = 0; $i < count($basket); $i++) {
      if($basket[$i]['id'] == $id) return $i;
    }
    return false;
  }

  public function add_in_cookies(Array $data = array()){
    return $this->c_save($data);
  }

  public function change_quan_in_cookies(Array $data = array()){
    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $quan = isset($data['quan']) ? (int)$data['quan'] : 1;
    $dir = isset($data['dir']) ? $data['dir'] : false;
    if($item_id <= 0) generate_exception('Товар не найден');
    $dir = ($dir == 'm' || $dir == 'p') === false ? false : $dir;

    $basket = $this->c_get_basket();
    $exs_item = $this->c_get_item($data['item_id']);

    if($dir !== false){
      $quan = $exs_item !== false ? $basket[$exs_item]['quan'] : $quan;
      $quan = $dir == 'm' ? $quan - 1 : $quan + 1;
    }

    if($quan <= 0) return $this->remove_in_cookies($item_id);

    if($exs_item == false){
      $data['quan'] = $quan;
      return $this->add_in_cookies($data);
    } else{
      $basket[$exs_item]['quan'] = $quan;
      return $this->c_save($basket[$exs_item]);
    }

  }

  public function remove_in_cookies($id){
    $basket = $this->c_get_basket();
    $exs_item = $this->c_get_item($id);
    if($exs_item !== false) unset($basket[$exs_item]);
    $basket = array_values($basket);
    $basket = serialize($basket);
    setcookie("basket", $basket, time()+3600, '/');
  }

  public function c_save(Array $data = array()){
    $basket = $this->c_get_basket();
    $item_id = (int)$data['item_id'];
    $quan = $data['quan'];

    $exs_item = $this->c_get_item($item_id);

    $cookie_data = array(
      'id' => $item_id,
      'quan' => $quan
    );

    if($exs_item === false){
      array_push($basket,$cookie_data);
    } else{
      $basket[$exs_item] = $cookie_data;
    }

    $basket = serialize($basket);

    return setcookie("basket", $basket, time()+3600, '/');
  }


  public function change_quan_in_db(Array $data = array()){
    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $quan = isset($data['quan']) ? (int)$data['quan'] : 1;
    $dir = isset($data['dir']) ? $data['dir'] : false;
    if($item_id <= 0) generate_exception('Товар не найден');
    $dir = ($dir == 'm' || $dir == 'p') === false ? false : $dir;

    $q_item = ("SELECT `id`,`quan` FROM `basket` WHERE `user_id` = '".$this->user_id."' AND `item_id` = '".$item_id."'");
    $r_item = mysql_query($q_item) or die(generate_exception(DB_ERROR));
    $n_item = mysql_numrows($r_item); // or die("cant get numrows search_company");
    if($n_item > 0){
      $id = htmlspecialchars(mysql_result($r_item, 0, "id"));
      $db_quan = htmlspecialchars(mysql_result($r_item, 0, "quan"));

      if($dir !== false){
        $quan = $dir == 'm' ? $db_quan - 1 : $db_quan + 1;
      }

      if($quan <= 0) return $this->remove_in_db($item_id);

      $q_update = ("UPDATE `basket` SET `quan` = '".$quan."' WHERE `id` = '".$id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));

    }

  }

  public function add_in_db(Array $data = array()){
    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $quan = isset($data['quan']) ? (int)$data['quan'] : 1;
    if($item_id <= 0) generate_exception('Товар не найден');
    $quan = $quan <= 0 ? 1 : $quan;

    $q_item = ("SELECT `id` FROM `basket` WHERE `user_id` = '".$this->user_id."' AND `item_id` = '".$item_id."'");
    $r_item = mysql_query($q_item) or die(generate_exception(DB_ERROR));
    $n_item = mysql_numrows($r_item); // or die("cant get numrows search_company");
    if($n_item > 0){
      $id = htmlspecialchars(mysql_result($r_item, 0, "id"));

      $q_update = ("UPDATE `basket` SET `quan` = '".$quan."' WHERE `id` = '".$id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));


    } else{

      $q_query = ("INSERT INTO
      `basket`
      (
      `user_id`,
      `item_id`,
      `quan`,
      `create_date`)
      values(
      '".$this->user_id."',
      '".$item_id."',
      '".$quan."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));


    }
  }

  public function remove_in_db($id){

    $q_delete = ("DELETE FROM `basket` WHERE `user_id` = '".$this->user_id."' AND `item_id` = '".$id."'");
    mysql_query($q_delete) or die(generate_exception(DB_ERROR));

  }

}


?>
