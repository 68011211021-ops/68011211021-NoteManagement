<?php
$host   = 'localhost';
$user   = 'root';
$pass   = '';
$dbname = 'webdb';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ กรุณาตรวจสอบ MySQL และข้อมูลใน db.php');
}
$conn->set_charset('utf8mb4');
