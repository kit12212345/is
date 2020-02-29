<?php
if(!class_exists('User')) require_once($root_dir.'/include/classes/user.php');


class Noti extends User{

  function __construct(Array $data = array()){
    parent::__construct($data);
  }

  public function send(Array $data = array()){
    $user_id = isset($data['user_id']) ? (int)$data['user_id'] : $this->user_id;
    $content_ru = $data['content_ru'];
    $content_en = $data['content_en'];
    $content_ru = mysql_real_escape_string($content_ru);
    $content_en = mysql_real_escape_string($content_en);

    $q_query = ("INSERT INTO
    `notifications`
    (
    `user_id`,
    `content_ru`,
    `content_en`,
    `create_date`)
    values(
    '".$user_id."',
    '".$content_ru."',
    '".$content_en."',
    '".$this->create_date."')");
    mysql_query($q_query) or die(generate_exception(DB_ERROR));

    $id = mysql_insert_id();

    return $id;

  }

  public function get_noti(Array $data = array()){
    GLOBAL $l,$time_offset;
    $noti = array();
    $html = '';

    $looked = isset($data['looked']) ? $data['looked'] : 0;

    $q_noti = ("SELECT * FROM `notifications` WHERE `user_id` = '".$this->user_id."'
      AND `looked` = '".$looked."' ORDER BY `id` DESC");
    $r_noti = mysql_query($q_noti) or die(generate_exception(DB_ERROR));
    $n_noti = mysql_numrows($r_noti); // or die("cant get numrows search_company");
    if($n_noti > 0){
      for ($i = 0; $i < $n_noti; $i++) {
        $id = htmlspecialchars(mysql_result($r_noti, $i, "id"));
        $content = htmlspecialchars(mysql_result($r_noti, $i, "content_".LANG));
        $create_date = htmlspecialchars(mysql_result($r_noti, $i, "create_date"));

        $date = date('d.m.Y в H:i',strtotime($create_date) + $time_offset);

        if(LANG == 'en'){
          $date = date('m/d/Y g:i A',strtotime($create_date) + $time_offset);
        }

        array_push($noti,array(
          'id' => $id,
          'content' => $content
        ));

        $html .= '<div class="noti_item">';
        $html .= '<div class="float_l noti_title">&nbsp; - '.$content.'</div>';
        if($looked == 0){
          $html .= '<div class="float_r look_noti">';
          $html .= '<a onclick="noti.set_looked_noti('.$id.');" class="cursor_p hl_color td_underline">'.$l->got_noti.'</a>';
          $html .= '</div>';
        }
        $html .= '<div class="clear_nm"></div>';
        $html .= '<div class="noti_date">'.$date.'</div>';
        $html .= '<div class="clear"></div>';
        $html .= '</div>';


      }
    } else $html = '<div style="margin-top: 10px;">'.$l->no_noti.'</div>';

    return array(
      'noti' => $noti,
      'html' => $html
    );

  }

  public function set_looked_noti(Array $data = array()){
    $item_id = (int)$data['item_id'];

    $q_user_info = ("UPDATE `notifications` SET `looked` = '1' WHERE `id` = '".$item_id."' AND `user_id` = '".$this->user_id."'");
    mysql_query($q_user_info) or die(generate_exception(DB_ERROR));

    return true;
  }


}


?>
