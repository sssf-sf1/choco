<?php 
$conn = new mysqli("127.0.0.1", "root", "", "chocoberry_place");
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}
?>