<?php
include_once($root_dir.'/include/classes/orders.php');
$order_id = (int)$_GET['order_id'];
$orders = new Orders();
$order_info = $orders->get_order_info($order_id);
$order_items = $orders->get_order_items($order_id);
$count_order_items = count($order_items);

$id = $order_info['id'];
$summa = $order_info['summa'];
$dm_name = $order_info['dm_name'];
$dm_cost = $order_info['dm_cost'];
$dm_days = $order_info['dm_days'];
$address1 = $order_info["address1"];
$address2 = $order_info["address2"];
$country = $order_info["country"];
$city = $order_info["city"];
$state = $order_info["state"];
$zip = $order_info["zip"];
$create_date = $order_info['create_date'];


?>
<style media="screen">
  .prm_label{
    margin: 0px 4px;
  }
</style>

<link rel="stylesheet" href="/admin/css/orders.css">
<h2 class="i_block" style="margin: 0px; margin-bottom: 20px;">Заказ № <?php echo $id; ?>, от <?php echo date('d.m.Y в H:i',strtotime($create_date)); ?></h2>

<div class="full_w">

  <div class="panel panel-flat order_info_items">
    <div class="ord_info_item">
      <div class="i_block">Способ доставки:</div>
      <strong><?php echo $dm_name; ?></strong>
       &nbsp;<a style="text-decoration: underline; font-size: 12px;" onclick="orders.show_change_dm('.$order_id.')">( Изменить )</a>
    </div>

    <div class="ord_info_item">
      <div class="i_block">Адрес доставки:</div>
      <strong><?php echo $country.' ---- '.$address1.' ---- '.$city.' ---- '.$state.' ---- '.$zip ?></strong>
    </div>

    <div class="ord_info_item">
      <div class="i_block">Клиент:</div>
      <span id="client_label'.$order_id.'">
      <strong>-----</strong>
       &nbsp;<a style="text-decoration: underline; font-size: 12px;" onclick="orders.show_change_client('.$order_id.')">( Изменить )</a>
      </span>
    </div>
    <div class="ord_info_item">
      <div class="i_block">Способ оплаты:</div>-----
    </div>
    <div class="ord_info_item">
      <div class="i_block">Стоимость доставки:</div>
      <strong><?php echo $dm_cost; ?></strong>
    </div>

      <div class="ord_info_item">
        <div class="i_block">Комментарий пользователя:</div>
        <strong >----</strong>
      </div>


    <div class="ord_info_item">
      <div class="i_block v_align_middle">Скидка:</div>
      <strong class="v_align_middle" >
      <input id="margin_'.$order_id.'" value="" type="text" style="border: 1px solid #ccc;">
      <div class="btn btn-xs btn-success" onclick="orders.set_margin('.$order_id.')" style="padding: 0px 10px; margin-left: 10px;">Применить</div>
      </strong>
    </div>


  </div>




  <div class="panel panel-flat">
    <table class="table datatable-select-checkbox dataTable no-footer" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">
      <thead>
        <tr role="row">
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Last Name: activate to sort column ascending">
          <input type="checkbox" onclick="orders.checked_all_oi(this,'.$order_id.');" checked="checked">
          </th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Last Name: activate to sort column ascending">№</th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Job Title: activate to sort column ascending">Наименование</th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Параметры</th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Количество</th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Цена за шт.</th>
          <th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Общая сумма</th>
          <th class="text-center sorting_disabled" rowspan="1" colspan="1" aria-label="Actions" style="width: 100px;"></th>
        </tr>
      </thead>
      <tbody>
        <?php
        if($count_order_items > 0){
          for ($i = 0; $i < $count_order_items; $i++) {
            $product_id = $order_items[$i]['product_id'];
            $name = $order_items[$i]['name'];
            $price = $order_items[$i]['price'];
            $quan = $order_items[$i]['quan'];
            $summa = $order_items[$i]['summa'];
            $options = $order_items[$i]['product_options'];
            $create_date = $order_items[$i]['create_date'];
            echo '<tr role="row" id="o_item_'.$product_id.'" class="odd">';
              echo '<td class="sorting_1"><input type="checkbox" checked="checked"></td>';
              echo '<td>'.($i+1).'</td>';
              echo '<td>'.$name.'</td>';
              echo '<td class="text_center">';
                foreach ($options as $key => $value) {
                  echo '<span class="prm_label label label-primary label-striped">'.$value['propert_name'].': '.$value['name'].'</span>';
                }
              echo '</td>';
              echo '<td class="text_center">';
                echo '<span>'.$quan.'</span>';
              echo '</td>';
              echo '<td class="text_center">';
                echo '<span>'.$price.'</span>';
              echo '</td>';
              echo '<td class="text_center">';
                echo '<span>';
                  echo '<span>'.$summa.'</span>';
              echo '</td>';
              echo '<td class="text-center">';
                echo '<ul class="icons-list">';
                  echo '<li class="dropdown">';
                    echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-menu9"></i></a>';
                    echo '<ul class="dropdown-menu dropdown-menu-right">';
                      echo '<li>';
                        echo '<a class="g_color" data-itemid="21" data-count="1" onclick="orders.edit_count_products(this)">';
                          echo '<i class="icon-cog3"></i>Изменить кол-во';
                        echo '</a>';
                      echo '</li>';
                      echo '<li>';
                        echo '<a data-itemid="21" data-orderid="23" onclick="orders.delete_product(this);" class="r_color">';
                          echo '<i class="icon-folder-remove"></i>Удалить';
                        echo '</a>';
                      echo '</li>';
                    echo '</ul>';
                  echo '</li>';
                echo '</ul>';
              echo '</td>';
            echo '</tr>';
          }
        } else{

        }
        ?>
      </tbody>
    </table>
    <div class="text_right order_btns">

      <div onclick="orders.show_add_product(this);" class="float_l btn btn-xs btn-primary cursor_p btn_add_product_'.$order_id.'" style="padding: 1px 15px;">Добавить товар</div>

      <div class="i_block v_align_middle select_order_status">
        Статус заказа:
        <select id="select_order_status_'.$order_id.'" data-orderid="'.$order_id.'" onchange="orders.listen_change_order_status(this);" name="">
        <option '.$st_selected_active.' value="active">Активный</option>
        <option '.$st_selected_apply.' value="apply">Подтвержден</option>
        <option '.$st_selected_waiting.' value="waiting">В обработке</option>
        <option '.$st_selected_in_way.' value="in_way">В пути</option>
        <option '.$st_selected_done.' value="done">Доставлен</option>
        <option '.$st_selected_reject.' value="reject">Отклонен</option>
        <option '.$st_selected_return.' value="return">Возврат</option>
        </select>
      </div>
      <button type="button" onclick="orders.change_order_status(this);" '.$data_element.' class="btn btn-success" data-handle="apply">Изменить статус</button>

      <div class="full_w text_left o_cont_rtn" id="o_cont_rtn_'.$order_id.'">
        <span>Причина возврата</span>
        <div class="">
          <input type="text" id="reason_return_'.$order_id.'" placeholder="Укажите причину возврата" class="full_w i_reason_return" name="" value="">
        </div>
      </div>


      </div>


    <!-- <div class="order_comments padd_ten">
      <div class="">
        <h6>Комментарии к заказу:</h6>
      </div>

      <div class="" id="comments_order_'.$order_id.'">

        <?php
        /*
      $q_message = ("SELECT * FROM
        `order_messages`
        INNER JOIN `users` ON (`users`.`id` = `order_messages`.`order_user_id`)
      WHERE
      `order_messages`.user_id = ".$order_id." AND
        `order_messages`.`deleted` = 0 ORDER BY `order_messages`.`id`");
      $r_message = mysql_query($q_message) or die("cant execute message");
      $n_message = mysql_numrows($r_message); // or die("cant get numrows message");e
      if ($n_message > 0) {
          for ($m = 0; $m < $n_message; $m++){

            $msg_id = htmlspecialchars(mysql_result($r_message, $m, "order_messages.id"));
            $msg_message_id = htmlspecialchars(mysql_result($r_message, $m, "order_messages.message_id"));
            $msg_content = htmlspecialchars(mysql_result($r_message, $m, "order_messages.content"));
            $msg_user_name = htmlspecialchars(mysql_result($r_message, $m, "users.name"));
            $msg_is_admin = htmlspecialchars(mysql_result($r_message, $m, "order_messages.its_admin"));
            $msg_readed = htmlspecialchars(mysql_result($r_message, $m, "order_messages.readed"));
            $msg_create_date = htmlspecialchars(mysql_result($r_message, $m, "order_messages.create_date"));

            $readed_comment = $msg_readed == '1' ? 'readed_comment' : '';
            $readed_comment_text = $msg_readed == '1' ? 'Сообщение прочитано' : 'Сообщение не прочитано';

            if($msg_is_admin == 'true'){

              <div class="float_r full_w comment_item" id="o_komment_'.$msg_id.'">
                <div class=""><strong class="firm1">Администратор</strong></div>
                <div class="">'.$msg_content.'</div>
                <div class="text_right">
                  <small>'.gmdate("d.m.y в H:i", $msg_create_date + $_SESSION['time_offset']).'</small>&nbsp;&nbsp;<i class="fa fa-check '.$readed_comment.'" aria-hidden="true" title="'.$readed_comment_text.'"></i>
                </div>
              </div>

            } else {

              <div class="float_l full_w comment_item" id="o_komment_'.$msg_id.'">
                <div class=""><strong class="firm1">'.$msg_user_name.'</strong></div>
                <div class="">'.$msg_content.'</div>
                <div class="text_right">
                  <small>'.gmdate("d.m.y в H:i", $msg_create_date + $_SESSION['time_offset']).'</small>
                </div>
              </div>

            }


        }
      } else {

        <div class="no_comments">Нет комментариев</div>

      }
      */
      ?>



      </div>
      <div class="clear"></div>

      <div class="">
        <div class="full_w ta_send_message" '.$data_element.' id="message_content_'.$order_id.'" style="padding: 10px; outline: none; border: 1px solid #ccc;" placeholder="Написать комментарий" contenteditable="true"></div>
        <div style="margin: 5px 0px;"><small style="color: grey;">Перенос строки Shift+Enter</small></div>
        <div class="text_right" style="margin-top: 10px;">
          <div class="btn btn-xs btn-info" onclick="orders.send_order_message(this)" '.$data_element.'>Отправить комментарий</div>
        </div>
      </div>


    </div> -->
