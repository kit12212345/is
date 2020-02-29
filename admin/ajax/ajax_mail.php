<?php
header('Content-Type: application/json');
session_start();
$LOGGED_USER=$_SESSION['logged_user'];
session_write_close();
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/db_connect.php');
$var_admin_id = (int)$LOGGED_USER['id'];
$var_create_date = time();

if($_GET['r'] == '24c824e4531115441c2878d82ba1ce9d'){

  $q_user = ("SELECT * FROM `user_recipes` WHERE `status` = 'in_moderation' AND `deleted` = '0'");
  $r_user = mysql_query($q_user) or die(generate_exception(DB_ERROR));
  $n_user = mysql_numrows($r_user); // or die("cant get numrows search_company");
  if($n_user > 0){

    $html = '';

    $to = 's2t8a0s1@yandex.ru';

    $subject = 'Новые рецепты';

    $html .= '<html lang="'.(LANG).'">';
    $html .= '<head>';
    $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    $html .= '<title>Новые рецепты</title>';
    $html .= '</head>';
    $html .= '<body>';
    $html .= '<table style="width: 100%;">';
    $html .= '<tbody>';
    $html .= '<tr>';
    $html .= '<td style="background: #f44336; padding: 20px 10px;">';
    $html .= '<span style="font-size: 26px; color: #fff;">Hluble.com</span>&nbsp;';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td>';
    $html .= ' <p>Есть новые рецепты, нужно сделать модерацию</p> </br>';
    $html .= ' <p>Hluble.com © 2017 - '.date('Y').' '.($l->rights).'</p> </br>';
    $html .= '</td>';
    $html .= '</tr>';
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</body>';
    $html .= '</html>';
    // echo $html;

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: Hluble.com <support@hluble.com>\r\n";
    $headers .= "Reply-To: support@hluble.com\r\n";

    mail($to, $subject, $html, $headers);
    echo 'success';
  }

}



?>
