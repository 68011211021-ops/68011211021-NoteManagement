<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();

if (!empty($_SESSION['user_id'])) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($username === '' || $full_name === '' || $password === '') {
        $error = 'กรุณากรอกข้อมูลให้ครบ';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
        $error = 'ชื่อผู้ใช้ต้องมี 3-50 ตัว และใช้ A-Z, a-z, 0-9 หรือ _ เท่านั้น';
    } elseif (strlen($password) < 4) {
        $error = 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร';
    } elseif ($password !== $confirm) {
        $error = 'ยืนยันรหัสผ่านไม่ตรงกัน';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password_hash, full_name, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param('sss', $username, $hash, $full_name);
            if ($stmt->execute()) {
                redirect('login.php?registered=1');
            }
            $error = 'ไม่สามารถสมัครสมาชิกได้';
        }
    }
}
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>สมัครสมาชิก</title><link rel="stylesheet" href="style.css"></head><body>
<div class="wrap"><div class="card narrow"><h1>สมัครสมาชิก</h1>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>ชื่อผู้ใช้</label><input name="username" value="<?= e($_POST['username'] ?? '') ?>" required maxlength="50">
<label>ชื่อ-นามสกุล</label><input name="full_name" value="<?= e($_POST['full_name'] ?? '') ?>" required maxlength="100">
<label>รหัสผ่าน</label><input type="password" name="password" required>
<label>ยืนยันรหัสผ่าน</label><input type="password" name="confirm_password" required>
<button class="btn" type="submit">สมัครสมาชิก</button>
</form><p><a href="login.php">กลับไปเข้าสู่ระบบ</a></p></div></div></body></html>
