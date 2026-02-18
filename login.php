<?php
require_once 'auth.php';

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin.php' : 'staff.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if ($password === getAdminPassword()) {
        loginAsRole('admin');
        header('Location: admin.php');
        exit;
    }

    if ($password === getStaffPassword()) {
        loginAsRole('staff');
        header('Location: staff.php');
        exit;
    }

    $error = 'Incorrect password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Rivek Men's Salon - Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #1a1a2e;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .login-card {
      background: rgba(255,255,255,0.06);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 40px 35px;
      max-width: 400px;
      width: 100%;
    }
    .brand { text-align: center; margin-bottom: 30px; }
    .brand-icon {
      width: 60px; height: 60px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 16px;
      display: inline-flex; align-items: center; justify-content: center;
      margin-bottom: 12px;
      color: white; font-size: 28px;
    }
    .brand-name { font-size: 22px; font-weight: 800; color: white; }
    .brand-sub {
      font-size: 13px; color: rgba(255,255,255,0.35); margin-top: 4px;
      letter-spacing: 2px; text-transform: uppercase;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block; margin-bottom: 6px; color: rgba(255,255,255,0.5);
      font-size: 13px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;
    }
    .form-group input {
      width: 100%; padding: 16px 18px; background: rgba(255,255,255,0.07);
      border: 2px solid rgba(255,255,255,0.08); border-radius: 14px;
      font-size: 18px; color: white;
    }
    .form-group input:focus { outline: none; border-color: #667eea; }
    .btn-login {
      width: 100%; padding: 18px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white; border: none; border-radius: 14px;
      font-size: 18px; font-weight: 700; cursor: pointer;
    }
    .error {
      background: rgba(235, 51, 73, 0.15); color: #ff6b7a;
      padding: 12px 16px; border-radius: 10px; font-size: 14px;
      text-align: center; margin-bottom: 20px;
    }
    .hint { margin-top: 12px; color: rgba(255,255,255,0.45); font-size: 12px; text-align:center; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="brand">
      <div class="brand-icon">🔐</div>
      <div class="brand-name">Rivek Men's Salon</div>
      <div class="brand-sub">Admin / Staff Login</div>
    </div>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter password" required autofocus>
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>

    <div class="hint">Admin and Staff can use same URL. Access is role-based by password.</div>
  </div>
</body>
</html>
