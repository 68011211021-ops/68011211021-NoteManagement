<?php
$host = 'sql201.infinityfree.com';
$user = 'ใส่ MySQL Username ของคุณ';
$pass = 'ใส่ MySQL Password ของคุณ';
$dbname = 'if0_43094972_notedb';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die('เชื่อมต่อฐานข้อมูลไม่สำเร็จ กรุณาตรวจสอบ MySQL และข้อมูลใน db.php');
}
$conn->set_charset('utf8mb4');
