<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();
require_login();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    if ($title === '' || $content === '') $error = 'กรุณากรอกหัวข้อและเนื้อหา';
    elseif (mb_strlen($title) > 255) $error = 'หัวข้อยาวเกินไป';
    else {
        $owner_id = current_user_id();
        $stmt = $conn->prepare('INSERT INTO notes (title, content, owner_id) VALUES (?, ?, ?)');
        $stmt->bind_param('ssi', $title, $content, $owner_id);
        if ($stmt->execute()) redirect('index.php');
        $error = 'ไม่สามารถบันทึกโน้ตได้';
    }
}
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>เพิ่มโน้ต</title><link rel="stylesheet" href="style.css"></head><body><div class="container"><div class="card narrow"><h1>เพิ่มโน้ต</h1><?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>หัวข้อ</label><input name="title" maxlength="255" value="<?= e($_POST['title'] ?? '') ?>" required><label>เนื้อหา</label><textarea name="content" rows="8" required><?= e($_POST['content'] ?? '') ?></textarea><button class="btn">บันทึก</button> <a class="btn gray" href="index.php">ยกเลิก</a></form></div></div></body></html>
