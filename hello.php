<?php
header('HTTP/1.1 200 OK');
header_remove('Set-Cookie');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS, DELETE, GET");
header("Access-Control-Allow-Headers: Origin,Content-Type,X-Amz-Date,Authorization,X-Api-Key,X-Amz-Security-Token,locale");
echo ("Hello");
?>