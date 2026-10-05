<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
start_secure_session();
security_headers();

if (!empty($_SESSION['user_id'])) redirect('index.php');
$error = '';
$success = isset($_GET['registered']) ? 'สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, username, password_hash, full_name, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        redirect('index.php');
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!doctype html><html lang="th"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>เข้าสู่ระบบ</title><link rel="stylesheet" href="style.css"></head><body>
<div class="wrap"><div class="card narrow"><h1>ระบบจัดการโน้ตส่วนตัว</h1>
<?php if ($success): ?><div class="alert success"><?= e($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<label>ชื่อผู้ใช้</label><input name="username" value="<?= e($_POST['username'] ?? '') ?>" required maxlength="50" autofocus>
<label>รหัสผ่าน</label><input type="password" name="password" required>
<button class="btn" type="submit">เข้าสู่ระบบ</button>
</form><p>ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิก</a></p></div></div></body></html>
