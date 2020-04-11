<?php
if(!class_exists('Property')) require($root_dir.'/admin/modules/options/property.php');

Class Options extends Property{

  public function get_options($parent_product_id,$allowed_properts = flase){

    $allowed_properts = $allowed_properts === false ? $this->get_allowed_properts($parent_product_id) : $allowed_properts;
    $keys_properts = array_keys($allowed_properts);


    $options = array();
    $q_allowed = ("SELECT DISTINCT `shop_options_items`.`product_id`,
    `catalog`.`price`,
    `catalog`.`quan` FROM `shop_options_items`
    INNER JOIN `catalog` ON (`catalog`.`id` = `shop_options_items`.`product_id`)
    WHERE `shop_options_items`.`parent_product_id` = '".$parent_product_id."' ORDER BY `catalog`.`id`");
    $r_allowed = mysql_query($q_allowed) or die(DB_ERROR);
    $n_allowed = mysql_numrows($r_allowed); // or die("cant get numrows query");
    for ($i = 0; $i < $n_allowed; $i++) {
      $product_id = htmlspecialchars(mysql_result($r_allowed, $i, "product_id"));
      $product_price = htmlspecialchars(mysql_result($r_allowed, $i, "price"));
      $product_quan = htmlspecialchars(mysql_result($r_allowed, $i, "quan"));

      $product_price = $product_price == 0 ? '' : $product_price;
      $product_quan = $product_quan == 0 ? '' : $product_quan;

      array_push($options,array(
        'product_id' => $product_id,
        'price' => $product_price,
        'quantity' => $product_quan,
        'options' => array()
      ));

      for ($p = 0; $p < count($allowed_properts); $p++) {
        $parent_propert_id = $keys_properts[$p];

        $q_val = ("SELECT `shop_options`.`id`,`shop_options`.`name`,`shop_options`.`color` FROM `shop_options_items`
        INNER JOIN `shop_options` ON (`shop_options`.`id` = `shop_options_items`.`propert_id`)
        WHERE `product_id` = '".$product_id."' AND `parent_propert_id` = '".$parent_propert_id."'");
        $r_val = mysql_query($q_val) or die(DB_ERROR);
        $n_val = mysql_numrows($r_val); // or die("cant get numrows query");
        if($n_val > 0){
          $id = htmlspecialchars(mysql_result($r_val, 0, "id"));
          $value = htmlspecialchars(mysql_result($r_val, 0, "name"));
          $color = htmlspecialchars(mysql_result($r_val, 0, "color"));

          $options[count($options) - 1]['options'][$parent_propert_id] = array(
            'id' => $id,
            'name' => $value,
            'color' => $color
          );
        }

      }

    }


    return $options;
  }

  public function get_product_options($parent_product_id,$product_id){
    $options = array();
    $allowed_properts = $this->get_allowed_properts($parent_product_id);
    $keys_properts = array_keys($allowed_properts);

    for ($p = 0; $p < count($allowed_properts); $p++) {
      $parent_propert_id = $keys_properts[$p];

      $q_val = ("SELECT `shop_options`.`id`,`shop_options`.`name`,`shop_options`.`color` FROM `shop_options_items`
      INNER JOIN `shop_options` ON (`shop_options`.`id` = `shop_options_items`.`propert_id`)
      WHERE `product_id` = '".$product_id."' AND `parent_propert_id` = '".$parent_propert_id."'");
      $r_val = mysql_query($q_val) or die(DB_ERROR);
      $n_val = mysql_numrows($r_val); // or die("cant get numrows query");
      if($n_val > 0){
        $id = htmlspecialchars(mysql_result($r_val, 0, "id"));
        $value = htmlspecialchars(mysql_result($r_val, 0, "name"));
        $color = htmlspecialchars(mysql_result($r_val, 0, "color"));

        array_push($options,array(
          'id' => $id,
          'propert_name' => $allowed_properts[$parent_propert_id],
          'name' => $value,
          'color' => $color
        ));

      }

    }

    return $options;

  }

  public function get_option_value($product_id,$propert_parent_id){
    $q_name = ("SELECT `shop_options`.`name` FROM `shop_options`
    INNER JOIN `shop_options_items` ON (`shop_options_items`.`propert_id` = `shop_options`.`id`)
    WHERE  `shop_options_items`.`parent_propert_id` = '".$propert_parent_id."' AND `shop_options_items`.`product_id` = '".$product_id."'");
    $r_name = mysql_query($q_name) or die(DB_ERROR);
    $n_name = mysql_numrows($r_name); // or die("cant get numrows query");
    if($n_name > 0){
      $name = htmlspecialchars(mysql_result($r_name, 0, "name"));
    }
    return $name;
  }


  public function get_propert_val($id){
    $value = '---';
    $q_val = ("SELECT `name` FROM `shop_options` WHERE `id` = '".$id."'");
    $r_val = mysql_query($q_val) or die(DB_ERROR);
    $n_val = mysql_numrows($r_val); // or die("cant get numrows query");
    if($n_val > 0){
      $value = htmlspecialchars(mysql_result($r_val, 0, "id"));
    }
    return $value;
  }


  public function save_options(Array $data = array()){
    $item_id = isset($data['item_id']) ? $data['item_id'] : 0;
    $name = $data['name'];
    $selected_items = $data['selected_items'];
    $product_id = (int)$data['product_id'];
    $event = $data['event'];
    $type_operation = $data['type_operation'];
    $values = $data['values'];

    $name = mysql_real_escape_string($name);

    $q_product_info = ("SELECT `price`,`parent_id` FROM `catalog`
    WHERE `id` = '".$product_id."'");
    $r_product_info = mysql_query($q_product_info) or die(DB_ERROR);
    $n_product_info = mysql_numrows($r_product_info); // or die("cant get numrows query");
    if($n_product_info > 0){
      $price = !isset($data['price']) || empty($data['price']) ? htmlspecialchars(mysql_result($r_product_info, 0, "price")) : $data['price'];
      $count = !isset($data['count']) ? 0 : (int)$data['count'];
      $parent_id = htmlspecialchars(mysql_result($r_product_info, 0, "parent_id"));
    } else generate_exception('Товар не найден');


    if(count($values) > 0) return $this->save_faster_options(array(
      'product_id' => $product_id,
      'values' => $values,
      'type_operation' => $type_operation
    ));


    if($event == 'save'){

      $q_delete = ("DELETE FROM `shop_options_items` WHERE `product_id`='".$item_id."'");
      mysql_query($q_delete) or die(generate_exception(DB_ERROR));


      $q_update = ("UPDATE `catalog` SET
      `quan`='".$count."',
      `price`='".$price."'
       WHERE `id`='".$item_id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));


      $child_product = $item_id;

    } else {

      $q_query = ("INSERT INTO `catalog`
      (`type`,
      `price`,
      `quan`,
      `parent_product`,
      `create_date`)
       values
       ('product_option',
        '".$price."',
        '".$count."',
        '".$product_id."',
        '".$this->create_date."'".")");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $child_product = mysql_insert_id();

      $product_quan_info = $this->get_parent_product_quan($child_product);

      $q_update = ("UPDATE `catalog` SET `quan` = '".$product_quan_info['quantity']."' WHERE `id`='".$product_quan_info['parent_product']."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));

    }




      $add_items = array();


      for ($i = 0; $i < count($selected_items); $i++){
        $item_id = $selected_items[$i];

        $parent_propert_id = $this->get_parent_propert($item_id);


        if($parent_propert_id === false) continue;

        $q_query = ("INSERT INTO `shop_options_items`
        (`propert_id`,
        `parent_propert_id`,
        `parent_product_id`,
        `product_id`,
        `create_date`)
         values
         ('".$item_id."',
          '".$parent_propert_id."',
          '".$product_id."',
          '".$child_product."',
          '".$this->create_date."'".")");
        mysql_query($q_query) or die(generate_exception(DB_ERROR));

        $propert_name = $this->get_propert_name($item_id);
        $propert_color = $this->get_propert_color($item_id);

        array_push($add_items,array(
          'parent_propert_id' => $parent_propert_id,
          'propert' => $item_id,
          'propert_name' => $propert_name,
          'propert_color' => $propert_color
        ));

        ProductsOptions::set_catalog_options($parent_id,$product_id,$item_id);


      }

    $allowed_properts = $this->get_allowed_properts($product_id);


    $product_quantity = isset($product_quan_info) ? $product_quan_info['quantity'] : 0;

    return array(
      'allowed_properts' => $allowed_properts,
      'price' => $price,
      'child_product' => $child_product,
      'quantity' => $count,
      'product_quantity' => $product_quantity,
      'add_items' => $add_items
    );

  }

  public function save_faster_options($data){
    $product_id = (int)$data['product_id'];
    $type_operation = $data['type_operation'];
    $values = $data['values'];

    $results = array();

    for ($i = 0; $i < count($values); $i++) {

      $result = $this->save_options(array(
        'product_id' => $product_id,
        'type_operation' => $type_operation,
        'selected_items' => $values[$i]
      ));

      array_push($results,$result);

    }

    return $results;

  }


  public function delete_option($data){
    $product_id = (int)$data['item_id'];
    $type_operation = $data['type_operation'];

    $q_product = ("SELECT `parent_product`,`quan` FROM `catalog`
    WHERE `id` = '".$product_id."'");
    $r_product = mysql_query($q_product) or die(DB_ERROR);
    $n_product = mysql_numrows($r_product); // or die("cant get numrows query");
    if($n_product > 0){
      $parent_product_id = htmlspecialchars(mysql_result($r_product, 0, "parent_product"));
      $product_quantity = htmlspecialchars(mysql_result($r_product, 0, "quan"));
    } else generate_exception('Товар не найден');

    $product_quan_info = $this->get_parent_product_quan($product_id);

    $q_delete = ("DELETE FROM `shop_options_items` WHERE `product_id`='".$product_id."'");
    mysql_query($q_delete) or die(generate_exception(DB_ERROR));

    $q_delete = ("DELETE FROM `catalog` WHERE `id`='".$product_id."'");
    mysql_query($q_delete) or die(generate_exception(DB_ERROR));

    /*update quantity*/

    $product_quan_info['quantity'] -= $product_quantity;

    $q_update = ("UPDATE `catalog` SET `quan` = '".$product_quan_info['quantity']."' WHERE `id`='".$product_quan_info['parent_product']."'");
    mysql_query($q_update) or die(generate_exception(DB_ERROR));

    /*END update quantity*/

    $product_quantity = isset($product_quan_info) ? $product_quan_info['quantity'] : 0;

    $allowed_properts = $this->get_allowed_properts($parent_product_id);

    return array(
      'product_id' => $product_id,
      'product_quantity' => $product_quantity,
      'allowed_properts' => $allowed_properts
    );
  }



}







?>
