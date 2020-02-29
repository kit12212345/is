<?php
require_once($root_dir.'/include/classes/categories.php');
require_once($root_dir.'/admin/include/classes/tree_cats.php');

$var_edit_item = (int)$_GET['edit_item'];

$Categories = new Categories();

$cat_info = $Categories->get_cat_info($var_edit_item);

$add_cat_tree_cats = Create_cats_tree::create(array(
  'in_table' => false,
  'parent_id' => $cat_info['parent_id'],
  'hide_childrens' => $cat_info['arr_last_items']
));

if($cat_info === false){
  exit('Категория не найдена');
}


?>

<script type="text/javascript">
  var current_edit_item = '<?php echo $var_edit_item; ?>';
</script>

<div class="title_page">
  <h2>Изменение рубрики</h2>
</div>
<div class="col-sm-12">
  <div class="panel panel-flat">
    <div class="panel-body">


      <fieldset class="content-group">


        <div class="form-group">
          <label class="control-label col-lg-2">Навзвание рубрики:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_name_ru" value="<?php echo $cat_info['name_ru']; ?>" placeholder="Название" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Category name:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_name_en" value="<?php echo $cat_info['name_en']; ?>" placeholder="Название" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Описание рубрики:</label>
          <div class="col-lg-10">
            <textarea rows="13" cols="5" id="cat_description_ru" class="form-control" placeholder="Описание"><?php echo $cat_info['description_ru']; ?></textarea>
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Category description:</label>
          <div class="col-lg-10">
            <textarea rows="13" cols="5" id="cat_description_en" class="form-control" placeholder="Описание"><?php echo $cat_info['description_en']; ?></textarea>
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Алиас:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_alias_ru" value="<?php echo $cat_info['alias_ru']; ?>" placeholder="Алиас" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">URl:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_alias_en" value="<?php echo $cat_info['alias_en']; ?>" placeholder="Алиас" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Падеж:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_translate" value="<?php echo $cat_info['translate']; ?>" placeholder="Translate" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Keywords ru:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_keywords_ru" value="<?php echo $cat_info['keywords_ru']; ?>" placeholder="Keywords" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Keywords en:</label>
          <div class="col-lg-10">
            <input type="text" id="cat_keywords_en" value="<?php echo $cat_info['keywords_en']; ?>" placeholder="Keywords" class="form-control">
          </div>
          <div class="clear"></div>
        </div>

        <div class="form-group">
          <label class="control-label col-lg-2">Родительская рубрика:</label>
          <div class="col-lg-10">
            <select class="form-control" id="cat_parent_id" name="cat_parent_id">
              <option value="0">Без рубрики</option>
              <?php echo $add_cat_tree_cats; ?>
            </select>
          </div>
          <div class="clear"></div>
        </div>

        <hr>

        <div class="text_right">
          <div class="btn btn-primary" onclick="categories.save(this);" data-action="edit_cat">
            Сохранить рубрику
          </div>
          <div class="btn r_color" onclick="categories.delete(this);">
            Удалить
          </div>
        </div>

      </fieldset>

    </div>
  </div>

</div>

<script src="/admin/js/scripts/categories.js?ver=<?=rand(0,10000000); ?>" charset="utf-8"></script>
