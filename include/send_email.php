<?php
$html = '';

$to = 'alykopecki@gmail.com';

$subject = 'Heya heya hey';

$html .= '<html lang="en">';
$html .= '<head>';
$html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
$html .= '<title>'.$subject.'</title>';
$html .= '</head>';
$html .= '<body>';
$html .= '<table style="width: 100%;">';
$html .= '<tbody>';
$html .= '<tr>';
$html .= '<td>';
$html .= ' <p>If you have received this letter it means I\'m on my way to the airport ;)</br>';
$html .= ' I\'m supposed to be there in a few hours and I\'m certainly sleeping now and my ass hurts from these shitty seats, I hope it\'s not cramped.</p>';
$html .= ' <p>I will be back to you soon and we will be together, you know i love you so much wibby 💓💓💓💓💓💓💓💓💓💓💓💓💓💓💕💕💕💕💕💕💕💕💕💕💕💕💕💕💖💖💖💖💖💖💖💖💖</p> ';
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

?>
