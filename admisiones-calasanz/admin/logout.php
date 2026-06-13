<?php
//<!-- Created by Yassin.lahhit@outlook.com. 2026 -->

require_once 'session_config.php';
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
