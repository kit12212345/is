<?php
// exit;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
require_once($root_dir.'/admin/config.php');
require_once($root_dir.'/db_connect.php');

$now_date = time();

$now_xml_date = gmdate('Y-m-d', $now_date);

$posts_table_name = AdmConfig::$posts_table_name;
$categories_table_name = AdmConfig::$categories_table_name;

$begin_html = '';
$host = 'https://hluble.com/';


$begin_html .= '<?xml version="1.0" encoding="UTF-8"?>';
$begin_html .= '<urlset xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">';

// $begin_html .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
 $begin_html .= '<url>';
  $begin_html .= '<loc>'.$host.'</loc>';
  $begin_html .= '<lastmod>'.$now_xml_date.'</lastmod>';
  $begin_html .= '<changefreq>always</changefreq>';
  $begin_html .= '<priority>0.5</priority>';
 $begin_html .= '</url>';


 $q_posts = ("SELECT * FROM `".$posts_table_name."` WHERE `".$posts_table_name."`.`status` <> 'deleted'");
 $r_posts = mysql_query($q_posts) or die("cant execute posts");
 $n_posts = mysql_num_rows($r_posts);
 if($n_posts > 0){
   for ($i = 0; $i < $n_posts; $i++) {
     $alias_ru = htmlspecialchars(mysql_result($r_posts, $i, $posts_table_name.".alias_ru"));
     $alias_en = htmlspecialchars(mysql_result($r_posts, $i, $posts_table_name.".alias_en"));
     $date_gmt = htmlspecialchars(mysql_result($r_posts, $i, $posts_table_name.".post_date_gmt"));
     $date_update_gmt = htmlspecialchars(mysql_result($r_posts, $i, $posts_table_name.".post_date_update_gmt"));

     $date_update_gmt = $date_update_gmt{0} == '0' && $date_update_gmt{1} == '0'
     ? $date_gmt : $date_update_gmt;

     $date_update_gmt = strtotime($date_update_gmt);

     $date_post_xml = date('Y-m-d', $date_update_gmt);

     $begin_html .= '<url>';
     $begin_html .= '<loc>'.$host.$alias_ru.'</loc>';
     $begin_html .= '<lastmod>'.$date_post_xml.'</lastmod>';
     $begin_html .= '<changefreq>always</changefreq>';
     $begin_html .= '<priority>1.0</priority>';
     $begin_html .= '</url>';

     if(!empty($alias_en)){
       $begin_html .= '<url>';
       $begin_html .= '<loc>'.$host.$alias_en.'</loc>';
       $begin_html .= '<lastmod>'.$date_post_xml.'</lastmod>';
       $begin_html .= '<changefreq>always</changefreq>';
       $begin_html .= '<priority>1.0</priority>';
       $begin_html .= '</url>';
     }

   }
 }


 $q_posts = ("SELECT * FROM `".$categories_table_name."`  ");
 $r_posts = mysql_query($q_posts) or die("cant execute posts");
 $n_posts = mysql_num_rows($r_posts);
 if($n_posts > 0){
   for ($i = 0; $i < $n_posts; $i++) {
     $alias_ru = htmlspecialchars(mysql_result($r_posts, $i, $categories_table_name.".alias_ru"));
     $alias_en = htmlspecialchars(mysql_result($r_posts, $i, $categories_table_name.".alias_en"));
     $date_update_gmt = htmlspecialchars(mysql_result($r_posts, $i, $categories_table_name.".create_date"));

     $date_post_xml = date('Y-m-d', $date_update_gmt);


     $begin_html .= '<url>';
     $begin_html .= '<loc>'.$host.$alias_ru.'</loc>';
     $begin_html .= '<lastmod>'.$date_post_xml.'</lastmod>';
     $begin_html .= '<changefreq>always</changefreq>';
     $begin_html .= '<priority>1.0</priority>';
     $begin_html .= '</url>';

   }
 }


$begin_html .= '</urlset>';

$fd = fopen($root_dir."/sitemap.xml", 'w') or die("не удалось создать файл");
$str = $begin_html;
fwrite($fd, $str);
fclose($fd);



?>
