<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();
require_admin();

$users = $conn->query('SELECT id, username, full_name, role FROM users ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
$notes = $conn->query('SELECT notes.id, notes.title, notes.created_at, users.full_name AS owner_name FROM notes JOIN users ON notes.owner_id = users.id ORDER BY notes.id DESC')->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin</title><link rel="stylesheet" href="style.css"></head><body><nav><b>หน้าผู้ดูแลระบบ</b><span><a href="index.php">หน้าหลัก</a> | <a href="logout.php">ออกจากระบบ</a></span></nav><div class="container"><div class="card"><h1>จัดการระบบ</h1><h2>ผู้ใช้งาน</h2><table><thead><tr><th>ID</th><th>ชื่อผู้ใช้</th><th>ชื่อ-นามสกุล</th><th>สิทธิ์</th></tr></thead><tbody><?php foreach($users as $u): ?><tr><td><?= (int)$u['id'] ?></td><td><?= e($u['username']) ?></td><td><?= e($u['full_name']) ?></td><td><?= e($u['role']) ?></td></tr><?php endforeach; ?></tbody></table><h2>โน้ตทั้งหมด</h2><table><thead><tr><th>ID</th><th>หัวข้อ</th><th>เจ้าของ</th><th>วันที่</th><th>จัดการ</th></tr></thead><tbody><?php foreach($notes as $n): ?><tr><td><?= (int)$n['id'] ?></td><td><?= e($n['title']) ?></td><td><?= e($n['owner_name']) ?></td><td><?= e($n['created_at']) ?></td><td><form class="inline" method="post" action="delete.php" onsubmit="return confirm('ยืนยันลบโน้ตนี้?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$n['id'] ?>"><button class="btn danger small">ลบ</button></form></td></tr><?php endforeach; ?></tbody></table></div></div></body></html>
