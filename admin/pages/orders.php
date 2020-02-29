<?php
$arr_status_translate = array(
  'apply' => 'подтверждения',
  'waiting' => 'обработки',
  'in_way' => 'нахождения в пути',
  'done' => 'доставки',
  'reject' => 'отклонения',
  'return' => 'возврата'
);

$arr_statuses = array(
  'active' => 'Активный',
  'apply' => 'Подтвержден',
  'waiting' => 'В обработке',
  'in_way' => 'В пути',
  'done' => 'Доставлен',
  'reject' => 'Отклонен',
  'return' => 'Возвращен'
);

$arr_types_pay = array(
  'online' => 'Онлайн',
  'cash' => 'Наличными (при получении)',
  'points' => 'Баллами'
);

$translate_dm_typs = array(
    'for_one_hour' => 'Доставка в течении часа',
    'today' => 'Доставка сегодня',
    'other_day' => 'Доставка в другой день'
  );
?>
<script type="text/javascript">
  var is_orders = true;
</script>
<style media="screen">
  .inp_search_product::before{
    left: 20px!important;
  }
  .oth_rc_item {
    width: 200px;
    height: 230px;
    display: inline-block;
    padding: 0px 10px;
    border: 1px solid #ddd;
    margin: 10px 5px;
}
.oti_title, .ms_rc_title {
    text-overflow: ellipsis;
    overflow: hidden;
    font-weight: 500;
    margin: 10px 0px;
}
.oti_img {
    height: 140px;
}
.dropdown_default2{
  display: inline-block;
  position: relative;
  vertical-align: middle;
  background: #ffffff;
  border: 1px solid #b1b1b1;
}

.dropdown_blocks{
  display: block;
  overflow: hidden;
  height: 100%;
  width: 100%;
}

.dropdown_block2{
  display: table;
}

.dropdown_title2{
  /* display: table-cell; */
width: 100%;
text-align: left;
vertical-align: middle;
/* padding: 10px; */
font-size: 13px;
color: #000000;
border: 1px solid #ccc;
/* line-height: 20px; */
}

.dropdown_down_img{
  display: table-cell;
  width: 25px;
  min-width: 25px;
  max-width: 25px;
  padding-bottom: 4px;
  text-align: center;
  vertical-align: middle;
}

.dropdown_down_img > .fa{
  font-size: 13px;
  color: #ffffff;
}

.dropdown_default2_list{
  position: absolute;
  /* background: #6c9ac1; */
  background: #416f96;
  width: 100%;
  /* border: 1px solid #507ea7; */
  /* margin-left: -1px; */
  box-shadow: 0 0px 6px 0px #4a7eab;
  z-index: 2;
}

.dropdown_default2_list_ul{
  display: block;
  margin: 0px;
  padding: 0px;
  box-sizing: border-box;
}

.dropdown_default2_list_hide{
  display: none;
}

.dropdown_default2_list_show{
  display: block;
}

.dropdown_default2_list:focus{
  outline: none;
}

.dropdown_default2_option{
  display: block;
  width: 100%;
  font-size: 13px;
  padding: 10px;
  color: #ffffff;
  overflow: hidden;
  text-overflow: ellipsis;
  border-bottom: 1px solid #507ea7;
  text-align: left;
  box-sizing: border-box;
}

.dropdown_default2_option:hover{
  background: rgba(255, 255, 255, 0.14);
}

</style>

<link rel="stylesheet" href="/applications/shop/css/orders.css">
<script src="/applications/shop/js/database.js" charset="utf-8"></script>
<script src="/applications/shop/js/database_basic.js" charset="utf-8"></script>
<script src="/applications/shop/js/scripts/create_order.js" charset="utf-8"></script>
<script src="/applications/shop/js/scripts/init_load_content.js" charset="utf-8"></script>
<script src="/js/mask/mask.js"></script>
<?php

$count_show_orders = (int)$_GET['count_show'] > 0 ? (int)$_GET['count_show'] : 10;
$count_show_orders = $count_show_orders > 100 ? 100 : $count_show_orders;
$count_show_orders = $count_show_orders < 10 ? 10 : $count_show_orders;

$type_view_orders = $_GET['type_orders'];
$orders_sort_by = $_GET['sort_by'];


$page = 1;
if(!empty($_GET['page'])) {
  $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
  if(false === $page) {
    $page = 1;
  }
}

$orders_info = array();

$orders_html = $orders_info['html'];
$n_count_orders = $orders_info['count_active_orders'];
$n_all_count_orders = $orders_info['count_all_orders'];

echo '<div id="orders_info" data-info=\'{"page": "'.(int)$page.'",';
echo '"count_show": "'.(int)$count_show_orders.'",';
echo '"count_orders": "'.$n_count_orders.'",';
echo '"sort_by": "'.$orders_sort_by.'",';
echo '"type_orders": "'.$type_view_orders.'"';
echo '}\'></div>';

?>


