<?php

session_start();
session_destroy();

echo "You are now logged out. <a href='auth.php?a=index'>Log in.</a>";

// header( 'Location: auth.php?a=index' );
header('Location: /Translation_Dashboard/index.php');
