<link rel="stylesheet" href="/admin/modules/options/styles.css">
<?php

require_once($root_dir.'/admin/modules/options/products_options.php');

$init = new ProductsOptions();

$allowed_properts = $init->get_allowed_properts($item_id);
$selected_properts = $init->get_selected_properts();
$properts = $init->get_properts();
$options = $init->get_options($item_id,$allowed_properts);
$count_options = count($options);
$_key = $init->search_key_size($properts,$selected_properts);

echo '<div id="properts_object" data-info=\'{';
  echo '"properts": {';
  foreach ($properts as $key => $value) {
    $propert_id = $value['id'];
    $propert_name = $value['name'];

    echo '"'.$propert_id.'": {';
    echo '"name": "'.$propert_name.'",';
    echo '"items": {';
    $count_selected_products = count($value['child']);

    for ($i = 0; $i < $count_selected_products; $i++) {
      $item = $value['child'][$i];

      $child_id = $item['id'];
      $child_name = $item['name'];
      $child_color = $item['color'];
      $child_name = addslashes($child_name);
      $child_color = addslashes($child_color);

      $z = $i == $count_selected_products - 1 ? '' : ',';

      echo '"'.$i.'" :{';
        echo '"id": "'.$child_id.'",';
        echo '"name": "'.$child_name.'",';
        echo '"color": "'.$child_color.'"';
        echo '}'.$z;

      }
      echo '}';

      $z = $key == count($properts) - 1 ? '' : ',';

      echo '}'.$z;
    }

    echo '},';
    echo '"selected_properts": [';
    foreach ($selected_properts as $key => $value) {
      $propert_id = $value;
      $z = $key == count($selected_properts) - 1 ? '' : ',';
      echo '"'.$propert_id.'"'.$z;
    }

      echo '],';
    echo '"options": {';

      for ($i = 0; $i < $count_options; $i++) {
        $item = $options[$i];
        $child = $options[$i]['options'];
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

          $z = $i == $count_options - 1 ? '' : ',';

          echo '}'.$z;


      }

      echo '},';
      echo '"allowed_properts": {';
        $num = 0;
        foreach ($allowed_properts as $key => $value) {
          $propert_id = $key;
          $propert_name = $value;
          $z = $num == count($allowed_properts) - 1 ? '' : ',';

          echo '"'.$propert_id.'": "'.$propert_name.'"'.$z;

          $num++;
        }
      echo '}';
echo '}\'></div>';
?>



<link rel="stylesheet"  href="/admin/modules/options/color_picker/bootstrap-colorpicker.min.css">
<script src="/admin/modules/options/color_picker/bootstrap-colorpicker.js" charset="utf-8"></script>


<style>
  .colorpicker-2x .colorpicker-saturation {
    width: 200px;
    height: 200px;
  }

  .colorpicker-2x .colorpicker-hue,
  .colorpicker-2x .colorpicker-alpha {
    width: 30px;
    height: 200px;
  }

  .colorpicker-2x .colorpicker-preview,
  .colorpicker-2x .colorpicker-preview div {
    height: 30px;
    font-size: 16px;
    line-height: 160%;
  }

  .colorpicker-saturation .colorpicker-guide,
  .colorpicker-saturation .colorpicker-guide i {
    height: 10px;
    width: 10px;
    border-radius: 10px;
  }
</style>
<!-- <div id="cp1" data-color="rgba(194, 39, 219, 0.4)"></div>
<script>
  $(function () {
    $('#cp1')
      .colorpicker({
        inline: true,
        container: true
      })
      .on('colorpickerHide', function (e) {
        $('body').hide();
        console.log('asdasd');
      });
  });
</script> -->


<style media="screen">
  .opt_backr{
    width: 20px;
    height: 20px;
    display: block;
    margin: auto;
  }

  .po_color_edit {
    visibility: hidden;
  }
</style>