<div id="modal_change_dm" class="modal fade">
  <div class="modal-dialog">
    <div id="disable_s_product" class="absolute all_null disable_content">
      <div class="absolute all_null spinner_load">
        <i class="icon-spinner2 spinner"></i>
      </div>
    </div>
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h6 class="modal-title">Изменение способа доставки</h6>
      </div>

      <div class="modal-body">
        <div class="oinv_item">
          <label>Способ доставки</label>
          <div class="">
            <select id="i_dm" onchange="orders.select_dm(this)" class="form-control">
              <?php
              // $q_city_dms = sprintf("SELECT * FROM `shop_delivery_methods_cities`
              // INNER JOIN `city` ON (`city`.`id` = `shop_delivery_methods_cities`.`city_id`)
              // WHERE `shop_delivery_methods_cities`.`org_id` = '".$var_org_id."' AND `shop_delivery_methods_cities`.`app_id` = '".$var_app_id."'
              // AND `shop_delivery_methods_cities`.`fs_id` = '".$shop_from_shop."' ORDER BY `shop_delivery_methods_cities`.`id`");
              // $r_city_dms = mysql_query($q_city_dms) or die(generate_exception('err db'));
              // $n_city_dms = mysql_numrows($r_city_dms); // or die("cant get numrows query_path");
              // if($n_city_dms > 0){
              //   for ($i = 0; $i < $n_city_dms; $i++) {
              //     $city_dm_id = htmlspecialchars(mysql_result($r_city_dms, $i, "shop_delivery_methods_cities.id"));
              //     $city_dm_name = htmlspecialchars(mysql_result($r_city_dms, $i, "city.name"));
              //
              //     echo '<optgroup label="г.'.$city_dm_name.'">';
              //
              //     $q_dms = sprintf("SELECT * FROM `shop_delivery_methods_install`
              //     INNER JOIN `shop_delivery_methods` ON (`shop_delivery_methods`.`id` = `shop_delivery_methods_install`.`dm_id`)
              //     WHERE `shop_delivery_methods_install`.`org_id` = '".$var_org_id."' AND `shop_delivery_methods_install`.`app_id` = '".$var_app_id."'
              //     AND `shop_delivery_methods_install`.`fs_id` = '".$shop_from_shop."'
              //     AND `shop_delivery_methods_install`.`dm_city_item` = '".$city_dm_id."' ORDER BY `shop_delivery_methods_install`.`id`");
              //     $r_dms = mysql_query($q_dms) or die(generate_exception('err db'));
              //     $n_dms = mysql_numrows($r_dms); // or die("cant get numrows query_path");
              //     if($n_dms > 0){
              //       for ($t = 0; $t < $n_dms; $t++) {
              //         $parent_dm_id = htmlspecialchars(mysql_result($r_dms, $t, "shop_delivery_methods.id"));
              //         $dm_id = htmlspecialchars(mysql_result($r_dms, $t, "shop_delivery_methods_install.id"));
              //         $dm_name = htmlspecialchars(mysql_result($r_dms, $t, "shop_delivery_methods.name"));
              //         $dm_address = htmlspecialchars(mysql_result($r_dms, $t, "shop_delivery_methods_install.address"));
              //         $dm_free_from = htmlspecialchars(mysql_result($r_dms, $t, "shop_delivery_methods_install.free_from"));
              //
              //         $write_address = !empty($dm_address) ? ' - Адрес: '.$dm_address : '';
              //         $write_free_from = !empty($dm_free_from) && $dm_free_from > 0 ? ' - Бесплатно при заказе от '.number_format($dm_free_from, 2, ',', ' ').' руб.' : '';
              //
              //         echo '<option value="'.$dm_id.'" data-parentid="'.$parent_dm_id.'">'.$dm_name.$write_address.$write_free_from.'</option>';
              //
              //       }
              //     } else{
              //       echo '<option value="0">Нет способов доставки</option>';
              //     }
              //
              //     echo '</optgroup>';
              //
              //   }
              // } else{
              //   echo '<option value="0">Нет способов доставки</option>';
              // }
              ?>
            </select>
          </div>
        </div>

        <div class="oinv_item" style=" margin-top: 15px; display: none;" id="wr_address">
          <label>Адрес</label>
          <div class="">
            <input type="text" id="dm_user_address" class="form-control" name="" placeholder="" value="">
          </div>
        </div>


        <div class="oinv_item" style=" margin-top: 15px; display: none;" id="wr_type_dm">
          <label>Тип доставки</label>
          <div class="">
            <select onchange="orders.select_dm(this)" id="i_type_dm" class="form-control" name="">
              <option value="today">Доставка сегодня</option>
              <option value="other_day">Доставка в другой день</option>
              <option value="for_one_hour">Доставка в течении часа</option>
            </select>
          </div>
        </div>


        <div class="oinv_item" style=" margin-top: 15px; display: none;" id="wr_time_dm">
          <label>Время доставки</label>
          <div class="">
            <input id="dm_time" type="text" class="form-control" name="" placeholder="HH:MM" value="">
          </div>
        </div>


        <div class="oinv_item" style=" margin-top: 15px; display: none;" id="wr_date_dm">
          <div class="">
            <div class="float_l bs_date">
            <label class="ls_date">День</label>
            <select id="dm_day" class="form-control ss_date">
            <?php
            for ($i = 1; $i < 31; $i++) {

              $current_day = date('d');

              $selected = $i == $current_day ? 'selected="selected"' : '';

              echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
            }
            ?>
            </select>
            </div>

            <div class="float_l bs_date" style="margin-left: 7px;">
            <label class="ls_date">Месяц</label>
            <select id="dm_month" class="form-control ss_date">
              <?php
              $months = array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
              for ($i = 0; $i < count($months); $i++) {
                $current_month = date('m');
                $selected = $i + 1 == $current_month ? 'selected="selected"' : '';
                echo '<option value="'.($i + 1).'" '.$selected.'>'.$months[$i].'</option>';
              }
              ?>
            </select>
            </div>

            <div class="float_l bs_date" style="margin-left: 7px;">
            <label class="ls_date">Год</label>
            <select id="dm_year" class="form-control fiss_date">
              <?php
              echo '<option value="'.date('Y').'">'.date('Y').'</option>';
              echo '<option value="'.(date('Y') + 1).'">'.(date('Y') + 1).'</option>';
              ?>
            </select>
            </div>
          </div>
        </div>

        <div class="clear"></div>



      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn bg-info" onclick="orders.change_dm();">Изменить</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  var current_order_id = 0;
</script>


<div id="modal_add_product" class="modal fade">
  <div class="modal-dialog">
    <div id="disable_s_product" class="absolute all_null disable_content">
      <div class="absolute all_null spinner_load">
        <i class="icon-spinner2 spinner"></i>
      </div>
    </div>
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h6 class="modal-title">Добавление товаров в заказ</h6>
      </div>

      <div class="modal-body">
        <h6 class="text-semibold">Поиск товара</h6>
        <div class="relative full_w inp_search_product">
          <input autocomplete="off" onkeyup="orders.search_product(this);" id="i_search_product" class="full_w" type="text" name="" value="">
          <div class="absolute full_w search_pr_res" id="s_pr_result" style="display: none;"></div>
        </div>
        <div class="p_by_search">
          <div class="i_block c_radio">
            <span>Искать:</span>
          </div>&nbsp;&nbsp;&nbsp;
          <div class="i_block c_radio">
            <label for="s_by_name">
              <span>по названию</span>
              <input type="radio" class="r_type_search" checked="checked" name="type_search_product" id="by_name" value="by_name">
            </label>
          </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <div class="i_block c_radio">
            <label for="s_by_code">
              <span>по коду</span>
              <input type="radio" class="r_type_search" name="type_search_product" id="by_code" value="by_code">
            </label>
          </div>
        </div>

        <hr>
        <div class="" id="s_add_products"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn bg-info" onclick="orders.add_s_products();">Добавить</button>
      </div>
    </div>
  </div>
