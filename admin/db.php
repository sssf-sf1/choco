<?php 
$mysqli = new mysqli("127.0.0.1", "root", "root", "chocoberry_place");
if ($mysqli->connect_error) {
    die("Ошибка подключения: " . $mysqli->connect_error);
}
?>