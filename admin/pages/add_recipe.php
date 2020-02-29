<link rel="stylesheet" href="/admin/css/categories.css">
<link rel="stylesheet" href="/admin/css/comments.css?ver=<?=rand(1,100000000); ?>">
<link rel="stylesheet" href="/admin/css/add_post.css?ver=<?=rand(1,100000000); ?>">
<script src="/admin/components/uploader/product_images/upload.js" charset="utf-8"></script>

<script src="/admin/js/ckeditor/ckeditor.js" charset="utf-8"></script>
<script src="/admin/js/ckeditor/config.js" charset="utf-8"></script>
<script src="/admin/js/ckeditor/styles.js" charset="utf-8"></script>

<?php
require_once($root_dir.'/admin/include/classes/categories.php');
require_once($root_dir.'/admin/include/classes/tree_cats.php');
include_once($root_dir.'/admin/include/classes/comments.php');
require($root_dir.'/admin/components/uploader/index.php');
include($root_dir.'/components/text_editor/modals.php');


require_once($root_dir.'/include/classes/user_recipes.php');

$edit_item = (int)$_GET['edit_item'];


$page_title = 'Добавление записи';
$post_status = 'Опубликовано';
$post_text_pub = 'Опубликовать';
$post_text_pub_date = 'Сразу';
$post_text_btn = 'Опубликовать';

$data_action = 'add_post';


$status_translate = array(
  'published' => 'Опубликовано',
  'approval' => 'На утверждении',
  'draft' => 'Черновик',
  'future' => 'Запланировано'
);

$arr_status_post = array(
  'published' => 'Опубликовано',
  'approval' => 'На утверждении',
  'draft' => 'Черновик'
);

$now_year = gmdate('Y', time() + $_SESSION['time_offset']);
$now_month = (int)gmdate('m', time() + $_SESSION['time_offset']);
$now_day = (int)gmdate('j', time() + $_SESSION['time_offset']);
$now_hours = (int)gmdate('H', time() + $_SESSION['time_offset']);
$now_minuts = (int)gmdate('i', time() + $_SESSION['time_offset']);
$now_seconds = (int)gmdate('s', time() + $_SESSION['time_offset']);

$now_month = $now_month < 10 ? '0'.$now_month : $now_month;
$now_day = $now_day < 10 ? '0'.$now_day : $now_day;
$now_hours = $now_hours < 10 ? '0'.$now_hours : $now_hours;
$now_minuts = $now_minuts < 10 ? '0'.$now_minuts : $now_minuts;
$now_seconds = $now_seconds < 10 ? '0'.$now_seconds : $now_seconds;

echo '<div id="now_time" data-info=\'{"year": "'.$now_year.'",';
echo '"month": "'.$now_month.'",';
echo '"day": "'.$now_day.'",';
echo '"hour": "'.$now_hours.'",';
echo '"minut": "'.$now_minuts.'",';
echo '"seconds": "'.$now_seconds.'"';
echo '}\'></div>';



$init_user_recipes = new UserRecipes(array(
  'item_id' => $edit_item
));

$recipe_info = $init_user_recipes->get_recipe_info(array(
  'sort_user' => false
),true);