</div>






<div id="modal_add_client" class="modal fade">
  <div class="modal-dialog">
    <div id="disable_s_client" class="absolute all_null disable_content">
      <div class="absolute all_null spinner_load">
        <i class="icon-spinner2 spinner"></i>
      </div>
    </div>
    <div class="modal-content">
      <div class="modal-header bg-info">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h6 class="modal-title">Справочник</h6>
      </div>

      <div class="modal-body">
        <h6 class="text-semibold">Поиск клиента</h6>
        <div class="relative full_w inp_search_product">
          <input autocomplete="off" onkeyup="orders.search_client(this);" id="i_search_client" class="full_w" type="text" name="" value="">
          <div class="absolute full_w search_pr_res" id="s_pr_result_client" style="display: none;"></div>
        </div>
        <div class="p_by_search">
          <div class="i_block c_radio">
            <span>Искать:</span>
          </div>&nbsp;&nbsp;&nbsp;
          <div class="i_block c_radio">
            <label for="s_by_name">
              <span>по названию</span>
              <input type="radio" class="r_type_search" checked="checked" name="type_search_product" id="by_name_client" value="by_name">
            </label>
          </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <div class="i_block c_radio">
            <label for="s_by_code">
              <span>по телефону</span>
              <input type="radio" class="r_type_search" name="type_search_product" id="by_code_client" value="by_code">
            </label>
          </div>
          <div class="btn btn-xs btn-primary float_r" onclick="location.href='https://zakaz.club/applications/shop/?q=points&org_id=<?php echo $var_org_id;?>&app_id=<?php echo $var_app_id;?>';">Новый клиент</div>
          <div class="clear"></div>
        </div>

        <hr>
        <div class="" id="s_add_products_client">
          <table width="100%">
            <thead>
              <tr>
                <td>
                </td>
                <td>
                  Имя
                </td>
                <td>
                  Телефон
                </td>
                <td>
                </td>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>


<div class="relative full_w">

  <div class="">
    <h2 class="i_block" style="margin: 0px; margin-bottom: 20px;">Заказы</h2>
    <div class="float_r wr_btn_co">
      <a href="?q=arhive_orders<?php echo $save_shop_info; ?>">
        <div class="btn btn-xs btn-brown">Архив заказов</div>
      </a>
    </div>
  </div>

<style media="screen">
  .ord_sort .form-group{
    margin-bottom: 0px;
  }
</style>

<div class="panel panel-flat padd_ten">
  <div class="ord_sort">
    <div class="i_block form-group">
      <label>Показывать</label>

      <?php

      $selected_all = $type_view_orders == '' || $type_view_orders == 'all' ? 'selected="selected"' : '';
      $selected_active = $type_view_orders == 'active' ? 'selected="selected"' : '';
      $selected_apply = $type_view_orders == 'apply' ? 'selected="selected"' : '';
      $selected_in_way = $type_view_orders == 'in_way' ? 'selected="selected"' : '';
      $selected_done = $type_view_orders == 'done' ? 'selected="selected"' : '';
      $selected_reject = $type_view_orders == 'reject' ? 'selected="selected"' : '';
      $selected_return = $type_view_orders == 'return' ? 'selected="selected"' : '';

      echo '<select class="selectbox" onchange="orders.sort_by_type(this)">';
      echo '<option '.$selected_all.' value="none" data-icon="icon-circle-small">Все</option>';
      echo '<option '.$selected_apply.' value="active" data-icon="icon-hour-glass2">Активные</option>';
      echo '<option '.$selected_apply.' value="apply" data-icon="icon-clipboard2">Подтверженные</option>';
      echo '<option '.$selected_active.' value="waiting" data-icon="icon-spinner10">В обработке</option>';
      // echo '<option '.$selected_in_way.' value="in_way" data-icon="icon-redo2">Отправлен</option>';
      echo '<option '.$selected_done.' value="in_way" data-icon="icon-truck">В пути</option>';
      echo '<option '.$selected_done.' value="done" data-icon="icon-plus3">Доставлен</option>';
      echo '<option '.$selected_reject.' value="reject" data-icon="icon-x">Отклоненные</option>';
      echo '<option '.$selected_return.' value="return" data-icon="icon-reply">Возврат</option>';
      echo '</select>';
      ?>

    </div>

    <div class="i_block form-group" style="margin-left: 10px;">
      <label>Сортировать по</label>

      <?php

      $selected_by_number = $orders_sort_by == '' || $orders_sort_by == 'by_number' ? 'selected="selected"' : '';
      $selected_by_date = $orders_sort_by == 'by_date' ? 'selected="selected"' : '';
      $selected_by_price_top = $orders_sort_by == 'by_price_top' ? 'selected="selected"' : '';
      $selected_by_by_price_bottom = $orders_sort_by == 'by_price_bottom' ? 'selected="selected"' : '';

      echo '<select class="selectbox" onchange="orders.change_sort_by(this)">';
      echo '<option '.$selected_by_number.' value="by_number">По номеру заказа</option>';
      echo '<option '.$selected_by_date.' value="by_date">По дате</option>';
      echo '<option '.$selected_by_price_top.' value="by_price_top">По возрастанию цены</option>';
      echo '<option '.$selected_by_by_price_bottom.' value="by_price_bottom">По убыванию цены</option>';
      echo '</select>';
      ?>

    </div>

  </div>

  <div class="clear">

  </div>

</div>

  <div class="clear"></div>
</div>


<div class="relative">
  <div class="absolute all_null prldr_table" id="prldr_table">
    <div class="absolute all_null spin_prld">
      <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
    </div>
  </div>
