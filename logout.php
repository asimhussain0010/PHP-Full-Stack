<?php
// Include the config file to ensure session is started
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with success message
session_start();
redirect("login.php");