if($recipe_info !== false){

  $lang = $recipe_info['lang'];
  $title = $recipe_info['title'];
  $post_title_ru = $recipe_info['title_ru'];
  $post_title_en = $recipe_info['title_en'];
  $content = $recipe_info['content'];
  $post_content_ru = $recipe_info['content_ru'];
  $post_content_en = $recipe_info['content_en'];
  $post_alias_ru = $recipe_info['alias_ru'];
  $post_alias_en = $recipe_info['alias_en'];
  $post_cats = $recipe_info['cats'];
  $post_status = $recipe_info['status'];
  $post_hash_tags = $recipe_info['hash_tags'];
  $post_create_date = $recipe_info['create_date'];
  $post_keywords_ru = $recipe_info['keywords_ru'];
  $post_keywords_en = $recipe_info['keywords_en'];
  $post_description_ru = $recipe_info['description_ru'];
  $post_description_en = $recipe_info['description_en'];
  $post_status_origin = $post_status;


  $post_ingredients_info = $init_user_recipes->get_ingredients($edit_item);
  $post_ingredients_ru = array();
  $post_ingredients_en = array();

  if($lang == 'en'){
    $post_title_en = $title;
    $post_content_en = $content;
    $post_ingredients_en = $post_ingredients_info;
  } else if($lang == 'ru'){
    $post_title_ru = $title;
    $post_content_ru = $content;
    $post_ingredients_ru = $post_ingredients_info;
  }


  $post_count_ingredients_ru = count($post_ingredients_ru);
  $post_count_ingredients_en = count($post_ingredients_en);

  $post_count_cats = count($post_cats);
  $post_count_hash_tags = count($post_hash_tags);

  $post_only_cats_ids = array();

  for ($i = 0; $i < $post_count_cats; $i++){
    array_push($post_only_cats_ids,$post_cats[$i]['id']);
  }


  $uploader = new Uploader(array(
    'table_name' => 'user_recipes_images',
    'item_id' => $edit_item,
    'path' => '/images/post_images/thumbnail_480/',
    'max_files' => 1
  ));

}  else{
  exit('Рецепт не найден');
}


$arr_status_post['approval'] = 'На утверждении';
$arr_status_post['draft'] = 'Черновик';

$tree_cats = Create_cats_tree::create_tree_post_cats(array(
  'checked_cats' => $post_only_cats_ids
));

$add_cat_tree_cats = Create_cats_tree::create(array(
  'in_table' => false
));

echo '<div id="post_info" data-info=\'{"edit_item": "'.$edit_item.'",';
echo '"post_status": "'.$post_status_origin.'"';
echo '}\'></div>';

echo '<div id="post_date" data-info=\'{"year": "'.$now_year.'",';
echo '"month": "'.$now_month.'",';
echo '"day": "'.$now_day.'",';
echo '"hour": "'.$now_hours.'",';
echo '"minut": "'.$now_minuts.'",';
echo '"seconds": "'.$now_seconds.'"';
echo '}\'></div>';

?>

<script type="text/javascript">
  var user_recipe_id = '<?php echo (int)$edit_item; ?>';
</script>

<script src="/admin/js/uniform.min.js" charset="utf-8"></script>
<script src="/admin/js/text_editor/summernote.min.js" charset="utf-8"></script>
<script src="/admin/js/text_editor/editor_summernote.js" charset="utf-8"></script>

<div class="title_page">
  <h2><?php echo $page_title; ?></h2>
</div>


