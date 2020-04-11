<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
if(!class_exists("Catalog")) include_once($root_dir.'/include/classes/catalog.php');
if(!class_exists('ProductsOptions')) include($root_dir.'/admin/modules/options/products_options.php');


$product_id = (int)$_GET['id'];

$init_options = new ProductsOptions();
$init_catlog = new Catalog();

$allowed_properts = $init_options->get_allowed_properts($product_id);

$properts = $init_options->get_properts();
$product_info = $init_catlog->get_product_info($product_id);
$product_options = $init_options->get_options($product_id,$allowed_properts);
$count_product_options = count($product_options);
$name = $product_info['name'];
$description = $product_info['description'];
$main_image = $product_info['main_image'];

$allowed_options = array();
foreach ($allowed_properts as $key => $value) {
  for ($i = 0; $i < $count_product_options; $i++) {
    $opts = $product_options[$i]['options'][$key];
    if(in_array($opts['id'],$allowed_options)) continue;
    array_push($allowed_options,$opts['id']);
  }
}

?>
<script type="text/javascript">
    $(document).ready(function(){
      // $('#product_main_image').zoom({url: 'photo-big.jpg'});
      $('#product_main_image')
      .wrap('<span style="display:inline-block"></span>')
      .css('display', 'block')
      .parent()
      .zoom();
    });
</script>
<div class="row">
  <div class="col-md-8">
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
        <a id="a_main_image" href="/images/leo.jpg">
          <img id="product_main_image" src="/images/leo.jpg" alt="">
        </a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="vp_info_item vp_name">
      <h1><?php echo $product_info['name']; ?></h1>
    </div>
    <div class="vp_info_item vp_price">
      <strong>$<?php echo $product_info['price']; ?></strong>
    </div>
    <style media="screen">
      .disabled_opt_item{
        background: linear-gradient(to top right, transparent 0%, transparent calc(50% - 1px), #b5b5b5 50%, transparent calc(50% + 1px), transparent 100%);
        background-color: #f5f5f5;
        cursor: not-allowed;
      }
    </style>

    <?php
    foreach ($properts as $key => $value) {
      $propert_id = $value['id'];
      $propert_name = $value['name'];
      echo '<div class="vp_info_item mt-2">';
        echo '<strong>'.$propert_name.':</strong>';
        echo '<div id="vp_propert_'.$propert_id.'">';
        $priv_items = array();
        $options = $value['child'];
        for ($i = 0; $i < count($options); $i++) {
          $opts = $options[$i];
          $element_id = 'id="opt_'.$propert_id.'_'.$opts['id'].'"';
          $disabled_class = in_array($opts['id'],$allowed_options) ? '' : 'disabled_opt_item';
          $onclick = in_array($opts['id'],$allowed_options) ? 'onclick="basket.pick_option(this);"' : '';

          echo '<div '.$onclick.' '.$element_id.' data-propertid="'.$propert_id.'" data-optid="'.$opts['id'].'" class="float_l item_prop_'.$propert_id.' item_opt_'.$opts['id'].' '.$disabled_class.' opt_item '.$active_opt.'">';
          echo !empty($opts['color']) ? '<div class="opt_backr" style="background: '.$opts['color'].'"></div>' : '<div class="opt_text text_center">'.$opts['name'].'</div>';
          echo '</div>';
        }
        echo '</div>';
        echo '<div class="clear"></div>';
      echo '</div>';
    }
    ?>

    <div class="vp_info_item mt-2">
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
      <div class="btn btn-secondary" onclick="basket.add(this);" data-itemid="<?php echo $product_id; ?>">
        Добавить в корзину
      </div>
    </div>

    <div class="vp_info_item mt-2">
      <span>Описание:</span>
      <div class="">
        <?php echo $product_info['description']; ?>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="">
  <h3>Похожие товары</h3>
  <div class="row">
    <?php
    for ($i=0; $i < 4; $i++) {
      $image_src = '/images/leo.jpg';

      echo '<div class="col-md-3 product">';
        echo '<a href="/product.php?id='.$id.'" title="'.$name.'">';
          echo '<div class="product_img">';
          echo '<img src="'.$image_src.'" alt="'.$name.'">';
          echo '</div>';
          echo '</a>';
          echo '<div class="card-body">';
          echo '<h4 class="card-title">'.$name.'</h4>';
        echo '<div class="card-text product_price">';
        // echo '<span class="p_old_price">$65</span> ';
        echo ' $'.$price;
        echo '</div>';
        echo '<div class="card-text mt-2">';
          echo '<strong>Бесплатная доставка от 40</strong>';
          echo '</div>';
        echo '</div>';
      echo '</div>';
    }
    ?>
  </div>

</div>

<hr>
<div class="">
  <h3>Отзывы</h3>
  <div class="">
    <div class="media">
      <div class="media-body">
        <h4 class="media-heading">
          User Name <small><i>Опубликован 23.01.2031</i></small>
        </h4>
        <p>Lorem ipsum dolor sit amet</p>
      </div>
    </div>
  </div>

</div>




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

</script>

<?php

include($root_dir.'/include/blocks/footer.php');
?>
