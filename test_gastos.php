<?php
require 'api/v1/_api.php';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'list';
// Mock auth
function v1Auth() {
    return ['user_type' => 'director', 'user_id' => 1];
}
// redefine to avoid fatal error
require 'api/v1/gastos.php';
