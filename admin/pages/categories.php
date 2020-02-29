<?php
require_once($root_dir.'/include/classes/categories.php');
require_once($root_dir.'/admin/include/classes/tree_cats.php');

$table_name = AdmConfig::$categories_table_name;

$var_parent_id = isset($_GET['cat_id']) && (int)$_GET['cat_id'] > 0 ? (int)$_GET['cat_id'] : 0;

$Categories = new Categories(array(
  'parent_id' => $var_parent_id
));

$categories = $Categories->get_cats();
$count_categories = count($categories);

// $Categories->reindex_cats();


$add_cat_tree_cats = Create_cats_tree::create(array(
  'in_table' => false
));
$add_cat_tree_cats_table = Create_cats_tree::create(array(
  'in_table' => true
));


?>
<link rel="stylesheet" href="/admin/css/categories.css">
<div class="title_page">
  <h2>Рубрики</h2>
</div>
<div class="col-sm-4">
  <div class="">
    <strong>Добавить новую рубрику</strong>
  </div>
  <div class="add_cat_item">
    <span>Название</span>
    <input type="text" id="cat_name" class="full_w" name="" value="">
    <div class="add_cat_desc">Название определяет, как элемент будет отображаться на вашем сайте.</div>
  </div>

  <div class="add_cat_item">
    <span>Алиас</span>
    <input type="text" id="cat_alias" class="full_w" name="" value="">
    <div class="add_cat_desc">«Алиас» — это вариант названия, подходящий для URL. Обычно содержит только латинские буквы в нижнем регистре, цифры и дефисы.</div>
  </div>

  <div class="add_cat_item">
    <span>Родительская рубрика</span>
    <select class="full_w" id="cat_parent_id" name="">
      <option value="0">Без категории</option>
      <?php
      echo $add_cat_tree_cats;
       ?>
    </select>
    <div class="add_cat_desc">Рубрики, в отличие от меток, могут иметь иерархию. Например, вы можете завести рубрику «Джаз», внутри которой будут дочерние рубрики «Бибоп» и «Биг-бэнды». Полностью произвольно</div>
  </div>

  <div class="add_cat_item">
    <span>Описание</span>
    <textarea name="name" id="cat_description" class="full_w" rows="8" cols="80"></textarea>
    <div class="add_cat_desc">Описание по умолчанию не отображается, однако некоторые темы могут его показывать.</div>
  </div>

  <hr />

  <div class="">
    <div class="btn btn-primary" onclick="categories.save(this);" data-action="add_cat">
      Добавить рубрику
    </div>
  </div>

  <script type="text/javascript">

  $(document).ready(function() {
    var write_alias = true;

    $('#cat_alias').on('keyup',function(e){
      write_alias = false;

      if($.trim($(this).val()) == '') write_alias = true;

      this.value=this.value
     .replace(/ /g, ".")
     .replace(/_/g, "-")
     .replace(/\.+/g, ".")
     .replace(/\-+/g, "-")
     .replace(/[^\w-]|[A-Z]|^[.-]/g, "")

    });

    $('#cat_name').on('keyup',function(e){
      var str = this.value;

      if(write_alias === false) return true;

      ajx({
        url: '/admin/ajax/ajax_categories.php',
        method: 'post',
        dataType: 'json',
        data: {
          action: 'create_chpy',
          str: str
        },
        success: function(data){
          if(data.result == 'true'){
            $('#cat_alias').val(data.str);
          }
        },
        error: function(err){
          console.log(err);
        }
      });
    });
  });
  </script>

</div>
<div class="col-sm-8">
  <div class="s_cats_actions">
    <div class="i_block">
      <select class="s_action_cats" id="s_action_cats" name="">
        <option value="null">Действия</option>
        <option value="delete_cat">Удалить</option>
      </select>
    </div>
    <div class="i_block">
      <div class="btn btn-xs btn-default" onclick="categories.apply_cats_action();">Применить</div>
    </div>
  </div>
  <div class="panel panel-flat">
    <div class="padd_ten">
      <div class="datatable-scroll">
        <table class="table dataTable no-footer">
          <thead>
            <tr>
              <th><input type="checkbox" id="checked_all_cats"></th>
              <th>Название</th>
              <th>Описание</th>
              <th>Алиас</th>
              <th>Записи</th>
            </tr>
          </thead>

          <!-- — -->
          <tbody class="body_cats">
            <?php
            if($count_categories > 0){
              echo $add_cat_tree_cats_table;
            } else{
              echo '<tr><td colspan="5" class="text_center"><strong>Нет рубрик</strong></td></tr>';
            }

            ?>

          </tbody>
        </table>

      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
  $('#checked_all_cats').on('click',function(e){
    var checked = $(this).prop('checked');

    $('.checked_cat_item').prop('checked',checked);

  });
</script>

<script src="/admin/js/scripts/categories.js?ver=<?=rand(0,10000000); ?>" charset="utf-8"></script>
