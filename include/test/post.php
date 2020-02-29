<?php
// exit;
$hide_mertica = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
include_once($root_dir.'/db_connect.php');
include_once($root_dir.'/components/alias/alias.php');
if(!class_exists("Lang")) include_once($root_dir.'/include/classes/lang.php');
// $is_mobile_device = check_mobile_device();

// if(!isset($_GET['rd'])){
//   $key = $_GET['post'];
//   $key1 = Alias::get_alias_name($key,'post',LANG);
//
//   if(empty($key1)){
//     include_once($root_dir.'/error_404.php');
//     exit;
//   }
//   $link = $key1;
//   Header('Status: 301 Moved Permanently');
//   header("Location: ".$scheme."://hluble.com/".$link);
// }

include_once($root_dir.'/blocks/header.php');
include_once($root_dir.'/include/classes/fav_recipes.php');
$is_mobile_device = true;

echo '<div class="display_none" id="post_info" data-info=\'{"post_id" : "'.$post_id.'"}\'></div>';


$init_fav_recipes = new UserFavRecipes(array(
  'user_id' => $user_id
));

$bookmark_html = '';
$in_bookmark = $init_fav_recipes->exist_in_bookmark($post_id);

$display_bmk_delete = $in_bookmark === true ? 'display: block;' : 'display: none;';
$display_bmk_add = $in_bookmark === false ? 'display: block;' : 'display: none;';

$bookmark_html .= '<span id="remove_from_bkm" class="cursor_p g_color" onclick="posts.bookmark('.$post_id.',\'delete\')" style="color: #129c12; '.$display_bmk_delete.'">';
$bookmark_html .= '<i class="fa fa-bookmark" style="color: #129c12;" aria-hidden="true"></i>&nbsp;';
$bookmark_html .= $l->in_bookmark;
$bookmark_html .= '</span>';

$bookmark_event = $user_id > 0 ? "posts.bookmark(".$post_id.",'add')" : "posts.temp_bookmark(".$post_id.");";

$bookmark_html .= '<span id="add_in_bmk" class="cursor_p hl_color" onclick="'.$bookmark_event.'" style="'.$display_bmk_add.'">';
$bookmark_html .= '<i class="fa fa-bookmark hl_color" aria-hidden="true"></i>&nbsp;';
$bookmark_html .= $l->bookmark_page;
$bookmark_html .= '</span>';

if($user_id == 20 || $user_id == 1 || $user_id == 22){
  include_once($root_dir.'/blocks/left_panel.php');
}
?>
<script src="/js/scripts/comments.js?ver=5"></script>

