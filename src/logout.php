<?php
require_once 'includes/session-handler.php';

session_destroy();
header("location: login.php");
?>