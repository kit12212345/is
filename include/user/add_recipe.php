<?php
require($root_dir.'/admin/components/uploader/index.php');
include($root_dir.'/components/text_editor/modals.php');
include_once($root_dir.'/include/classes/categories.php');

$init_categories = new Categories();
$categories = $init_categories->get_cats();
$count_categories = count($categories);

$recipe_hash = isset($_GET['h']) ? $_GET['h'] : '';
$event = 'add';
$cats = array();
$cats_items = array();

if($item_id == 0){
  $item_id = $init_user_recipes->add_draft(array(
    'h' => $recipe_hash
  ));
  $init_user_recipes->recipe_id = $item_id;
}

if($item_id > 0){

  $recipe_info = $init_user_recipes->get_recipe_info();
  $ingredients = $recipe_info['ingredients'];
  $count_ingredients = count($ingredients);
  $cats = $recipe_info['cats'];
  foreach ($cats as $key => $value) {
    array_push($cats_items,$cats[$key]['id']);
  }

  if($recipe_info === false){
    echo "Not found";
  }

  $event = 'update';

}

$uploader = new Uploader(array(
  'table_name' => 'user_recipes_images',
  'item_id' => $item_id,
  'path' => '/images/post_images/thumbnail_480/',
  'max_files' => 1
));

?>
<script type="text/javascript">
  var item_id = '<?php echo (int)$item_id; ?>';
  var recipe_hash = '<?php echo $recipe_hash; ?>';
</script>
<link rel="stylesheet" href="/components/text_editor/css/styles.css">
<link rel="stylesheet" href="/admin/css/styles.css?ver=1123">
<script src="/components/uploader/uploader.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/text_editor.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/resize_image.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/js/edit_image.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
<script src="/components/text_editor/uploader/uploader.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>

<div class="it_add_comment">
  <em><?php echo $l->add_recipe_info ?></em>
</div>

<div class="it_add_comment ask_post">
  <label><?php echo $l->name_rec ?></label>
  <input onkeyup="user_recipes.listen_save_rc();" class="full_w" value="<?php echo $recipe_info['title'] ?>" id="r_title" placeholder="<?php echo $l->name_rec_ent ?>" type="text">
</div>

<div class="it_add_comment">
  <label><?php echo $l->description_rec ?></label>
  <div id="r_description">
    <?php echo $recipe_info['content']; ?>
  </div>
  <!-- <textarea class="full_w" name="name" rows="8" cols="80" id="r_description" placeholder="<?php echo $l->description_rec_ent ?>"></textarea> -->
</div>

<style media="screen">
.bd_check_ci{
  width: 17px;
  height: 17px;
  display: inline-block;
  background: #fff;
  vertical-align: sub;
  box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
  border: 1px solid #ccc;
  margin-right: 4px;
}
.selectit > input:checked + .bd_check_ci::before{
  content: "\2714";
  position: absolute;
  top: 5px;
  bottom: 0px;
  right: 0px;
  left: 0px;
  width: 10px;
  height: 18px;
  color: #3c95d6;
  font-weight: 600;
  margin: auto;
  font-size: 11.5px;
}
.selectit > input{
  display: none;

}
.ch_post_param > span{
  text-decoration: underline;
  font-size: 12px;
  color: #0073aa;
  margin-left: 5px;
}
</style>

<div class="it_add_comment">
  <label><?php echo $l->category ?></label>
  <ul id="cats_list_0" class="cats_list">
    <?php
    if($count_categories > 0){
      for ($i = 0; $i < $count_categories; $i++) {
        $cat_id = $categories[$i]['id'];
        $cat_name = $categories[$i]['name'];

        $checked = in_array($cat_id,$cats_items) ? 'checked="checked"' : '';

        echo '<li id="category_'.$cat_id.'">';
          echo '<label class="selectit">';
            echo '<input onchange="user_recipes.listen_save_rc();" value="'.$cat_id.'" type="checkbox" '.$checked.' class="select_tree_cat" id="in_category_'.$cat_id.'">';
            echo '<div class="relative bd_check_ci">';
              echo '<div class="absolute all_null checked_ci"></div>';
            echo '</div>';
            echo '<span class="v_align_middle">'.$cat_name.'</span>';
          echo '</label>';
        echo '</li>';

      }
    }
    ?>
  </ul>