<div class="float_l c_left_grid" id="center_grid">

  <meta itemprop="datePublished" content="<?php echo date('Y-m-d',$post_create_date + $time_offset); ?>">
  <meta itemprop="dateModified" content="<?php echo date('Y-m-d',$post_create_date + $time_offset); ?>">

  <div class="box post_content def_lists">
    <div class="post_title">
      <h1 itemprop="name" class="i_block v_align_middle"><?php echo $post_title; ?></h1>
      <div class="float_r bookmark_page">
        <?php echo $bookmark_html; ?>
      </div>
      <div class="clear"></div>
    </div>
    <div class="post_cn">
      <div class="post_tags">
        <div class="i_block v_align_middle tag_item m_tg">
          <i class="fa fa-hashtag" aria-hidden="true"></i> <?php echo $l->tags ?>:
        </div>
        <?php
        for ($i = 0; $i < count($post_cats); $i++) {
          $p_cat_id = $post_cats[$i]['id'];
          $p_cat_name = $post_cats[$i]['name'];
          $p_cat_alias = $post_cats[$i]['alias'];

          $cat_link = !empty($p_cat_alias) ? '/'.$p_cat_alias : '/posts.php?cat='.$p_cat_id;
          $mic_cat = $i == count($post_cats) - 1 ? 'itemprop="recipeCategory"' : '';

          echo '<a href="'.$cat_link.'" rel="nofollow">';
          echo '<div class="i_block v_align_middle tag_item" '.$mic_cat.'>'.$p_cat_name.'</div>';
          echo '</a>';

        }

        ?>
      </div>
      <div class="cont_post">

        <?php

        if($count_post_ingredients > 0){

          echo '<div class="p_ings">';
            echo '<div class="p_image float_r" itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
              echo '<img itemprop="image url" class="i_block" src="/images/post_images/thumbnail_480/'.$post_image.'" title="'.$post_title.'" alt="'.$post_title.'" />';
            echo '</div>';

            echo '<h3>'.($l->ingredients).'</h3>';
            echo '<div>';
            for ($i = 0; $i < $count_post_ingredients; $i++) {
              $ing_name = $post_ingredients[$i];
              echo '<div itemprop="recipeIngredient" class="p_ing_item">'.$ing_name.'</div>';
            }
            echo '</div>';
          echo '</div>';

        }

        $post_date = date('d.m.Y H:i',$post_create_date + $time_offset);

        if(LANG == 'en'){
          $post_date = date('m/d/Y g:i A',$post_create_date + $time_offset);
        }


        echo '<div class="">';
          echo '<h3>'.($l->cooking).'</h3>';
        echo '</div>';
        echo '<div>';
        if($count_post_ingredients == 0){
          echo '<div class="p_image float_r">';
            echo '<img class="i_block" src="/images/post_images/thumbnail_480/'.$post_image.'" title="'.$post_title.'" alt="'.$post_title.'" />';
          echo '</div>';


        }
        echo '<div itemprop="recipeInstructions">';
        echo $post_content;
        echo '<div class="clear"></div>';


        echo '</div>';
        echo '</div>';
        echo '<div class="text_right why_pub">';
          echo '<div class="float_l post_pub_date"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;&nbsp;'.($l->views).': '.$post_count_view.'</div>';
          echo '<div class="i_block post_pub_date" itemprop="author" itemscope itemtype="http://schema.org/Person"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;&nbsp;'.($l->author).':<span itemprop="name">'.(empty($post_user_name) ? 'Admin' : trim($post_user_name)).'</span></div>';
          echo '<div class="i_block post_pub_date"><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;&nbsp;'.($l->published).': '.$post_date.'</div>';
        echo '</div>';

        // echo '<noindex>';
          echo '<div rel="nofollow" class="text_right no_wrap social_icons why_pub">';
            echo '<div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter" data-counter=""></div>';
          echo '</div>';
        // echo '</noindex>';

         if($post_allow_add_review === true){

           echo '<div class="relative ask_post" id="ask_post">';
             echo '<div class="visible_ask_post" id="visible_ask_post">';
               echo '<div class="i_block v_align_middle">';
                 echo '<h4 id="text_review_ask_post">'.($l->helpful).'</h4>';
               echo '</div>';
               echo '<div class="i_block v_align_middle btns_like_post" id="btns_like_post">';
                 echo '<div class="cursor_p float_l p_like_green btn_like_post" onclick="posts.add_review(this);" data-review="yes" data-postid="'.$post_id.'">';
                   echo '<span class="w_color">'.($l->yes).'</span>&nbsp;';
                   echo '<i class="fa w_color fa-thumbs-up" aria-hidden="true"></i>';
                 echo '</div>';
                 echo '<div class="cursor_p float_l p_like_red btn_like_post" onclick="posts.add_review(this);" data-review="no" data-postid="'.$post_id.'">';
                   echo '<span class="w_color">'.($l->no).'</span>&nbsp;';
                   echo '<i class="fa w_color fa-thumbs-down" aria-hidden="true"></i>';
                 echo '</div>';
                 echo '<div class="clear"></div>';
               echo '</div>';
             echo '</div>';

           echo '</div>';

         }
         ?>

        </div>
      </div>
    </div>

    <?php
    if($count_similar_posts == 4){
      echo '<div class="full_w box post_content other_posts">';

        echo '<div class="op_title">';
          echo '<h3>'.($l->similar_recipes).'</h3>';
        echo '</div>';

        echo '<div class="">';

          for ($s = 0; $s < $count_similar_posts; $s++) {
            $si_post_id = $similar_posts[$s]['id'];
            $si_post_title = $similar_posts[$s]['title'];
            $si_post_image = $similar_posts[$s]['image'];
            $si_post_content = $similar_posts[$s]['content'];
            $si_post_alias = $similar_posts[$s]['alias'];

            $si_post_content = strip_tags($si_post_content);

            $si_post_link = !empty($si_post_alias) ? '/'.$si_post_alias : '/post.php?post='.$si_post_id;

            $si_post_content = remove_html($si_post_content);

            if(strlen($si_post_content) > 120) {
              $pos = strpos($si_post_content, ' ', 120);
              if ($pos>0) {
                $si_post_content = substr($si_post_content, 0, $pos);
                $si_post_content.= "...";
              }
            };

            echo '<div class="float_l op_item">';
              echo '<a href="'.$si_post_link.'" rel="nofollow">';
                echo '<div class="opi_image">';
                  echo '<img class="cover_img sc_load_image" src="/images/logo.png" data-image="/images/post_images/thumbnail_480/'.$si_post_image.'" title="'.$si_post_title.'" alt="'.$si_post_title.'" />';
                echo '</div>';
                echo '<div class="opi_title no_wrap">';
                  echo '<h4>'.$si_post_title.'</h4>';
                echo '</div>';
                echo '<div class="opi_desc">'.$si_post_content.'</div>';
              echo '</a>';
            echo '</div>';
          }


          echo '<div class="clear"></div>';
        echo '</div>';

      echo '</div>';
    }
    ?>

    <div class="full_w box post_content other_posts">

      <div class="comments">

        <h3><?php echo $l->comments ?> <span id="count_post_comments">(<?php echo $post_count_comments; ?>)</span></h3>
        <div class="">
          <div class="it_add_comment">
            <label><?php echo $l->name ?></label>
            <input class="full_w" id="c_user_name" placeholder="<?php echo $l->enter_name ?>" type="text">
          </div>
          <div class="it_add_comment">
            <label><?php echo $l->email ?></label>
            <input type="text" id="c_user_email" placeholder="<?php echo $l->enter_email ?>" class="full_w">
          </div>
          <div class="it_add_comment">
            <label><?php echo $l->comment_text ?></label>
            <textarea placeholder="<?php echo $l->comment_text ?>" onkeydown="if(event.keyCode == 13) return comments.add();" id="c_content" class="full_w" rows="8" cols="80"></textarea>
          </div>
          <div class="it_add_comment">
            <div class="g-recaptcha" data-sitekey="6LdBDj4UAAAAAHCR8E3b-NqfO7rgHnwN4Kc_tx09"></div>
          </div>
          <div class="text_right">
            <div class="btn cursor_p w_color i_block btn_top_login" onclick="comments.add();">
              <i class="w_color fa fa-send-o" aria-hidden="true"></i>&nbsp;&nbsp; <?php echo $l->send_comment ?>
            </div>
          </div>
        </div>

        <div class="comments_item" id="comments_item">
          <?php
          if($count_post_comments > 0){
            for ($i = 0; $i < $count_post_comments; $i++) {
              $comment_id = $post_comments[$i]['id'];
              $comment_user_name = $post_comments[$i]['user_name'];
              $comment_content = $post_comments[$i]['content'];
              $comment_create_date = $post_comments[$i]['create_date'];

              $comment_create_date = strtotime($comment_create_date);

              $comment_date = date('d.m.Y H:i',$comment_create_date + $time_offset);

              if(LANG == 'en'){
                $comment_date = date('m/d/Y g:i A',$comment_create_date + $time_offset);
              }


              echo '<div class="comment_item">';
                echo '<div class="float_l ci_image">';
                  echo '<img src="/images/noavatar.png" alt="'.$comment_user_name.'" title="'.$comment_user_name.'" />';
                echo '</div>';
                echo '<div class="ci_cont">';
                  echo '<div class="ci_user_name">';
                    echo '<strong>'.$comment_user_name.'</strong>';
                  echo '</div>';
                  echo '<div class="ci_desc">'.$comment_content.'</div>';
                  echo '<div class="ci_date text_right">';
                    echo '<small>'.($l->sent).' ';
                    echo $comment_date;
                    echo '</small>';
                  echo '</div>';
                echo '</div>';
              echo '</div>';

            }
          } else{
            echo '<div class="not_comments">'.($l->no_comments).'</div>';
          }
          ?>


        </div>
      </div>

    </div>


  </div>
  <script type="text/javascript">
  var init_sticky_block = function(data){
    const self = this;
    this.blocks = {};
    this.top_offset = 15;
    this.bottom_offset = 15;
    this.init = function(data){
      for (var _i = 0; _i < data.blocks.length; _i++) {
        this.blocks[_i] = {
          element: data.blocks[_i],
          b: null,
          k: null,
          z: 0
        };
      }
      window.addEventListener('scroll', this._scroll, false);
      document.body.addEventListener('scroll', this._scroll, false);
    };
    this._scroll = function(){
      for (var _i in self.blocks) {
        var a = self.blocks[_i].element;
        var t_o = gId('header') ? gId('header').offsetHeight + self.top_offset : self.top_offset;
        var b = self.blocks[_i].b, K = self.blocks[_i].k, Z = self.blocks[_i].z, P = t_o, N = self.bottom_offset;
        var Ra = a.getBoundingClientRect(),
            R1bottom = document.querySelector('#center_grid').getBoundingClientRect().bottom;
        if (Ra.bottom < R1bottom) {
          if (b == null) {
            var Sa = getComputedStyle(a, ''), s = '';
            for (var i = 0; i < Sa.length; i++) {
              if (Sa[i].indexOf('overflow') == 0 || Sa[i].indexOf('padding') == 0 || Sa[i].indexOf('border') == 0 || Sa[i].indexOf('outline') == 0 || Sa[i].indexOf('box-shadow') == 0 || Sa[i].indexOf('background') == 0) {
                s += Sa[i] + ': ' +Sa.getPropertyValue(Sa[i]) + '; '
              }
            }
            b = document.createElement('div');
            b.className = "stop";
            b.style.cssText = s + ' box-sizing: border-box; width: ' + a.offsetWidth + 'px;';
            a.insertBefore(b, a.firstChild);
            var l = a.childNodes.length;
            for (var i = 1; i < l; i++) {
              b.appendChild(a.childNodes[1]);
            }
            a.style.height = b.getBoundingClientRect().height + 'px';
            a.style.padding = '0';
            a.style.border = '0';
          }
          var Rb = b.getBoundingClientRect(),
              Rh = Ra.top + Rb.height,
              W = document.documentElement.clientHeight,
              R1 = Math.round(Rh - R1bottom),
              R2 = Math.round(Rh - W);

          if (Rb.height > W) {
            if (Ra.top < K) {  // скролл вниз
              if (R2 + N > R1) {  // не дойти до низа
                if (Rb.bottom - W + N <= 0) {  // подцепиться
                  b.className = 'sticky';
                  b.style.top = W - Rb.height - N + 'px';
                  Z = N + Ra.top + Rb.height - W;
                } else {
                  b.className = 'stop';
                  b.style.top = - Z + 'px';
                }
              } else {
                b.className = 'stop';
                b.style.top = - R1 +'px';
                Z = R1;
              }
            } else {  // скролл вверх

              if (Ra.top - P < 0) {  // не дойти до верха
                if (Rb.top - P >= 0) {  // подцепиться
                  b.className = 'sticky';
                  b.style.top = P + 'px';
                  Z = Ra.top - P;
                } else {
                  b.className = 'stop';
                  b.style.top = - Z + 'px';
                }
              } else {
                b.className = '';
                b.style.top = '';
              }
            }
            K = Ra.top;
          } else {
            if ((Ra.top - P) <= 0) {
              if ((Ra.top - P) <= R1) {
                b.className = 'stop';
                b.style.top = - R1 +'px';
              } else {
                b.className = 'sticky';
                b.style.top = P + 'px';
              }
            } else {
              b.className = '';
              b.style.top = '';
            }
          }
          // window.addEventListener('resize', function() {
          //   a.children[0].style.width = getComputedStyle(a, '').width
          // }, false);
        }
        self.blocks[_i].k = K;
        self.blocks[_i].z = Z;
        self.blocks[_i].b = b;
      }
    };
    return this.init(data);
  };
  // window.onload = function(){
  //   if(gId('desktop_left_panel') && gId('desktop_right_panel')){
  //     new init_sticky_block({
  //       blocks: [gId('desktop_left_panel'),gId('desktop_right_panel')]
  //     });
  //   }
  // };
  </script>

  <style>
  .sticky {
    position: fixed;
  }
  .stop {
    position: relative;
  }
  </style>

<?php
include_once($root_dir.'/blocks/footer.php');
?>
