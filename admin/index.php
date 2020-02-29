<?php
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/admin/blocks/header.php');
?>
<!-- Main content -->
<div class="content-wrapper">
  <!-- Content area -->
  <div class="content">
  <?php
  if($admin_id == 0){
    include_once($root_dir.'/admin/login.php');
  } else{
    include_once($root_dir.'/admin/pages/index.php');
  }
  ?>
  </div>
  <!-- END Content area -->
</div>
<!-- END Main content -->
<?php
include_once($root_dir.'/admin/blocks/footer.php');
?>
