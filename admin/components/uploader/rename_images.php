<?php
exit;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');

$q_check = sprintf("SELECT * FROM `posts`");
$r_check = mysql_query($q_check) or die("cant execute query");
$n_check = mysql_numrows($r_check); // or die("cant get numrows query");
if($n_check > 0){
  for ($i = 0; $i < $n_check; $i++) {
    $post_id = htmlspecialchars(mysql_result($r_check, $i, "posts.id"));
    $post_image = htmlspecialchars(mysql_result($r_check, $i, "posts.main_image"));
    $post_alias = htmlspecialchars(mysql_result($r_check, $i, "posts.alias"));





    $new_post_image = $post_alias.'.jpg';

    rename($root_dir.'/images/post_images/'.$post_image,$root_dir.'/images/post_images/'.$new_post_image);
    rename($root_dir.'/images/post_images/thumbnail_1024/'.$post_image,$root_dir.'/images/post_images/thumbnail_1024/'.$new_post_image);
    rename($root_dir.'/images/post_images/thumbnail_140/'.$post_image,$root_dir.'/images/post_images/thumbnail_140/'.$new_post_image);
    rename($root_dir.'/images/post_images/thumbnail_480/'.$post_image,$root_dir.'/images/post_images/thumbnail_480/'.$new_post_image);


    $q_update_item_alias = sprintf("UPDATE `posts` SET `posts`.`main_image`='".$new_post_image."'"."
    WHERE `posts`.`id`='".$post_id."'");
    mysql_query($q_update_item_alias) or die(generate_exception($db_error));


  }


}
?>
