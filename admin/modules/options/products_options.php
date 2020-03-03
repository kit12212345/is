<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];

if(!class_exists('Options')) require($root_dir.'/admin/modules/options/options.php');
if(!class_exists('Property')) require($root_dir.'/admin/modules/options/property.php');


class ProductsOptions extends Options{
  public $org_id;
  public $app_id;
  public $fs_id;
  public $create_date;
  public static $db_error = 'Произошла неожиданная ошибка, повторите действие позже';


  function __construct(Array $data = array()){
    $this->org_id = isset($data['org_id']) ? $data['org_id'] : 0;
    $this->app_id = isset($data['app_id']) ? $data['app_id'] : 0;
    $this->fs_id = isset($data['fs_id']) ? $data['fs_id'] : 0;
    $this->create_date = gmdate('Y-m-d H:i:s');
  }

  public function exists_product($product_id){
    $q_exist = ("SELECT `id` FROM `catalog` WHERE `id` = '".$product_id."'");
    $r_exist = mysql_query($q_exist) or die(DB_ERROR);
    $n_exist = mysql_numrows($r_exist); // or die("cant get numrows query");
    return !!$n_exist;
  }

  public function get_parent_propert($id){
    $parent_id = false;
    $q_exist = ("SELECT `parent_id` FROM `shop_options` WHERE `id` = '".$id."'");
    $r_exist = mysql_query($q_exist) or die(DB_ERROR);
    $n_exist = mysql_numrows($r_exist); // or die("cant get numrows query");
    if($n_exist > 0){
      $parent_id = htmlspecialchars(mysql_result($r_exist, 0, "parent_id"));
    }
    return $parent_id;
  }

  public function exists_option($product_id,$data,$fs_id){
    $str_items = implode(',',$data);
    $count_items = count($data);

    $sort_prop = '';

    for ($i=0; $i < $count_items; $i++){
      // $and = $i == 0 ? '' : ' AND ';
      $and = ' AND ';
      $sort_prop .= " ".$and." EXISTS (
       SELECT `id` FROM `shop_options_items`
       WHERE `propert_id` = '".(int)$data[$i]."' AND `parent_product_id` = '".$product_id."' AND `shop_options_items`.`product_id` = `catalog`.`id`)";
    }



    $q_exist = ("SELECT DISTINCT `catalog`.`id`,`catalog`.`price` FROM `shop_options_items`
    INNER JOIN `catalog` ON (`catalog`.`id` = `shop_options_items`.`product_id`)
    WHERE `shop_options_items`.`parent_product_id` = '".$product_id."'
    ".$sort_prop."");
    $r_exist = mysql_query($q_exist) or die($q_exist);
    $n_exist = mysql_numrows($r_exist); // or die("cant get numrows query");
    if($n_exist > 0){
      $product_id = htmlspecialchars(mysql_result($r_exist, 0, "id"));
      $price = htmlspecialchars(mysql_result($r_exist, 0, "price"));

      return array(
        'product_id' => $product_id,
        'price' => $price
      );

    }

    return !!$n_exist;
  }

  public function get_parent_product_quan($product_id){
    $current_quan = 0;
    $q_quan = ("SELECT `quan`,`parent_product` FROM `catalog`
    WHERE `parent_product` = (SELECT `parent_product` FROM `catalog` WHERE `id` = '".$product_id."' LIMIT 1)");
    $r_quan = mysql_query($q_quan) or die(DB_ERROR);
    $n_quan = mysql_numrows($r_quan); // or die("cant get numrows query");
    for ($i = 0; $i < $n_quan; $i++){
      $quan = htmlspecialchars(mysql_result($r_quan, $i, "quantity"));
      $parent_product = htmlspecialchars(mysql_result($r_quan, $i, "parent_product"));
      $current_quan += $quan;
    }
    return array(
      'quantity' => $current_quan,
      'parent_product' => $parent_product
    );
  }

  public function get_allowed_properts($product_id = 0,$show_all = false){
    $items = array();

    if(((is_array($product_id) && count($product_id) == 0) || $product_id == 0) && $show_all === false) return array();

    $and_product_id = is_array($product_id) ? " `shop_options_items`.`parent_product_id` IN (".join(',',$product_id).") " : " `shop_options_items`.`parent_product_id` = '".$product_id."'";

    if($show_all === true) $and_product_id = '';

    $q_allowed = ("SELECT DISTINCT `shop_options`.`id`,`shop_options`.`name` FROM `shop_options_items`
    INNER JOIN `shop_options` ON (`shop_options`.`id` = `shop_options_items`.`parent_propert_id`)
    WHERE ".$and_product_id);
    $r_allowed = mysql_query($q_allowed) or die(DB_ERROR);
    $n_allowed = mysql_numrows($r_allowed); // or die("cant get numrows query");
    for ($i = 0; $i < $n_allowed; $i++){
      $id = htmlspecialchars(mysql_result($r_allowed, $i, "id"));
      $name = htmlspecialchars(mysql_result($r_allowed, $i, "name"));
      $items[$id] = $name;
    }
    return $items;
  }


  public function save($type,$data){

    if($type == 'propert'){

      return $this->save_propert($data);

    } else if($type == 'options'){
      return $this->save_options($data);

    }

  }
  public function delete($type,$data){
    if($type == 'propert'){

      return $this->delete_propert($data);

    } else if($type == 'option'){

      return $this->delete_option($data);

    }
  }


  public function save_product_value($data){
    $event = $data['event'];
    $value = $data['value'];
    $product_id = $data['product_id'];
    $type_operation = $data['type_operation'];

    $value = mysql_real_escape_string($value);

    $update_field = false;

    if($event == 'quantity'){
      $update_field = 'quan';
    } else if($event == 'price'){
      $update_field = 'price';
    }

    if($update_field !== false){

      $q_update = ("UPDATE `catalog` SET `".$update_field."` = '".$value."' WHERE `id`='".$product_id."'");
      mysql_query($q_update) or die(generate_exception(DB_ERROR));

      if($event == 'quantity'){
        $product_quan_info = $this->get_parent_product_quan($product_id);

        $q_update = ("UPDATE `catalog` SET `quan` = '".$product_quan_info['quantity']."' WHERE `id`='".$product_quan_info['parent_product']."'");
        mysql_query($q_update) or die(generate_exception(DB_ERROR));

      }

    }

    $product_quantity = isset($product_quan_info) ? $product_quan_info['quantity'] : 0;

    return array(
      'value' => $value,
      'product_quantity' => $product_quantity
    );


  }



}


?>
