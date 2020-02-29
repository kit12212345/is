<!-- EVENT CHANGE HISTORY  $(window).on('popstate', function(e) {
	console.log('sad');
	return false;
    // current href: document.location.pathname
}); -->
<script src="/admin/js/database.js" charset="utf-8"></script>
<script src="/admin/js/database_basic.js" charset="utf-8"></script>
<script src="/admin/js/switch.min.js" charset="utf-8"></script>
<script src="/admin/js/switchery.min.js" charset="utf-8"></script>
<script src="/admin/js/form_checkboxes_radios.js" charset="utf-8"></script>

<script type="text/javascript">

$(window).on('popstate', function(e) {
  var _this = products;
  var url_vars = get_url_vars();
  var cat_id = url_vars['cat_id'];
  var page = url_vars['page'];
  var count_show = url_vars['count_show'];
  page = typeof page === 'undefined' ? 1 : page;
  count_show = typeof count_show === 'undefined' ? 10 : count_show;

  _this.current_parent_id = cat_id;
  _this.current_page = page;
  _this.count_show = count_show;

  return _this.show_products();
});
</script>
<?php
include_once($root_dir.'/include/classes/catalog.php');

$catalog = new Catalog();


$goods_per_page = (int)$_GET['goods_per_page'] > 0 ? (int)$_GET['goods_per_page'] : 25;

$goods_per_page = $goods_per_page < 10 ? 10 : $goods_per_page;
$goods_per_page = $goods_per_page > 100 ? 100 : $goods_per_page;

$search_str = $_GET['search_str'];
$search_by = $_GET['search_by'];

$search_str = mysql_real_escape_string($search_str);
$search_by = trim($search_by);

$parent_id = (int)$_GET['parent_id'];


$page = 1;
if(!empty($_GET['page'])){
  $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
  if(false === $page) {
    $page = 1;
  }
}


$products_info = $catalog->get_catalog(array(
  'parent_id' => $parent_id,
  'search_str' => $search_str,
  'search_by' => $search_by,
  'page' => $page,
  'goods_per_page' => $goods_per_page
));
$cat_path = $products_info['cat_path'];
$cat_path_html = $products_info['cat_path_html'];
$pages_html = $products_info['pages_html'];
$tree_cats = $catalog->get_tree_cats($parent_id);

$cat_path_cout = count($cat_path);
$products = $products_info['products'];
$count_products = count($products);

echo '<div id="products_info" data-info=\'{"parent_id": "'.$parent_id.'",';
echo '"goods_per_page": "'.(int)$goods_per_page.'",';
echo '"search_by": "'.$search_by.'",';
echo '"page": "'.(int)$page.'"';
echo '}\'></div>';

?>

<style media="screen">
  .product_img{
    width: 21px;
    height: 21px;
    margin-right: 8px;
  }
  .pr_map_cats{
    margin: 0px;
    padding: 0px;
    margin-top: 15px;
  }
  .pr_map_cats li{
    padding: 0px;
    display: inline-block;
    color: #026aa7;
    cursor: pointer;
  }
  .active_map_item {
    color: orange!important;
  }
</style>

  <div class="title_add_product">
    <h2 class="panel-title">Товары</h2>
  </div>

