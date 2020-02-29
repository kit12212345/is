<?php
  session_start();
  unset($_SESSION['logged_user']);
  // session_unset();
  // session_destroy();
  header("Location: /");
  die();
?>
