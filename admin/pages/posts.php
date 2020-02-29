<link rel="stylesheet" href="/admin/css/categories.css">
<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
require_once($root_dir.'/include/classes/posts.php');
require_once($root_dir.'/admin/include/classes/tree_cats.php');

$search_str = $_GET['search_str'];
$sort_posts = $_GET['status'];
$cat_id = (int)$_GET['cat_id'];
$sort_date = $_GET['date'];
$hash_tag = (int)$_GET['tag'];

$data_delete_forever = $sort_posts == 'basket' ? 'data-forever="true"' : '';

$add_cat_tree_cats = Create_cats_tree::create(array(
  'in_table' => false,
  'selected_cat' => $cat_id
));

$posts_date = Posts::get_posts_date($sort_date);

$Posts = new Posts();

$page = (int)$_GET['page'];

$page = $page <= 0 ? 1 : $page;

$posts_info = $Posts->get_posts(array(
  'page' => $page,
  'status' => $sort_posts,
  'cat_id' => $cat_id,
  'date' => $sort_date,
  'search_str' => $search_str,
  'hash_tag' => $hash_tag
));
$posts = $posts_info['posts'];
$count_posts = count($posts);

$posts_statuses = Posts::get_statuses_posts();
$arr_posts_statuses = $posts_statuses['statuses'];
$count_all_posts = $posts_statuses['counts']['all'];
$count_published_posts = $posts_statuses['counts']['published'];
$count_future_posts = $posts_statuses['counts']['future'];
$count_approval_posts = $posts_statuses['counts']['approval'];
$count_draft_posts = $posts_statuses['counts']['draft'];

