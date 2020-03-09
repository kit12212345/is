<?php

if(!class_exists("Auth")) include_once($root_dir.'/include/classes/auth.php');
if(!class_exists("Catalog")) include_once($root_dir.'/include/classes/catalog.php');
if(!class_exists("Common")) include_once($root_dir.'/include/classes/common.php');
if(!class_exists("ProductsOptions")) include_once($root_dir.'/admin/modules/options/products_options.php');
$product_options = new ProductsOptions();


class Basket extends User{

  function __construct(Array $data = array()){
    parent::__construct($data);
  }


  public function checkout($data = array()){
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $phone = $data['phone'];
    $country = (int)$data['country'];
    $state = (int)$data['state'];
    $city = $data['city'];
    $address = $data['address'];
    $zip = $data['zip'];
    $comment = $data['comment'];
    $dm_id = (int)$data['dm_id'];

    $first_name = mysql_real_escape_string($first_name);
    $last_name = mysql_real_escape_string($last_name);
    $phone = mysql_real_escape_string($phone);
    $city = mysql_real_escape_string($city);
    $address = mysql_real_escape_string($address);
    $zip = mysql_real_escape_string($zip);
    $comment = mysql_real_escape_string($comment);

    if(empty($first_name)) generate_exception('Введите имя');
    if(empty($last_name)) generate_exception('Введите фамилию');
    if(empty($phone)) generate_exception('Введите телефон');
    if(empty($city)) generate_exception('Укажите город');
    if(empty($address)) generate_exception('Укажите адрес');
    if(empty($zip)) generate_exception('Укажите почтовый индекс');

    $check_country = Common::get_loc_info($country,'country');
    if($check_country === false) generate_exception('Выберите страну');
    $check_state = Common::get_loc_info($state,'state');
    if($check_state === false) generate_exception('Выберите штат');

    $check_dm = Common::get_dm_info($dm_id);
    if($check_dm === false) generate_exception('Выберите способ доставки');

    $basket = $this->get_basket();
    $basket_total_summa = $this->get_total_summa($basket);

    $count_basket = count($basket);

    if($count_basket == 0) generate_exception('Корзина пуста');

    $q_query = ("INSERT INTO
    `orders`
    (
    `user_id`,
    `delivery_method`,
    `address1`,
    `country`,
    `city`,
    `state`,
    `zip`,
    `comment`,
    `status`,
    `create_date`)
    values(
    '".$this->user_id."',
    '".$dm_id."',
    '".$address."',
    '".$country."',
    '".$city."',
    '".$state."',
    '".$zip."',
    '".$comment."',
    'active',
    '".$this->create_date."')");
    mysql_query($q_query) or die(generate_exception(DB_ERROR));

    $order_id = mysql_insert_id();

    foreach ($basket as $key => $item) {
      $id = $item['id'];
      $quan = $item['quan'];
      $price = $item['price'];
      $total = $price * $quan;

      $q_query = ("INSERT INTO
      `order_items`
      (
      `order_id`,
      `product_id`,
      `quan`,
      `price`,
      `summa`,
      `create_date`)
      values(
      '".$order_id."',
      '".$id."',
      '".$quan."',
      '".$price."',
      '".$total."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

    }

    $this->clear_basket();

  }


  public function get_basket(){
    GLOBAL $product_options;

    $catalog = new Catalog();

    $basket = array();
    if($this->user_id > 0) $basket = $this->get_basket_from_db();
    else $basket = $this->c_get_basket();

    foreach ($basket as $key => $value) {
      $product_id = $value['id'];

      $q_order_items = ("SELECT * FROM `catalog` WHERE `id` = '".$product_id."' ");
      $r_order_items = mysql_query($q_order_items) or die("cant execute query");
      $n_order_items = mysql_numrows($r_order_items); // or die("cant get numrows query");
      if($n_order_items > 0){
        for ($i = 0; $i < $n_order_items; $i++) {
          $price = htmlspecialchars(mysql_result($r_order_items, $i, "catalog.price"));
          $parent_product_id = htmlspecialchars(mysql_result($r_order_items, $i, "catalog.parent_product"));

          $parent_product_info = $catalog->get_product_info($parent_product_id);

          $name = $parent_product_info['name'];
          $main_image = $parent_product_info['main_image'];

          $options = $product_options->get_product_options($parent_product_id,$product_id);

          $basket[$key]['name'] = $name;
          $basket[$key]['main_image'] = $main_image;
          $basket[$key]['price'] = $price;
          $basket[$key]['options'] = $options;
          $basket[$key]['parent_product_id'] = $parent_product_id;

        }
      }

    }


    return $basket;
  }

  public function get_total_summa($basket = array()){
    $basket = !is_array($basket) ? array() : $basket;
    if(count($basket) == 0) $basket = $this->get_basket();
    $total_summa = 0;
    foreach ($basket as $key => $item) {
      $quan = $item['quan'];
      $price = $item['price'];
      $total_summa += $price * $quan;
    }
    return $total_summa;
  }

  public function clear_basket(){
    if($this->user_id > 0) return $this->clear_basket_in_db();
    return $this->clear_basket_in_cookies();
  }

  public function add(Array $data = array()){
    GLOBAL $product_options;

    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;

    if($item_id <= 0) generate_exception('Товар не найден');

    $selected_options = isset($data['selected_options']) ? $data['selected_options'] : array();

    $allowed_properts = $product_options->get_allowed_properts($item_id);

    foreach ($selected_options as $key => $value) {
      if($allowed_properts[$value['propert_id']]) unset($allowed_properts[$value['propert_id']]);
    }

    $err_str = '';
    $count_prop = count($allowed_properts);
    if($count_prop > 0){
      foreach ($allowed_properts as $key => $value) {
        $err_str .= "Выберите ".$value."\n";
      }
      generate_exception($err_str);
    }

    $product_id = $product_options->get_product_by_options($item_id,$selected_options);

    if($product_id === false) generate_exception('Товар не найден');

    $data['item_id'] = $product_id;

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
      $this->add_in_cookies($data);
    } else{
      $basket[$exs_item]['quan'] = $quan;
      // $this->c_save($basket[$exs_item]);
      $basket = serialize($basket);
      setcookie("basket", $basket, time()+3600, '/');
    }

    return array(
      'quan' => $quan
    );
  }

  public function remove_in_cookies($id){
    $basket = $this->c_get_basket();
    $exs_item = $this->c_get_item($id);
    if($exs_item !== false) unset($basket[$exs_item]);
    $basket = array_values($basket);
    $basket = serialize($basket);
    setcookie("basket", $basket, time()+3600, '/');
  }

  public function clear_basket_in_cookies(){
    return setcookie("basket", "", time()+3600, '/');
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

  public function get_basket_from_db(){

    $basket = array();

    $q_basket = ("SELECT * FROM `basket` WHERE `user_id` = '".$this->user_id."'");
    $r_basket = mysql_query($q_basket) or die(generate_exception(DB_ERROR));
    $n_basket = mysql_numrows($r_basket); // or die("cant get numrows search_company");
    if($n_basket > 0){
      for ($i = 0; $i < $n_basket; $i++) {
        $id = htmlspecialchars(mysql_result($r_basket, $i, "id"));
        $item_id = htmlspecialchars(mysql_result($r_basket, $i, "item_id"));
        $quan = htmlspecialchars(mysql_result($r_basket, $i, "quan"));

        array_push($basket,array(
          'db_id' => $id,
          'id' => $item_id,
          'quan' => $quan
        ));

      }
    }

    return $basket;
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

    return array(
      'quan' => $quan
    );

  }

  public function add_in_db(Array $data = array()){
    $item_id = isset($data['item_id']) ? (int)$data['item_id'] : 0;
    $quan = isset($data['quan']) ? (int)$data['quan'] : 1;
    $selected_options = isset($data['selected_options']) ? $data['selected_options'] : array();
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

  public function clear_basket_in_db(){
    $q_delete = ("DELETE FROM `basket` WHERE `user_id` = '".$this->user_id."'");
    mysql_query($q_delete) or die(generate_exception(DB_ERROR));

  }

}


?>