<div class="col-md-8 body_add_post">
  <div class="panel panel-flat">

        <div class="panel-body">


          <fieldset class="content-group">


            <div class="form-group">
              <label class="control-label col-lg-2">Заголовок:</label>
              <div class="col-lg-10">
                <input type="text" id="post_title_ru" value="<?php echo $post_title_ru; ?>" placeholder="Название" class="form-control">
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">Title:</label>
              <div class="col-lg-10">
                <input type="text" id="post_title_en" value="<?php echo $post_title_en; ?>" placeholder="Название" class="form-control">
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">Алиас:</label>
              <div class="col-lg-10">
                <input type="text" id="post_alias_ru" value="<?php echo $post_alias_ru; ?>" placeholder="Название" class="form-control">
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">URL:</label>
              <div class="col-lg-10">
                <input type="text" id="post_alias_en" value="<?php echo $post_alias_en; ?>" placeholder="Название" class="form-control">
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">Описание:</label>
              <div class="col-lg-10">
                <div id="content_post_ru">
                  <?php echo $post_content_ru; ?>
                </div>
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">Description:</label>
              <div class="col-lg-10">
                <div id="content_post_en">
                  <?php echo $post_content_en; ?>
                </div>
              </div>
              <div class="clear"></div>
            </div>

            <style media="screen">
              .btn_add_ing{
                margin-top: 10px;
                line-height: normal;
              }
              .ing_item{
                margin: 10px 0px;
                box-shadow: 1px 1px 1px #ccc,0px 0px 1px #ccc;
                padding: 5px;
              }
              .ing_name{
                width: 90%;
                line-height: 25px;
              }
            </style>

            <div class="form-group">
              <label class="control-label col-lg-2">Ингредиенты:</label>
              <div class="col-lg-10">
                <textarea name="name" id="img_name_ru" class="full_w form-control" placeholder="Название ингредиента" rows="2" cols="80"></textarea>
                <div class="text_right" id="save_ing_ru" style="display: none;">
                  <div class="btn btn-xs btn-info btn_add_ing" onclick="save_ing('ru');">
                    Сохранить
                  </div>
                </div>
                <div class="text_right">
                  <div class="btn btn-xs btn-success btn_add_ing" onclick="group_add_ing('ru');">
                    Разделить ингредиенты
                  </div>
                  <div class="btn btn-xs btn-success btn_add_ing" onclick="add_ing('ru');">
                    Добавить ингредиент
                  </div>
                </div>
                <div class="ing_items" id="ing_items_ru">
                  <?php
                  for ($i = 0,$x = 1; $i < $post_count_ingredients_ru; $i++,$x++) {
                    $ing_name = $post_ingredients_ru[$i];

                    echo '<div class="ing_item ing_item_ru" data-ingid="'.$x.'" id="ing_'.$x.'_ru">';
                      echo '<div class="float_l ing_name" id="img_name_'.$x.'_ru">'.$ing_name.'</div>';
                      echo '<div class="float_r ing_sett">';
                        echo '<i class="fa fa-cog g_color cursor_p" onclick="show_edit_ing('.$x.',\'ru\')" title="Изменить"></i>&nbsp;&nbsp;';
                        echo '<i class="fa fa-close r_color cursor_p" onclick="delete_ing('.$x.',\'ru\')" title="Удалить"></i>';
                      echo '</div>';
                      echo '<div class="clear"></div>';
                    echo '</div>';

                  }

                  ?>
                </div>
              </div>
              <div class="clear"></div>
            </div>

            <div class="form-group">
              <label class="control-label col-lg-2">Ingredients:</label>
              <div class="col-lg-10">
                <textarea name="name" id="img_name_en" class="full_w form-control" placeholder="The name of the ingredient" rows="2" cols="80"></textarea>
                <div class="text_right" id="save_ing_en" style="display: none;">
                  <div class="btn btn-xs btn-info btn_add_ing" onclick="save_ing('en');">
                    Сохранить
                  </div>
                </div>
                <div class="text_right">
                  <div class="btn btn-xs btn-success btn_add_ing" onclick="group_add_ing('en');">
                    Divide the ingredients
                  </div>
                  <div class="btn btn-xs btn-success btn_add_ing" onclick="add_ing('en');">
                    Add ingredient
                  </div>
                </div>
                <div class="ing_items" id="ing_items_en">
                  <?php
                  for ($i = 0,$x = 1; $i < $post_count_ingredients_en; $i++,$x++) {
                    $ing_name = $post_ingredients_en[$i];

                    echo '<div class="ing_item ing_item_en" data-ingid="'.$x.'" id="ing_'.$x.'_en">';
                      echo '<div class="float_l ing_name" id="img_name_'.$x.'_en">'.$ing_name.'</div>';
                      echo '<div class="float_r ing_sett">';
                        echo '<i class="fa fa-cog g_color cursor_p" onclick="show_edit_ing('.$x.',\'en\')" title="Edit"></i>&nbsp;&nbsp;';
                        echo '<i class="fa fa-close r_color cursor_p" onclick="delete_ing('.$x.',\'en\')" title="Delete"></i>';
                      echo '</div>';
                      echo '<div class="clear"></div>';
                    echo '</div>';

                  }

                  ?>
                </div>
              </div>
              <div class="clear"></div>
            </div>

          </fieldset>

        </div>
      </div>

      <div class="panel panel-flat">
        <div class="padd_ten tp_comment">
          <strong>Комментарии</strong>
        </div>
        <div class="">
          <div class="cont_add_comment padd_ten">
            <strong>Добавить комментарий</strong>
            <div class="cadd_field">
              <textarea id="comment_content" class="full_w padd_ten" placeholder="Текст комментария" name="name" rows="8" cols="80"></textarea>
            </div>
            <div class="text_right">
              <!-- <div class="float_l btn btn-xs btn-default">Отмена</div> -->
              <div data-postid="<?php echo $edit_item; ?>" class="btn btn-xs btn-info" onclick="comments.add_comment(this);">Добавить комментарий</div>
            </div>
          </div>
        </div>
        <div class="">
          <table class="table dataTable no-footer">
            <tbody class="body_cats">
              <?php

              if($count_comments > 0){

                for ($i = 0; $i < $count_comments; $i++) {
                  $comment_id = $comments[$i]['id'];
                  $comment_status = $comments[$i]['status'];
                  $comment_parent = $comments[$i]['parent_comment'];
                  $comment_parent_user_name = $comments[$i]['parent_comment_user_name'];
                  $comment_content = $comments[$i]['content'];
                  $comment_user_name = $comments[$i]['user_name'];
                  $comment_user_email = $comments[$i]['user_email'];
                  $comment_user_site = $comments[$i]['user_site'];
                  $comment_user_ip = $comments[$i]['user_ip'];
                  $comment_post_id = $comments[$i]['post_id'];
                  $comment_post_title = $comments[$i]['post_title'];
                  $comment_post_count_comments = $comments[$i]['post_count_comments'];
                  $comment_create_date = $comments[$i]['create_date'];

                  $comment_create_date = strtotime($comment_create_date) + $_SESSION['time_offset'];

                  $answer_autor = $comment_parent > 0 ? '<div><small>Ответ автору <a href="#">'.$comment_parent_user_name.'</a></small></div>' : '';

                  echo '<tr id="comment_item_'.$comment_id.'">';
                  echo '<td class="c_td_autor" style="width: 35%;">';

                  echo '<div class="float_l uc_left_grid">';
                    echo '<img src="/images/i_website.png" class="cover_img" alt="">';
                  echo '</div>';
                  echo '<div class="float_l uc_right_grid">';
                    echo '<div class="uc_name"><strong>'.$comment_user_name.'</strong></div>';
                    echo '<div class="uc_email">';
                      echo '<a href="mailto:'.$comment_user_email.'">'.$comment_user_email.'</a>';
                    echo '</div>';
                  echo '</div>';
                  echo '<div class="clear"></div>';
                  echo '<div class="uc_ip">';
                    echo '<a href="#">'.$comment_user_ip.'</a>';
                  echo '</div>';

                  echo '</td>';
                  echo '<td class="_col_title">';
                  echo '<div class="cmt_content">'.$answer_autor.$comment_content.'</div>';
                  echo '<div class="hidden_actions_cat">';

                  if($comment_status == 'pending' || $comment_status == 'approved'){

                    if($comment_status == 'pending'){
                      echo '<div class="ha_cat_item g_color" data-commentid="'.$comment_id.'" data-status="approved" onclick="comments.change_status(this);">Одобрить | </div>';
                    } else {
                      echo '<div class="ha_cat_item cancel_comment" data-commentid="'.$comment_id.'" data-status="pending" onclick="comments.change_status(this);">Отклонить | </div>';
                    }
                    echo '<div class="ha_cat_item" data-commentid="'.$comment_id.'" onclick="comments.show_answer_comment(this);" data-postid="'.$edit_item.'" data-colspan="2">Ответить | </div>';
                    echo '<a href="?q=add_post&edit_item='.$comment_id.'">';
                    echo '<div class="ha_cat_item">Изменить | </div>';
                    echo '</a>';
                    echo '<div class="ha_cat_item" data-commentid="'.$comment_id.'" onclick="comments.show_edit(this);">Свойства | </div>';
                    echo '<div class="ha_cat_item" data-commentid="'.$comment_id.'" data-status="spam" onclick="comments.change_status(this);" style="color: #a00;">Спам | </div>';
                    echo '<div class="ha_cat_item" data-commentid="'.$comment_id.'" onclick="comments._delete(this);" style="color: #a00;">Удалить</div>';
                    echo '<div class="clear"></div>';
                    echo '</div>';

                  }

                  echo '</td>';
                  echo '</tr>';
                }

              } else{

              }

              ?>
            </tbody>
          </table>
        </div>
      </div>

