<?php
require_once 'auth.php';

if (isLoggedIn()) {
    header('Location: staff.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password === getAdminPassword()) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: staff.php');
        exit;
    } else {
        $error = 'Incorrect password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Rivek Men's Salon - Staff Login</title>
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

    .brand {
      text-align: center;
      margin-bottom: 30px;
    }

    .brand-icon {
      width: 60px;
      height: 60px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 16px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }

    .brand-icon svg { width: 30px; height: 30px; fill: white; }

    .brand-name {
      font-size: 22px;
      font-weight: 800;
      color: white;
    }

    .brand-sub {
      font-size: 13px;
      color: rgba(255,255,255,0.35);
      margin-top: 4px;
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 6px;
      color: rgba(255,255,255,0.5);
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .form-group input {
      width: 100%;
      padding: 16px 18px;
      background: rgba(255,255,255,0.07);
      border: 2px solid rgba(255,255,255,0.08);
      border-radius: 14px;
      font-size: 18px;
      color: white;
      transition: all 0.3s;
    }

    .form-group input::placeholder { color: rgba(255,255,255,0.2); }
    .form-group input:focus { outline: none; border-color: #667eea; background: rgba(102, 126, 234, 0.1); }

    .btn-login {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 14px;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s;
    }

    .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4); }

    .error {
      background: rgba(235, 51, 73, 0.15);
      color: #ff6b7a;
      padding: 12px 16px;
      border-radius: 10px;
      font-size: 14px;
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24"><path d="M20 7h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM10 4h4v3h-4V4z"/></svg>
      </div>
      <div class="brand-name">Rivek Men's Salon</div>
      <div class="brand-sub">Staff Login</div>
    </div>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Enter staff password" required autofocus>
      </div>
      <button type="submit" class="btn-login">Login</button>
    </form>
  </div>
</body>
</html>
