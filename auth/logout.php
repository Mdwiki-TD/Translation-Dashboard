<?php

session_start();
session_destroy();

// echo "You are now logged out. <a href='auth.php?a=index'>Log in.</a>";

// return to the previous page
$return_to = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/Translation_Dashboard/index.php';

// echo json_encode($_SERVER);
header("Location: $return_to");
