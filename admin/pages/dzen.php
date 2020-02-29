<link rel="stylesheet" href="/admin/css/categories.css">
<?php
require_once($root_dir.'/include/classes/user_recipes.php');

$search_str = $_GET['search_str'];
$sort_posts = $_GET['status'];
$cat_id = (int)$_GET['cat_id'];
$sort_date = $_GET['date'];
$hash_tag = (int)$_GET['tag'];

$data_delete_forever = $sort_posts == 'basket' ? 'data-forever="true"' : '';

$init_user_recipes = new UserRecipes(array(
  'user_id' => $user_id,
  'item_id' => $item_id
));


// $posts_date = Posts::get_posts_date($sort_date);

// $Posts = new Posts();

$page = (int)$_GET['page'];

$page = $page <= 0 ? 1 : $page;

$recipes_info = $init_user_recipes->get_recipes(array(
  'page' => $page,
  'status' => $sort_posts,
  'cat_id' => $cat_id,
  'date' => $sort_date,
  'search_str' => $search_str,
  'hash_tag' => $hash_tag,
  'sort_user' => false
),true);


$recipes = $recipes_info['recipes'];
$count_recipes = count($recipes);
?>
<style media="screen">
  .ib_posts{
    margin-bottom: 10px;
  }
  .ib_posts a{
    margin-right: 5px;
  }
  .ib_posts a + a{
    margin-left: 5px;
  }
  .active_sort_item, .active_sort_item:hover{
    color: #000;
    text-decoration: underline;;
  }
  .fa-comments{
    font-size: 19px;
  }
</style>
<div class="title_page">
  <h2>Записи</h2>
