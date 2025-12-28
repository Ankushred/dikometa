<?php
session_start();
session_destroy(); // Destroy all data
header("Location: login.php");
exit();
?>