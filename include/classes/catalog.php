<?php
class Catalog{
  public $create_date;
  public $goods_per_page = 25;

  function __construct($data = array()){
    $this->create_date = gmdate('Y-m-d H:i:s');
  }

  public function get_catalog($data = array()){
    $html = '';

    $parent_id = (int)$data['parent_id'];
    $search_str = $data['search_str'];
    $search_by = $data['search_by'];
    $page = (int)$data['page'];
    $goods_per_page = (int)$data['goods_per_page'];

    $goods_per_page = $goods_per_page <= 0 ? $this->goods_per_page : $goods_per_page;
    $page = $page <= 0 ? 1 : $page;
    $search_str = mysql_real_escape_string($search_str);

    $search_field = 'name';

    if($search_by == 'by_code') $search_field = 'id';

    $query_search = !empty($search_str) ? " AND `catalog`.`".$search_field."` LIKE '%".$search_str."%'" : "";

    $order_by = " ORDER BY
    CASE WHEN `catalog`.`type` = 'catalog' THEN `catalog`.`position` END DESC,
    CASE WHEN `catalog`.`type` = 'product' THEN `catalog`.`name` END ASC ";

    $offset = ($page - 1) * $goods_per_page;
    $sql_limit = " LIMIT " . $offset . "," .  $goods_per_page;


    $products = array();
    $q_products = ("SELECT * FROM `catalog` WHERE `catalog`.`deleted` = '0' AND (`catalog`.`type` = 'catalog' OR `catalog`.`type` = 'product')
    AND `catalog`.`parent_id` = '".$parent_id."' ".$query_search." ".$order_by.$sql_limit);
    $r_products = mysql_query($q_products) or die("cant execute query");
    $n_products = mysql_numrows($r_products); // or die("cant get numrows query");
    if($n_products > 0){
      for ($i = 0; $i < $n_products; $i++) {
        $id = htmlspecialchars(mysql_result($r_products, $i, "catalog.id"));
        $type = htmlspecialchars(mysql_result($r_products, $i, "catalog.type"));
        $name = htmlspecialchars(mysql_result($r_products, $i, "catalog.name"));
        $description = htmlspecialchars(mysql_result($r_products, $i, "catalog.description"));
        $price = htmlspecialchars(mysql_result($r_products, $i, "catalog.price"));
        $quan = htmlspecialchars(mysql_result($r_products, $i, "catalog.quan"));
        $status = htmlspecialchars(mysql_result($r_products, $i, "catalog.status"));
        $main_image = htmlspecialchars(mysql_result($r_products, $i, "catalog.main_image"));
        $create_date = htmlspecialchars(mysql_result($r_products, $i, "catalog.create_date"));

        $product_data = array(
          'id' => $id,
          'type' => $type,
          'name' => $name,
          'description' => $description,
          'price' => $price,
          'quan' => $quan,
          'status' => $status,
          'main_image' => $main_image,
          'create_date' => $create_date
        );

        array_push($products,$product_data);
        $html .= $this->get_products_html($product_data);

      }
    }

    $cat_path = array();
    $cat_path = $this->create_cat_path($parent_id,$cat_path);
    $cat_path_count = count($cat_path);
    $cat_path_html = '';
    if($cat_path_count > 0){
      $cat_path_html .= '<li onclick="catalog.set_parent_id(0)">Все</li> ';
    }
    for ($i = 0; $i < $cat_path_count; $i++) {
      $active = $cat_path_count - 1 == $i ? 'class="active_map_item"' : '';
      $js_event = $cat_path_count - 1 != $i ? 'onclick="catalog.set_parent_id('.$cat_path[$i]['id'].')"' : '';
      $cat_path_html .= '<li '.$active.' '.$js_event.'> / '.$cat_path[$i]['name'].'</li> ';
    }


    $q_products = str_replace($order_by.$sql_limit,"",$q_products);
    $r_products = mysql_query($q_products) or die("cant execute query");
    $count_all_items = mysql_numrows($r_products); // or die("cant get numrows query");

    $pages_html = $this->get_pages_buttons($page,$count_all_items,$goods_per_page);

    return array(
      'html' => $html,
      'products' => $products,
      'pages_html' => $pages_html,
      'cat_path' => $cat_path,
      'cat_path_html' => $cat_path_html
    );

  }

  public function get_products($data = array()){
    $html = '';

    $parent_id = (int)$data['parent_id'];
    $search_str = $data['search_str'];
    $search_by = $data['search_by'];
    $page = (int)$data['page'];
    $goods_per_page = (int)$data['goods_per_page'];

    $goods_per_page = $goods_per_page <= 0 ? $this->goods_per_page : $goods_per_page;
    $page = $page <= 0 ? 1 : $page;
    $search_str = mysql_real_escape_string($search_str);

    $search_field = 'name';

    if($search_by == 'by_code') $search_field = 'id';

    $query_search = !empty($search_str) ? " AND `catalog`.`".$search_field."` LIKE '%".$search_str."%'" : "";

    $order_by = " ORDER BY `catalog`.`name` ASC";

    $offset = ($page - 1) * $goods_per_page;
    $sql_limit = " LIMIT " . $offset . "," .  $goods_per_page;


    $products = array();
    $q_products = ("SELECT * FROM `catalog` WHERE `catalog`.`deleted` = '0' AND `catalog`.`type` = 'product'
    AND `catalog`.`parent_id` = '".$parent_id."' ".$query_search." ".$order_by.$sql_limit);
    $r_products = mysql_query($q_products) or die("cant execute query");
    $n_products = mysql_numrows($r_products); // or die("cant get numrows query");
    if($n_products > 0){
      for ($i = 0; $i < $n_products; $i++) {
        $id = htmlspecialchars(mysql_result($r_products, $i, "catalog.id"));
        $type = htmlspecialchars(mysql_result($r_products, $i, "catalog.type"));
        $name = htmlspecialchars(mysql_result($r_products, $i, "catalog.name"));
        $description = htmlspecialchars(mysql_result($r_products, $i, "catalog.description"));
        $price = htmlspecialchars(mysql_result($r_products, $i, "catalog.price"));
        $quan = htmlspecialchars(mysql_result($r_products, $i, "catalog.quan"));
        $status = htmlspecialchars(mysql_result($r_products, $i, "catalog.status"));
        $main_image = htmlspecialchars(mysql_result($r_products, $i, "catalog.main_image"));
        $create_date = htmlspecialchars(mysql_result($r_products, $i, "catalog.create_date"));

        $product_data = array(
          'id' => $id,
          'type' => $type,
          'name' => $name,
          'description' => $description,
          'price' => $price,
          'quan' => $quan,
          'status' => $status,
          'main_image' => $main_image,
          'create_date' => $create_date
        );

        array_push($products,$product_data);
        $html .= $this->get_products_html($product_data);

      }
    }

    $cat_path = array();
    $cat_path = $this->create_cat_path($parent_id,$cat_path);
    $cat_path_count = count($cat_path);
    $cat_path_html = '';
    if($cat_path_count > 0){
      $cat_path_html .= '<li onclick="catalog.set_parent_id(0)">Все</li> ';
    }
    for ($i = 0; $i < $cat_path_count; $i++) {
      $active = $cat_path_count - 1 == $i ? 'class="active_map_item"' : '';
      $js_event = $cat_path_count - 1 != $i ? 'onclick="catalog.set_parent_id('.$cat_path[$i]['id'].')"' : '';
      $cat_path_html .= '<li '.$active.' '.$js_event.'> / '.$cat_path[$i]['name'].'</li> ';
    }


    $q_products = str_replace($order_by.$sql_limit,"",$q_products);
    $r_products = mysql_query($q_products) or die("cant execute query");
    $count_all_items = mysql_numrows($r_products); // or die("cant get numrows query");

    $pages_html = $this->get_pages_buttons($page,$count_all_items,$goods_per_page);

    return array(
      'html' => $html,
      'products' => $products,
      'pages_html' => $pages_html,
      'cat_path' => $cat_path,
      'cat_path_html' => $cat_path_html
    );
  }

  public function get_product_info($item_id){
    $data = array();
    $q_products = ("SELECT * FROM `catalog` WHERE `id` = '".$item_id."'");
    $r_products = mysql_query($q_products) or die("cant execute query");
    $n_products = mysql_numrows($r_products); // or die("cant get numrows query");
    if($n_products > 0){
        $id = htmlspecialchars(mysql_result($r_products, 0, "catalog.id"));
        $name = htmlspecialchars(mysql_result($r_products, 0, "catalog.name"));
        $description = htmlspecialchars(mysql_result($r_products, 0, "catalog.description"));
        $price = htmlspecialchars(mysql_result($r_products, 0, "catalog.price"));
        $quan = htmlspecialchars(mysql_result($r_products, 0, "catalog.quan"));
        $parent_id = htmlspecialchars(mysql_result($r_products, 0, "catalog.parent_id"));
        $main_image = htmlspecialchars(mysql_result($r_products, 0, "catalog.main_image"));
        $create_date = htmlspecialchars(mysql_result($r_products, 0, "catalog.create_date"));

        $data = array(
          'id' => $id,
          'name' => $name,
          'description' => $description,
          'price' => $price,
          'quan' => $quan,
          'parent_id' => $parent_id,
          'main_image' => $main_image,
          'create_date' => $create_date
        );

    } else return false;

    return $data;
  }


  public function delete_product($product_id){
    $q_set_deleted = ("UPDATE `catalog` SET `deleted`='1' WHERE `id`='".$product_id."'");
    mysql_query($q_set_deleted) or die("cant execute update set_deleted");
  }

  public function get_tree_cats($selected_item){
    $cats = array();
    $html = '';
    $q_cats = ("SELECT * FROM `catalog` WHERE `type` = 'catalog' ORDER BY `position` DESC");
    $r_cats = mysql_query($q_cats) or die("cant execute query_path");
    $n_cats = mysql_num_rows($r_cats);
    if($n_cats > 0){
      for ($i = 0; $i < $n_cats; $i++) {
        $id = htmlspecialchars(mysql_result($r_cats, $i, "id"));
        $title = htmlspecialchars(mysql_result($r_cats, $i, "name"));
        $parent_id = htmlspecialchars(mysql_result($r_cats, $i, "parent_id"));

        $cats[$parent_id][$id] = array(
          'id' => $id,
          'title' => $title,
          'parent_id' => $parent_id
        );
      }
    }

    $this->cats = $cats;
    $html = $this->create_tree(0,0,$html,$selected_item);
    return $html;
  }

  public function create_tree($parent_id,$level,&$html,$selected_item){
    $line = '-';
    $cats = $this->cats;
    if(isset($cats[$parent_id])) {
      foreach ($cats[$parent_id] as $value) {

        $gline = '';

        for ($i = 0; $i < $level; $i++) {
          $gline .= $line;
        }

        $selected = $selected_item == $value["id"] ? 'selected="selected"' : '';

        $html .= '<option value="'.$value["id"].'" '.$selected.'>'.$gline.' '.$value["title"].'</option>';
        $level++;
        $this->create_tree($value["id"], $level,$html,$selected_item);
        $level--;
      }
    }
    return $html;
  }


  public function get_pages_buttons($page,$count_items,$goods_per_page){
    $pages_html = '';
    $page_count = 0;
    if (0 === $count_items) {
    } else {
      $page_count = (int)ceil($count_items / $goods_per_page);
      if($page > $page_count) {
        $page = 1;
      }
    }


    $max_pages_nav=10;
    $center_pos=ceil($max_pages_nav/2);
    $center_offset=round($max_pages_nav/2);

    if($page_count>1){
      if($page>$center_pos) $start_page_count=$page-2;
      else  $start_page_count=1;
      $end_page_count=$start_page_count+($max_pages_nav-1);
      if($end_page_count>$page_count){
        $end_page_count=$page_count;
        $start_page_count=$page_count-($max_pages_nav-1);
      }

      if ($start_page_count<1) $start_page_count=1;

      if ($page!=1) $pages_html .= '<a onclick="catalog.switch_page('.($page-1).');" data-direct="previous" class="paginate_button previous" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_previous">←</a>';
      $pages_html .= '<span id="nums_pages_pr">';
      for ($i = $start_page_count; $i <= $end_page_count; $i++) {
        if($page_count <= 1) continue;
        if ($i === $page) {
          $pages_html .= '<a class="paginate_button current page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="1" tabindex="0">'.$i.'</a>';
        } else {
          $pages_html .= '<a onclick="catalog.switch_page('.$i.');" class="paginate_button page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0">'.$i.'</a>';
        }
      }
      $pages_html .= '</span>';
      if ($page!=$page_count) $pages_html .= '<a onclick="catalog.switch_page('.($page+1).');" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" id="btn_sw_next">→</a>';
    }

    return $pages_html;

  }

  public function get_products_html($data){
    $html = '';
    $id = $data['id'];
    $type = $data['type'];
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $quan = $data['quan'];
    $status = $data['status'];
    $main_image = $data['main_image'];
    $create_date = $data['create_date'];


    if($type == 'catalog'){
      $image_src = '/images/folder.png';
      $href_view_product = 'onclick="catalog.set_parent_id('.$id.');"';
    } else{
      $image_src = !empty($main_image) ? '/images/catalog/t_140/'.$main_image : '/images/no_img.png';
      $href_view_product = 'href="?q=product&item_id='.$id.'"';
    }

    $edit_link = $type == 'catalog' ? '?q=cat&item_id='.$id : '?q=product&item_id='.$id;

    $html .= '<tr role="row" class=" odd relative" id="pr_'.$id.'">';
    $html .= '<td class="tpr_select relative sorting_1 text_center table_checked_pr">';
    $html .= '<input value="'.$id.'" type="checkbox" class="product_checked">';
    $html .= '</td>';
    $html .= '<td class="tpr_code sorting_1 text_center">'.$id.'</td>';
      $html .= '<td class="tpr_name sorting_1"><a '.$href_view_product.'>';
      $html .= '<div class="product_img float_l"><img src="'.$image_src.'" class="full_w"></div>'.$name.'</a></td>';
      $html .= '<td class="tpr_price text_center relative">';
      $html .= $price;
      $html .= '</td>';
      $html .= '<td class="tpr_quan text_center">';
      $html .= $quan;
      $html .= '</td>';


      $html .= '<td class="tpr_status" id="product_status_'.$id.'">';

      if($status == 'enabled'){
        $html .= '<span class="label label-success" id="item_status_'.$id.'">Активен</span>';
      } else if($status == 'disabled'){
        $html .= '<span class="label label-danger" id="item_status_'.$id.'">Отключен</span>';
      }
      $html .= '</td>';
      $html .= '<td class="tpr_action text-center">';
        $html .= '<ul class="icons-list">';
          $html .= '<li class="dropdown">';
            $html .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
              $html .= '<i class="icon-menu9"></i>';
            $html .= '</a>';
            $html .= '<ul class="dropdown-menu dropdown-menu-right">';
            $html .= '<li>';
              $html .= '<a class="g_color" '.$link_href_item.'>';
                $html .= '<i class="icon-eye"></i>Просмотр';
              $html .= '</a>';
            $html .= '</li>';
              $html .= '<li>';
                $html .= '<a class="g_color" href="'.$edit_link.'">';
                  $html .= '<i class="icon-cog3"></i>Изменить';
                $html .= '</a>';
              $html .= '</li>';

              if($type == 'product'){
                $html .= '<li>';
                $html .= '<a class="g_color" onclick="products.copy(this)" data-productid="'.$product_id.'">';
                $html .= '<i class=" icon-copy4"></i>Копировать';
                $html .= '</a>';
                $html .= '</li>';
              }

              $display_enabled = $status == 'enabled' ? 'display: block;' : 'display: none;';
              $display_disabled = $status == 'disabled' ? 'display: block;' : 'display: none;';

                $html .= '<li>';
                if($status == 'enabled'){
                  $html .= '<a onclick="catalog.switch_status(this);" data-itemid="'.$id.'" class="r_color" data-status="disabled">';
                  $html .= '<i class="icon-blocked"></i>Снять с продажи';
                  $html .= '</a>';
                } else if($status == 'disabled'){
                  $html .= '<a onclick="catalog.switch_status(this);" data-itemid="'.$id.'" class="g_color" data-status="enabled">';
                  $html .= '<i class="icon-circle-down2"></i>Опубликовать';
                  $html .= '</a>';
                }
                $html .= '</li>';
              $html .= '<li>';
                $html .= '<a onclick="catalog.delete_product('.$id.');" class="r_color">';
                  $html .= '<i class="icon-folder-remove"></i>Удалить';
                $html .= '</a>';
              $html .= '</li>';
              $html .= '</ul>';
            $html .= '</li>';
          $html .= '</ul>';
        $html .= '</td>';

      $html .= '</tr>';
      return $html;
  }

  public function switch_status($data = array()){
    $allowed_statuses = array('enabled','disabled');
    $item_id = (int)$data['item_id'];
    $status = $data['status'];
    $status = mysql_real_escape_string($status);

    if(!in_array($status,$allowed_statuses)) generate_exception('Неизвесный статус');

    $update = ("UPDATE `catalog` SET `status`='".$status."' WHERE `id`='".$item_id."'");
    mysql_query($update) or die("cant execute update set_deleted");

  }

  public function save_catalog($data = array()){
    $cat_id = isset($data['cat_id']) ? (int)$data['cat_id'] : 0;
    $name = $data['name'];
    $description = $data['description'];
    $parent_id = (int)$data['parent_id'];

    if($cat_id > 0){

      $update = ("UPDATE `catalog` SET
      `name`='".$name."',
      `parent_id`='".$parent_id."',
      `description` = '".$description."'
      WHERE `id`='".$cat_id."'");
      mysql_query($update) or die("cant execute update set_deleted");

    } else{

      $q_query = ("INSERT INTO
      `catalog`
      (
      `name`,
      `type`,
      `description`,
      `parent_id`,
      `create_date`)
      values(
      '".$name."',
      'catalog',
      '".$description."',
      '".$parent_id."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $cat_id = mysql_insert_id();

    }

  }

  public function create_cat_path($item_id,&$path){
    $q_query_path = ("SELECT * FROM `catalog` WHERE `id` = '".$item_id."' AND `type`='catalog'");
    $r_query_path = mysql_query($q_query_path) or die(generate_exception(DB_ERROR));
    $n_query_path = mysql_numrows($r_query_path); // or die("cant get numrows query_path");
    if ($n_query_path > 0) {
      for ($i = 0; $i < $n_query_path; $i++) {
        $id = htmlspecialchars(mysql_result($r_query_path, $i, "id"));
        $name = htmlspecialchars(mysql_result($r_query_path, $i, "name"));
        $parent_id = htmlspecialchars(mysql_result($r_query_path, $i, "parent_id"));
        array_unshift($path, array('id' => $id,'name' => $name));
        $this->create_cat_path($parent_id,$path);
      }
    }
    return $path;
  }

  public function save_product($data = array()){
    $product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
    $name = $data['name'];
    $description = $data['description'];
    $price = $data['price'];
    $quan = $data['quan'];
    $image_hash = $data['image_hash'];

    $name = mysql_real_escape_string($name);
    $description = mysql_real_escape_string($description);
    $price = mysql_real_escape_string($price);
    $quan = mysql_real_escape_string($quan);

    if($product_id > 0){

      $update = ("UPDATE `catalog` SET
      `name`='".$name."',
      `description` = '".$description."',
      `price` = '".$price."',
      `quan` = '".$quan."',
      WHERE `id`='".$product_id."'");
      mysql_query($update) or die("cant execute update set_deleted");

    } else{

      $q_query = ("INSERT INTO
      `catalog`
      (
      `name`,
      `type`,
      `description`,
      `price`,
      `quan`,
      `create_date`)
      values(
      '".$name."',
      'product',
      '".$description."',
      '".$price."',
      '".$quan."',
      '".$this->create_date."')");
      mysql_query($q_query) or die(generate_exception(DB_ERROR));

      $product_id = mysql_insert_id();


      $q_temp_images = ("SELECT * FROM `temp_images` WHERE `temp_images`.`md5_hash` = '".$image_hash."'");
      $r_temp_images = mysql_query($q_temp_images) or die(generate_exception($db_error));
      $n_temp_images = mysql_numrows($r_temp_images); // or die("cant get numrows query");
      if ($n_temp_images > 0){
        for ($i = 0; $i < $n_temp_images; $i++) {
          $image_id = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.id"));
          $image_name = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.image"));
          $image_position = htmlspecialchars(mysql_result($r_temp_images, $i, "temp_images.position"));

          if($image_position == 1){

            $q_set_deleted = ("UPDATE `catalog` SET `main_image`='".$image_name."' WHERE `id`='".$product_id."'");
            mysql_query($q_set_deleted) or die("cant execute update set_deleted");

          }

          $q_query = ("INSERT INTO
          `catalog_images`
          (
          `item_id`,
          `image`,
          `position`,
          `create_date`)
          values(
          '".$product_id."',
          '".$image_name."',
          '".$image_position."',
          '".$this->create_date."')");
          mysql_query($q_query) or die(generate_exception(DB_ERROR));

          $q_delete_temp_image = ("DELETE FROM `temp_images` WHERE `temp_images`.`id`='".$image_id."'");
          mysql_query($q_delete_temp_image) or die(generate_exception($db_error));


          }
        }

    }


  }

}


?>