</div>
<div class="col-md-4">
  <div class="panel panel-flat">
    <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
      <h6 class="panel-title"><strong>Рубрики</strong></h6>
    </div>
    <div class="panel-body padd_ten">
      <div class="p_cats_content">
        <?php echo $tree_cats; ?>
        </div>
        <div class="">
          <div class="pt_add_new_cat" id="pt_add_new_cat">
            <span>+ Добавить новую рубрику</span>
          </div>
          <div class="cont_add_new_cat" id="cont_add_new_cat">
            <div class="item_add_new_cat">
              <input type="text" id="n_cat_name" class="full_w" name="" placeholder="Название рубрики" value="">
            </div>
            <div class="item_add_new_cat">
              <select class="full_w" id="n_cat_parent" name="">
                <option value="0">Родительска рубрика</option>
                <?php echo $add_cat_tree_cats; ?>
              </select>
            </div>
            <div class="item_add_new_cat">
              <div class="btn btn-default full_w" onclick="post.add_cat();">Добавить новую рубрику</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h6 class="panel-title"><strong>Действия</strong></h6>
      </div>
      <div class="panel-body padd_ten">

        <div class="" style="margin-bottom: 15px;">
          <label>Причина отклонения</label>
          <input type="text" class="form-control" id="rejected_reason">
        </div>

        <div class="btn btn-xs btn-default" onclick="user_recipes.change_status('in_moderation');">На модерации</div>
        <div class="btn btn-xs btn-danger" onclick="user_recipes.change_status('rejected');">Отклонить</div>
        <div class="btn btn-xs btn-info float_r" id="btn_pub" onclick="post.add_post(this);" data-action="<?php echo $data_action;?>"><?php echo $post_text_btn; ?></div>
      </div>
    </div>

    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h6 class="panel-title"><strong>Метки</strong></h6>
      </div>
      <div class="panel-body padd_ten">
        <div class="">
          <div class="i_block relative v_align_middle pi_add_ht" id="pi_add_ht">
            <input id="wr_hash_tags" onkeyup="post.init_search_ht(this);" type="text" class="full_w relative i_add_ht" name="" value="">
            <div class="absolute ico_ht">
              <i class="fa fa-hashtag" aria-hidden="true"></i>
            </div>
            <div class="absolute ht_search_result" id="ht_search_result"></div>
          </div>
          <div class="btn btn-default btn-xs" onclick="post.add_hash_tags();">Добавить</div>
        </div>
        <div class="blk_info">Метки разделяются запятыми</div>
        <div class="ht_items" id="ht_items">

          <?php
          for ($i = 0; $i < $post_count_hash_tags; $i++) {
            $name = $post_hash_tags[$i]['name'];

            echo '<div class="relative ht_item" data-name="'.$name.'">';
              echo '<div class="absolute delete_ht" onclick="post.delete_hash_tag(this);">×</div>';
              echo '<div class="ht_name">'.$name. '</div>';
            echo '</div>';

          }
          ?>

        </div>
      </div>
    </div>


    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h6 class="panel-title"><strong>Изображение</strong></h6>
      </div>

      <style media="screen">
        .upl_post_img .col-lg-10{
          width: 100%;
        }
      </style>
      <div class="panel-body padd_ten upl_post_img text_center">

        <?php
        echo $uploader->create_html();

         ?>

        <!-- <div class="post_image">
          <img src="http://chto-polezno.ru/images/masha1/bliny-na-moloke-00.jpg" alt="">
        </div>

        <div class="btn btn-xs full_w btn-info btn_load_pi" id="btn_load_img">
          Загрузить изображение
        </div> -->

      </div>
    </div>


    <div class="panel panel-flat">
      <div class="panel-heading" style="border-bottom: 1px solid #eee; padding: 10px 20px; margin-bottom: 15px;">
        <h6 class="panel-title"><strong>SEO</strong></h6>
      </div>
      <div class="panel-body padd_ten">
        <div class="">
          <label for="">Keywords ru</label>
          <textarea name="name" id="post_keywords_ru" class="full_w relative i_add_ht" rows="2" cols="80"><?php echo $post_keywords_ru; ?></textarea>
        </div>

        <div class="">
          <label for="">Description ru</label>
          <textarea name="name" id="post_description_ru" class="full_w relative i_add_ht" rows="4" cols="80"><?php echo $post_description_ru; ?></textarea>
        </div>

        <hr>

        <div class="">
          <label for="">Keywords en</label>
          <textarea name="name" id="post_keywords_en" class="full_w relative i_add_ht" rows="2" cols="80"><?php echo $post_keywords_en; ?></textarea>
        </div>

        <div class="">
          <label for="">Description en</label>
          <textarea name="name" id="post_description_en" class="full_w relative i_add_ht" rows="4" cols="80"><?php echo $post_description_en; ?></textarea>
        </div>

      </div>
    </div>

