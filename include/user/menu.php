<div class="user_menu">

  <div class="gr_cats_head">
    <h2><?php echo $l->menu ?></h2>
  </div>

  <hr>

  <div class="gr_cats_content">
    <ul class="list-group">
      <?php
      foreach ($init_user->menu_items as $p => $name) {
        $active_item = $page == $p ? 'active_menu_item' : '';
        $noti_count_html = '';
        echo '<li class="list-group-item">';
        echo '<a class="d_block '.$active_item.'" href="?p='.$p.'">'.$name.' '.$noti_count_html.'</a>';
        echo '</li>';
      }
      ?>
    </ul>
  </div>

</div>
<hr>

<div class="">
  <a href="/logout">
    <div class="btn d-block btn-default"><i class="fa fa-sign-out w_color" aria-hidden="true"></i>&nbsp;<?php echo $l->logout ?></div>
  </a>
</div>