<div class="panel-group panel-group-control panel-group-control-right" id="orders_content">

  <?php

  $html_points_pay = $points_pay > 0
      ? '<small class="g_color">(Оплачено - '.number_format($points_pay, 2, ',', ' ').' руб)</small>'
      : '<small class="r_color">(Не оплачен)</small>';

      $html_who_return_order = $order_return_admin > 0 ? ' <small >(возвращен администратором)</small>' : ' <small>(возвращен пользователем)</small>';

      $html_pay_confirm = $order_pay_confirmed > 0 ? '<small class="g_color">(Оплачено '.number_format((float)$pay_withdraw_amount, 2, ',', ' ').' руб, поступило на счет '.number_format((float)$pay_amount, 2, ',', ' ').')</small>' : '<small class="r_color">(Не оплачен)</small>';

      $html_pay_online = $order_type_pay == 'online' ? $html_pay_confirm : '';
      $html_points_pay = $order_type_pay == 'points' ? $html_points_pay : '';

      $html_type_pay = '<strong class="g_color">'.$arr_types_pay[$order_type_pay].' '.$html_pay_online.$html_points_pay.'</strong>';


      $order_dm_name = $order_dm_name == '' ? $order_fix_dm_name : $order_dm_name;

      $order_user_phone = '+'.substr($order_user_phone, 0, 1).' ('.substr($order_user_phone, 1, 3).') '.substr($order_user_phone, 4, 3).'-'.substr($order_user_phone, 7, 2).'-'.substr($order_user_phone, 9, 2);


      $order_total_summa = number_format($order_total_summa, 2, ',', ' ');
      $order_cost_delivery = number_format($order_cost_delivery, 2, ',', ' ');

      $data_element = 'data-orderid="'.$order_id.'"';


      echo '<div class="panel relative panel-white order_item" id="s_order_'.$order_id.'">';

      // $q_count_comments = sprintf("SELECT * FROM
      //   `shop_orders_messages`
      // WHERE
      // `shop_orders_messages`.`user_id` = '".$order_user_id."'"." AND
      // `shop_orders_messages`.`item_id` = '".$order_id."'"." AND
      // `shop_orders_messages`.`direction` = 'outbox' AND
      // `shop_orders_messages`.`readed` != '1' AND
      // `shop_orders_messages`.`its_admin` = 'false'");
      // $r_count_comments = mysql_query($q_count_comments) or die("cant execute message");
      // $n_count_comments = mysql_numrows($r_count_comments); // or die("cant get numrows message");e
      if($n_count_comments > 0){
        echo '<div class="absolute count_commnets" id="count_comments_'.$order_id.'" title="Количество новых комментариев">'.$n_count_comments.'</div>';
      }


        echo '<div class="panel-heading">';
          echo '<h6 class="panel-title">';
            echo '<div class="cursor_p" style="width: 100%;" id="btn_soi_'.$order_id.'" '.$data_element.' onclick="orders.show_order_items(this)">';
            echo '<div class="i_block v_align_middle" style="margin-right: 10px;">';
            echo '<input class="checked_order" onclick="event.stopPropagation();" value="'.$order_id.'" type="checkbox" checked="checked">';
            echo '</div>';

              if($order_user_id == 0){
                // echo '<i class="icon-bag position-left text-slate"></i> Заказ № '.$order_number_pp.', от '.$order_user_name.'';
                echo ' Заказ № '.$order_number_pp.', от '.$order_user_name.'';
              } else{
                // echo '<i class="icon-bag position-left text-slate"></i> Заказ № '.$order_number_pp.', от <a onclick="event.stopPropagation();" target="_blank" class="i_block" href="/view_user.php?id='.$order_user_id.'">'.$order_user_name.'</a>';
                echo ' Заказ № '.$order_number_pp.', от <a onclick="event.stopPropagation();" target="_blank" class="i_block" href="/view_user.php?id='.$order_user_id.'">'.$order_user_name.'</a>';
              }
              echo '<span class="float_r arrow_order_show" style="margin-left: 10px;"><i id="fa_o_'.$order_id.'" class="fa fa-chevron-right"></i></span>';
              echo '<span class="float_r">Общая сумма заказа: <span id="o_total_summa_'.$order_id.'">'.$order_total_summa.'</span>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></span>';
            echo '</div>';
          echo '</h6>';

          $order_reason_return_display = $order_status == 'return' ? 'display: block;' : 'display: none;';
          echo '<div class="ordr_info" id="order_reason_return_'.$order_id.'" style="'.$order_reason_return_display.'">';
            echo '<div class="order_reason_return">';
              echo '<strong>';
                echo '<small class="r_color">Возвращен по причине: <span id="text_reason_return_'.$order_id.'" class="r_color">'.$order_reason_return.$html_who_return_order.'</span></small>';
              echo '</strong>';
              echo '</div>';
              echo '</div>';

            $order_status_info_display = $order_status != 'active' ? 'display: block;' : 'display: none;';
            echo '<div class="order_reason_return" id="order_status_date_'.$order_id.'" style="'.$order_status_info_display.'">';
              echo '<strong>';
                echo '<small class="g_color">';
                  echo 'Дата <span class="g_color" id="text_status_name_'.$order_id.'">'.$arr_status_translate[$order_status].'</span>: <span class="g_color" id="text_date_status_'.$order_id.'">'.gmdate("d.m.y в H:i", $date_update_status + $_SESSION['time_offset']).'</span>';
                echo '</small>';
              echo '</strong>';
            echo '</div>';

            echo '<div class="float_l" style="margin-top: 10px;">';
            echo '<strong>Статус: <span id="order_status_'.$order_id.'">'.$arr_statuses[$order_status].'</span></strong>';
            echo '</div>';
            echo '<div class="clear"></div>';
            echo '<div class="float_l" style="margin-top: 10px;">';
            echo '<strong>Дата заказа: '.date("d.m.y в H:i", $order_create_date + $_SESSION['time_offset']).'</strong>';
            echo '</div>';
            echo '<div class="float_r o_info">';
            echo '<div class="cursor_p btn_delete_order float_r" '.$data_element.' onclick="orders.delete_order(this,'.$order_id.')"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;Удалить заказ</div>';
            echo '<div class="cursor_p btn_delete_order float_r" '.$data_element.' onclick="orders.show_add_comment(this,'.$order_id.')">Добавить комментарий</div>';
            echo '</div>';
            echo '<div class="clear"></div>';
          echo '</div>';
        // echo '</div>';
        echo '<div id="o_hidden_items_'.$order_id.'" class="panel-collapse collapse orders_items" aria-expanded="false" style="display: none;">';

          echo '<div class="order_info_items">';
            echo '<div class="ord_info_item">';
              echo '<div class="i_block">Способ доставки:</div>';
              echo '<strong>'.$order_dm_name.', г.'.$order_city_name.', '.$translate_dm_typs[$type_dm].'</strong>';
              echo ' &nbsp;<a style="text-decoration: underline; font-size: 12px;" onclick="orders.show_change_dm('.$order_id.')">( Изменить )</a>';
            echo '</div>';
            if($order_dm_id == 2){
              echo '<div class="ord_info_item">';
                echo '<div class="i_block">Адрес доставки:</div>';
                echo '<strong>'.$order_user_address.'</strong>';
              echo '</div>';
            }
            echo '<div class="ord_info_item">';
              echo '<div class="i_block">Клиент:</div>';
              echo '<span id="client_label'.$order_id.'">';
              echo '<strong>'.$order_user_name.', '.$order_user_phone.'</strong>';
              echo ' &nbsp;<a style="text-decoration: underline; font-size: 12px;" onclick="orders.show_change_client('.$order_id.')">( Изменить )</a>';
              echo '</span>';
              echo '<span id="client_label_edit'.$order_id.'" style="display:none;">';
              echo '<div class="dropdown_default2"><div class="dropdown_blocks"><div class="dropdown_block2">
              <input type="text" autocomplete="off" placeholder="Пользователь" style="color:#000000;" class="dropdown_title2 to_user_name" id="to_user_name'.$order_id.'" value="'.$order_user_name.'" onkeyup="orders.get_user_create_order_drops(event,this,'.$order_id.')">
              </div></div><div tabindex="983068039" id="dropdown_default2_list_hide_user_drops'.$order_id.'" class="dropdown_default2_list dropdown_default2_list_hide"><ul class="dropdown_default2_list_ul" id="to_user_name_drops_ul'.$order_id.'"><li class="dropdown_default2_option " onclick="dropdown_opt_click(this);" data-value=""></li></ul></div></div>';
              // echo '<input type="text" class="to_user_name" id="to_user_name'.$order_id.'" value="'.$order_user_name.'" style="border: 1px solid #ccc;font-weight:500;" placeholder="Пользователь">';
              echo '<input type="text" class="to_user_phone" id="to_user_phone'.$order_id.'" value="'.$order_user_phone.'" style="border: 1px solid #ccc;margin-left:10px;font-weight:500;" placeholder="Телефон" onkeyup="orders.get_user_create_order(event,this,'.$order_id.')">';
              echo '<div class="btn btn-xs btn-success" onclick="orders.set_client_orders('.$order_id.')" style="padding: 0px 10px; margin-left: 10px;">Сохранить</div>';
              echo '<div><a onclick="orders.show_client_orders_modals('.$order_id.');" style="font-size:12px;text-decoration:underline;">Справочник</a></div>';
              echo '</span>';
            echo '</div>';
            echo '<div class="ord_info_item">';
              echo '<div class="i_block">Способ оплаты:</div>'.$html_type_pay;
            echo '</div>';
            echo '<div class="ord_info_item">';
              echo '<div class="i_block">Стоимость доставки:</div>';
              echo '<strong>';
              echo '<input type="text" onchange="orders.change_cost_delivery(this,'.$order_id.')" id="cost_delivery_'.$order_id.'" value="'.$order_cost_delivery.'" style="border: 1px solid #ccc;"> руб.</strong>';
            echo '</div>';

            if($date_dm ){
              echo '<div class="ord_info_item">';
                echo '<div class="i_block">Дата доставки:</div>';
                echo '<strong >'.$date_dm.' </strong>';
              echo '</div>';
            }

            if($time_dm){
              echo '<div class="ord_info_item">';
                echo '<div class="i_block">Время доставки:</div>';
                echo '<strong >'.$time_dm.' </strong>';
              echo '</div>';
            }


            if($order_comment){
              echo '<div class="ord_info_item">';
                echo '<div class="i_block">Комментарий пользователя:</div>';
                echo '<strong >'.$order_comment.'</strong>';
              echo '</div>';
            }

            echo '<div class="ord_info_item">';
              echo '<div class="i_block v_align_middle">Скидка:</div>';
              echo '<strong class="v_align_middle" >';
              echo '<input id="margin_'.$order_id.'" value="'.$margin.'" type="text" style="border: 1px solid #ccc;">';
              echo '<div class="btn btn-xs btn-success" onclick="orders.set_margin('.$order_id.')" style="padding: 0px 10px; margin-left: 10px;">Применить</div>';
              echo '</strong>';
            echo '</div>';


            echo '<div class="ord_info_item">';
              echo '<div class="i_block v_align_middle">Тип цены:</div>';
              echo '<strong class="v_align_middle" >';
              echo '<select id="type_price_'.$order_id.'" style="width: 162px; outline: none; border: 1px solid #ccc;">';
              $selected_s = $order_type_price == 1 ? 'selected="selected"' : '';

              echo '<option value="0">Розничная</option>';
              echo '<option '.$selected_s.' value="1">Закупочная</option>';

              // $q_check_item = ("SELECT * FROM  `shop_types_prices`
              // WHERE `shop_types_prices`.`org_id`= '".$var_org_id."' AND `shop_types_prices`.`app_id` = '".$var_app_id."'
              // AND `shop_types_prices`.`fs_id` = '".$shop_from_shop."'");
              // $r_check_item = mysql_query($q_check_item) or die('cant');
              // $n_check_item = mysql_numrows($r_check_item); // or die("cant get numrows query");
              if($n_check_item > 0){
                for($p = 0; $p < $n_check_item; $p++) {
                  $id = htmlspecialchars(mysql_result($r_check_item, $p, "shop_types_prices.id"));
                  $name = htmlspecialchars(mysql_result($r_check_item, $p, "shop_types_prices.name"));

                  $selected = $id == $order_type_price ? 'selected="selected"' : '';

                  echo '<option '.$selected.' value="'.$id.'">'.$name.'</option>';

                }
              }


              echo '</select>';
              echo '<div class="btn btn-xs btn-success" onclick="orders.set_type_price('.$order_id.')" style="padding: 0px 10px; margin-left: 10px;">Применить</div>';
              echo '</strong>';
            echo '</div>';


            echo '<div class="ord_info_item">';
            // $q_com = ("SELECT `shop_orders_comments`.`id`,`shop_orders_comments`.`create_date`,`shop_orders_comments`.`comment`,`users`.`name`,`users`.`id` FROM `shop_orders_comments`
            //   INNER JOIN `users` ON (`users`.`id` = `shop_orders_comments`.`user_id`)
            //   WHERE `shop_orders_comments`.`order_id` = '".$order_id."'");
            //   $r_com = mysql_query($q_com) or die("cant execute query");
            //   $n_com = mysql_numrows($r_com); // or die("cant get numrows query");
              echo '<div class="i_block">Комментарии (<span id="o_count_comments_'.$order_id.'">'.$n_com.'</span>) <a style="text-decoration: underline;" onclick="orders.show_comments(this,'.$order_id.');">Показать</a></div>';
              echo '<strong style="display: none;" id="o_comments_'.$order_id.'">';
              if($n_com > 0){
                for ($c=0; $c < $n_com; $c++){
                  $c_item_id = htmlspecialchars(mysql_result($r_com, $c, "shop_orders_comments.id"));
                  $c_user_id = htmlspecialchars(mysql_result($r_com, $c, "users.id"));
                  $c_user_name = htmlspecialchars(mysql_result($r_com, $c, "users.name"));
                  $c_comment = htmlspecialchars(mysql_result($r_com, $c, "shop_orders_comments.comment"));
                  $create_date = htmlspecialchars(mysql_result($r_com, $c, "shop_orders_comments.create_date"));

                  echo '<div id="o_comment_'.$c_item_id.'" class="i_block v_align_middle" style="background: #ddddddbd;margin-right: 10px; margin-bottom: 10px; padding: 6px;border-radius: 4px;">';
                  echo '<table>';
                  echo '<tbody>';
                  echo '<tr>';
                  echo '<td>';
                  echo '<div><a href="/view_user.php?id='.$c_user_id.'" target="_blank">'.$c_user_name.'</a></div>';
                  echo '<div style="margin-top: 5px;">'.$c_comment.'</div>';
                  echo '<div class="text_right" style="margin-top: 5px; font-size: 12px; color: grey;">'.date('d.m.y в H:i',$create_date + $_SESSION['time_offset']).'</div>';
                  echo '</td>';
                  echo '<td>';
                  echo '<div style="padding: 0px 10px;"><i onclick="orders.delete_comment('.$c_item_id.','.$order_id.');" class="cursor_p fa fa-close" aria-hidden="true"></i></div>';
                  echo '</td>';
                  echo '</tr>';
                  echo '</tbody>';
                  echo '</table>';
                  echo '</div>';

                }
              }


              echo '</strong>';
            echo '</div>';


          echo '</div>';




          echo '<div class="">';
            echo '<table class="table datatable-select-checkbox dataTable no-footer" id="DataTables_Table_3" role="grid" aria-describedby="DataTables_Table_3_info">';
              echo '<thead>';
                echo '<tr role="row">';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Last Name: activate to sort column ascending">';
                  echo '<input type="checkbox" onclick="orders.checked_all_oi(this,'.$order_id.');" checked="checked">';
                  echo '</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Last Name: activate to sort column ascending">Артикул</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Job Title: activate to sort column ascending">Наименование</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Параметры</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Кол-во факт</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" class="text_center">Количество</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Цена за шт.</th>';
                  echo '<th tabindex="0" aria-controls="DataTables_Table_3" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Общая сумма</th>';
                  echo '<th class="text-center sorting_disabled" rowspan="1" colspan="1" aria-label="Actions" style="width: 100px;">Действия</th>';
                echo '</tr>';
              echo '</thead>';
              echo '<tbody id="o_items_'.$order_id.'">';
              echo '<tr role="row" id="o_item_21" class="odd">';
                echo '<td class="sorting_1">21</td>';
                echo '<td>1660</td>';
                echo '<td>чист СИФ 250мл крем Актив/Нормал Фреш</td>';
                echo '<td class="text_center">';
                  echo '<span class="label label-params" id="params_21">[params]</span>';
                echo '</td>';
                echo '<td class="text_center">';
                  echo '<span class="label label-danger" id="o_count_21">1</span>';
                echo '</td>';
                echo '<td>';
                  echo '<span class="label label-info">87,63&nbsp;<i class="fa fa-rub" aria-hidden="true"></i></span>';
                echo '</td>';
                echo '<td>';
                  echo '<span class="label label-success">';
                    echo '<span class="o_i_t_summa_23_21">87,63</span>&nbsp;<i class="fa fa-rub" aria-hidden="true"></i>';
                  echo '</span>';
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
              echo '</tbody>';
            echo '</table>';
            echo '<div class="text_right order_btns">';

            $st_selected_active = $order_status == 'active' || $order_status == '' ? 'selected="selected"' : '';
            $st_selected_apply = $order_status == 'apply' ? 'selected="selected"' : '';
            $st_selected_in_way = $order_status == 'in_way' ? 'selected="selected"' : '';
            $st_selected_done = $order_status == 'done' ? 'selected="selected"' : '';
            $st_selected_reject = $order_status == 'reject' ? 'selected="selected"' : '';
            $st_selected_return = $order_status == 'return' ? 'selected="selected"' : '';
            $st_selected_waiting = $order_status == 'waiting' ? 'selected="selected"' : '';

            $display_btn_add_product = $order_status == 'active' || $order_status == '' ? 'display: block;' : 'display: none';
            $display_btn_print_trade_check = $order_status == 'in_way' || $order_status == 'done' ||
            $order_status == 'reject' || $order_status == 'return' ? 'display: block;' : 'display: none';

              echo '<div onclick="orders.show_add_product(this);" '.$data_element.' style="'.$display_btn_add_product.'; margin-right: 10px;" class="float_l btn btn-xs btn-primary cursor_p btn_add_product_'.$order_id.'" style="padding: 1px 15px;">Добавить товар</div>';
              // echo '<a id="hpt_'.$order_id.'" class="btn_print_trade_check_'.$order_id.'" href="/applications/shop/print_trade_check.php?org_id='.$var_org_id.'&app_id='.$var_app_id.'&order_id='.$order_id.'" target="_blank"><div class="float_l btn btn-xs bg-brown"><i class="icon-printer2"></i>&nbsp;&nbsp;Печать товарного чека</div></a>';
              // echo '<a class="btn_print_trade_check_'.$order_id.'" target="_blank" href="/applications/shop/print_labels.php?order_id='.$order_id.'&org_id='.$var_org_id.'&app_id='.$var_app_id.'" id="hpl_'.$order_id.'"><div style="margin-left: 10px;" class="float_l btn btn-xs bg-brown"><i class="icon-printer2"></i>&nbsp;&nbsp;Печать этикеток</div></a>';

              echo '<div class="i_block v_align_middle select_order_status">';
                echo 'Статус заказа:';
                echo '<select id="select_order_status_'.$order_id.'" data-orderid="'.$order_id.'" onchange="orders.listen_change_order_status(this);" name="">';
                echo '<option '.$st_selected_active.' value="active">Активный</option>';
                echo '<option '.$st_selected_apply.' value="apply">Подтвержден</option>';
                echo '<option '.$st_selected_waiting.' value="waiting">В обработке</option>';
                echo '<option '.$st_selected_in_way.' value="in_way">В пути</option>';
                echo '<option '.$st_selected_done.' value="done">Доставлен</option>';
                echo '<option '.$st_selected_reject.' value="reject">Отклонен</option>';
                echo '<option '.$st_selected_return.' value="return">Возврат</option>';
                echo '</select>';
              echo '</div>';
              echo '<button type="button" onclick="orders.change_order_status(this);" '.$data_element.' class="btn btn-success" data-handle="apply">Изменить статус</button>';

              echo '<div class="full_w text_left o_cont_rtn" id="o_cont_rtn_'.$order_id.'">';
                echo '<span>Причина возврата</span>';
                echo '<div class="">';
                  echo '<input type="text" id="reason_return_'.$order_id.'" placeholder="Укажите причину возврата" class="full_w i_reason_return" name="" value="">';
                echo '</div>';
              echo '</div>';


              echo '</div>';



            echo '<div>';
            for ($r = 0; $r < count($sim_recipes); $r++) {
              $id = $sim_recipes[$r]['id'];
              $title = $sim_recipes[$r]['title'];
              $main_image = $sim_recipes[$r]['main_image'];
              echo '<div class="relative oth_rc_item">';
              echo '<a href="/applications/shop/?q=add_recipes&org_id='.$var_org_id.'&app_id='.$var_app_id.'&item_id='.$id.'" target="_blank">';
              echo '<div class="no_wrap oti_title">'.$title.'</div>';
              echo '<div class="oti_img">';
              if($main_image){
                echo '<img class="cover_img" src="http://fzakaz.xyz/shop_images/thumbnail_480/'.$main_image.'" alt="Шоколадный торт">';
              } else {
                echo '<img class="cover_img" src="/images/nophoto.jpg" alt="Шоколадный торт">';
              }
              echo '</div>';
              echo '</a>';
              echo '<a target="_blank" href="/applications/shop/print_rc.php?org_id='.$var_org_id.'&app_id='.$var_app_id.'&item_id='.$id.'" target="_blank">';
              echo '<div class="btn btn-xs bg-brown full_w" style="margin-top: 15px; padding: 1px;">Печать</div>';
              echo '</a>';
              echo '</div>';
            }
            echo '</div>';

            echo '<div class="order_comments padd_ten">';
              echo '<div class="">';
                echo '<h6>Комментарии к заказу:</h6>';
              echo '</div>';

              echo '<div class="" id="comments_order_'.$order_id.'">';


              // $q_message = ("SELECT * FROM
              //   `shop_orders_messages`
              //   INNER JOIN `users` ON (`users`.`id` = `shop_orders_messages`.`order_user_id`)
              // WHERE
              // `shop_orders_messages`.user_id = ".$order_id." AND
              //   `shop_orders_messages`.`deleted` = 0 ORDER BY `shop_orders_messages`.`id`");
              // $r_message = mysql_query($q_message) or die("cant execute message");
              // $n_message = mysql_numrows($r_message); // or die("cant get numrows message");e
              if ($n_message > 0) {
                  for ($m = 0; $m < $n_message; $m++){

                    $msg_id = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.id"));
                    $msg_message_id = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.message_id"));
                    $msg_content = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.content"));
                    $msg_user_name = htmlspecialchars(mysql_result($r_message, $m, "users.name"));
                    $msg_is_admin = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.its_admin"));
                    $msg_readed = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.readed"));
                    $msg_create_date = htmlspecialchars(mysql_result($r_message, $m, "shop_orders_messages.create_date"));

                    $readed_comment = $msg_readed == '1' ? 'readed_comment' : '';
                    $readed_comment_text = $msg_readed == '1' ? 'Сообщение прочитано' : 'Сообщение не прочитано';

                    if($msg_is_admin == 'true'){

                      echo '<div class="float_r full_w comment_item" id="o_komment_'.$msg_id.'">';
                        echo '<div class=""><strong class="firm1">Администратор</strong></div>';
                        echo '<div class="">'.$msg_content.'</div>';
                        echo '<div class="text_right">';
                          echo '<small>'.gmdate("d.m.y в H:i", $msg_create_date + $_SESSION['time_offset']).'</small>&nbsp;&nbsp;<i class="fa fa-check '.$readed_comment.'" aria-hidden="true" title="'.$readed_comment_text.'"></i>';
                        echo '</div>';
                      echo '</div>';

                    } else {

                      echo '<div class="float_l full_w comment_item" id="o_komment_'.$msg_id.'">';
                        echo '<div class=""><strong class="firm1">'.$msg_user_name.'</strong></div>';
                        echo '<div class="">'.$msg_content.'</div>';
                        echo '<div class="text_right">';
                          echo '<small>'.gmdate("d.m.y в H:i", $msg_create_date + $_SESSION['time_offset']).'</small>';
                        echo '</div>';
                      echo '</div>';

                    }


                }
              } else {

                echo '<div class="no_comments">Нет комментариев</div>';

              }



              echo '</div>';
              echo '<div class="clear"></div>';

              echo '<div class="">';
                echo '<div class="full_w ta_send_message" '.$data_element.' id="message_content_'.$order_id.'" style="padding: 10px; outline: none; border: 1px solid #ccc;" placeholder="Написать комментарий" contenteditable="true"></div>';
                echo '<div style="margin: 5px 0px;"><small style="color: grey;">Перенос строки Shift+Enter</small></div>';
                echo '<div class="text_right" style="margin-top: 10px;">';
                  echo '<div class="btn btn-xs btn-info" onclick="orders.send_order_message(this)" '.$data_element.'>Отправить комментарий</div>';
                echo '</div>';
              echo '</div>';


            echo '</div>';



          echo '</div>';
        echo '</div>';
      echo '</div>';

  ?>



