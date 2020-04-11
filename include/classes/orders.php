<?php

if(!class_exists('Catalog')) include($root_dir.'/include/classes/catalog.php');
if(!class_exists('ProductsOptions')) include($root_dir.'/admin/modules/options/products_options.php');

class Orders extends Catalog{

  public $orders_per_page = 25;

  public $order_statuses;

  function __construct(){
    $this->order_statuses = array(
      'active' => 'Активный'
    );
  }


  public function get_orders($data = array()){

    $html = '';

    $parent_id = (int)$data['parent_id'];
    $page = (int)$data['page'];

    $page = $page <= 0 ? 1 : $page;

    $order_by = " ORDER BY `id` DESC";

    $offset = ($page - 1) * $this->orders_per_page;
    $sql_limit = " LIMIT ".$offset. "," .$this->orders_per_page;


    $orders = array();
    $q_orders = ("SELECT * FROM `orders` WHERE `orders`.`deleted` = '0' ".$order_by.$sql_limit);
    $r_orders = mysql_query($q_orders) or die("cant execute query");
    $n_orders = mysql_numrows($r_orders); // or die("cant get numrows query");
    if($n_orders > 0){
      for ($i = 0; $i < $n_orders; $i++) {
        $id = htmlspecialchars(mysql_result($r_orders, $i, "orders.id"));
        $summa = htmlspecialchars(mysql_result($r_orders, $i, "orders.summa"));
        $status = htmlspecialchars(mysql_result($r_orders, $i, "orders.status"));
        $create_date = htmlspecialchars(mysql_result($r_orders, $i, "orders.create_date"));

        $order_data = array(
          'id' => $id,
          'summa' => $summa,
          'status' => $status,
          'create_date' => $create_date
        );

        array_push($orders,$order_data);
        $html .= $this->get_orders_html($order_data);

      }
    }


    $q_orders = str_replace($order_by.$sql_limit,"",$q_orders);
    $r_orders = mysql_query($q_orders) or die("cant execute query");
    $count_all_items = mysql_numrows($r_orders); // or die("cant get numrows query");
    $pages_html = $this->get_pages_buttons($page,$count_all_items);

    return array(
      'html' => $html,
      'orders' => $orders,
      'pages_html' => $pages_html
    );

  }