<div class="panel panel-flat">
  <div class="relative panel-heading">

    <div class="float_r i_block text_right v_align_bottom">
      <div class="dataTables_length pr_action_at_selected" id="DataTables_Table_0_length" style="margin-bottom: 0px;">
        <label>
          <?php
          echo '<select onchange="products.group_action(this)" name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="select2-hidden-accessible" tabindex="-1" aria-hidden="true">';
          echo '<option value="0">Действие к выбранным</option>';
          echo '<option value="change_cat">Изменить категорию</option>';
          echo '<option value="remove_from_sale">Снять с продажи</option>';
          echo '<option value="to_publish">Опубликовать</option>';
          echo '<option value="delete_product">Удалить</option>';
          echo '</select>';
          ?>
        </label>
      </div>
    </div>

    <div class="float_r">
      <div class="dataTables_length" id="DataTables_Table_0_length" style="margin-bottom: 0px;">
        <label>
          <span>Показать:</span>
          <?php

          $selected_10 = $goods_per_page == 10 ? 'selected' : '';
          $selected_25 = $goods_per_page == 25 ? 'selected' : '';
          $selected_50 = $goods_per_page == 50 ? 'selected' : '';
          $selected_100 = $goods_per_page == 100 ? 'selected' : '';

          echo '<select onchange="catalog.set_gpp(this.value)" class="select2-hidden-accessible" tabindex="-1" aria-hidden="true">';
            echo '<option value="10" '.$selected_10.'>10</option>';
            echo '<option value="25" '.$selected_25.'>25</option>';
            echo '<option value="50" '.$selected_50.'>50</option>';
            echo '<option value="100" '.$selected_100.'>100</option>';
          echo '</select>';
          ?>
        </label>
      </div>
    </div>


    <div class="i_block v_align_bottom">
      <label>
        <label>Категория:</label>
        <?php
        echo '<select onchange="catalog.set_parent_id(this.value);" class="full_w form-control sp_select_cat">';
        echo '<option value="0" style="min-width: 500px;">Без категории</option>';
        echo $tree_cats;
        echo '</select>';
        ?>
      </label>
    </div>
    <div>
      <div class="i_block v_align_bottom ">
        <div class="full_w">
          <label>Поиск:</label>
        </div>
        <label>
          <input onkeyup="catalog.search();" class="form-control" type="search" id="search_value" value="<?php echo $search_str; ?>" placeholder="Поиск...">
        </label>

      </div>

      <?php
      $checked_s_by_name = $search_by == '' || $search_by == 'by_name' ? 'checked="checked"' : '';
      $checked_s_by_code = $search_by == 'by_code' ? 'checked="checked"' : '';
      ?>

      <div class="i_block s_by_search v_align_bottom" style="margin-left: 10px;">
        <div class="i_block c_radio">
          <label for="s_by_name">
            <span>по названию</span>&nbsp;
            <input type="radio" onchange="catalog.set_search_by(this.value);" class="i_by_search" <?php echo $checked_s_by_name; ?> name="type_search_product" id="s_by_name" value="by_name">
          </label>
        </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <div class="i_block c_radio">
          <label for="s_by_code">
            <span>по коду</span>&nbsp;
            <input type="radio" onchange="catalog.set_search_by(this.value);" class="i_by_search" <?php echo $checked_s_by_code; ?> name="type_search_product" id="s_by_code" value="by_code">
          </label>
        </div>
      </div>
      <div class="float_r">
        <div class="i_block v_align_middle" style="margin-left: 10px;">
          <a href="?q=cat">
            <div class="btn btn-info">Добавить категорию</div>
          </a>
        </div>

        <div class="i_block v_align_middle" style="margin-left: 10px;">
          <a href="?q=product">
            <div class="btn btn-success">Добавить товар</div>
          </a>
        </div>

      </div>
    </div>
    <div>
      <ul class="pr_map_cats" id="catalog_map_cats">
        <?php echo $cat_path_html; ?>
      </ul>
    </div>


    <div class="clear"></div>
  </div>
  <hr style="margin: 0px;">
  <div class="relative panel-body">
    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer disable_table stickyTo">

      <div class="relative datatable-scroll">
        <table class="table_list_products table dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info">
          <thead>
            <tr role="row">
              <th class="tpr_select sorting_disabled table_checked_pr text_center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="First Name: activate to sort column descending">
                <input type="checkbox" id="checked_all_products">
              </th>
              <th class="tpr_code sorting_disabled text_center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="First Name: activate to sort column descending">Код</th>
              <th class="tpr_name sorting_disabled" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-sort="ascending" aria-label="First Name: activate to sort column descending">Наименование</th>
              <th class="tpr_price sorting_disabled text_center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Job Title: activate to sort column ascending">Цена</th>
              <th class="tpr_count sorting_disabled text_center" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="DOB: activate to sort column ascending">Количество</th>
              <th class="tpr_status sorting_disabled" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending">Статус</th>
              <th class="tpr_action text-center sorting_disabled" rowspan="1" colspan="1" aria-label="Actions" style="width: 100px;"></th>
            </tr>
          </thead>
          <tbody id="catalog_content">
            <?php
            echo $products_info['html'];
            ?>
            </tbody>
          </table>
        </div>

        <div class="datatable-footer" style="padding-bottom: 20px; margin-right: 20px;">
          <div class="dataTables_paginate paging_simple_numbers" id="swith_pages_wrap">
              <?php
              echo $pages_html;
              ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="/admin/js/scripts/catalog.js?ver=<?=rand(1,1000000); ?>" charset="utf-8"></script>
  <script type="text/javascript">
  </script>
