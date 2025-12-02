<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
session_destroy();
header("Location: admin_login.php");
exit();
?>