</div>
</div>

  <div class="dataTables_paginate paging_simple_numbers" id="swith_page_product">

    <?php
    $items_per_page = 10;

    $page_count = 0;
    if (0 === $n_count_orders) {
    } else {
      $page_count = (int)ceil($n_count_orders / $items_per_page);
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

    if ($page!=1) echo '<a onclick="orders.switch_pages(this);" data-page="'.($page-1).'" data-direct="previous" class="paginate_button previous" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_previous">←</a>';
    echo '<span id="nums_pages_pr">';
    for ($i = $start_page_count; $i <= $end_page_count; $i++) {
      if($page_count <= 1) continue;
      if ($i === $page) {
        echo '<a class="paginate_button current page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="1" tabindex="0">'.$i.'</a>';
      } else {
        echo '<a onclick="orders.switch_pages(this);" data-page="'.$i.'" class="paginate_button page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0">'.$i.'</a>';
      }
    }
    echo '</span>';
    if ($page!=$page_count) echo '<a onclick="orders.switch_pages(this);" data-page="'.($page+1).'" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" id="btn_sw_next">→</a>';
    } else{
      echo '<span id="nums_pages_pr"></span>';
    }

    ?>
  </div>

<script src="/include/apps/shop/for_edit_panels/pages/orders/script.js" charset="utf-8"></script>
<script src="/applications/shop/js/selects/form_selectbox.js" charset="utf-8"></script>
<script src="/applications/shop/js/selects/selectboxit.min.js" charset="utf-8"></script>
<script src="/applications/shop/js/scripts/orders.js?ver=<?=rand(1,10000000); ?>" charset="utf-8"></script>
<script type="text/javascript">
orders.init();
$('.to_user_phone').mask("+7 (999) 999-99-99",{
  placeholder: "+7 (999) 999-99-99"
});
function isFunction(functionToCheck) {
 return functionToCheck && {}.toString.call(functionToCheck) === '[object Function]';
}

