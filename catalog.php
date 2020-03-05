<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include($root_dir.'/include/blocks/header.php');
include($root_dir.'/include/classes/catalog.php');

$init_catlog = new Catalog();

$products_info = $init_catlog->get_products();
$products = $products_info['products'];
foreach ($products as $key => $value){
echo '<div class="">';
echo '<a href="/product.php?id='.$value['id'].'">'.$value['name'].'</a>';
echo '</div>';
}
?>



<?php
include($root_dir.'/include/blocks/footer.php');
?>
