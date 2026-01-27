<?php
session_start();

// Destroy session and redirect to home
session_destroy();
header('Location: /index.php');
exit;