var loader_products = new Init_load_content({
  url: this.url,
  page: parseInt(create_order.current_page_all) + 1,
  parent_offset_element: isMobile.any() ? $('#modal_add_items')[0] : $('#tbs_scroll_y')[0],
  scroll_element: isMobile.any() ? $('#modal_add_items')[0] : $('#tbs_scroll_y')[0],
  offset_element: isMobile.any() ? $('#db_s_pr')[0] : $('#db_s_pr')[0],
  before_load: function(){

    this.mini_preload = document.createElement('tr');
    $(this.mini_preload).addClass('text_center');
    this.mini_preload.innerHTML = '<td colspan="10" class="mini_preload"><strong>Загрузка..</strong></div>';

    this.mini_preload_mobile = document.createElement('div');
    $(this.mini_preload_mobile).addClass('text_center');
    this.mini_preload_mobile.innerHTML = '<div class="mini_preload"><strong>Загрузка..</strong></div>';
    $('#contr_s_content').append(this.mini_preload);
    $('#mobile_contr_s_content').append(this.mini_preload_mobile);
  },
  load_request: function(_call){

    $('#offset_element').animate({'scrollTop': '0px'},10);

    var self = this;

    var _this = create_order;

    var search_str = $('#i_search_product_all').val();
    var search_by = $('.r_type_search[name="al_type_search_product"]:checked').val();

    var action_select = typeof _this.action_select === 'undefined' ? 'create_order.handle_input_count(event,this);' : _this.action_select;

    $.ajax({
      url: create_order.url,
      method: 'post',
      dataType:'json',
      data: {
        action: 'show_all_products',
        org_id: shop_info.org_id,
        app_id: shop_info.app_id,
        action_select: action_select,
        fs: shop_info.fs,
        page: self.page,
        count_show: 20,
        search_str: search_str,
        selected_items: _this.selected_products,
        parent_id: _this.current_parent_id,
        search_by: search_by
      },
      success: function(data){
        $('#preloader_body_all').hide();
        if(data.result == 'true'){
          $(self.mini_preload).remove();
          $(self.mini_preload_mobile).remove();

          _this.current_page_all = data.page;

          if(data.is_last === false){
            $('#products_s_content').append(data.html);
            $('#mobile_products_s_content').append(data.mobile_html);
          }
          _this.count_products_all = data.count_all_products;

          // $('#contr_html_map').html(data.html_map);

          if($('#modal_add_items').css('display') == 'none') $('#modal_add_items').modal('show');

          $('#preloader_body_ap').hide();
          return _call(data);
        } else{
          message.show(data.string);
          $('#preloader_body_ap').hide();
        }
      },
      error: function(e){
        console.log(e);
        $('#preloader_body_ap').hide();
        $('#preloader_body_all').hide();
      }
    });
  }
});

</script>
