<?php
include($root_dir.'/include/classes/user_recipes.php');
$current_page_title = $l->my_recipes;
if($action == 'add'){
  $current_page_title = $l->adding_recipe;
} else if($action == 'edit'){
  $current_page_title = $l->editing_recipe;

}

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search_str = isset($_GET['search_str']) ? $_GET['search_str'] : '';

$acitve_all_class = empty($status) || $status == 'all' ? 'active_lnk' : '';

$init_user_recipes = new UserRecipes(array(
  'user_id' => $user_id,
  'item_id' => $item_id
));

$user_recipes = $init_user_recipes->get_recipes(array(
  'status' => $status,
  'page' => $page,
  'search_str' => $search_str
));

$statuses = $user_recipes['statuses'];

$user_recipes_html = $user_recipes['html'];

?>
<script src="/js/core/functions.js?ver=1" charset="utf-8"></script>
<script src="/js/core/dom.js" charset="utf-8"></script>
<div class="box post_content">
  <div class="post_title">
    <h1 class="i_block" style="margin-bottom: 10px;"><?php echo $current_page_title ?></h1>
    <?php
    if($action == 'add' || $action == 'edit'){
      echo '<a class="float_r td_underline" href="/user?p=my_recipes">'.($l->back).'</a>';
    } else {
      echo '<a class="float_r td_underline" href="/user?p=my_recipes&action=add">'.($l->add_recipe_btn).'</a>';
    }
    ?>
    <div class="clear"></div>
  </div>
  <div class="post_cn">
    <div class="cont_post">

      <?php
      if($action == 'add' || $action == 'edit'){
        include($root_dir.'/include/user/add_recipe.php');
      } else{
        ?>

        <div class="ur_sort_items">
          <ul id="ul_sort_recipes">
            <li class="i_block v_align_middle">
              <a class="<?php echo $acitve_all_class; ?>" id="lnk_status_all" onclick="user_recipes.set_status('all')"><?php echo $l->all ?>(<span id="ica_recipes"><?php echo $user_recipes['count_all'] ?></span>)</a>
            </li>
            <?php
            foreach ($statuses as $key => $value) {
              $active = $status == $key ? 'active_lnk' : '';
              echo '<li class="i_block v_align_middle addns_r_status">';
                echo '| <a class="'.$active.'" id="lnk_status_'.$key.'" onclick="user_recipes.set_status(\''.$key.'\')"> '.($l->$key).'('.$value.')</a>';
              echo '</li>';
            }
            ?>
          </ul>
        </div>

        <div class="ur_sort_items ask_post">
          <div class="padd_search">
            <div class="full_w p_search_input">
              <input type="text" id="ur_search" placeholder="<?php echo $l->search_by_recipes ?>" class="full_w" onkeyup="user_recipes.search_recipe(event);" value="<?php echo $search_str; ?>">
            </div>
          </div>
        </div>

        <div class="ask_post">

          <table class="table table_user_recipes mobile_hide">
            <thead>
              <tr>
                <th class="text_left"><?php echo $l->title ?></th>
                <th><?php echo $l->assessments ?></th>
                <th class="text_left"><?php echo $l->categories ?></th>
                <th class="text_left"><?php echo $l->status ?></th>
                <th class="text_left"><?php echo $l->create_date ?></th>
              </tr>
            </thead>
            <tbody id="u_desktop_recipes">
              <?php
              if($user_recipes_html['desktop']) echo $user_recipes_html['desktop'];
              else{
                echo '<tr>';
                  echo '<td colspan="5" class="text_center">'.($l->no_recipes).'</td>';
                echo '</tr>';
              }
              ?>

            </tbody>
          </table>

          <div class="mobile_show" id="u_mobile_recipes">
              <?php
              if($user_recipes_html['mobile']) echo $user_recipes_html['mobile'];
              else{
                echo '<div class="text_center no_recipes">'.($l->no_recipes).'</div>';
              }

              ?>
          </div>

          <div class="btns_sw_page text_right">
            <div class="i_block" id="u_recipes_btns">
              <?php echo $user_recipes['btns'];; ?>
            </div>
          </div>

        </div>
        <script type="text/javascript">
          domReady(function(){
            user_recipes.init();
          });
        </script>

        <?php
      }
      ?>

      </div>
    </div>
  </div>
  <script src="/js/scripts/user_recipes.js?ver=<?php echo rand(1,199999999); ?>" charset="utf-8"></script>