</div>
<div class="col-sm-12">
  <div class="ib_posts">
    <?php
    if($count_posts_statuses > 0){
      for ($i = 0; $i < $count_posts_statuses; $i++) {
        $name = $arr_posts_statuses[$i]['name'];
        $translate_name = $arr_posts_statuses[$i]['translate'];
        $count = $arr_posts_statuses[$i]['count'];
        $line = $i == $count_posts_statuses - 1 ? '' : ' | ';

        $active_class = $sort_posts == $name || (empty($sort_posts) && $name == 'all') ? 'active_sort_item' : '';

        echo '<a class="'.$active_class.'" href="?q=posts&status='.$name.'">'.$translate_name.'('.$count.')</a>'.$line;

      }
    }
    ?>
  </div>
  <div class="">
    <div class="i_block v_align_middle s_cats_actions">
      <div class="i_block">
        <select class="s_action_cats" id="s_action_posts" name="">
          <option value="null">Действия</option>
          <option value="delete_post">Удалить</option>
        </select>
      </div>
      <div class="i_block">
        <div class="btn btn-xs btn-default" data-group="true" <?php echo $data_delete_forever; ?> onclick="post.group_post_action(this);">Применить</div>
      </div>
    </div>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <div class="i_block v_align_middle s_cats_actions">
      <div class="i_block v_align_middle">
        <select class="s_action_cats" id="select_post_date" name="">
          <option value="0">Все даты</option>
          <?php echo $posts_date; ?>
        </select>
      </div>
      <div class="i_block v_align_middle">
        <select class="s_action_cats" id="select_post_cat" name="">
          <option value="0">Все рубрики</option>
          <?php echo $add_cat_tree_cats; ?>
        </select>
      </div>
      <div class="i_block">
        <div class="btn btn-xs btn-default" onclick="post.apply_filter();">Применить</div>
      </div>
    </div>
    <div class="float_r s_cats_actions">
      <div class="i_block v_align_middle">
        <input type="text" placeholder="Поиск" id="post_search_str" class="s_action_cats" value="<?php echo $search_str; ?>">
      </div>
      <div class="i_block">
        <div class="btn btn-xs btn-default" onclick="post.search_posts();">Поиск записей</div>
      </div>
    </div>
  </div>
  <div class="panel panel-flat">
    <div class="padd_ten">
      <div class="datatable-scroll">
        <table class="table dataTable no-footer">
          <thead>
            <tr>
              <th class="_col_select"><input onchange="post.selected_all_posts(this);" type="checkbox" id="checked_all_posts"></th>
              <th class="_col_select">№</th>
              <th>Заголовок</th>
              <th>Автор</th>
              <th>Статус</th>
              <th>Дата</th>
            </tr>
          </thead>
          <!-- — -->
          <tbody class="body_cats">
            <style media="screen">
              ._col_select{
                /*width: 3em;*/
                /*padding: 5px!important;*/
              }
              .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th{
                padding: 12px 10px
              }
              ._col_title{

              }
              ._col_autor, ._col_date{
                width: 10%;
              }
              ._col_cats, ._col_ht{
                width: 15%;
              }
              ._col_comments{
                width: 5.5em;
              }
            </style>
            <?php

            $q_quests = sprintf("SELECT * FROM `posts` WHERE `id` > '69' ORDER BY `posts`.`id` ASC");
            $r_quests = mysql_query($q_quests) or die("cant execute query_path");
            $count_recipes = mysql_num_rows($r_quests); // or die("cant get numrows query_path");
            if ($count_recipes > 0) {
              for ($i = 0; $i < $count_recipes; $i++){
                $post_id = htmlspecialchars(mysql_result($r_quests, $i, "id"));
                $title = htmlspecialchars(mysql_result($r_quests, $i, "title_ru"));
                $content = htmlspecialchars(mysql_result($r_quests, $i, "content_ru"));
                $create_date = htmlspecialchars(mysql_result($r_quests, $i, "create_date"));


                echo '<tr id="post_item_'.$post_id.'">';
                echo '<td class="_col_select"><input value="'.$post_id.'" class="checked_post" type="checkbox"></td>';
                echo '<td class="_col_select">'.($i + 1).'</td>';
                echo '<td class="_col_title">';
                echo '<span>'.$title.' '.$status.' <br> - </span>';
                echo '<div class="hidden_actions_cat">';
                echo '</div>';
                echo '</td>';
                echo '<td class="_col_autor">'.$user_name.'</td>';
                echo '<td class="_col_cats">';
                if($status == 'in_moderation'){
                  echo 'На модерации';
                } else if($status == 'published'){
                  echo '<span class="g_color">Опубликован</span>';
                } else if($status == 'rejected'){
                  echo '<span class="r_color">Отклонен</span>';
                }
                echo '</td>';
                echo '<td class="_col_date">'.gmdate('d.m.Y, H:i',$create_date).'</td>';
                echo '</tr>';

              }


            } else{
              echo '<tr><td colspan="7" class="text_center"><strong>Нет записей</strong></td></tr>';
            }

            ?>

          </tbody>
        </table>

      </div>

      <div class="datatable-footer">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">

          <?php
          $page_count = 0;
          $items_per_page = 20;
          if (0 === $count_all_posts) {
          } else {
            $page_count = (int)ceil($count_all_posts / $items_per_page);
            if($page > $page_count) {
              $page = 1;
            }
          }


          $max_pages_nav = 10;
          $center_pos=ceil($max_pages_nav/2);
          $center_offset=round($max_pages_nav/2);

          //if($page_count>1){
          if($page>$center_pos) $start_page_count=$page-2;
          else  $start_page_count=1;
          $end_page_count=$start_page_count+($max_pages_nav-1);
          if($end_page_count>$page_count){
            $end_page_count=$page_count;
            $start_page_count=$page_count-($max_pages_nav-1);
          }

          if ($start_page_count<1) $start_page_count=1;

          if ($page!=1) echo '<a href="?q=posts&page='.($page-1).'" class="paginate_button previous" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" id="btn_sw_previous">←</a>';
          echo '<span id="nums_pages_pr">';
          for ($i = $start_page_count; $i <= $end_page_count; $i++) {
            if($page_count <= 1) continue;
            if ($i === $page) {
              echo '<a class="paginate_button current page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="1" tabindex="0">'.$i.'</a>';
            } else {
              echo '<a href="?q=posts&page='.$i.'" class="paginate_button page_btn_switch" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0">'.$i.'</a>';
            }
          }
          echo '</span>';
          if ($page!=$page_count) echo '<a href="?q=posts&page='.($page+1).'" data-direct="next" class="paginate_button next" aria-controls="DataTables_Table_0" data-dt-idx="3" tabindex="0" id="btn_sw_next">→</a>';

          ?>


        </div>
      </div>

    </div>
  </div>
</div>
<script src="/admin/js/scripts/post.js?ver=<?=rand(1,10000000000); ?>" charset="utf-8"></script>
