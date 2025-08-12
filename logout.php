<?php
session_start();
require_once 'db.php';

session_destroy();
session_abort();
header("location:new_register_cust.php");
exit();

?>
