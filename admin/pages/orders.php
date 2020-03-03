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

<link rel="stylesheet" href="/admin/css/orders.css">
<?php

include_once($root_dir.'/include/classes/orders.php');

$orders = new Orders();

$type_view_orders = $_GET['type_orders'];
$orders_sort_by = $_GET['sort_by'];


$page = 1;
if(!empty($_GET['page'])) {
  $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
  if(false === $page) {
    $page = 1;
  }
}

$orders_info = $orders->get_orders();

$orders_html = $orders_info['html'];
$pages_html = $orders_info['pages_html'];

echo '<div id="orders_info" data-info=\'{"page": "'.(int)$page.'",';
echo '"count_show": "'.(int)$count_show_orders.'",';
echo '"count_orders": "'.$n_count_orders.'",';
echo '"sort_by": "'.$orders_sort_by.'",';
echo '"type_orders": "'.$type_view_orders.'"';
echo '}\'></div>';
?>

<div class="relative full_w">

  <div class="">
    <h2 class="i_block" style="margin: 0px; margin-bottom: 20px;">Заказы</h2>
    <div class="float_r wr_btn_co">
      <a href="?q=arhive_orders<?php echo $save_shop_info; ?>">
        <div class="btn btn-xs btn-brown">Архив заказов</div>
      </a>
    </div>
  </div>

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

      echo '<select class="form-control" onchange="orders.sort_by_type(this)">';
      echo '<option '.$selected_all.' value="none">Все</option>';
      echo '<option '.$selected_apply.' value="active">Активные</option>';
      echo '<option '.$selected_apply.' value="apply">Подтверженные</option>';
      echo '<option '.$selected_active.' value="waiting">В обработке</option>';
      echo '<option '.$selected_done.' value="in_way">В пути</option>';
      echo '<option '.$selected_done.' value="done">Доставлен</option>';
      echo '<option '.$selected_reject.' value="reject">Отклоненные</option>';
      echo '<option '.$selected_return.' value="return">Возврат</option>';
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

      echo '<select class="form-control" onchange="orders.change_sort_by(this)">';
      echo '<option '.$selected_by_number.' value="by_number">По номеру заказа</option>';
      echo '<option '.$selected_by_date.' value="by_date">По дате</option>';
      echo '<option '.$selected_by_price_top.' value="by_price_top">По возрастанию цены</option>';
      echo '<option '.$selected_by_by_price_bottom.' value="by_price_bottom">По убыванию цены</option>';
      echo '</select>';
      ?>
    </div>
  </div>

  <div class="clear"></div>

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
  echo $orders_html;
  ?>
</div>
</div>

  <div class="dataTables_paginate paging_simple_numbers" id="swith_page_product">
    <?php
    echo $pages_html;
    ?>
  </div>
<script src="/admin/js/scripts/orders.js?ver=<?php echo rand(100,100000000000); ?>" charset="utf-8"></script>
<script type="text/javascript">
orders.init();
</script>
