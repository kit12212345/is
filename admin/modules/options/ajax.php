<?php
session_start();
header('Content-Type: application/json');
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');

require_once($root_dir.'/admin/modules/options/products_options.php');


$init = new ProductsOptions(array(
  'org_id' => $var_org_id,
  'app_id' => $var_app_id,
  'fs_id' => $var_fs_id
));


$action = $_POST['action'];

if($action == 'save_propert'){

  $result = $init->save('propert',$_POST);

  echo json_encode(array(
    'result' => 'true',
    'id' => $result['id'],
    'name' => $result['name']
  ));

} else if($action == 'delete_propert'){
  $result = $init->delete('propert',$_POST);
  echo json_encode(array(
    'result' => 'true',
    'parent_id' => $result['parent_id']
  ));
} else if($action == 'save_options'){
  $result = $init->save('options',$_POST);
  $result['result'] = 'true';
  echo json_encode($result);
} else if($_POST['action'] == 'delete_option'){
  $result = $init->delete('option',$_POST);
  $result['result'] = 'true';
  echo json_encode($result);
} else if($_POST['action'] == 'save_product_value'){
  $result = $init->save_product_value($_POST);
  $result['result'] = 'true';
  echo json_encode($result);
} else if($action == 'update_properts_positions'){
  $result = $init->update_properts_positions($_POST);
  $result['result'] = 'true';
  echo json_encode($result);
}


?>
