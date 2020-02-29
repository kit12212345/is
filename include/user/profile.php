<?php
if(!class_exists('Action')) include_once($root_dir.'/include/classes/action.php');

$init_action = new Action();
$is_user_in_contest = $init_action->get_users(array(
  'user_id' => $user_id
));
?>

<div class="box post_content">
  <div class="post_title">
    <h1 class="i_block"><?php echo $action == 'edit' ? $l->t_edit_profile : $l->my_profile ?></h1>
    <?php
    if($action == 'edit'){
      echo '<a class="float_r td_underline" href="/user?p=profile">'.($l->back).'</a>';
    } else{
      echo '<a class="float_r td_underline" href="/user?p=profile&action=edit">'.($l->edit).'</a>';
    }
    ?>
    <div class="clear"></div>
  </div>
  <div class="post_cn">
    <div class="cont_post">

      <?php
      if($action == 'edit'){
        require($root_dir.'/admin/components/uploader/index.php');

        $uploader = new Uploader(array(
          'table_name' => 'user_images',
          'item_id' => $user_id,
          'path' => '/images/user_images/thumbnail_480/',
          'max_files' => 1
        ));

        ?>
        <link rel="stylesheet" href="/admin/css/styles.css?ver=1123">
        <script src="/components/uploader/uploader.js?ver=<?php echo rand(0,199999); ?>" charset="utf-8"></script>
        <script src="/js/core/functions.js" charset="utf-8"></script>
        <script src="/js/core/dom.js" charset="utf-8"></script>

        <div class="it_add_comment">
          <label><?php echo $l->name ?></label>
          <input class="full_w" value="<?php echo $user_name; ?>" id="u_name" placeholder="<?php echo $l->enter_name ?>" type="text">
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->last_name ?></label>
          <input class="full_w" value="<?php echo $user_last_name; ?>" id="u_last_name" placeholder="<?php echo $l->enter_last_name ?>" type="text">
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->middle_name ?></label>
          <input class="full_w" value="<?php echo $user_middle_name ?>" id="u_middle_name" placeholder="<?php echo $l->enter_middle_name ?>" type="text">
        </div>

        <?php
        $checked_male = $sex == 1 ? 'checked="checked"' : '';
        $checked_female = $sex == 2 ? 'checked="checked"' : '';
        ?>

        <div class="it_add_comment">
          <label><?php echo $l->sex ?></label>
          <div class="float_l l_radio">
            <label class="i_block v_align_middle" for="u_male"><?php echo $l->male ?></label>
            <input <?php echo $checked_male; ?> class="i_block v_align_middle u_sex" id="u_male" name="u_sex" value="male" type="radio">
          </div>
          <div class="float_l l_radio">
            <label class="i_block v_align_middle" for="u_female"><?php echo $l->female ?></label>
            <input <?php echo $checked_female; ?> class="i_block v_align_middle u_sex" id="u_female" name="u_sex" value="female" type="radio">
          </div>
          <div class="clear"></div>
        </div>

        <div class="it_add_comment">
          <label><?php echo $l->birth_date ?></label>

          <select id="ub_day">
            <option value="0"><?php echo $l->day ?></option>
            <?php
            for ($i = 1; $i <= 31; $i++) {
              $selected = $time_birth_date !== false && $i == date('d',$time_birth_date) ? 'selected="selected"' : '';
              echo '<option '.$selected.' value="'.$i.'">'.($i >= 10 ? $i : '0'.$i).'</option>';
            }
            ?>
          </select>

          <select id="ub_month">
            <option value="0"><?php echo $l->month ?></option>
            <?php
            for ($i = 1; $i <= 12; $i++) {
              $selected = $time_birth_date !== false && $i == date('m',$time_birth_date) ? 'selected="selected"' : '';
              echo '<option '.$selected.' value="'.$i.'">'.($i >= 10 ? $i : '0'.$i).'</option>';
            }
            ?>
          </select>

          <select id="ub_year">
            <option value="0"><?php echo $l->year ?></option>
            <?php
            for ($i = date('Y') - 16; $i > 1900; $i--) {
              $selected = $time_birth_date !== false && $i == date('Y',$time_birth_date) ? 'selected="selected"' : '';
              echo '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
            }
            ?>
          </select>

        </div>

        <div class="it_add_comment">
          <label><?php echo $l->about_me ?></label>
          <textarea rows="8" cols="80" class="full_w" id="u_about_me" placeholder="<?php echo $l->about_me ?>"><?php echo $about_me ?></textarea>
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

        <div class="text_right">
          <div class="btn cursor_p w_color i_block btn_top_login" onclick="user.save_profile();">
            <?php echo $l->save_changes ?>
          </div>
        </div>

        <script type="text/javascript">
        var uploader_user_image;

        domReady(function(){

          uploader_recipe_image = new Uploader({
            value_item: '<?php echo $user_id; ?>',
            _event: 'edit_user_image'
          });
        });
        </script>

        <?php
      } else{
        ?>
          <?php
          if($init_action->competition_id > 0){
            echo '<div class="post_title">';
            if($is_user_in_contest !== false){
              echo '<p>';
                if($is_user_in_contest[0]['count_recipes'] < 5){
                  echo $l->lk_in_the_contest_3.' <a target="_blank" href="/contest">'.$l->lk_in_the_contest_2.'</a> , '.$l->lk_in_the_contest_4.' '.(5 - $is_user_in_contest[0]['count_recipes']).'</a><br>';
                } else{
                  echo $l->lk_in_the_contest.' <a target="_blank" href="/contest">'.$l->lk_in_the_contest_2.'</a>.<br>';
                }
                echo $l->lk_num_yourr_in_contest.' '.$is_user_in_contest[0]['count_recipes'];
              echo '</p>';
            } else{
              echo '<p>';
                echo $l->lk_now_contest.' <a target="_blank" href="/contest">'.$l->on_this_link.'</a><br>';
                echo $l->lk_desc_contest.' <a target="_blank" href="/contest">'.$l->lk_desc_contest_2.'</a>';
              echo '</p>';
            }
            ?>
            <?php
          }
          ?>
        </div>
        <table class="user_profile_table">
          <tbody>
            <tr>
              <td><?php echo $l->name ?></td>
              <td><?php echo $user_name; ?></td>
            </tr>
            <tr>
              <td><?php echo $l->last_name ?></td>
              <td><?php echo $user_last_name ? $user_last_name : $l->blank; ?></td>
            </tr>
            <tr>
              <td><?php echo $l->middle_name ?></td>
              <td><?php echo $user_middle_name ? $user_middle_name : $l->blank; ?></td>
            </tr>
            <tr>
              <td><?php echo $l->sex ?></td>
              <td><?php echo $sex > 0 ? $init_user->sex_names[$sex] : $l->blank; ?></td>
            </tr>
            <tr>
              <td>Email</td>
              <td><?php echo $user_email ?></td>
            </tr>
            <tr>
              <td><?php echo $l->birth_date ?></td>
              <td><?php echo $time_birth_date !== false ? $format_birth_date : $l->blank; ?></td>
            </tr>
            <tr>
              <td><?php echo $l->about_me ?></td>
              <td><?php echo $about_me ? $about_me : $l->blank; ?></td>
            </tr>
          </tbody>
        </table>
        <?php
      }
      ?>
      </div>
    </div>
  </div>
