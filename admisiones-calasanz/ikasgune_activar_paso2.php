<?php
session_start();
$_SESSION['paso2'] = true;
http_response_code(200);
