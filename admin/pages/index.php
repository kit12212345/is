<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];

$page = $_GET['q'];

switch ($page) {
  case 'goods':
    include($root_dir.'/admin/pages/goods.php');
    break;
  case 'product':
    include($root_dir.'/admin/pages/product.php');
    break;
  case 'cat':
    include($root_dir.'/admin/pages/cat.php');
    break;
  case 'add_recipe':
    include($root_dir.'/admin/pages/add_recipe.php');
    break;
  case 'cats':
    include($root_dir.'/admin/pages/categories.php');
    break;
  case 'about':
    include($root_dir.'/admin/pages/about.php');
    break;
  case 'edit_cat':
    include($root_dir.'/admin/pages/edit_cat.php');
    break;
  case 'add_post':
    include($root_dir.'/admin/pages/add_post.php');
    break;
  case 'posts':
    include($root_dir.'/admin/pages/posts.php');
    break;
  case 'comments':
    include($root_dir.'/admin/pages/comments.php');
    break;
  case 'edit_comment':
    include($root_dir.'/admin/pages/edit_comment.php');
    break;
  default:
    include($root_dir.'/admin/pages/goods.php');
    break;
}
?>
