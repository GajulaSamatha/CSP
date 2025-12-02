<?php

require_once __DIR__ . '/../src/mysqli_compat.php';
session_start();
require_once __DIR__ . '/../config/db.php';

session_destroy();
session_abort();
header("location:new_register_cust.php");
exit();

?>


