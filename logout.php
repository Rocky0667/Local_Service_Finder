<?php
// 1. Initialize the session state to gain access to current cookies
session_start();

// 2. Clear all active session variables completely
session_unset();

// 3. Destroy the tracking session payload on the server
session_destroy();

// FIXED: Redirect the user directly to the login gateway instead of the homepage
header("Location: login.html");
exit;
?>