  public function get_order_info($order_id){
    $order_data = array();
    $q_orders = ("SELECT * FROM `orders`
    LEFT JOIN `delivery_methods` ON (`delivery_methods`.`id` = `orders`.`delivery_method`)
    LEFT JOIN `countries` ON (`countries`.`id` = `orders`.`country`)
    LEFT JOIN `states` ON (`states`.`id` = `orders`.`state`)
    WHERE `orders`.`id` = '".$order_id."'");
    $r_orders = mysql_query($q_orders) or die("cant execute query");
    $n_orders = mysql_numrows($r_orders); // or die("cant get numrows query");
    if($n_orders > 0){
      $id = htmlspecialchars(mysql_result($r_orders, 0, "orders.id"));
      $summa = htmlspecialchars(mysql_result($r_orders, 0, "orders.summa"));
      $dm_name = htmlspecialchars(mysql_result($r_orders, 0, "delivery_methods.name"));
      $dm_cost = htmlspecialchars(mysql_result($r_orders, 0, "delivery_methods.cost"));
      $dm_days = htmlspecialchars(mysql_result($r_orders, 0, "delivery_methods.days"));
      $address1 = htmlspecialchars(mysql_result($r_orders, 0, "orders.address1"));
      $address2 = htmlspecialchars(mysql_result($r_orders, 0, "orders.address2"));
      $country = htmlspecialchars(mysql_result($r_orders, 0, "countries.name"));
      $city = htmlspecialchars(mysql_result($r_orders, 0, "orders.city"));
      $state = htmlspecialchars(mysql_result($r_orders, 0, "states.name"));
      $zip = htmlspecialchars(mysql_result($r_orders, 0, "orders.zip"));
      $status = htmlspecialchars(mysql_result($r_orders, 0, "orders.status"));
      $create_date = htmlspecialchars(mysql_result($r_orders, 0, "orders.create_date"));

      $order_data = array(
        'id' => $id,
        'summa' => $summa,
        'dm_name' => $dm_name,
        'dm_cost' => $dm_cost,
        'dm_days' => $dm_days,
        'address1' => $address1,
        'address2' => $address2,
        'country' => $country,
        'city' => $city,
        'state' => $state,
        'zip' => $zip,
        'status' => $status,
        'create_date' => $create_date
      );
    } else return false;

      return $order_data;
  }

  public function get_order_items($order_id){
    $items = array();

    $options = new ProductsOptions();

    $q_order_items = ("SELECT * FROM `order_items`
    INNER JOIN `catalog` ON (`catalog`.`id` = `order_items`.`product_id`)
    WHERE `order_items`.`order_id` = '".$order_id."' ");
    $r_order_items = mysql_query($q_order_items) or die("cant execute query");
    $n_order_items = mysql_numrows($r_order_items); // or die("cant get numrows query");
    if($n_order_items > 0){
      for ($i = 0; $i < $n_order_items; $i++) {
        $id = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.id"));
        $product_id = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.product_id"));
        $price = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.price"));
        $quan = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.quan"));
        $summa = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.summa"));
        $parent_product_id = htmlspecialchars(mysql_result($r_order_items, $i, "catalog.parent_product"));
        $discount = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.discount"));
        $create_date = htmlspecialchars(mysql_result($r_order_items, $i, "order_items.create_date"));

        $parent_product_info = $this->get_product_info($parent_product_id);

        $name = $parent_product_info['name'];

        $product_options = $options->get_product_options($parent_product_id,$product_id);

        array_push($items,array(
          'id' => $id,
          'product_id' => $product_id,
          'name' => $name,
          'price' => $price,
          'quan' => $quan,
          'summa' => $summa,
          'product_options' => $product_options,
          'discount' => $discount,
          'create_date' => $create_date
        ));

      }
    }

    return $items;
  }

  function get_total_summa($order_id){
    $total_summa = 0;
    $q_order_info = ("SELECT * FROM `order_items` WHERE `order_id` = '".$order_id."'");
    $r_order_info = mysql_query($q_order_info) or die(generate_exception($db_error));
    $n_order_info = mysql_numrows($r_order_info); // or die("cant get numrows query_path");
    if($n_order_info > 0){
      for ($i=0; $i < $n_order_info; $i++) {
        $order_item_total_summa = htmlspecialchars(mysql_result($r_order_info, $i, "total_summa"));
        $total_summa += $order_item_total_summa;
      }
    }
    return $total_summa;
  }

  public function delete_order($order_id){
    $update = ("UPDATE `orders` SET `deleted`='1' WHERE `id`='".$order_id."'");
    mysql_query($update) or die("cant execute update set_deleted");
  }

  public function get_orders_html($data = array()){
    $id = $data['id'];
    $summa = $data['summa'];
    $status = $data['status'];
    $create_date = $data['create_date'];
    $html = '';
    $html_who_return_order = $order_return_admin > 0 ? ' <small >(возвращен администратором)</small>' : ' <small>(возвращен пользователем)</small>';

    $html_pay_confirm = $order_pay_confirmed > 0 ? '<small class="g_color">(Оплачено '.number_format((float)$pay_withdraw_amount, 2, ',', ' ').' руб, поступило на счет '.number_format((float)$pay_amount, 2, ',', ' ').')</small>' : '<small class="r_color">(Не оплачен)</small>';

    $html_pay_online = $order_type_pay == 'online' ? $html_pay_confirm : '';

    $html_type_pay = '<strong class="g_color">'.$arr_types_pay[$order_type_pay].' '.$html_pay_online.'</strong>';


    $order_dm_name = $order_dm_name == '' ? $order_fix_dm_name : $order_dm_name;

    $order_user_phone = '+'.substr($order_user_phone, 0, 1).' ('.substr($order_user_phone, 1, 3).') '.substr($order_user_phone, 4, 3).'-'.substr($order_user_phone, 7, 2).'-'.substr($order_user_phone, 9, 2);


    $order_total_summa = number_format($order_total_summa, 2, ',', ' ');
    $order_cost_delivery = number_format($order_cost_delivery, 2, ',', ' ');

    $data_element = 'data-orderid="'.$order_id.'"';


    $html .= '<div class="panel relative panel-white order_item" id="s_order_'.$order_id.'">';

    $html .= '<div class="absolute count_commnets" id="count_comments_'.$order_id.'" title="Количество новых комментариев">1</div>';


      $html .= '<div class="panel-heading">';
        $html .= '<h6 class="panel-title">';
          $html .= '<div class="cursor_p full_w" id="btn_soi_'.$order_id.'">';
          $html .= '<div class="i_block v_align_middle" style="margin-right: 10px;">';
          $html .= '<input class="checked_order" onclick="event.stopPropagation();" value="'.$order_id.'" type="checkbox" checked="checked">';
          $html .= '</div>';
          $html .= '<a href="?q=view_order&order_id='.$id.'">';
            $html .= ' Заказ № '.$id.', от '.date('d.m.Y в H:i',strtotime($create_date));
            $html .= '</a>';
            $html .= '<span class="float_r">Общая сумма заказа: <span id="o_total_summa_'.$order_id.'">'.$summa.'</span></span>';
          $html .= '</div>';
        $html .= '</h6>';

        $order_reason_return_display = $order_status == 'return' ? 'display: block;' : 'display: none;';
        $html .= '<div class="ordr_info" id="order_reason_return_'.$order_id.'" style="'.$order_reason_return_display.'">';
          $html .= '<div class="order_reason_return">';
            $html .= '<strong>';
              $html .= '<small class="r_color">Возвращен по причине: <span id="text_reason_return_'.$order_id.'" class="r_color">'.$order_reason_return.$html_who_return_order.'</span></small>';
            $html .= '</strong>';
            $html .= '</div>';
            $html .= '</div>';

          $html .= '<div class="float_l" style="margin-top: 10px;">';
          $html .= '<strong>Статус: <span id="order_status_'.$order_id.'">'.$this->order_statuses[$status].'</span></strong>';
          $html .= '</div>';
          $html .= '<div class="float_r o_info">';
          $html .= '<div class="cursor_p btn_delete_order float_r" '.$data_element.' onclick="orders.delete_order('.$id.')"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Удалить заказ</div>';
          $html .= '</div>';
          $html .= '<div class="clear"></div>';
        $html .= '</div>';
        $html .= '</div>';
    return $html;
  }


  public function get_pages_buttons($page,$count_items){
    $pages_html = '';
    $page_count = 0;
    if (0 === $count_items) {
    } else {
      $page_count = (int)ceil($count_items / $this->orders_per_page);
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
















  /* DOESNT WORK YET */
  public function send_message($data = array()){
    $var_date_create = time();

    $var_content = $_POST['content'];
    $var_origin_content = $var_content;

    $var_content = mysql_real_escape_string($var_content);

    $var_content = strip_tags($var_content);

    $var_content = str_replace('&nbsp;', ' ',$var_content);


    $var_content = prc_to_str($var_content);


    $count = 0;


    $var_readed='0';
    $var_date_readed='0';
    $var_deleted='0';
    $var_date_deleted='0';

    if($its_adim == 'true'){
      $var_item_id=(int)$_POST['order_id'];
      $var_user_id = $order_user_id;
    } else{
      $var_item_id = (int)$_POST['order_id'];
      $var_user_id = $order_user_id;
    }



    $var_direction='outbox';


    $q_last = sprintf("SELECT `shop_orders_messages`.`message_id` FROM `shop_orders_messages` WHERE `shop_orders_messages`.`user_id`='".$var_user_id."' ORDER BY `shop_orders_messages`.`id` DESC");
    $r_last = mysql_query($q_last) or die("cant execute last");
    $n_last = mysql_numrows($r_last); // or die("cant get numrows last");
      if ($n_last > 0) {
          $user_last_message_id = htmlspecialchars(mysql_result($r_last, 0, "shop_orders_messages.message_id"));
          $user_last_message_id = $user_last_message_id + 1;
      } else {
        $user_last_message_id = 1;
      }


    $q_insert_from = sprintf("INSERT INTO `shop_orders_messages` (
      `create_date`,
      `order_id`,
      `order_user_id`,
      `user_id`,
      `message_id`,
      `item_id`,
      `its_admin`,
      `direction`,
      `content`,
      `readed`,
      `date_readed`,
      `deleted`,
      `date_deleted`
    ) values(
      '".$var_date_create."',
      '".$var_order_id."',
      '".$order_user_id."',
      '".$var_user_id."',
      '".$user_last_message_id."',
      '".$var_item_id."',
      '".$its_adim."',
      '".$var_direction."',
      '".$var_content."',
      '".$var_readed."',
      '".$var_date_readed."',
      '".$var_deleted."',
      '".$var_date_deleted."
      '".")");
    mysql_query($q_insert_from) or die(generate_exception('err db'));

    $var_user_id=(int)$_POST['order_id'];
    $var_item_id=(int)$_SESSION['user_id'];
    $var_direction='inbox';

    if($its_adim == 'true'){
      $var_item_id = $order_user_id;
    }


    $q_last = sprintf("SELECT `shop_orders_messages`.`message_id` FROM `shop_orders_messages` WHERE `shop_orders_messages`.`user_id`='".$var_user_id."' ORDER BY `shop_orders_messages`.`id` DESC");
    $r_last = mysql_query($q_last) or die("cant execute last");
    $n_last = mysql_numrows($r_last); // or die("cant get numrows last");
      if ($n_last > 0) {
          $item_last_message_id = htmlspecialchars(mysql_result($r_last, 0, "shop_orders_messages.message_id"));
          $item_last_message_id = $item_last_message_id + 1;
      } else {
          $item_last_message_id = 1;
      }


    $q_insert_to = sprintf("INSERT INTO `shop_orders_messages` (
      `create_date`,
      `order_id`,
      `order_user_id`,
      `user_id`,
      `message_id`,
      `item_id`,
      `its_admin`,
      `direction`,
      `content`,
      `readed`,
      `date_readed`,
      `deleted`,
      `date_deleted`
    ) values(
      '".$var_date_create."',
      '".$var_order_id."',
      '".$order_user_id."',
      '".$var_user_id."',
      '".$item_last_message_id."',
      '".$var_item_id."',
      '".$its_adim."',
      '".$var_direction."',
      '".$var_content."',
      '".$var_readed."',
      '".$var_date_readed."',
      '".$var_deleted."',
      '".$var_date_deleted."
      '".")");
    mysql_query($q_insert_to) or die(generate_exception('err db'));

    $last_id = mysql_insert_id();

    if($its_adim == 'true'){

      $msg_html .= '<div class="float_r full_w comment_item" id="o_komment_'.$order_id.$order_komment_id.'">';
        $msg_html .= '<div class=""><strong class="firm1">Администратор</strong></div>';
        $msg_html .= '<div class="">'.$var_origin_content.'</div>';
        $msg_html .= '<div class="text_right">';
          $msg_html .= '<small>'.gmdate("d.m.y в H:i", $var_date_create + $_SESSION['time_offset']).'</small>&nbsp;&nbsp;<i class="fa fa-check '.$readed_comment.'" aria-hidden="true" title="'.$readed_comment_text.'"></i>';
        $msg_html .= '</div>';
      $msg_html .= '</div>';

    } else{

      $msg_html .= '<div class="float_l full_w comment_item" id="o_komment_'.$order_id.$order_komment_id.'">';
        $msg_html .= '<div class=""><strong class="firm1">'.$order_user_name.'</strong></div>';
        $msg_html .= '<div class="">'.$var_origin_content.'</div>';
        $msg_html .= '<div class="text_right">';
          $msg_html .= '<small>'.gmdate("d.m.y в H:i", $var_date_create + $_SESSION['time_offset']).'</small>&nbsp;&nbsp;<i class="fa fa-check '.$readed_comment.'" aria-hidden="true" title="'.$readed_comment_text.'"></i>';
        $msg_html .= '</div>';
      $msg_html .= '</div>';

    }
  }


  public function delete_message($data = array()){
    $var_user_id=(int)$LOGGED_USER['id'];
    $message_id=(int)($_POST['message_id']);
    $q_check = sprintf("SELECT messages.id FROM messages WHERE messages.user_id='".$var_user_id."'"." AND messages.message_id='".$message_id."'");
    $r_check = mysql_query($q_check) or die("cant execute check");
    $n_check = mysql_numrows($r_check); // or die("cant get numrows check");
    if ($n_check > 0) {
        $check_id = htmlspecialchars(mysql_result($r_check, 0, "messages.id"));
        $var_user_id=(int)$LOGGED_USER['id'];
        $var_message_id=(int)($_POST['message_id']);
        $var_deleted='1';
        $var_date_deleted=(int)time();
        $q_set_deleted = sprintf("UPDATE messages SET messages.deleted='".$var_deleted."'".",messages.date_deleted='".$var_date_deleted."'"." WHERE messages.user_id='".$var_user_id."'"." AND messages.message_id='".$var_message_id."'");
        mysql_query($q_set_deleted) or die("cant execute update set_deleted");
        echo '{"result": true, "msg_id": '.(int)($_POST['message_id']).', "time": '.$var_date_deleted.'}';
    }
  }
  /*END DOESNT WORK YET */


}


?>
