<?php
$s_birthday = $birthday === '0000-00-00' ? '' : date('d.m.Y',strtotime($birthday));
?>
<div class="box p-3">
  <div class="page_title">
    <h1><?php echo $action == 'edit' ? $l->t_edit_profile : $l->my_profile ?></h1>
  </div>
  <hr>
  <div class="post_cn">

    <div class="form-group">
      <label><?php echo $l->first_name ?></label>
      <input class="form-control" value="<?php echo $user_first_name; ?>" id="u_first_name" placeholder="<?php echo $l->enter_name ?>" type="text">
    </div>

    <div class="form-group">
      <label><?php echo $l->last_name ?></label>
      <input class="form-control" value="<?php echo $user_last_name; ?>" id="u_last_name" placeholder="<?php echo $l->enter_last_name ?>" type="text">
    </div>

    <?php
    $checked_male = $sex == 1 ? 'checked="checked"' : '';
    $checked_female = $sex == 2 ? 'checked="checked"' : '';
    ?>

    <div class="form-group">
      <label class="mr-1"><?php echo $l->sex ?>: </label>
      <div class="form-check form-check-inline">
        <label class="form-check-label mr-1" for="u_male"><?php echo $l->male ?></label>
        <input <?php echo $checked_male; ?> class="form-check-input u_sex" id="u_male" name="u_sex" value="male" type="radio">
      </div>
      <div class="form-check form-check-inline">
        <label class="form-check-label mr-1" for="u_female"><?php echo $l->female ?></label>
        <input <?php echo $checked_female; ?> class="form-check-input u_sex" id="u_female" name="u_sex" value="female" type="radio">
      </div>
    </div>

    <div class="form-group">
      <label><?php echo $l->birth_date ?></label>
      <input class="form-control" value="<?php echo $s_birthday ?>" id="u_birthday" placeholder="дд.мм.гггг" type="text">
    </div>

    <div class="d-flex justify-content-end">
      <div class="btn btn-default" onclick="user.save_profile();">
        <?php echo $l->save_changes ?>
      </div>
    </div>

  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $('#u_birthday').mask('00.00.0000');
  });
</script>
