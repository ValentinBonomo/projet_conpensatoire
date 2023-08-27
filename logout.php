<?php
setcookie('connected', '', time() - 3600, '/');
header('Location: home.php');
exit;
?>
