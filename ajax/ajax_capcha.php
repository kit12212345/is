<?php
header('Content-Type: application/json');
session_start();
$main_page = true;
$is_capcha_page = true;
$root_dir = $_SERVER['DOCUMENT_ROOT'];
include_once($root_dir.'/include/classes/lang.php');

function generate_exception($string){
  echo json_encode(array('result' => 'false','string' => $string));
  exit;
}

if($_POST['action'] == 'check'){

  $gipadress=$_SERVER['REMOTE_ADDR'];
  $grecaptcha=$_POST['recaptcha'];
  $postdata = http_build_query(
    array(
      'secret' => '6LdBDj4UAAAAAEc-2RiAhFxx2mYDGSJG5PPq4E3w',
      'response' => $grecaptcha,
      'remoteip' => $gipadress
    )
  );
  $opts = array('http' =>
  array(
    'method'  => 'POST',
    'header'  => 'Content-type: application/x-www-form-urlencoded',
    'content' => $postdata
  )
  );



  $gcontents = stream_context_create($opts);

  $gresults = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $gcontents);

  $jsonresults = json_decode($gresults);

  if ($jsonresults->success === false) {
    generate_exception($l->didnt_confirm_robot);
  }

  unset($_SESSION['blocked']);
  unset($_SESSION['inf']);

  echo json_encode(array('result' => 'true'));


}

?>
