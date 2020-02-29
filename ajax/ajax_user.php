<?php
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
header('Content-Type: application/json');
session_start();
$LOGGED_USER = $_SESSION['logged_user'];
$time_offset = $_SESSION['time_offset'];
session_write_close();
$main_page = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/user.php');
include_once($root_dir.'/include/classes/user_recipes.php');
include_once($root_dir.'/include/classes/fav_recipes.php');
include_once($root_dir.'/include/classes/noti.php');
$action = $_POST['action'];
$user_id = isset($LOGGED_USER['id']) ? (int)$LOGGED_USER['id'] : 0;
$user_name = isset($LOGGED_USER['name']) ? $LOGGED_USER['name'] : '';

if($user_id == 0) generate_exception($l->not_auth);

$init_user = new User(array(
  'user_id' => $user_id
));

$init_user_recipes = new UserRecipes(array(
  'user_id' => $user_id
));

$init_fav_recipes = new UserFavRecipes(array(
  'user_id' => $user_id
));

$init_noti = new Noti(array(
  'user_id' => $user_id
));


if($action == 'save_profile'){

  $result = $init_user->save_profile($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));

} else if($action == 'save_new_password'){

  $result = $init_user->save_new_password($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'save_recipe'){

  $result = $init_user_recipes->save_recipe($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'save_recipe_changes'){

  $result = $init_user_recipes->save_recipe_changes($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'delete_recipe'){

  $result = $init_user_recipes->delete_recipe($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'get_recipes'){

  $result = $init_user_recipes->get_recipes($_POST);
  $result['result'] = 'true';

  echo json_encode($result);
} else if($action == 'get_fav_recipes'){

  $result = $init_fav_recipes->get_recipes($_POST);
  $result['result'] = 'true';

  echo json_encode($result);
} else if($action == 'delete_fav_recipe'){

  $result = $init_fav_recipes->delete_item($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'save_dir'){

  $result = $init_fav_recipes->save_dir($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'bookmark'){

  $result = $init_fav_recipes->bookmark($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
} else if($action == 'get_noti'){

  $result = $init_noti->get_noti($_POST);
  $result['result'] = 'true';

  echo json_encode($result);
} else if($action == 'set_looked_noti'){

  $init_noti->set_looked_noti($_POST);

  echo json_encode(array(
    'result' => 'true'
  ));
}
?>
