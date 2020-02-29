<!-- Main sidebar -->
<div class="sidebar sidebar-main">
  <div class="sidebar-content">

    <!-- User menu -->
    <div class="sidebar-user">
      <div class="category-content">
        <div class="media">
          <a href="#" class="media-left">
            <!-- <img src="/admin/images/user.jpg" class="img-circle img-sm cover_img" alt="avatar "> -->
          </a>
          <div class="media-body">
            <span class="media-heading text-semibold"><?php echo $LOGGED_USER['admin_first_name'].' '.$LOGGED_USER['admin_last_name']; ?></span>
            <div class="text-size-mini text-muted">
              Главный администратор
            </div>
          </div>

        </div>
      </div>
    </div>
    <!-- /user menu -->


    <!-- Main navigation -->
    <div class="sidebar-category sidebar-category-visible">
      <div class="category-content no-padding">
        <ul class="navigation navigation-main navigation-accordion">
          <!-- Main -->

          <?php
          $active_cats = $_GET['q'] == 'cats' ? 'active' : '';
          $active_goods = $_GET['q'] == 'goods' || empty($_GET['q']) || !isset($_GET['q']) ? 'active' : '';
          $active_comments = $_GET['q'] == 'comments' ? 'active' : '';
          $active_orders = $_GET['q'] == 'orders' ? 'active' : '';
          $active_add_post = $_GET['q'] == 'add_post' ? 'active' : '';
          $active_user_recipes = $_GET['q'] == 'user_recipes' ? 'active' : '';
          $display_count_questions = $count_not_readed_questions > 0 ? 'display: inline-block;' : 'display: none;';

          echo '<li class="navigation-header"><span>Главное меню</span> <i class="icon-menu" title="Main pages"></i></li>';
          echo '<li class="'.$active_goods.'"><a href="?q=goods"><i class="icon-menu3"></i> <span>Товары</span></a></li>';
          echo '<li class="'.$active_orders.'"><a href="?q=orders"><i class="icon-plus3"></i> <span>Заказы</span></a></li>';
          echo '<li class="'.$active_cats.'"><a href="?q=cats"><i class="icon-stack2"></i> <span>Рубрики</span></a></li>';
          echo '<li class="'.$active_comments.'"><a href="?q=comments"><i class="icon-comment-discussion"></i> <span>Комментарии</span></a></li>';
          echo '<li class="'.$active_about.'"><a href="?q=about"><i class="icon-headphones"></i> <span>Обратная связь <span id="count_not_readed_questions" style="'.$display_count_questions.'" class="label bg-blue-400">'.$count_not_readed_questions.'</span></span></a></li>';
          echo '<li class="'.$active_user_recipes.'"><a href="?q=user_recipes"><i class="icon-menu3"></i> <span>Рецпты пользователей </span></a></li>';


          ?>
          <!-- /main -->

        </ul>
      </div>
    </div>
    <!-- /main navigation -->

  </div>
</div>
<!-- /main sidebar -->
