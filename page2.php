<?php
session_start();
//
// page2.php
//echo 'ini'.ini_get('session.cookie_domain');


echo 'Welcome to page #2<br />';

echo 'fav color:'.$_SESSION['favcolor'] .'</br>'; // green
echo 'animal:'.$_SESSION['animal'].'</br>';   // cat
echo 'time:'. date('Y m d H:i:s', $_SESSION['time']).'</br>';
echo 'session id:'. session_id();
// You may want to use SID here, like we did in page1.php
echo '<br /><a href="page1.php">page 1</a>';
exit();
?>
