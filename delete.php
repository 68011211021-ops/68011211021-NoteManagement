<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();
require_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
verify_csrf();
$id = (int)($_POST['id'] ?? 0);
$owner_id = current_user_id();
if ($_SESSION['role'] === 'admin') {
    $stmt = $conn->prepare('DELETE FROM notes WHERE id = ?');
    $stmt->bind_param('i', $id);
} else {
    $stmt = $conn->prepare('DELETE FROM notes WHERE id = ? AND owner_id = ?');
    $stmt->bind_param('ii', $id, $owner_id);
}
$stmt->execute();
redirect('index.php');