</div>

<script src="/admin/js/scripts/comments.js?ver=<?=rand(1,10000000000); ?>" charset="utf-8"></script>
<script src="/admin/js/scripts/post.js?ver=<?=rand(1,10000000000); ?>" charset="utf-8"></script>
<script type="text/javascript">
  $('#pt_add_new_cat').on('click',function(){
    if($('#cont_add_new_cat').hasClass('active_add_new_cat')){
      $('#cont_add_new_cat').removeClass('active_add_new_cat');
    } else{
      $('#cont_add_new_cat').addClass('active_add_new_cat');
    }
  });


  $('.hund_event').on('click',hund_event);

  function hund_event(e){
    var link = $(this).attr('data-link');
    if($('#' + link).hasClass(link)){
      $('#' + link).removeClass(link);
    } else{
      $('#' + link).addClass(link);
    }
  }
  var write_alias_ru = true;
  var write_alias_en = true;

  function click_img_item(e){
    e = _e.getEvent(e);
    var target = _e.getTarget(e);
    if(search_class(target,'btn_set_main')){
      return set_main_image.call(target);
    }
    if(search_class(target,'close')){
      return upload_image.delete_image.call(target);
    }
  }

  $(document).ready(function(){
    post.init();

    if(post.current_edit_item > 0){
      upload_image.init({
        url: '/admin/components/uploader/uploader.php',
        value_item: post.current_edit_item,
        page: 'edit_post'
      });


      $('.l_img_item').on('click',click_img_item);

    } else{
      var hash = gId('md5_hash').value;
      upload_image.init({
        url: '/admin/components/uploader/uploader.php',
        value_item: hash,
        page: 'add_post'
      });
    }

    $('#post_alias_ru').on('keyup',function(e){
      write_alias_ru = false;

      if($.trim($(this).val()) == '') write_alias_ru = true;

      this.value=this.value
     .replace(/ /g, ".")
     .replace(/_/g, "-")
     .replace(/\.+/g, ".")
     .replace(/\-+/g, "-")
     .replace(/[^\w-]|[A-Z]|^[.-]/g, "")
    });

    $('#post_alias_en').on('keyup',function(e){
      write_alias_en = false;

      if($.trim($(this).val()) == '') write_alias_en = true;

      this.value=this.value
     .replace(/ /g, ".")
     .replace(/_/g, "-")
     .replace(/\.+/g, ".")
     .replace(/\-+/g, "-")
     .replace(/[^\w-]|[A-Z]|^[.-]/g, "")
    });

    $('#post_title_ru').on('keyup',function(e){
      var str = this.value;

      if(write_alias_ru === false) return true;

      ajx({
        url: '/admin/ajax/ajax_categories.php',
        method: 'post',
        dataType: 'json',
        data: {
          action: 'create_chpy',
          lang: 'ru',
          str: str
        },
        success: function(data){
          if(data.result == 'true'){
            $('#post_alias_ru').val(data.str);
          }
        },
        error: function(err){
          console.log(err);
        }
      });
    });

    $('#post_title_en').on('keyup',function(e){
      var str = this.value;

      if(write_alias_en === false) return true;

      ajx({
        url: '/admin/ajax/ajax_categories.php',
        method: 'post',
        dataType: 'json',
        data: {
          action: 'create_chpy',
          lang: 'en',
          str: str
        },
        success: function(data){
          if(data.result == 'true'){
            $('#post_alias_en').val(data.str);
          }
        },
        error: function(err){
          console.log(err);
        }
      });
    });

  });

  var te_upload_image;
  var content_post_ru;
  var content_post_en;

  $(document).ready(function() {
    var hash = gId('md5_hash').value;

    te_upload_image.init({
      url: '/components/text_editor/uploader/uploader.php',
      page: 'text_editor',
      value_item: hash
    });

    content_post_ru = new TextEditor({
      element: gId('content_post_ru')
    });



    content_post_en = new TextEditor({
      element: gId('content_post_en')
    });

  });



    var current_ing_id = {};
    function save_ing(px){
      var name = $('#img_name_' + px).val();
      $('#img_name_' + current_ing_id[px] + '_' + px).text(name);
      $('#img_name_' + px).val('');
      $('#save_ing_' + px).hide();
    }

    function group_add_ing(px){
      var content = $('#img_name_' + px).val();
      var ings = content.split(';');
      for (var i = 0; i < ings.length; i++) {
        if(ings[i] && (ings[i].replace(/\s/g, '') != '')) add_ing(px,ings[i]);
      }
    }

    function add_ing(px,_name) {
      var name = typeof _name !== 'undefined' ? _name : $('#img_name_' + px).val();
      var ing_id = $('.ing_item').length;
      ing_id++;
      if(!name) return alert('Введите название ингредиента');
      var html = '';

      html += '<div class="ing_item ing_item_' + px + '" data-ingid="' + ing_id + '" id="ing_' + ing_id + '_' + px + '">';
        html += '<div class="float_l ing_name" id="img_name_' + ing_id + '_' + px + '">' + name + '</div>';
        html += '<div class="float_r ing_sett">';
          html += '<i class="fa fa-cog g_color cursor_p" onclick="show_edit_ing(' + ing_id + ',\'' + px + '\')" title="Изменить"></i>&nbsp;&nbsp;';
          html += '<i class="fa fa-close r_color cursor_p" onclick="delete_ing(' + ing_id + ',\'' + px + '\')" title="Удалить"></i>';
        html += '</div>';
        html += '<div class="clear"></div>';
      html += '</div>';

      $('#img_name_' + px).val('');
      $('#ing_items_' + px).append(html);

    }

    function show_edit_ing(ing_id,px) {
      current_ing_id[px] = ing_id;
      var name = $('#img_name_' + ing_id + '_' + px).text();
      $('#img_name_' + px).val(name);
      $('#save_ing_' + px).show();
    }
    function delete_ing(ing_id,px) {
      return $('#ing_' + ing_id + '_' + px).remove();
    }

</script>
<link rel="stylesheet" href="/components/text_editor/css/styles.css">
<script src="/admin/js/scripts/user_recipes.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/text_editor.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/resize_image.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/edit_image.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/uploader/uploader.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
