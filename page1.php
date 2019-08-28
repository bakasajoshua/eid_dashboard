<?php
echo phpinfo();
exit();
session_start();
// page1.php
//echo session_id();
//echo 'ini 1'.ini_get('session.cookie_domain');



echo 'Welcome to page #133333333333333333333333333777777';

$_SESSION['favcolor'] = 'green';
$_SESSION['animal']   = 'cat';
$_SESSION['time']     = time();
echo 'session id:'. session_id();




// Works if session cookie was accepted
echo '<br /><a href="page2.php">page 2</a>';

// Or maybe pass along the session id, if needed
echo '<br /><a href="page2.php?' . SID . '">page 2</a>';




?>