$count_posts_statuses = count($arr_posts_statuses);

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
              <th>Заголовок</th>
              <th>Автор</th>
              <th>Рубрики</th>
              <th>Метки</th>
              <th><i class="fa fa-comments" aria-hidden="true"></i></th>
              <th class="text_center">Оценки</th>
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
            if($count_posts > 0){

              for ($i = 0; $i < $count_posts; $i++) {
                $post_id = $posts[$i]['id'];
                $post_alias = $posts[$i]['alias_ru'];
                $post_name = $posts[$i]['title_ru'];
                $post_title = $posts[$i]['title_ru'];
                $post_content = $posts[$i]['content_ru'];
                $post_cats = $posts[$i]['cats'];
                $post_count_comments = $posts[$i]['count_comments'];
                $post_status = $posts[$i]['status'];
                $post_hash_tags = $posts[$i]['hash_tags'];
                $post_create_date = $posts[$i]['create_date'];
                $post_update_date = $posts[$i]['update_date'];
                $post_count_likes = $posts[$i]['count_likes'];
                $post_count_not_likes = $posts[$i]['count_not_likes'];

                $post_create_date = strtotime($post_create_date) + $_SESSION['time_offset'];

                $post_status_text = '';

                if($post_status == 'draft'){
                  $post_status_text = '<strong> — Черновик</strong>';
                } else if($post_status == 'approval'){
                  $post_status_text = '<strong> — На утверждении</strong>';
                } else if($post_status == 'future'){
                  $post_status_text = '<strong> — Запланировано</strong>';
                }

                $str_ht = '';
                $str_cats = '';

                for ($c = 0; $c < count($post_cats); $c++){
                  $cat_id = $post_cats[$c]['id'];
                  $cat_name = $post_cats[$c]['name_ru'];
                  $str_cats .= $str_cats == '' ? '<a href="?q=posts&cat_id='.$cat_id.'">'.$cat_name.'</a>' : ', <a href="?q=posts&cat_id='.$cat_id.'">'.$cat_name.'</a>';
                }

                for ($h = 0; $h < count($post_hash_tags); $h++) {
                  $ht_id = $post_hash_tags[$h]['id'];
                  $ht_name = $post_hash_tags[$h]['name'];
                  $str_ht .= $str_ht == '' ? '<a href="?q=posts&tag='.$ht_id.'">'.$ht_name.'</a>' : ', <a href="?q=posts&tag='.$ht_id.'">'.$ht_name.'</a>';
                }

                $str_cats = !empty($str_cats) ? $str_cats : '—';
                $str_ht = !empty($str_ht) ? $str_ht : '—';

                $text_status_post = '—';

                if($post_status == 'future'){
                  $text_status_post = 'Запланировано';
                } else if(!empty($post_update_date)){
                  $text_status_post = 'Последнее изменение';
                } else if($post_status == 'published'){
                  $text_status_post = 'Опубликовано';
                }

                $post_name = '<span id="post_title_'.$post_id.'">'.$post_name.'</span>'. $post_status_text;


                $html_count_comments = $post_count_comments > 0 ? '<span class="badge bg-warning-400">'.$post_count_comments.'</span>' : '—';

                echo '<tr id="post_item_'.$post_id.'">';
                echo '<td class="_col_select"><input value="'.$post_id.'" class="checked_post" type="checkbox"></td>';
                echo '<td class="_col_title">';
                echo '<span>'.$post_name.'</span>';
                echo '<div class="hidden_actions_cat">';
                if($post_status == 'deleted'){
                  echo '<div class="ha_cat_item" data-postid="'.$post_id.'" onclick="post.restore_post(this);">Восстановить | </div>';
                  echo '<div class="ha_cat_item" data-postid="'.$post_id.'" data-forever="true" onclick="post._delete(this);" style="color: #a00;">Удалить навсегда </div>';
                  echo '<div class="clear"></div>';
                } else{
                  echo '<a href="?q=add_post&edit_item='.$post_id.'">';
                  echo '<div class="ha_cat_item">Изменить | </div>';
                  echo '</a>';
                  echo '<div class="ha_cat_item" data-postid="'.$post_id.'" onclick="post.show_edit(this);">Свойства | </div>';
                  echo '<div class="ha_cat_item" data-postid="'.$post_id.'" onclick="post._delete(this);" style="color: #a00;">Удалить | </div>';
                  echo '<a href="/'.$post_alias.'" target="_blank">';
                  echo '<div class="ha_cat_item">Перейти</div>';
                  echo '</a>';
                  echo '<div class="clear"></div>';
                }
                echo '</div>';
                echo '</td>';
                echo '<td class="_col_autor">User_name</td>';
                echo '<td class="_col_cats">';
                echo $str_cats;
                echo '</td>';
                echo '<td class="_col_ht">';
                echo $str_ht;
                echo '</td>';
                echo '<td class="_col_comments">'.$html_count_comments.'</td>';
                echo '<td class="_col_comments text_center"><strong class="g_color">'.$post_count_likes.'</strong> / <strong class="r_color">'.$post_count_not_likes.'</strong></td>';
                echo '<td class="_col_date">'.$text_status_post.'<br /> '.gmdate('d.m.Y, H:i',$post_create_date).'</td>';
                echo '</tr>';


                echo '<tr id="c_hide_edit_'.$post_id.'" class="et_cat_cont" style="display: none;">';
                echo '<td colspan="8">';
                echo '<div>';
                echo '<h5>Свойства</h5>';
                echo '</div>';
                echo '<fieldset class="content-group">';
                echo '<div class="form-group">';
                echo '<label class="control-label col-lg-2">Заголовок:</label>';
                echo '<div class="col-lg-10">';
                echo '<input type="text" id="i_post_title_'.$post_id.'" value="'.$post_title.'" placeholder="Заголовок" class="form-control">';
                echo '</div>';
                echo '<div class="clear"></div>';
                echo '</div>';
                echo '<div class="form-group">';
                echo '<label class="control-label col-lg-2 ">Алиас:</label>';
                echo '<div class="col-lg-10">';
                echo '<input type="text" id="i_post_alias_'.$post_id.'" value="'.$post_alias.'" placeholder="Алиас" class="form-control">';
                echo '</div>';
                echo '<div class="clear"></div>';
                echo '</div>';
                echo '<div class="text_right">';
                echo '<div class="float_l btn btn-xs btn-default" onclick="post.show_edit(this);" data-postid="'.$post_id.'">Отмена</div>';
                echo '<div class="btn btn-xs btn-primary" onclick="post.small_update(this);" data-postid="'.$post_id.'">Обновить</div>';
                echo '<div class="clear"></div>';
                echo '</div>';
                echo '</fieldset>';
                echo '</td>';
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
