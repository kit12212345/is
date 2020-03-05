<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');

$basket = $init_basket->get_basket();
?>

<style media="screen">
  .tb_basket{
  }
  .tb_basket > thead > tr > th{
    text-transform: uppercase;
    font-weight: bold;
  }
  .tb_basket > thead{
    background: #f1f4f6;
  }
  .tb_basket > tbody > tr{
    border-bottom: 1px solid #f1f4f6;
  }
  .tb_basket > tbody > tr > td, .tb_basket > thead > tr > th{
    padding: 20px;
    vertical-align: top;
  }
  .basket_pr_info{
    width: 40%;
  }
  .basket_pr_dt{
    width: 20%;
  }
  .bpr_img{
    width: 30%;
  }
  .bpr_inf{
    width: 70%;
    padding-left: 15px;
  }
  .bpr_name h3{
    margin: 0px;
  }
  .bpr_options{
    margin-top: 10px;
  }
  .bpr_opt_item + .bpr_opt_item{
    margin-top: 5px;
  }
  .bpr_price{
    font-size: 16px;
  }
  .bpr_edit_delails{
    color: #656a6e;
    text-decoration: underline;
    font-size: 12.5px;
  }
  .basket_l_grid{
    width: 70%;
  }
  .basket_r_grid{
    width: 28%;
    margin-left: 2%;
  }
</style>
<div class="float_l basket_l_grid">
  <table class="tb_basket full_w text_left">
    <thead>
      <tr>
        <th>Товар</th>
        <th>Количество</th>
        <th>Цена</th>
        <th>Сумма</th>
      </tr>
    </thead>
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
              echo '</div>';
              echo '<div class="bpr_options">';
                echo '<a class="bpr_edit_delails">Изменить парамерты</a>';
              echo '</div>';
            echo '</div>';
            echo '<div class="clear"></div>';
          echo '</td>';
          echo '<td class="basket_pr_dt">';
            echo '<table class="text_center basket_btns">';
              echo '<tbody>';
                echo '<tr>';
                  echo '<td>';
                    echo '<div onclick="basket.change_quan(this);" data-itemid="'.$id.'" data-dir="m" class="cursor_p basket_btn_n disable_select_text">';
                      echo '<i class="fa fa-minus" aria-hidden="true"></i>';
                    echo '</div>';
                  echo '</td>';
                  echo '<td><input class="vp_b_quan text_center bsk_quan_'.$id.'" data-itemid="'.$id.'" type="number" value="'.$quan.'" onkeyup="basket.l_change_quan(this);"></td>';
                  echo '<td>';
                    echo '<div onclick="basket.change_quan(this);" data-itemid="'.$id.'" data-dir="p" class="cursor_p basket_btn_n disable_select_text">';
                      echo '<i class="fa fa-plus" aria-hidden="true"></i>';
                    echo '</div>';
                  echo '</td>';
                echo '</tr>';
              echo '</tbody>';
            echo '</table>';
            echo '<div class="bpr_options">';
              echo '<a class="bpr_edit_delails" onclick="basket.remove('.$id.');">Удалить товар</a>';
            echo '</div>';
          echo '</td>';
          echo '<td class="basket_pr_dt">';
            echo '<div class="bpr_price">$'.$price.'</div>';
          echo '</td>';
          echo '<td class="basket_pr_dt">';
            echo '<div class="bpr_price bpr_total_'.$id.'">$'.$total.'</div>';
          echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>
<div class="float_r basket_r_grid">
  <style media="screen">
  .bsk_total_wrap{
    padding: 20px;
    border: 1px solid #d0d4d7;
  }
  .total_title strong{
    font-size: 20px;
    text-transform: uppercase;
  }
  .bpr_g_total{
    border-top: 1px solid #f1f4f6;
    margin-top: 20px;
    padding-top: 20px;
  }
  </style>
  <div class="bsk_total_wrap">
    <div class="total_title">
      <strong>Итого</strong>
      <strong class="float_r">$45</strong>
    </div>

    <div class="bpr_options">
      <span>Доставка</span>
      <span class="float_r">$5</span>
    </div>

    <div class="total_title bpr_g_total">
      <strong>Итого</strong>
      <strong class="float_r">$45</strong>
    </div>

  </div>

  <div style="margin-top: 15px;" class="text_center full_w btn btn_default">
    Оформить заказ
  </div>


</div>



<?php
include($root_dir.'/include/blocks/footer.php');
?>