</div>

<div class="it_add_comment ask_post">
  <label><?php echo $l->ingredients ?> (<?php echo $l->if_any ?>)</label>
  <div class="it_add_comment">
    <em><?php echo $l->divide_ingredient_info ?></em>
  </div>
  <textarea id="ing_name" class="full_w" rows="2" cols="80" placeholder="<?php echo $l->ingredient_name ?>"></textarea>
  <div class="text_right" style="margin-top: 10px;">
    <div id="btns_add_ing">
      <div class="btn cursor_p w_color float_l btn_top_login" onclick="user_recipes.group_add_ingredient(); user_recipes.listen_save_rc();">
        <?php echo $l->divide_ingredient ?>
      </div>
      <div class="btn cursor_p w_color i_block btn_top_login btn_m_t_10" onclick="user_recipes.add_ingredient(); user_recipes.listen_save_rc();">
        <?php echo $l->add_ingredient ?>
      </div>
      <div class="clear"></div>
    </div>
    <div id="btns_edit_ing" style="display:none;">
      <div class="btn btn_edit_ing cursor_p w_color i_block btn_top_login" onclick="user_recipes.save_ingredient(); user_recipes.listen_save_rc();">
        <?php echo $l->save ?>
      </div>
      <div class="btn btn_edit_ing cursor_p w_color i_block btn_top_login" onclick="user_recipes.cancel_edit_ingredient()">
        <?php echo $l->cancel ?>
      </div>
    </div>
  </div>
  <div class="ing_items" id="ing_items">
    <?php
    for ($i = 0,$in = 1; $i < $count_ingredients; $i++,$in++) {
      echo '<div class="ing_item" data-ingid="'.$in.'" id="ing_'.$in.'">';
        echo '<div class="float_l ing_name" id="ing_name_'.$in.'">'.$ingredients[$i].'</div>';
        echo '<div class="float_r ing_sett">';
          echo '<i class="fa fa-cog g_color cursor_p" onclick="user_recipes.show_edit_ingredient('.$in.')" title="Изменить"></i>&nbsp;&nbsp;';
          echo '<i class="fa fa-close r_color cursor_p" onclick="user_recipes.delete_ingredient('.$in.'); user_recipes.listen_save_rc();" title="Удалить"></i>';
        echo '</div>';
        echo '<div class="clear"></div>';
      echo '</div>';
    }
    ?>
  </div>
</div>

<div class="it_add_comment ask_post">
  <label><?php echo $l->main_image ?></label>
  <div>
    <?php
    echo $uploader->create_html();
    ?>
  </div>
  <!-- <textarea class="full_w" name="name" rows="8" cols="80" id="r_description" placeholder="<?php echo $l->description_rec_ent ?>"></textarea> -->
</div>


<div class="text_right ask_post">
  <div class="btn cursor_p w_color i_block btn_top_login" data-event="<?php echo $event; ?>" onclick="user_recipes.save_recipe(this);">
    <?php echo $item_id > 0 ? $l->update_recipe_btn : $l->add_recipe_btn ?>
  </div>
</div>

<script type="text/javascript">
var te_upload_image;
var uploader_recipe_image;
var waiting_for_lang;

domReady(function(){

  uploader_recipe_image = new Uploader({
    value_item: item_id > 0 ? item_id : gId('md5_hash').value,
    _event: item_id > 0 ? 'edit_user_recipe' : 'add_user_recipe'
  });

  te_upload_image.init({
    url: '/components/text_editor/uploader/uploader.php',
    page: 'text_editor',
    value_item: '<?php echo md5(time().rand(1,199999999)) ?>'
  });

  waiting_for_lang = setInterval(function(){
    if(l.hasOwnProperty('edit')){
      user_recipes.init_recipe_content();
      clearInterval(waiting_for_lang);
    }
  },50);


});
</script>
