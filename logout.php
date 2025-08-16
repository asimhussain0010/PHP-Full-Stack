<?php
// Include the config file to ensure session is started


// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with success message
session_start();
$_SESSION['success'] = "You have been successfully logged out";
redirect("login.php");
