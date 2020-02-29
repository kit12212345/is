<?php
include_once($root_dir.'/include/classes/noti.php');
$init_noti = new Noti(array(
  'user_id' => $user_id
));

$noti = $init_noti->get_noti();
$noti_html = $noti['html'];

?>

<div class="box post_content">
  <div class="post_title">
    <h1><?php echo $l->noti ?></h1>
  </div>
  <div class="post_cn">
    <div class="cont_post">

      <div class="ur_sort_items">
        <ul id="ul_sort_recipes">
          <li class="i_block v_align_middle">
            <a class="active_lnk" id="lnk_looked_0" onclick="noti.switch_view(0)"><?php echo $l->new_noti ?></a> &nbsp;&nbsp;|
          </li>
          <li class="i_block v_align_middle">
            <a class="" id="lnk_looked_1" onclick="noti.switch_view(1)"><?php echo $l->old_noti ?></a>
          </li>
        </ul>
      </div>

      <div class="ask_post" style="padding-top:5px;" id="noti_items">

        <?php echo $noti_html; ?>

      </div>

    </div>
    </div>
  </div>
  <script src="/js/core/functions.js?ver=1" charset="utf-8"></script>
  <script src="/js/core/dom.js" charset="utf-8"></script>
  <script src="/js/scripts/noti.js?ver=<?php echo rand(1,1000000); ?>" charset="utf-8"></script>
