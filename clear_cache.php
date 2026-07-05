<?php
if (session_status() === PHP_SESSION_NONE) session_start();
unset($_SESSION['_fg_ts']);
unset($_SESSION['_fg_data']);
if (function_exists('apcu_delete')) {
    apcu_delete('aulapro_fg');
}
echo "Cache cleared.";
