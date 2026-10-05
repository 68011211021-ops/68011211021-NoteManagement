<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();
require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $conn->prepare('SELECT id, title, content, owner_id FROM notes WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { http_response_code(404); exit('ไม่พบข้อมูลที่ต้องการแก้ไข'); }
if ((int)$row['owner_id'] !== current_user_id()) { http_response_code(403); exit('คุณไม่มีสิทธิ์แก้ไขข้อมูลนี้'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') $error = 'กรุณากรอกหัวข้อและเนื้อหา';
    elseif (mb_strlen($title) > 255) $error = 'หัวข้อยาวเกินไป';
    else {
        $owner_id = current_user_id();
        $stmt = $conn->prepare('UPDATE notes SET title = ?, content = ? WHERE id = ? AND owner_id = ?');
        $stmt->bind_param('ssii', $title, $content, $id, $owner_id);
        if ($stmt->execute()) redirect('index.php');
        $error = 'ไม่สามารถแก้ไขโน้ตได้';
    }
}
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>แก้ไขโน้ต</title><link rel="stylesheet" href="style.css"></head><body><div class="container"><div class="card narrow"><h1>แก้ไขโน้ต</h1><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$row['id'] ?>"><label>หัวข้อ</label><input name="title" maxlength="255" value="<?= e($_POST['title'] ?? $row['title']) ?>" required><label>เนื้อหา</label><textarea name="content" rows="8" required><?= e($_POST['content'] ?? $row['content']) ?></textarea><button class="btn">บันทึกการแก้ไข</button> <a class="btn gray" href="index.php">ยกเลิก</a></form></div></div></body></html>
