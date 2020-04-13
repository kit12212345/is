<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
if(!class_exists("User")) include_once($root_dir.'/include/classes/user.php');
if(!class_exists("Catalog")) include_once($root_dir.'/include/classes/catalog.php');
if(!class_exists('ProductsOptions')) include($root_dir.'/admin/modules/options/products_options.php');

$page = isset($_GET['p']) ? $_GET['p'] : 'profile';

$init_user = new User(array(
  'user_id' => $user_id
));

$user_info = $init_user->get_user_info();
if($user_info === false) {
  exit('Пользователь не найден'); // fixme
}

$user_first_name = $user_info['first_name'];
$user_last_name = $user_info['last_name'];
$birthday = $user_info['birthday'];
$sex = $user_info['sex'];

?>
<link rel="stylesheet" href="/css/user.css?ver=<?php echo rand(1,100000000000); ?>">
<div class="row">
  <div class="col-md-3">
    <?php
    include($root_dir.'/include/user/menu.php');
    ?>
  </div>
  <div class="col-md-9">
    <?php
    switch ($page) {
      case 'main':
        include($root_dir.'/include/user/main.php');
        break;
      case 'edit_profile':
      include($root_dir.'/include/user/edit_profile.php');
        break;
      case 'privacy_settings':
        include($root_dir.'/include/user/privacy_settings.php');
        break;
      default:
        include($root_dir.'/include/user/main.php');
        break;
    }
    ?>
  </div>
</div>

<script src="/js/user.js?ver=<?php echo rand(1,100000000000); ?>" charset="utf-8"></script>
<script src="/js/mask.js" charset="utf-8"></script>
<?php
include($root_dir.'/include/blocks/footer.php');
?>
