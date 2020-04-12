<script src="/admin/components/uploader/uploader.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<?php
require($root_dir.'/admin/components/uploader/index.php');
include_once($root_dir.'/include/classes/catalog.php');

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;

$catalog = new Catalog();

$uploader_table_name = 'catalog_images';

if($item_id > 0){


  $product_info = $catalog->get_product_info($item_id);

  if($product_info !== fasle){
    $name = $product_info['name'];
    $description = $product_info['description'];
    $price = $product_info['price'];
    $quan = $product_info['quan'];
    $parent_id = $product_info['parent_id'];


  }
}

$tree_cats = $catalog->get_tree_cats($parent_id);

$uploader = new Uploader(array(
  'table_name' => $uploader_table_name,
  'item_id' => $item_id,
  'path' => '/images/catalog/',
  'max_files' => 10
));

echo '<div id="products_info" data-info=\'{"parent_id": "'.$product_parent_id.'",';
echo '"product_id": "'.(int)$item_id.'",';
echo '"save_product_cat": "'.(int)$save_product_cat.'",';
echo '"action_id": "'.(int)$product_action_id.'"';
echo '}\'></div>';
?>

<script type="text/javascript">
$(document).ready(function(){
  catalog.init_product_edit();
});
</script>

<div id="modal_theme_success" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h6 class="modal-title">Выберите категорию</h6>
      </div>

      <div class="modal-body">
        <div id="s_cats_map"></div>
        <div id="s_cats_content"></div>
      </div>

      <hr>

      <div class="modal-footer">
        <button type="button" class="btn btn-link" data-dismiss="modal">Закрыть</button>
        <button type="button" class="btn btn-success" onclick="products.select_product_cat();">Выбрать</button>
      </div>
    </div>
  </div>
</div>


<div class="title_add_product">
    <h2 class="panel-title"><?php echo $page_title; ?></h2>
</div>

  <div class="col-md-8">
    <div class="panel panel-flat">

      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h3 class="panel-title">Основное</h3>
      </div>

      <div class="panel-body">

        <fieldset class="content-group">

          <div class="form-group">
            <label class="control-label col-lg-2">Название:</label>
            <div class="relative col-lg-10">
              <input type="text" id="product_name" value="<?php echo $name; ?>" placeholder="Название" class="form-control">
            </div>
            <div class="clear"></div>
          </div>

          <div class="form-group">
            <label class="control-label col-lg-2">Описание:</label>
            <div class="col-lg-10">
              <textarea rows="13" cols="5" id="product_description" class="wysihtml5 wysihtml5-min form-control" placeholder="Описание"><?php echo $description; ?></textarea>
            </div>
            <div class="clear"></div>
          </div>

          <div class="form-group">
            <label class="control-label col-lg-2">Цена:</label>
            <div class="col-lg-10">
              <input type="number" id="product_price" value="<?php echo $price; ?>" placeholder="999,99" class="form-control">
            </div>
            <div class="clear"></div>
          </div>

          <div class="form-group">
            <label class="control-label col-lg-2">Количество:</label>
            <div class="col-lg-10">
              <input type="number" id="product_quan" value="<?php echo $quan; ?>" placeholder="100" class="form-control">
            </div>
            <div class="clear"></div>
          </div>

          <div class="form-group">
            <?php
            include($root_dir.'/admin/modules/options/index.php');
            ?>
            <div class="clear"></div>
          </div>


          <div class="form-group">
            <label class="control-label col-lg-2">Изображения:</label>
            <div class="cont_add_image col-lg-10">
              <?php
                 $root_dir = $_SERVER['DOCUMENT_ROOT'];
                 if($product_id > 0){
                   $uploader_mode='shop_product_edit';
                 } else{
                   echo $uploader->create_html();
                 }
               ?>
            </div>
            <div class="clear"></div>
          </div>

          <div class="mobile_display_none">
            <p>
              <small style="color: grey;">Горячие клавиши: <div>- Сохранить и создать новый ctrl + shift + s</div> <div> - Сохранить товар ctrl + s</div></small>
            </p>

          </div>

        </fieldset>

      </div>
    </div>

  </div>

  <div class="col-md-4">

    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h3 class="panel-title">Категория товара</h3>
      </div>
      <div class="panel-body padd_ten">
        <div class="">
          <select class="form-control" id="parent_id" name="">
            <?php echo $tree_cats ?>
          </select>
        </div>
      </div>
    </div>

    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h3 class="panel-title">Публикация</h3>
      </div>
      <div class="panel-body padd_ten">
        <div class="full_w">
          <div class="text_center">
            <div class="btn btn-xs btn-success col-sm-8" onclick="catalog.save_product();">Сохранить и создать новый</div>
            <div class="col-sm-1"></div>
            <div class="btn btn-xs btn-info col-sm-3" onclick="catalog.save_product();">Сохранить</div>
          </div>
        </div>
      </div>
    </div>



  </div>

  <button type="button" class="display_none" id="show_modal_cats" data-toggle="modal" data-target="#modal_theme_success"></button>


  <script type="text/javascript">

    var pressed_ctrl = false;
    var pressed_shift = false;



    $(document).ready(function(){
      $(document).on('keydown',document_key_down);
      $(document).on('keyup',document_key_up);
      // $(window.frames[0].document).on('keydown',document_key_down);
      // $(window.frames[0].document).on('keyup',document_key_up);
    });

    function document_key_down(e){
      e = e || window.event;
      if(e.keyCode == 17 && pressed_ctrl === false){
        pressed_ctrl = true;
      }
      if(e.keyCode == 16 && pressed_shift === false){
        pressed_shift = true;
      }

      if(pressed_shift === true && pressed_ctrl === true && e.keyCode == 83){
        e.preventDefault();
        pressed_ctrl = false;
        pressed_shift = false;
        return catalog.save_product(true);

      } else if(pressed_ctrl === true && e.keyCode == 83){
        e.preventDefault();
        pressed_ctrl = false;
        return catalog.save_product();

      }

    }

    function document_key_up(e){
      e = e || window.event;
      if(e.keyCode == 17 && pressed_ctrl === true){
        pressed_ctrl = false;
      }
      if(e.keyCode == 16 && pressed_shift === true){
        pressed_shift = false;
      }
    }

  </script>

  <script src="/admin/js/scripts/catalog.js?ver=<?=rand(1,1000000); ?>" charset="utf-8"></script>
  <script type="text/javascript">
    // products.init();
  </script>
