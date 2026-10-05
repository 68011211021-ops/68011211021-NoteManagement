<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();
require_login();

$rows = [];
$stmt = $conn->prepare('SELECT notes.*, users.full_name AS owner_name FROM notes JOIN users ON notes.owner_id = users.id ORDER BY notes.id DESC');
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>รายการโน้ต</title><link rel="stylesheet" href="style.css"></head><body>
<nav><b>ระบบจัดการโน้ตส่วนตัว</b><span>สวัสดี <?= e($_SESSION['full_name']) ?> (<?= e($_SESSION['role']) ?>) | <a href="create.php">+ เพิ่มโน้ต</a> <?php if ($_SESSION['role'] === 'admin'): ?>| <a href="admin.php">หน้าผู้ดูแล</a><?php endif; ?> | <a href="logout.php">ออกจากระบบ</a></span></nav>
<div class="container"><div class="card"><h1>โน้ตทั้งหมด</h1>
<?php if (!$rows): ?><p>ยังไม่มีโน้ต</p><?php else: ?><table><thead><tr><th>หัวข้อ</th><th>เนื้อหา</th><th>เจ้าของ</th><th>วันที่สร้าง</th><th>จัดการ</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= nl2br(e($r['content'])) ?></td><td><?= e($r['owner_name']) ?><?php if ((int)$r['owner_id'] === current_user_id()): ?> <span class="tag">ของคุณ</span><?php endif; ?></td><td><?= e($r['created_at']) ?></td><td>
<?php if ((int)$r['owner_id'] === current_user_id()): ?><a class="btn small" href="edit.php?id=<?= (int)$r['id'] ?>">แก้ไข</a><form class="inline" method="post" action="delete.php" onsubmit="return confirm('ยืนยันการลบโน้ตนี้?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn danger small" type="submit">ลบ</button></form><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php endif; ?></div></div></body></html>
