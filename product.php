<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
if(!class_exists("Catalog")) include_once($root_dir.'/include/classes/catalog.php');
if(!class_exists('ProductsOptions')) include($root_dir.'/admin/modules/options/products_options.php');


$product_id = (int)$_GET['id'];

$init_options = new ProductsOptions();
$init_catlog = new Catalog();

$allowed_properts = $init_options->get_allowed_properts($product_id);

$product_info = $init_catlog->get_product_info($product_id);
$product_options = $init_options->get_options($product_id,$allowed_properts);
$count_product_options = count($product_options);

$name = $product_info['name'];
$description = $product_info['description'];
$main_image = $product_info['main_image'];

?>

<div class="float_l vp_left_grid">
  <div class="vp_image">
    <div class="float_l product_images">
      <div class="ltl_img">
        <img src="/images/leo.jpg" alt="">
      </div>
      <div class="ltl_img">
        <img src="/images/leo.jpg" alt="">
      </div>
      <div class="ltl_img">
        <img src="/images/leo.jpg" alt="">
      </div>
    </div>
    <div class="float_l product_main_image">
      <img src="/images/leo.jpg" alt="">
    </div>
  </div>
</div>
<div class="float_r vp_right_grid">
  <div class="vp_info_item vp_name">
    <h1><?php echo $product_info['name']; ?></h1>
  </div>
  <div class="vp_info_item vp_price">
    <strong>$<?php echo $product_info['price']; ?></strong>
  </div>


  <?php
  foreach ($allowed_properts as $key => $value) {
    echo '<div class="vp_info_item">';
      echo '<strong>'.$value.':</strong>';
      echo '<div id="vp_propert_'.$key.'">';
      $priv_items = array();
      for ($i = 0; $i < $count_product_options; $i++) {
        $opts = $product_options[$i]['options'][$key];
        if(in_array($opts['id'],$priv_items)) continue;
        array_push($priv_items,$opts['id']);
        // $active_opt = $i == 0 ? 'active_opt' : '';
        $element_id = 'id="opt_'.$key.'_'.$opts['id'].'"';
        echo '<div onclick="basket.pick_option(this);" '.$element_id.' data-propertid="'.$key.'" data-optid="'.$opts['id'].'" class="float_l item_prop_'.$key.' item_opt_'.$opts['id'].' opt_item '.$active_opt.'">';
        echo !empty($opts['color']) ? '<div class="opt_backr" style="background: '.$opts['color'].'"></div>' : '<div class="opt_text text_center">'.$opts['name'].'</div>';
        echo '</div>';
      }
      echo '</div>';
      echo '<div class="clear"></div>';
    echo '</div>';
  }
  ?>

  <div class="vp_info_item" style="margin-top: 15px;">
    <table class="text_center basket_btns i_block v_align_middle">
      <tbody>
        <tr>
          <td>
            <div onclick="basket.spl_update_quan(this);" data-itemid="<?php echo $product_id; ?>" data-dir="m" class="cursor_p basket_btn_n disable_select_text">
              <i class="fa fa-minus" aria-hidden="true"></i>
            </div>
          </td>
          <td><input class="vp_b_quan text_center bsk_quan_<?php echo $product_id; ?>" type="number" value="1"></td>
          <td>
            <div onclick="basket.spl_update_quan(this);" data-itemid="<?php echo $product_id; ?>" data-dir="p" class="cursor_p basket_btn_n disable_select_text">
              <i class="fa fa-plus" aria-hidden="true"></i>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div class="btn btn_default" onclick="basket.add(this);" data-itemid="<?php echo $product_id; ?>">
      Добавить в корзину
    </div>
  </div>

  <div class="vp_info_item">
    <span>Описание:</span>
    <div class="">
      <?php echo $product_info['description']; ?>
    </div>
  </div>
</div>
<div class="clear"></div>

<?php

echo '<div id="properts_object" data-info=\'{';
    echo '"options": {';

      for ($i = 0; $i < $count_product_options; $i++) {
        $item = $product_options[$i];
        $child = $product_options[$i]['options'];
        echo '"'.$item['product_id'].'": {';
          echo '"price": "'.$item['price'].'",';
          echo '"quantity": "'.$item['quantity'].'",';
          echo '"items": {';
            $num = 0;
            foreach ($child as $key => $value) {

              $z = $num == count($child) - 1 ? '' : ',';

              echo '"'.$key.'" :{';
              echo '"id": "'.$value['id'].'",';
              echo '"name": "'.$value['name'].'",';
              echo '"color": "'.$value['color'].'"';
              echo '}'.$z;
              $num++;
            }

            echo '}';

          $z = $i == $count_product_options - 1 ? '' : ',';

          echo '}'.$z;


      }

      echo '}';
echo '}\'></div>';

?>

<script type="text/javascript">
var options;
var allowed = {};
$(document).ready(function(){
  var info = $('#properts_object').attr('data-info');
  options = JSON.parse(info).options;


  var optns = {};

  for (var pr_id in options) {
    var items = options[pr_id]['items'];
    optns[pr_id] = items;
  }

  for (var pr_id in optns) {
    for (var prop_id in optns[pr_id]) {

      if (!allowed.hasOwnProperty(prop_id)) {
        allowed[prop_id] = {};
      }

      if (!allowed[prop_id].hasOwnProperty(optns[pr_id][prop_id].id)) {
        allowed[prop_id][optns[pr_id][prop_id].id] = {};
      }

      for (var lpr_id in optns) {

        if(optns[lpr_id][prop_id].id == optns[pr_id][prop_id].id){

        } else continue;

        for (var lprop_id in optns[lpr_id]) {

          if(lprop_id != prop_id){



            if (!allowed[prop_id][optns[pr_id][prop_id].id].hasOwnProperty(lprop_id)) {
              allowed[prop_id][optns[pr_id][prop_id].id][lprop_id] = [];
            }

            if(allowed[prop_id][optns[pr_id][prop_id].id][lprop_id].indexOf(optns[lpr_id][lprop_id].id) == -1)
            allowed[prop_id][optns[pr_id][prop_id].id][lprop_id].push(optns[lpr_id][lprop_id].id);

          }
        }
      }
    }
  }


});


function show_p(propert_id,option_id) {

  $('.opt_item').hide();
  $('.item_prop_' + propert_id).show();

  for (var prop in allowed[propert_id][option_id]) {

    for (var i = 0; i < allowed[propert_id][option_id][prop].length; i++) {

      $('#opt_' + prop + '_' + allowed[propert_id][option_id][prop][i]).show();

    }

  }
}

</script>

<?php

include($root_dir.'/include/blocks/footer.php');
?>
