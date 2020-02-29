<?php
// exit;
define(LANG,'en');
$root_dir = $_SERVER['DOCUMENT_ROOT'];
require_once($root_dir.'/admin/config.php');
require_once($root_dir.'/db_connect.php');
include_once($root_dir.'/include/classes/posts.php');

$now_date = time();

$now_xml_date = gmdate('Y-m-d', $now_date);

$posts_table_name = AdmConfig::$posts_table_name;
$categories_table_name = AdmConfig::$categories_table_name;

$begin_html = '';
$host = 'https://hluble.com/';


$begin_html .= '<?xml version="1.0" encoding="UTF-8"?>';
$begin_html .= '<rss
    xmlns:yandex="http://news.yandex.ru"
    xmlns:media="http://search.yahoo.com/mrss/"
    xmlns:turbo="http://turbo.yandex.ru"
    version="2.0">
';

  $begin_html .= '<channel>';
  	$begin_html .= '<title>Hluble.com - the website of recipes</title>';
  	$begin_html .= '<link>https://hluble.com</link>';
  	$begin_html .= '<description>The website of recipes</description>';
  	$begin_html .= '<language>en-EN</language>';
  	$begin_html .= '<generator>https://hluble.com</generator>';

// $begin_html .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';



 $posts_init = new Posts();
 $q_posts = ("SELECT * FROM `".$posts_table_name."` WHERE `".$posts_table_name."`.`status` <> 'deleted'");
 $r_posts = mysql_query($q_posts) or die("cant execute posts");
 $n_posts = mysql_num_rows($r_posts);
 if($n_posts > 0){
   for ($i = 0; $i < $n_posts; $i++) {
     $post_id = htmlspecialchars(mysql_result($r_posts, $i, $posts_table_name.".id"));

     $posts_init->add_count_view($post_id);
     $post_info = $posts_init->get_post_info($post_id);
     $similar_posts = $posts_init->get_similar_posts($post_id);
     $count_similar_posts = count($similar_posts);
     $post_title = $post_info['title'];
     $post_ingredients = $post_info['ingredients'];
     $page_keywords = $post_info['keywords'];
     $page_description = $post_info['description'];
     $post_content = $post_info['content'];
     $post_count_comments = (int)$post_info['count_comments'];
     $post_count_view = $post_info['count_view'];
     $post_image = $post_info['image'];
     $post_alias = $post_info['alias'];
     $post_cats = $post_info['cats'];
     $post_create_date = $post_info['create_date'];
     $post_allow_add_review = $post_info['allow_add_review'];

     $count_post_ingredients = count($post_ingredients);

     $date_update_gmt = $post_create_date;
     $date_update_gmt = $date_update_gmt{0} == '0' && $date_update_gmt{1} == '0'
     ? $date_gmt : $date_update_gmt;

     $date_update_gmt = strtotime($date_update_gmt);

     $date_post_xml = date('D, d M Y H:m:i +0000', $date_update_gmt);

     $begin_html .= '<item turbo="true">';
   		$begin_html .= '<title>'.$post_title.'</title>';
   		$begin_html .= '<link>'.$host.$post_alias.'</link>';
   		$begin_html .= '<pubDate>'.$date_post_xml.'</pubDate>';
      $begin_html .= '<category>'.$post_cats[0]['name'].'</category>';
   		$begin_html .= '<turbo:content><![CDATA[';

      $post_create_date = strtotime($post_create_date);
      ob_start();
      echo '<div class="box post_content">';
        echo '<div class="post_cn">';
          echo '<div class="post_tags">';
            echo '<div class="i_block v_align_middle tag_item m_tg">';
              echo '<i class="fa fa-hashtag" aria-hidden="true"></i> Теги:';
            echo '</div>';

            for ($r = 0; $r < count($post_cats); $r++) {
              $p_cat_id = $post_cats[$r]['id'];
              $p_cat_name = $post_cats[$r]['name'];
              $p_cat_alias = $post_cats[$r]['alias'];

              $cat_link = !empty($p_cat_alias) ? '/'.$p_cat_alias : '/posts.php?cat='.$p_cat_id;
              $zp = $r > 0 ? ', ' : '';

              echo $zp.'<a style="margin-left: 5px;" href="'.$cat_link.'">';
              echo '<div class="i_block v_align_middle tag_item">'.$p_cat_name.'</div>';
              echo '</a>';
            }

          echo '</div>';
          echo '<div class="cont_post">';


            if($count_post_ingredients > 0){

              echo '<div class="p_ings">';
                echo '<div class="p_image float_r">';
                  echo '<img class="i_block" src="/images/post_images/thumbnail_480/'.$post_image.'" title="'.$post_title.'" alt="'.$post_title.'" />';
                echo '</div>';
                echo '<h3>Ingredients</h3>';
                echo '<p>';
                echo '<ul>';
                for ($in = 0; $in < $count_post_ingredients; $in++) {
                  $ing_name = $post_ingredients[$in];
                  echo '<li class="p_ing_item">'.$ing_name.'</li>';
                }
                echo '</ul>';
                echo '</p>';
              echo '</div>';

            }


            echo '<div class="">';
              echo '<h3>Cooking</h3>';
            echo '</div>';
            echo '<p>';
            if($count_post_ingredients == 0){
              echo '<div class="p_image float_r">';
                echo '<img class="i_block" src="/images/post_images/thumbnail_480/'.$post_image.'" title="'.$post_title.'" alt="'.$post_title.'" />';
              echo '</div>';
            }
            echo $post_content;
            echo '</p>';

            echo '</div>';
          echo '</div>';
        echo '</div>';

        $content = ob_get_contents();
        $begin_html .= $content;
        ob_end_clean();

      $begin_html .= ']]></turbo:content>';
      $begin_html .= '<yandex:related>';


      if($count_similar_posts == 4){
        for ($s = 0; $s < $count_similar_posts; $s++) {
          $si_post_id = $similar_posts[$s]['id'];
          $si_post_title = $similar_posts[$s]['title'];
          $si_post_image = $similar_posts[$s]['image'];
          $si_post_alias = $similar_posts[$s]['alias'];

          $si_post_link = !empty($si_post_alias) ? '/'.$si_post_alias : '/post.php?post='.$si_post_id;


          $begin_html .= '<link url="'.$si_post_link.'" img="/images/post_images/thumbnail_480/'.$si_post_image.'">'.$si_post_title.'</link>';
        }

      }

      $begin_html .= '</yandex:related>';
   		$begin_html .= '</item>';

     // $begin_html .= '<url>';
     // $begin_html .= '<loc>'.$host.$alias.'</loc>';
     // $begin_html .= '<lastmod>'.$date_post_xml.'</lastmod>';
     // $begin_html .= '<changefreq>always</changefreq>';
     // $begin_html .= '<priority>1.0</priority>';
     // $begin_html .= '</url>';

   }
 }

$begin_html .= '</channel>';
$begin_html .= '</rss>';

$fd = fopen($root_dir."/rss_en.rss", 'w') or die("не удалось создать файл");
$str = $begin_html;
fwrite($fd, $str);
fclose($fd);



?>
