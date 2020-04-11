<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
if(!class_exists('ProductsOptions')) include($root_dir.'/admin/modules/options/products_options.php');


$init_options = new ProductsOptions();
$init_catlog = new Catalog();

$page = (int)$_GET['page'];
$page = $page <= 0 ? 1 : $page;
$parent_id = (int)$_GET['parent_id'];
$selected_options = isset($_GET['options']) ? $_GET['options'] : '';
$arr_selected_options = explode(',',$selected_options);

$current_cat_info = $init_catlog->get_product_info($parent_id);
$is_last_cat = $current_cat_info === false ? 1 : $current_cat_info['is_last'];
$catalog_options = $current_cat_info !== false ? $current_cat_info['catalog_options'] : array();

$catalogs_info = $init_catlog->get_catalogs(array(
  'parent_id' => $parent_id
));

$products_info = $is_last_cat > 0 ? $init_catlog->get_products(array(
  'page' => $page,
  'parent_id' => $parent_id,
  'options' => $selected_options
)) : false;


$properts = $init_options->get_properts();

$products = $products_info !== false ? $products_info['products'] : array();
$catalogs = $catalogs_info['catalogs'];
$count_products = count($products);
$count_all_products = (int)$products_info['count_all_products'];
$count_catalogs = count($catalogs);
$products_html = $products_info['html'];
$cat_path_html = $catalogs_info['cat_path_html'];
$cat_tree_html = $catalogs_info['tree_html'];
$pages_html = $products_info['pages_html'];
?>
<div class="row">
  <div class="col-md-12">
    <div class="row">
        <?php echo $cat_path_html; ?>
    </div>
  </div>
  <div class="col-md-3">
    <div class="catalog">
      <div class="w_color catalog_header">
        <i class="w_color fa fa-bars" aria-hidden="true"></i>&nbsp;
        Категории
      </div>
      <div class="catalog_wrap">
        <ul class="list-group catalog_list">
          <?php
          echo $cat_tree_html;
          ?>
        </ul>
      </div>
    </div>
    <?php
    if($parent_id > 0 && $is_last_cat > 0){
      foreach ($properts as $key => $value) {
        $propert_id = $value['id'];
        $propert_name = $value['name'];

        echo '<hr>';
        echo '<div class="wrap_params">';
          echo '<h4>'.$propert_name.'</h4>';
          echo '<div class="text-center">';
            echo '<ul class="params_ list-group flex-wrap flex-row">';
            $options = $value['child'];
            for ($i = 0; $i < count($options); $i++) {
              $opts = $options[$i];
              $opt_id = $opts['id'];

              if(!in_array($opt_id,$catalog_options)) continue;

              $active_opt = in_array($opt_id,$arr_selected_options) ? 'active_param_item' : '';
              $opt_name = !empty($opts['color']) ? '' : $opts['name'];
              $background_color = !empty($opts['color']) ? 'background: '.$opts['color'].';' : '';

              echo '<li onclick="catalog.pick_option(this)" data-optionid="'.$opt_id.'" class="cursor_p param_item '.$active_opt.'" style="'.$background_color.'">';
                echo '<span>'.$opt_name.'</span>';
              echo '</li>';

            }
            echo '</ul>';
          echo '</div>';

        echo '</div>';

      }
    }
    ?>

  </div>
  <div class="col-md-9">
    <div class="d-flex flex-row align-items-end">
      <div class="mr-auto">
        Товров: <span id="count_all_products"><?php echo $count_all_products; ?></span>
      </div>
      <div class="ml-auto">
        <select class="form-control" id="catalog_sort_by" onchange="catalog.pick_sort_by(this.value)" name="">
          <option value="default">Сортировать по:</option>
          <option value="price_high_to_low">По возрастанию цены</option>
          <option value="price_low_to_high">По убыванию цены</option>
        </select>
      </div>
    </div>

    <hr>

    <div class="relative products_wrap row" id="products_wrap">
      <?php
      if($is_last_cat == 0 && $count_catalogs > 0){
        foreach ($catalogs as $key => $value){
          $id = $value['id'];
          $name = $value['name'];
          $price = $value['price'];
          $main_image = $value['main_image'];

          $image_src = empty($main_image) ? '/images/no_img.png' : '/images/catalog/t_480/'.$main_image;

          echo '<div class="col-md-3 b_catalog">';
          echo '<a href="/catalog.php?parent_id='.$id.'">';
          echo '<div class="catalog_img">';
          echo '<img src="'.$image_src.'" alt="'.$name.'">';
          echo '<div class="catalog_body">';
          echo '<h4 class="catalog_title">'.$name.'</h4>';
          echo '</div>';
          echo '</div>';
          echo '</a>';
          echo '</div>';


        }

      } else if($count_products > 0){
        echo $products_html;
      } else{
        echo '<div class="text_center"><strong>Нет товаров</strong></div>';
      }

      ?>
    </div>

    <div class="pages_bar" id="pages_bar">
      <?php echo $pages_html; ?>
    </div>

  </div>
</div>

<?php
echo '<div id="catalog_info" data-info=\'{';
  echo '"page": "'.$page.'",';
  echo '"parent_id": "'.$parent_id.'",';
  echo '"selected_options": "'.$selected_options.'"';
echo '}\'></div>';
?>

<script type="text/javascript">
  function get_child_cats(event,element,id){
    event.preventDefault();
    event.stopPropagation();
    if($('#child_cats_' + id).css('display') == 'none'){
      $(element).addClass('rotate_90');
      $('#child_cats_' + id).animate({height:'show'},200);
    } else{
      $(element).removeClass('rotate_90');
      $('#child_cats_' + id).animate({height:'hide'},200);
    }
  }
  $(document).ready(function(){
    catalog.init();
  });
</script>

<?php
include($root_dir.'/include/blocks/footer.php');
?>
