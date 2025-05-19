<?php
$mysqli = new mysqli('localhost', 'root', '', 'nhasach');
$mysqli->set_charset('utf8');
if ($mysqli->connect_errno){
  die('Lỗi kết nối cơ sở dữ liệu: ' . $mysqli->connect_error);
}
?>