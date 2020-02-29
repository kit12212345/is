<div class="box post_content mobile_show">
  <div class="text_center cursor_p" onclick="show_prof_menu();">
    <h3 style="margin: 0px;"><i class="fa fa-bars" aria-hidden="true"></i> &nbsp;<?php echo $l->menu ?></h3>
  </div>
  <div id="hidden_prof_menu" style="display: none; margin-top: 10px;">
    <ul class="m_ul_cats">
      <?php
      foreach ($init_user->menu_items as $p => $name) {
        $active_item = $page == $p ? 'active_menu_item' : '';
        $noti_count_html = '';
        if($p == 'noti'){
          if($count_new_noti > 0) $noti_count_html = '<span class="count_new_noti">'.$count_new_noti.'</span>';
        }
        echo '<li class="mb_t_menu_item relative">';
          echo '<a class="d_block '.$active_item.'" href="?p='.$p.'">'.$name.' '.$noti_count_html.'</a>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>
</div>


<div class="float_l c_right_grid">

  <div class="full_w box gr_cats">

    <div class="gr_cats_head">
      <strong><?php echo $l->menu ?></strong>
    </div>

    <div class="gr_cats_content">
      <ul class="m_ul_cats">
        <?php
        if($user_id == 22){
          echo '<li class="relative">';
            echo '<a style="font-size: 18px;color: #FF0033;text-shadow: 1px 1px 1px #0000006b;" class="text_center d_block" href="/include/bd/index.php">Happy birthday';
            echo '<img style="margin-top: 10px;" src="/include/bd/img/hbty.png"></a>';
          echo '</li>';
        }
        foreach ($init_user->menu_items as $p => $name) {
          $active_item = $page == $p ? 'active_menu_item' : '';
          $noti_count_html = '';
          if($p == 'noti'){
            if($count_new_noti > 0) $noti_count_html = '<span class="count_new_noti">'.$count_new_noti.'</span>';
          }
          echo '<li class="relative">';
            echo '<a class="d_block '.$active_item.'" href="?p='.$p.'">'.$name.' '.$noti_count_html.'</a>';
          echo '</li>';
        }
        ?>
      </ul>
    </div>

  </div>

  <div class="full_w box padd_ten">
    <a href="/logout">
      <div class="btn_top_login w_color text_center"><i class="fa fa-sign-out w_color" aria-hidden="true"></i>&nbsp;<?php echo $l->logout ?></div>
    </a>
  </div>


</div>
