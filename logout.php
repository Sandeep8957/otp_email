<?php
session_start();

// सभी सेशन वेरिएबल्स डिलीट करें
$_SESSION = array();

// सेशन डिस्ट्रॉय करें
session_destroy();

// लॉगिन पेज पर रीडायरेक्ट
header('Location: login.php');
exit;
?>