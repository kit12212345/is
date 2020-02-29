<?php

include($root_dir.'/include/classes/fav_recipes.php');
$current_page_title = $l->bookmarks;
if($action == 'add'){
  $current_page_title = $l->adding_recipe;
} else if($action == 'edit'){
  $current_page_title = $l->editing_recipe;

}

$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : '';

$init_fav_recipes = new UserFavRecipes(array(
  'user_id' => $user_id,
  'item_id' => $item_id
));

$recipes = $init_fav_recipes->get_recipes(array(
  'page' => $page,
  'search_str' => $search_str,
  'parent_id' => $parent_id
));



$cat_path = $recipes['cat_path'];
$cp_html = $recipes['cp_html'];
$recipes_html = $recipes['html'];
$tree_cats = $recipes['tree_cats'];
?>
<script src="/js/core/functions.js?ver=1" charset="utf-8"></script>
<script src="/js/core/dom.js" charset="utf-8"></script>
<div class="box post_content">
  <div class="post_title">
    <h1 class="i_block"><?php echo $current_page_title ?></h1>
    <?php
    if($action == 'add' || $action == 'edit'){
      echo '<a class="float_r td_underline" href="/user?p=my_recipes">'.($l->back).'</a>';
    } else {
      echo '<a class="float_r cursor_p td_underline" onclick="fav_recipes.show_add_dir();">'.($l->add_dir).'</a>';
    }
    ?>
    <div class="clear"></div>
  </div>

  <style media="screen">
    .fr_lp{
      width: 20px;
      padding: 10px 0px!important;
      text-align: center;
    }
    .ur_sort_items .it_add_comment{
      margin: 0px;
    }
  </style>

  <div class="post_cn">
    <div class="cont_post">

      <div class="ur_sort_items">
        <div class="text_right">
          <div class="i_block v_align_middle it_add_comment">
            <select onchange="return fav_recipes.set_action_to_selected(this);">
              <option value="0"><?php echo $l->action_to_seleted ?></option>
              <option value="edit"><?php echo $l->edit ?></option>
              <option value="delete"><?php echo $l->delete ?></option>
            </select>
          </div>
          <div class="i_block v_align_middle it_add_comment">
            <select onchange="return fav_recipes.set_show(this.value);">
              <option value="20">20</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <div id="cp_html">
        <?php echo $cp_html; ?>
      </div>

      <div class="ur_sort_items ask_post">
        <div class="padd_search">
          <div class="full_w p_search_input">
            <input id="fr_search" placeholder="<?php echo $l->search_by_recipes ?>" class="full_w" onkeyup="fav_recipes.search_recipe(event);" value="<?php echo $search_str; ?>" autocomplete="off" name="fr_search" type="text">
          </div>
        </div>
      </div>

      <div class="ask_post">

        <table class="table table_user_recipes mobile_hide">
          <thead>
            <tr>
              <th class="text_left fr_lp">
                <input type="checkbox" id="check_all_d" onchange="fav_recipes.check_all(this,'d')">
              </th>
              <th class="text_left"><?php echo $l->title ?></th>
              <th class="text_left"><?php echo $l->author ?></th>
              <th class="text_right"><?php echo $l->create_date ?></th>
            </tr>
          </thead>
          <tbody id="u_desktop_recipes">
            <?php
            if($recipes_html['desktop']) echo $recipes_html['desktop'];
            else{
              echo '<tr>';
                echo '<td colspan="5" class="text_center">'.($l->no_recipes).'</td>';
                echo '</tr>';
              }
              ?>
            </tbody>
          </table>

          <!-- <div class="mobile_show">
            <input type="checkbox" id="check_all_m" onchange="fav_recipes.check_all(this,'m')">
            <div id="u_mobile_recipes">
              <?php
              if($recipes_html['mobile']) echo $recipes_html['mobile'];
              else{
                echo '<div class="text_center no_recipes">'.($l->no_recipes).'</div>';
              }
              ?>
            </div>
          </div> -->


          <div class="mobile_show">

            <table class="table table_user_recipes">
              <thead>
                <tr>
                  <th class="text_left fr_lp">
                    <input type="checkbox" id="check_all_m" onchange="fav_recipes.check_all(this,'m')">
                  </th>
                  <th class="text_left"><?php echo $l->title ?></th>
                  <th class="text_right"></th>
                </tr>
              </thead>
              <tbody id="u_mobile_recipes">
                <?php
                if($recipes_html['mobile']) echo $recipes_html['mobile'];
                else{
                  echo '<div class="text_center no_recipes">'.($l->no_recipes).'</div>';
                }
                ?>
                </tbody>
              </table>
          </div>

          <div class="btns_sw_page text_right">
            <div class="i_block" id="u_recipes_btns">
              <?php echo $recipes['btns']; ?>
            </div>
          </div>

        </div>
        <script type="text/javascript">
          domReady(function(){
            // user_recipes.init();
          });
        </script>

      </div>
    </div>
  </div>

  <div class="fixed all_null modal_body" id="modal_add_dir">

    <div class="absolute modal">

      <div class="modal_head">
        <div class="w_color molad_title" id="modal_title_dir"><?php echo $l->adding_directory ?></div>
        <div onclick="fav_recipes.hide_modal_dir();" class="absolute cursor_p w_color modal_close" data-action="close">×</div>
      </div>

      <div class="modal_content">

        <div class="m_auth_item" id="wrap_name_dir">
          <label><?php echo $l->name_rec ?></label>
          <input type="text" class="full_w" id="name_dir">
        </div>

        <div class="m_auth_item">
          <label><?php echo $l->parent_directory ?></label>
          <select class="full_w" id="dir_parent_id">
            <option value="0"><?php echo $l->no ?></option>
            <?php echo $tree_cats; ?>
          </select>
        </div>

      </div>
      <div class="text_right modal_footer ">
        <div class="btn cursor_p w_color i_block btn_top_login" id="btn_save_dir" data-event="add" onclick="fav_recipes.save_dir(this)">
          <?php echo $l->add ?>
        </div>
        <div class="clear"></div>
      </div>

    </div>

  </div>



  <script src="/js/scripts/user_recipes.js?ver=<?php echo rand(1,199999999); ?>" charset="utf-8"></script>
  <script src="/js/scripts/fav_recipes.js?ver=<?php echo rand(1,199999999); ?>" charset="utf-8"></script>
