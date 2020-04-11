<?php
if(!class_exists("ProductsOptions")) include($root_dir.'/admin/modules/options/products_options.php');

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


  public function get_catalogs($data = array()){

    $parent_id = (int)$data['parent_id'];

    $order_by = " ORDER BY `catalog`.`name` ASC";


    $catalogs = array();
    $q_catalogs = ("SELECT * FROM `catalog` WHERE `catalog`.`deleted` = '0' AND `catalog`.`type` = 'catalog'
    AND `catalog`.`parent_id` = '".$parent_id."' ".$order_by);
    $r_catalogs = mysql_query($q_catalogs) or die("cant execute query");
    $n_catalogs = mysql_numrows($r_catalogs); // or die("cant get numrows query");
    if($n_catalogs > 0){
      for ($i = 0; $i < $n_catalogs; $i++) {
        $id = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.id"));
        $type = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.type"));
        $name = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.name"));
        $description = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.description"));
        $price = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.price"));
        $quan = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.quan"));
        $status = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.status"));
        $main_image = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.main_image"));
        $create_date = htmlspecialchars(mysql_result($r_catalogs, $i, "catalog.create_date"));

        $data = array(
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

        array_push($catalogs,$data);

      }
    }


    $tree_html = $this->get_tree_cats($parent_id,true);


    $cat_path = array();
    $cat_path = $this->create_cat_path($parent_id,$cat_path);
    $cat_path_count = count($cat_path);
    $cat_path_html = '';
    if($cat_path_count > 0){
      $cat_path_html .= '<ol class="breadcrumb">';
      $cat_path_html .= '<li class="breadcrumb-item"><a href="/catalog.php">Все</a></li>';

      for ($i = 0; $i < $cat_path_count; $i++){
        $active = $cat_path_count - 1 == $i ? 'class="active"' : '';
        $href = $cat_path_count - 1 != $i ? 'href="?parent_id='.$cat_path[$i]['id'].'"' : '';

        $cat_path_html .= '<li class="breadcrumb-item">';
          $cat_path_html .= ' <a '.$active.' '.$href.'>'.$cat_path[$i]['name'].'</a>';
        $cat_path_html .= '</li>';
      }
      $cat_path_html .= '</ol>';

    }

    return array(
      'tree_html' => $tree_html,
      'catalogs' => $catalogs,
      'cat_path' => $cat_path,
      'cat_path_html' => $cat_path_html
    );
  }

  public function get_products($data = array()){
    $this->goods_per_page = 28;
    $html = '';

    $parent_id = (int)$data['parent_id'];
    $search_str = $data['search_str'];
    $search_by = $data['search_by'];
    $sort_by = $data['sort_by'];
    $page = (int)$data['page'];
    $options = isset($data['options']) ? $data['options'] : '';

    $goods_per_page = $this->goods_per_page;
    $page = $page <= 0 ? 1 : $page;
    $search_str = mysql_real_escape_string($search_str);

    $order_by = " ORDER BY `catalog`.`name` ASC";

    if(!empty($sort_by) && $sort_by != 'default'){
      if($sort_by == 'price_high_to_low'){
        $order_by = 'ORDER BY `price` DESC';
      } else if($sort_by == 'price_low_to_high'){
        $order_by = 'ORDER BY `price` ASC';
      }
    }



    $search_field = 'name';

    if($search_by == 'by_code') $search_field = 'id';

    $query_search = !empty($search_str) ? " AND `catalog`.`".$search_field."` LIKE '%".$search_str."%'" : "";

    $query_parent = '';

    $offset = ($page - 1) * $goods_per_page;
    $sql_limit = " LIMIT " . $offset . "," .  $goods_per_page;

    $query_options = '';

    if(!empty($options)){

      $explode_options = array();

      $q_items = ("SELECT `propert_id`,`parent_propert_id` FROM `shop_options_items` WHERE `propert_id` IN (".$options.") ");
      $r_items = mysql_query($q_items) or die("cant execute query");
      $n_items = mysql_numrows($r_items); // or die("cant get numrows query");
      if($n_items > 0){
        for ($i = 0; $i < $n_items; $i++) {
          $option_id = htmlspecialchars(mysql_result($r_items, $i, "propert_id"));
          $parent_propert_id = htmlspecialchars(mysql_result($r_items, $i, "parent_propert_id"));
          if(!isset($explode_options[$parent_propert_id])) $explode_options[$parent_propert_id] = array();
          if(!in_array($option_id,$explode_options[$parent_propert_id])) array_push($explode_options[$parent_propert_id],$option_id);
        }
      }

      $keys_parents = array_keys($explode_options);
      $count_keys_parents = count($keys_parents);

      $query_options .= " AND  `catalog`.`id` IN ";
      $query_options .= '( SELECT `parent_product_id` FROM `shop_options_items` AS soi WHERE';

      for ($p = 0; $p < count($keys_parents); $p++){
        $propert_id = (int)$keys_parents[$p];
        $options_str = join($explode_options[$propert_id],',');

        $query_options .= $p == 0 ? " EXISTS (
        SELECT `shop_options_items`.`id` FROM `shop_options_items`
        WHERE `shop_options_items`.`propert_id` IN (".$options_str.") AND `shop_options_items`.`product_id` = soi.product_id)"
        : " AND EXISTS (
        SELECT `shop_options_items`.`id` FROM `shop_options_items`
        WHERE `shop_options_items`.`propert_id` IN (".$options_str.") AND `shop_options_items`.`product_id` = soi.product_id) ";

      }

      $query_options .= ')';

    }


    if($parent_id > 0){
      $query_parent = " AND `catalog`.`parent_id` = '".$parent_id."' ";
      $q_last_items = ("SELECT `last_items` FROM `catalog` WHERE `id` = '".$parent_id."' ");
      $r_last_items = mysql_query($q_last_items) or die("cant execute query");
      $n_last_items = mysql_numrows($r_last_items); // or die("cant get numrows query");
      if($n_last_items > 0){
        $last_items = htmlspecialchars(mysql_result($r_last_items, 0, "last_items"));
      }
      if(!empty($last_items)) $query_parent = " AND `catalog`.`parent_id` IN (".$last_items.",".$parent_id.") ";
    }


    $products = array();
    $q_products = ("SELECT
      DISTINCT `catalog`.`id`,
      `catalog`.`name`,
      `catalog`.`price`,
      `catalog`.`main_image`
    FROM `catalog` WHERE `catalog`.`deleted` = '0' AND `catalog`.`type` = 'product' ".$query_options."
     ".$query_search.$query_parent." ".$order_by.$sql_limit);
    $r_products = mysql_query($q_products) or die($q_products);
    $n_products = mysql_numrows($r_products); // or die("cant get numrows query");
    if($n_products > 0){
      for ($i = 0; $i < $n_products; $i++) {
        $id = htmlspecialchars(mysql_result($r_products, $i, "catalog.id"));
        $name = htmlspecialchars(mysql_result($r_products, $i, "catalog.name"));
        $price = htmlspecialchars(mysql_result($r_products, $i, "catalog.price"));
        $main_image = htmlspecialchars(mysql_result($r_products, $i, "catalog.main_image"));

        $product_data = array(
          'id' => $id,
          'name' => $name,
          'price' => $price,
          'main_image' => $main_image,
        );

        array_push($products,$product_data);

        $image_src = empty($main_image) ? '/images/no_img.png' : '/images/catalog/t_480/'.$main_image;

        $html .= '<div class="col-md-3 product">';
        $html .= '<a href="/product.php?id='.$id.'" title="'.$name.'">';
        $html .= '<div class="product_img">';
        $html .= '<img src="'.$image_src.'" alt="'.$name.'">';
        $html .= '</div>';
        $html .= '</a>';
        $html .= '<div class="product_body">';
        $html .= '<h4 class="product_title">'.$name.'</h4>';
        $html .= '<div class="product_price">';
        // $html .= '<span class="p_old_price">$65</span> ';
        $html .= ' $'.$price;
        $html .= '</div>';
        $html .= '<div class="mt-2">';
        $html .= '<strong>Бесплатная доставка от 40</strong>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';


      }
    }


    $q_products = str_replace($order_by.$sql_limit,"",$q_products);
    $r_products = mysql_query($q_products) or die("cant execute query");
    $count_all_items = mysql_numrows($r_products); // or die("cant get numrows query");

    $pages_html = $this->get_products_pages_buttons($page,$count_all_items,$goods_per_page);

    return array(
      'html' => $html,
      'products' => $products,
      'pages_html' => $pages_html,
      'cat_path' => $cat_path,
      'count_all_products' => $count_all_items,
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
        $last_items = htmlspecialchars(mysql_result($r_products, 0, "catalog.last_items"));
        $main_image = htmlspecialchars(mysql_result($r_products, 0, "catalog.main_image"));
        $is_last = htmlspecialchars(mysql_result($r_products, 0, "catalog.is_last"));
        $create_date = htmlspecialchars(mysql_result($r_products, 0, "catalog.create_date"));


        $catalog_options = array();

        $q_c_options = ("SELECT * FROM `catalog_options` WHERE `catalog_id` = '".$item_id."'");
        $r_c_options = mysql_query($q_c_options) or die("cant execute query");
        $n_c_options = mysql_numrows($r_c_options); // or die("cant get numrows query");
        if($n_c_options > 0){
          for ($i = 0; $i < $n_c_options; $i++) {
            $c_option_id = htmlspecialchars(mysql_result($r_c_options, $i, "option_id"));
            array_push($catalog_options,$c_option_id);
          }
        }

        $data = array(
          'id' => $id,
          'name' => $name,
          'description' => $description,
          'price' => $price,
          'quan' => $quan,
          'parent_id' => $parent_id,
          'last_items' => $last_items,
          'is_last' => $is_last,
          'catalog_options' => $catalog_options,
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

  public function get_tree_cats($selected_item,$output = fasle){
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
        $last_items = htmlspecialchars(mysql_result($r_cats, $i, "last_items"));

        $cats[$parent_id][$id] = array(
          'id' => $id,
          'title' => $title,
          'last_items' => $last_items,
          'parent_id' => $parent_id
        );
      }
    }

    $selected_item_info = array();
    if($selected_item > 0){
      $selected_item_info = $this->get_product_info($selected_item);
    }
    $this->cats = $cats;
    if($output === true){
      $html = $this->create_tree_output(0,$html,$selected_item_info);
    } else{
      $html = $this->create_tree(0,0,$html,$selected_item_info);
    }
    return $html;
  }


  public function create_tree_output($parent_id,&$html,$selected_item_info){
    $cats = $this->cats;
    if(isset($cats[$parent_id])){
      foreach ($cats[$parent_id] as $value) {
        $id = $value["id"];
        $name = $value["title"];

        $last_items = $value["last_items"];
        $arr_last_items = explode(',',$last_items);
        $is_parent_selected = in_array($selected_item_info['id'],$arr_last_items) || $selected_item_info['id'] == $id;

        $rotate_90 = $is_parent_selected ? 'rotate_90' : '';
        $display_block = $is_parent_selected ? 'style="display: block"' : '';

        $active_catalog = $selected_item_info['id'] == $id ? 'active_cl_item' : '';

        $html .= '<li class="cl_item">';
        $html .= '<a class="relative '.$active_catalog.'" href="?parent_id='.$id.'">'.$name.'';

        if(isset($cats[$id])){
          $html .= '<span onclick="get_child_cats(event,this,'.$id.');" class="absolute cursor_p cl_arrow '.$rotate_90.'"><i class="fa fa-angle-right" aria-hidden="true"></i></span>';
        }
        $html .= '</a>';


        $html .= '<ul class="list-group catalog_list child_catalog_list" '.$display_block.' id="child_cats_'.$id.'">';
        $this->create_tree_output($id,$html,$selected_item_info);
        $html .= '</ul>';
        $html .= '</li>';
      }
    }
    return $html;
  }


  public function create_tree($parent_id,$level,&$html,$selected_item_info){
    $line = '-';
    $cats = $this->cats;
    if(isset($cats[$parent_id])) {
      foreach ($cats[$parent_id] as $value) {

        $gline = '';

        for ($i = 0; $i < $level; $i++) {
          $gline .= $line;
        }

        $selected = $selected_item_info['id'] == $value["id"] ? 'selected="selected"' : '';

        $html .= '<option value="'.$value["id"].'" '.$selected.'>'.$gline.' '.$value["title"].'</option>';
        $level++;
        $this->create_tree($value["id"], $level,$html,$selected_item_info);
        $level--;
      }
    }
    return $html;
  }


  public function get_products_pages_buttons($page,$count_items,$goods_per_page){
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


      $pages_html .= '<ul class="pagination justify-content-end">';
      if ($page!=1){
        $pages_html .= '<li class="page-item">';
        $pages_html .= '<a class="page-link" onclick="catalog.switch_page('.($page-1).');" aria-label="Previous">';
        $pages_html .= '<span aria-hidden="true">&laquo;</span>';
        $pages_html .= '</a>';
        $pages_html .= '</li>';
      }
      for ($i = $start_page_count; $i <= $end_page_count; $i++) {
        if($page_count <= 1) continue;
        if ($i === $page) {
          $pages_html .= '<li class="page-item active"><a class="page-link">'.$i.'</a></li>';
        } else {
          $pages_html .= '<li class="page-item"><a class="page-link" onclick="catalog.switch_page('.$i.');">'.$i.'</a></li>';
        }
      }
      if ($page!=$page_count){
        $pages_html .= '<li class="page-item">';
        $pages_html .= '<a class="page-link" onclick="catalog.switch_page('.($page-1).');" aria-label="Next">';
        $pages_html .= '<span aria-hidden="true">&raquo;</span>';
        $pages_html .= '</a>';
        $pages_html .= '</li>';
      }
      $pages_html .= '</ul>';
    }

    return $pages_html;

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

    $this->reindex_cats();

  }

  public function reindex_cats(){
    $cat_arr = array();

    $q_query_path = ("SELECT * FROM `catalog` WHERE `type` = 'catalog'");
    $r_query_path = mysql_query($q_query_path) or die(generate_exception(DB_ERROR));
    $n_query_path = mysql_numrows($r_query_path); // or die("cant get numrows query_path");
    if ($n_query_path > 0) {
      for ($i = 0; $i < $n_query_path; $i++) {
        $id = htmlspecialchars(mysql_result($r_query_path, $i, "id"));
        $name = htmlspecialchars(mysql_result($r_query_path, $i, "name"));

        $q_is_last = ("SELECT * FROM `catalog` WHERE `type` = 'catalog' AND `catalog`.`parent_id` = '".$id."'");
        $r_is_last = mysql_query($q_is_last) or die(generate_exception(DB_ERROR));
        $exists_cats = mysql_numrows($r_is_last); // or die("cant get numrows query_path");
        $is_last = $exists_cats > 0 ? 0 : 1;

        $update = ("UPDATE `catalog` SET `is_last`='".$is_last."' WHERE `id`='".$id."'");
        mysql_query($update) or die("cant execute update set_deleted");

      }
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
    $parent_id = $data['parent_id'];

    $name = mysql_real_escape_string($name);
    $description = mysql_real_escape_string($description);
    $price = mysql_real_escape_string($price);
    $quan = mysql_real_escape_string($quan);

    $q_last_parent_id = ("SELECT * FROM `catalog` WHERE `parent_id` = '".$parent_id."' AND `type` = 'catalog'");
    $r_last_parent_id = mysql_query($q_last_parent_id) or die(generate_exception($db_error));
    $is_last_parent_id = mysql_numrows($r_last_parent_id); // or die("cant get numrows query");
    if($is_last_parent_id > 0) generate_exception('Выберите последний каталог');

    if($product_id > 0){

      $update = ("UPDATE `catalog` SET
      `name`='".$name."',
      `description` = '".$description."',
      `price` = '".$price."',
      `quan` = '".$quan."',
      `parent_id` = '".$parent_id."'
      WHERE `id`='".$product_id."'");
      mysql_query($update) or die("cant execute update set_deleted");

      ProductsOptions::set_catalog_options($parent_id,$product_id,false);

    } else{

      $q_query = ("INSERT INTO
      `catalog`
      (
      `name`,
      `type`,
      `description`,
      `price`,
      `quan`,
      `parent_id`,
      `create_date`)
      values(
      '".$name."',
      'product',
      '".$description."',
      '".$price."',
      '".$quan."',
      '".$parent_id."',
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
