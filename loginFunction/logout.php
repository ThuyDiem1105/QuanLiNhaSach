<?php
session_start();
session_unset(); // optional: remove all session variables
session_destroy(); // fully destroy the session

// Redirect to login page
header("Location: login.php");
exit;
