<?php
// ========================================================================
// FILE: logout.php
// ========================================================================
?>
<?php
require_once 'classes/User.php';
User::logout();
header("Location: login.php");
exit();
?>
