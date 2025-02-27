<?php
session_start();
session_destroy();
echo "<script>window.location.href = 'index.php';</script>";
//header("Location: index.php");
exit();
?>