<div class="wrap_product_options">

  <h3>Варианты товаров</h3>

  <div class="">

    <div class="product_options_btns">
      <div class="btn btn-xs btn-success" onclick="products_options.show_options_modal(this);">Добавить вариант</div>
      <div class="btn btn-xs btn-success" onclick="products_options.show_properts_modal(this)">Редактирование свойств</div>
      <div class="btn btn-xs btn-success" onclick="products_options.show_selected_properts_modal(this)">Выбор свойств</div>
      <div class="btn btn-xs btn-success" onclick="products_options.show_faster_select_options(this)">Быстрое добавление</div>
    </div>

    <div class="product_options_content">
      <table class="table_options">
        <thead id="thead_options">
          <tr>
            <?php
            foreach ($allowed_properts as $key => $value) {
              echo '<th id="th_item_'.$key.'">'.$value.'</th>';
            }
            ?>
            <th>Цена</th>
            <th>Количество</th>
            <th></th>
            <th></th>
          </tr>
        </thead>
        <tbody id="tbody_options">
          <?php
          if($count_options > 0){
            foreach($options as $key => $value) {
              $child_product_id = $value['product_id'];
              echo '<tr id="opt_child_'.$child_product_id.'">';

              $options = $value['options'];
              $keys_properts = array_keys($allowed_properts);
              for ($i = 0; $i < count($allowed_properts); $i++) {

                $__val = !empty($options[$keys_properts[$i]]['color']) ? '<div class="opt_backr" style="background: '.$options[$keys_properts[$i]]['color'].'"></div>' : $options[$keys_properts[$i]]['name'];

                $val = isset($options[$keys_properts[$i]]) ? $__val : '———';

                echo '<td>'.$val.'</td>';
              }
              echo '<td><input placeholder="0" style="min-width: 70px;" class="i_po_val" type="number" onkeyup="return products_options.save_product_value(this)" data-event="price" data-productid="'.$child_product_id.'" value="'.$value['price'].'"></td>';
              echo '<td><input placeholder="0" style="min-width: 70px;" class="i_po_val" type="number" onkeyup="return products_options.save_product_value(this)" data-event="quantity" data-productid="'.$child_product_id.'" value="'.$value['quantity'].'"></td>';
              echo '<td class="text_center"><i onclick="return products_options.show_edit_option('.$value['product_id'].')" class="cursor_p g_color icon-pencil"></i></td>';
              echo '<td class="text_center"><i onclick="return products_options.delete_option('.$value['product_id'].')" class="cursor_p r_color icon-cross2"></i></td>';
              echo '</tr>';
            }
          } else{
            echo '<tr>';
              echo '<td colspan="20" class="text_center"><strong>Нет видов</strogn></td>';
              echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>

  </div>
  <style media="screen">
    .modal_bg{
      display: none;
      background: #000;
      position: fixed;
      left: 0px;
      right: 0px;
      top: 0px;
      bottom: 0px;
      width: 100%;
      height: 100%;
      z-index: 1001;
      opacity: .4;
    }
    ._modal_body{
      display: none;
      overflow-y: auto;
      position: fixed;
      position: fixed;
      left: 0px;
      right: 0px;
      top: 0px;
      bottom: 0px;
      width: 100%;
      height: 100%;
      z-index: 1002;
    }
    ._modal_{
      /* background: #fff; */
      position: absolute;
      left: 0px;
      right: 0px;
      top: 3%;
      margin: auto;
      width: 650px;
    }
  </style>

  <div class="modal_bg" id="__modal_bg"></div>

  <div id="modal_set_properts" class="_modal_body">
    <div class="_modal_">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" onclick="products_options.close_modal('modal_set_properts')">×</button>
          <h6 class="modal-title">Выбор свойств</h6>
        </div>
        <div class="modal-body" id="body_select_properts">
          <?php
          foreach ($properts as $key => $value) {
            $selected = in_array($value['id'],$selected_properts) ? 'active_select_prop_item' : '';
            echo '<div data-itemid="'.$value['id'].'" onclick="products_options.select_propert(this);" class="select_prop_item '.$selected.'">';
              echo '<span>'.$value['name'].'</span>';
            echo '</div>';
          }
          ?>
        </div>
        <hr>
        <div class="modal-footer">
          <button onclick="products_options.close_modal('modal_set_properts')" type="button" class="btn btn-link">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

  <style media="screen">
  /* .show_opt_color{
    display: none;
  } */
  .opt_color{
    width: 25px;
    height: 25px;
    border-radius: 50%;
    margin: 5px;
    cursor: pointer;
  }
  .opt_color ~ .active_sto{
    border: 3px solid #4f4f4f!important;
    box-shadow: 1px 1px 10px 1px #1c1c1c!important;
  }
  @media screen and (max-width: 800px) {
    .modal_scroll_seto{
      -webkit-overflow-scrolling: touch;
      overflow-y: scroll;
      max-height: 100%;
      -webkit-transform: translate(0,0);
      -ms-transform: translate(0,0);
      -o-transform: translate(0,0);
      transform: translate(0,0);
      /* transform: translate3d(0px, 0px, 0px); */
    }
    .modal_seto{
      width: 100%;
      height: 100%;
      top: 0px;
    }
    .modal_seto .modal-footer{
      background: #fff;
      position: relative;
      bottom: 0px;
      padding: 15px;
      width: 100%;
      border: 1px solid #ccc;
    }
    .modal_seto hr{
      display: none;
    }


    .modal_seto .modal-content, .modal_seto .modal-body{
      height: 100%;
    }
    .modal_seto .modal-body{
      height: 85%;
    }
    .modal_seto .modal-header{
      box-shadow: 0px 1px 2px #00000052;
      border: 1px solid #ccc;
      padding: 15px;
    }
    .modal_seto .modal-header .modal-title{
      font-weight: 500;
      font-size: 19px;
    }
    ._modal_body_seto{
      overflow: hidden;
      height: 100%;
    }

    /* .show_opt_color{
      display: inline-block;
    }
    .hide_opt_color{
      display: none;
    } */

    .product_options_content{
      overflow-x: auto;
    }

  }

  @media screen and (max-width: 480px){
    ._modal_{
      width: 95%;
    }
    .product_options_btns .btn{
      width: 100%;
      margin-top: 7px;
    }
    .modal_seto .modal-body{
      height: 75%;
    }
    ._modal_body_seto .modal-footer{
      white-space: nowrap;
    }
  }
  </style>

  <div id="modal_seto" class="_modal_body _modal_body_seto">
    <div class="_modal_ modal_seto">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" onclick="products_options.close_modal('modal_seto')">×</button>
          <h6 class="modal-title">Добавление вариантов</h6>
        </div>

        <div class="modal-body">

          <div class="modal_scroll_seto" id="modal_scroll_seto">
            <div id="fast_options_content">
              <table class="table_options">
                <thead id="thead_spb_options">
                  <tr>
                    <th>
                      <input type="checkbox" name="" value="">
                    </th>
                    <?php
                    foreach ($properts as $key => $value) {
                      if(!in_array($value['id'],$selected_properts)){
                        continue;
                      }
                      echo '<th id="th_item_'.$value['id'].'">'.$value['name'].'</th>';
                    }
                    ?>
                  </tr>
                </thead>
                <tbody id="tbody_spb_options">
                  <?php
                  if($key !== false && count($properts[$_key]['child']) > 0){

                    for ($i = 0; $i < count($properts[$_key]['child']); $i++) {
                      $item = $properts[$_key]['child'][$i];
                      $size_id = $item['id'];
                      $size_name = $item['name'];

                      echo '<tr class="sopt_child_hidden" id="sopt_child_'.$size_id.'">';

                      echo '<td>';
                      echo '<input class="sto_checked" type="checkbox" value="'.$size_id.'">';
                      echo '</td>';

                      echo '<td>';
                      echo '<div class="sto_name">'.$size_name.'</div>';
                      echo '</td>';



                      for ($c = 0; $c < count($properts); $c++){
                        if($c == $_key) continue;

                        if(!in_array($properts[$c]['id'],$selected_properts)){
                          continue;
                        }


                        $other_id = $properts[$c]['id'];
                        $other = $properts[$c]['child'];

                        $active = 'active_sto';

                        echo '<td>';
                        foreach ($other as $key => $value) {
                          $_id = $value['id'];
                          $_name = $value['name'];
                          $_color = $value['color'];

                          $cls_color = !empty($_color) ? 'hide_opt_color' : '';

                          echo '<div onclick="products_options.set_active_propert(this)" data-size="'.$size_id.'" data-typeid="'.$other_id.'" data-itemid="'.$_id.'" class="'.$cls_color.' sto_item prms_s_'.$size_id.'_'.$other_id.' '.$active.' val_active_'.$size_id.'">'.$_name.'</div>';
                          if(!empty($_color)){
                            echo '<div title="'.$_name.'" style="background: '.$_color.';" onclick="products_options.set_active_propert(this)" data-size="'.$size_id.'" data-typeid="'.$other_id.'" data-itemid="'.$_id.'" class="show_opt_color opt_color sto_item prms_s_'.$size_id.'_'.$other_id.' '.$active.' val_active_'.$size_id.'"></div>';
                          }
                          $active = '';
                        }
                        echo '</td>';

                      }

                      echo '</tr>';

                    }


                  } else{
                    echo '<tr>';
                      echo '<td colspan="20" class="text_center"><strong>Нет свойств</strogn></td>';
                      echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>

            </div>
          </div>

        </div>

        <hr>

        <div class="modal-footer">
          <button type="button" onclick="products_options.close_modal('modal_seto')" class="float_l btn btn-link" >Закрыть</button>
            <button id="btn_save_options" onclick="products_options.hide_sto_other(this);" data-event="add" type="button" class="btn btn-success">Скрыть неотмеченные</button>
          <button id="btn_save_options" onclick="products_options.save_options(true);" data-faster="true" data-event="add" type="button" class="btn btn-success">Добавить выбраные</button>
        </div>
      </div>
    </div>
  </div>


  <div id="modal_eto" class="_modal_body">
    <div class="_modal_">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <button type="button" onclick="products_options.close_modal('modal_eto')" class="close" >×</button>
          <h6 class="modal-title">Добавление вариантов</h6>
        </div>
        <div class="modal-body">
          <div id="wrap_add_properts">
            <?php
            foreach($properts as $key => $value){
              $propert_id = $value['id'];
              $propert_name = $value['name'];

              $child = $value['child'];
              $count_child = count($child);

              echo '<div class="form-group mb-10">';
                echo '<label class="control-label full_w">'.$propert_name.':</label>';
                echo '<div class="full_w">';
                  echo '<select class="form-control select_propert">';
                    echo '<option value="0">Не выбрано</option>';
                    for($i = 0; $i < $count_child; $i++){
                      $child_id = $child[$i]['id'];
                      $child_name = $child[$i]['name'];
                      echo '<option value="'.$child_id.'">'.$child_name.'</option>';
                    }
                  echo '</select>';
                echo '</div>';
                echo '<div class="clear"></div>';
              echo '</div>';
            }
            ?>
          </div>
          <div class="form-group po_params_wrap mt-20">
            <label class="control-label full_w">Количество:</label>
            <div class="full_w">
              <input type="number" class="form-control" id="opt_child_count" placeholder="0">
            </div>
            <div class="clear"></div>
          </div>


        </div>

        <hr>

        <div class="modal-footer">
          <button onclick="products_options.close_modal('modal_eto')" type="button" class="float_l btn btn-link">Закрыть</button>
          <button id="btn_save_options" onclick="products_options.save_options(this);" data-event="add" type="button" class="btn btn-success">Добавить выбраные</button>
        </div>
      </div>
    </div>
  </div>

  <div id="modal_etp" class="_modal_body">
    <div class="_modal_">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <button type="button" onclick="products_options.close_modal('modal_etp')" class="close">×</button>
          <h6 class="modal-title">Редактирование свойств</h6>
        </div>

        <div class="modal-body" id="modal_body_etp">

          <div class="form-group">
            <label class="control-label full_w">Выберите свойство:</label>
            <div class="full_w">
              <select onchange="return products_options.handle_toggle_properts(this);" class="form-control" id="selected_propert" name="">
                <option value="0">Не выбрано</option>
                <?php
                foreach ($properts as $key => $value) {
                  echo '<option id="parent_propert_'.$value['id'].'" value="'.$value['id'].'">'.$value['name'].'</option>';
                }
                ?>
              </select>
            </div>
            <div class="mt-20 text_right">
              <div onclick="return products_options.add_main_propert(this);" class="btn btn-xs btn-success">
                Добавить свойство
              </div>
              <div class="btn_propert_hide btn btn-xs btn-info" onclick="return products_options.show_edit_propert(false);">
                Изменить
              </div>
              <div class="btn_propert_hide btn btn-xs btn-danger" onclick="return products_options.delete_propert(false)">
                Удалить
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="po_hidden_form form-group po_params_wrap">
            <label class="control-label full_w">Название параметра:</label>
            <div class="full_w">
              <input onkeyup="if(event.keyCode == 13) return products_options.save_propert(this);" id="propert_name" type="text" class="form-control" placeholder="Введите название параметра">
            </div>
            <div class="mt-20 text_right">
              <div onclick="return products_options.save_propert(this);" class="btn btn-xs btn-info">
                Добавить параметр
              </div>
            </div>
            <div class="clear"></div>
          </div>

          <div class="po_hidden_form form-group po_params_wrap">
            <label class="control-label full_w">Параметры свойства</label>
            <div class="full_w wrap_po_child">
              <ul class="po_params_items" id="po_params_items"></ul>
            </div>
            <div class="clear"></div>
          </div>

        </div>

        <hr>

        <div class="modal-footer">
          <button onclick="products_options.close_modal('modal_etp')" type="button" class="btn btn-link">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
</div>



<script src="/admin/modules/options/js/common.js?ver=<?php echo rand(1,100000000); ?>" charset="utf-8"></script>
<script src="/admin/modules/options/js/options.js?ver=<?php echo rand(1,100000000); ?>" charset="utf-8"></script>
<script src="/admin/modules/options/js/properts.js?ver=<?php echo rand(1,100000000); ?>" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
  products_options.init();
})
</script>
