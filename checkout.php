<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
if(!class_exists('Common')) include($root_dir.'/include/classes/common.php');

$basket = $init_basket->get_basket();
$basket_total_summa = $init_basket->get_total_summa($basket);

$countries = Common::get_countries();
$states = Common::get_states();
$dm = Common::get_delivery_methods();
$count_dm = count($dm);
?>

<style media="screen">
  .inp_item + .inp_item{
    margin-top: 15px;
  }
  .inp_lab{
    font-size: 17px;
  }
  .inp_i{
    margin-top: 5px;
  }
  .inp_i input, .inp_i select, .inp_i textarea{
    width: 100%;
    border: 1px solid #d0d4d7;
    padding: 10px;
  }
  .cs_title{
    font-size: 25px;
    text-transform: uppercase;
    background: #757575;
    font-weight: 500;
    padding: 20px;
  }

  .checkout_section + .checkout_section{
    margin-top: 20px;
  }
  .checkout_section + .checkout_section{
    border-top: 0px;
  }
  .important_field{
    color: #ff3333;
    font-size: 12.5px;
  }
  .cs_wrap{
    border: 1px solid #f1f4f6;
  }

  .cs_content{
    padding: 20px;
  }

  .cs_head{
    background: #fafcfc;
    font-size: 18px;
    padding: 20px;
  }

</style>

<div class="float_l basket_l_grid">
  <div class="relative checkout_section">

    <div class="w_color cs_title">
      1. Доставка
    </div>

    <div class="cs_wrap" id="checkout_ci">

      <div class="cs_head">
        Контактная информация
      </div>

      <div class="cs_content">

        <div class="inp_item">
          <label class="inp_lab">Имя <span class="important_field">*</span></label>
          <div class="inp_i">
            <input type="text" id="co_first_name" value="">
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Фамилия <span class="important_field">*</span></label>
          <div class="inp_i">
            <input type="text" id="co_last_name" value="">
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Телефон <span class="important_field">*</span></label>
          <div class="inp_i">
            <input type="text" id="co_phone" value="">
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Страна <span class="important_field">*</span></label>
          <div class="inp_i">
            <select class="" id="co_country">
              <?php
              foreach ($countries as $key => $value) {
                echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Штат <span class="important_field">*</span></label>
          <div class="inp_i">
            <select class="" id="co_state">
              <?php
              foreach ($states as $key => $value) {
                echo '<option value="'.$value['id'].'">'.$value['name'].'</option>';
              }
              ?>
            </select>
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Город <span class="important_field">*</span></label>
          <div class="inp_i">
            <input type="text" id="co_city" value="">
          </div>
        </div>

        <div class="inp_item">
          <label class="inp_lab">Адрес <span class="important_field">*</span></label>
          <div class="inp_i">
            <input type="text" id="co_address" value="">
          </div>
        </div>

        <div class="inp_item">
          <label class="inp_lab">Индекс</label>
          <div class="inp_i">
            <input type="text" id="co_zip" value="">
          </div>
        </div>
        <div class="inp_item">
          <label class="inp_lab">Коммантарий к заказу</label>
          <div class="inp_i">
            <textarea name="name" id="co_comment" rows="8" cols="80"></textarea>
          </div>
        </div>

      </div>

      <div class="cs_head">
        Способ доставки
      </div>

      <div class="cs_content">
        <style media="screen">
        .co_dm_items{
          width: 50%;
          padding-bottom: 15px;
          padding-right: 15px;
        }
        .tb_dm{
          width: 100%;
        }
        .tb_dm tbody tr td{
          vertical-align: middle;
        }
          .co_dm_info{
            border-top: 1px solid #f1f4f6;
            color: #656a6e;
            padding-top: 5px;
            margin-top: 5px;
          }
        </style>
        <?php
        for ($i=0; $i < $count_dm; $i++) {
          $checked = $i == 0 ? 'checked="checked"' : '';
          echo '<div class="float_l co_dm_items">';
            echo '<table class="tb_dm">';
              echo '<tbody>';
                echo '<tr>';
                  echo '<td>';
                    echo '<input type="radio" '.$checked.' name="dm_item" value="'.$dm[$i]['id'].'">';
                  echo '</td>';
                  echo '<td>';
                    echo '<div class="float_l co_dm_name">';
                      echo '<strong>'.$dm[$i]['name'].':</strong>';
                    echo '</div>';
                    echo '<div class="co_dm_price float_r">';
                      echo '<strong>$'.$dm[$i]['cost'].'</strong>';
                    echo '</div>';
                    echo '<div class="clear"></div>';
                    echo '<div class="co_dm_info">Примерно до '.$value['days'].' марта</div>';
                  echo '</td>';
                echo '</tr>';
              echo '</tbody>';
            echo '</table>';
          echo '</div>';
        }
        ?>
        <div class="clear"></div>

        <div class="inp_item text_right" onclick="basket.checkout();">
          <div class="btn btn_default">
            Перейти к оплате
          </div>
        </div>


      </div>



    </div>

  </div>


  <div class="relative checkout_section">
    <div class="w_color cs_title">
      Оплата
    </div>
  </div>
</div>
<div class="float_r basket_r_grid">
  <div class="cs_head">
    Сумма заказа
    <a href="/basket.php" class="float_r bpr_edit_delails">
      Изменить
    </a>
  </div>
  <div class="bsk_total_wrap">
    <div class="mini_basket">

      <table class="tb_basket full_w text_left">
        <tbody>
          <?php
          foreach ($basket as $key => $item) {
            $id = $item['id'];
            $name = $item['name'];
            $quan = $item['quan'];
            $price = $item['price'];
            $total = $price * $quan;
            $main_image = $item['main_image'];
            $option = $item['options'];

            $image_url = empty($main_image) ? '/images/no_img.png' : '/images/catalog/t_480/'.$main_image;

            echo '<tr>';
              echo '<td class="basket_pr_info">';
                echo '<div class="bpr_img float_l">';
                  echo '<img src="'.$image_url.'" alt="">';
                echo '</div>';
                echo '<div class="bpr_inf float_r">';
                  echo '<div class="bpr_name">';
                    echo '<h3>'.$name.'</h3>';
                  echo '</div>';
                  echo '<div class="bpr_options">';
                  foreach ($option as $opt_key => $opt_item) {
                    echo '<div class="bpr_opt_item">';
                    echo '<strong>'.$opt_item['propert_name'].': </strong>';
                    echo '<span>'.$opt_item['name'].'</span>';
                    echo '</div>';
                  }
                  echo '<div class="bpr_opt_item">';
                  echo '<strong>Кол-во: </strong>';
                  echo '<span>'.$quan.'</span>';
                  echo '</div>';
                  echo '<div class="bpr_opt_item">';
                  echo '<strong>Сумма: </strong>';
                  echo '<span>'.($price * $quan).'</span>';
                  echo '</div>';
                  echo '</div>';
                echo '</div>';
                echo '<div class="clear"></div>';
              echo '</td>';
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>

    </div>

    <div class="bpr_options">
      <span>Доставка</span>
      <span class="float_r">$0</span>
    </div>

    <div class="total_title bpr_g_total">
      <strong>Итого</strong>
      <strong class="float_r">$<?php echo $basket_total_summa; ?></strong>
    </div>

  </div>
</div>


<?php
include($root_dir.'/include/blocks/footer.php');
?>
