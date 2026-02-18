<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Rivek Men's Salon - Get Token</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #1a1a2e;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .container {
      max-width: 400px;
      width: 100%;
    }

    /* Brand header */
    .brand {
      text-align: center;
      margin-bottom: 30px;
    }

    .brand-icon {
      width: 84px;
      height: 84px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 20px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }

    .brand-icon svg {
      width: 42px;
      height: 42px;
      fill: white;
    }

    .brand-name {
      font-size: 26px;
      font-weight: 800;
      color: white;
      letter-spacing: 1px;
    }

    .brand-tagline {
      font-size: 13px;
      color: rgba(255,255,255,0.35);
      margin-top: 4px;
      letter-spacing: 3px;
      text-transform: uppercase;
    }

    /* Form card */
    .form-card {
      background: rgba(255,255,255,0.06);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 24px;
      padding: 35px 30px;
    }

    .form-title {
      font-size: 22px;
      font-weight: 700;
      color: white;
      text-align: center;
      margin-bottom: 5px;
    }

    .form-subtitle {
      text-align: center;
      color: rgba(255,255,255,0.4);
      font-size: 14px;
      margin-bottom: 28px;
    }

    .form-group {
      margin-bottom: 18px;
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

    .form-group input::placeholder {
      color: rgba(255,255,255,0.2);
    }

    .form-group input:focus {
      outline: none;
      border-color: #667eea;
      background: rgba(102, 126, 234, 0.1);
    }

    .btn-submit {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 14px;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 8px;
      transition: all 0.3s;
      letter-spacing: 0.5px;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:active { transform: translateY(0); }
    .btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* Success state */
    .success { display: none; text-align: center; }
    .success.show { display: block; }
    .form-state.hide { display: none; }

    .token-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 20px;
      padding: 35px 25px;
      margin: 20px 0;
      position: relative;
      overflow: hidden;
    }

    .token-card::before {
      content: '';
      position: absolute;
      top: -30px;
      right: -30px;
      width: 100px;
      height: 100px;
      background: rgba(255,255,255,0.1);
      border-radius: 50%;
    }

    .token-card::after {
      content: '';
      position: absolute;
      bottom: -20px;
      left: -20px;
      width: 70px;
      height: 70px;
      background: rgba(255,255,255,0.08);
      border-radius: 50%;
    }

    .token-label {
      font-size: 12px;
      color: rgba(255,255,255,0.6);
      letter-spacing: 3px;
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .token-number {
      font-size: 56px;
      font-weight: 800;
      color: white;
      letter-spacing: 4px;
      position: relative;
      z-index: 1;
    }

    .token-name {
      font-size: 20px;
      color: rgba(255,255,255,0.85);
      margin-top: 8px;
      position: relative;
      z-index: 1;
    }

    .success-title {
      font-size: 22px;
      font-weight: 700;
      color: white;
      margin-bottom: 5px;
    }

    .success-check {
      width: 64px;
      height: 64px;
      background: rgba(56, 239, 125, 0.15);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 15px;
    }

    .success-check svg {
      width: 34px;
      height: 34px;
      stroke: #38ef7d;
      fill: none;
      stroke-width: 3;
    }

    .emoji-icon {
      font-size: 1.25em;
      line-height: 1;
    }

    .info-text {
      color: rgba(255,255,255,0.35);
      font-size: 14px;
      line-height: 1.7;
      margin-top: 15px;
    }

    .btn-another {
      width: 100%;
      padding: 16px;
      background: rgba(255,255,255,0.08);
      color: rgba(255,255,255,0.6);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 14px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 18px;
      transition: all 0.3s;
    }

    .btn-another:hover {
      background: rgba(255,255,255,0.12);
      color: white;
    }

    /* Footer */
    .footer {
      text-align: center;
      margin-top: 25px;
    }

    .footer-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 20px;
    }

    .footer a {
      color: rgba(255,255,255,0.3);
      text-decoration: none;
      font-size: 13px;
      transition: color 0.3s;
    }

    .footer a:hover { color: rgba(255,255,255,0.6); }

    .footer .dot {
      width: 3px;
      height: 3px;
      background: rgba(255,255,255,0.15);
      border-radius: 50%;
    }

    .footer .address {
      color: rgba(255,255,255,0.15);
      font-size: 11px;
      margin-top: 8px;
      line-height: 1.4;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Brand -->
    <div class="brand">
      <div class="brand-icon">
        <svg viewBox="0 0 24 24"><path d="M20 7h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v3H4c-1.1 0-2 .9-2 2v11c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zM10 4h4v3h-4V4z"/></svg>
      </div>
      <div class="brand-name">Rivek Men's Salon</div>
      <div class="brand-tagline">Hair Salon</div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
      <div class="form-state">
        <div class="form-title">Get Your Token <span class="emoji-icon" aria-hidden="true">🎟️</span></div>
        <div class="form-subtitle">Enter your details to join the queue</div>

        <form id="tokenForm">
          <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" id="name" placeholder="Enter your name" required autocomplete="off">
          </div>
          <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" placeholder="Enter your phone number" required autocomplete="off">
          </div>
          <button type="submit" class="btn-submit" id="submitBtn">Get Token <span class="emoji-icon" aria-hidden="true">🎟️</span></button>
        </form>
      </div>

      <div class="success">
        <div class="success-check">
          <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
        </div>
        <div class="success-title">You're in the queue! <span class="emoji-icon" aria-hidden="true">✅</span></div>
        <div class="token-card">
          <div class="token-label">Your Token <span class="emoji-icon" aria-hidden="true">🎫</span></div>
          <div class="token-number" id="tokenNumber"></div>
          <div class="token-name" id="tokenName"></div>
        </div>
        <p class="info-text">
          Please wait for your token to be called.<br>
          Watch the display screen for updates.
        </p>
        <button class="btn-another" onclick="resetForm()">Get Another Token <span class="emoji-icon" aria-hidden="true">🔄</span></button>
      </div>
    </div>

    <!-- Footer -->
    <div class="footer">
      <div class="footer-row">
        <a href="https://instagram.com/rivek_mens_salon" target="_blank">@rivek_mens_salon</a>
        <div class="dot"></div>
        <a href="tel:9601084421">96010 84421</a>
      </div>
      <div class="address">The Edge, 07, opp. Malbar Royal, Nikol, Ahmedabad 382350</div>
    </div>
  </div>

  <script>
    const form = document.getElementById('tokenForm');
    const formState = document.querySelector('.form-state');
    const success = document.querySelector('.success');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('name').value.trim();
      const phone = document.getElementById('phone').value.trim();
      if (!name || !phone) return;

      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.innerHTML = 'Getting token... <span class="emoji-icon" aria-hidden="true">⏳</span>';

      try {
        const res = await fetch('api.php?action=create_token', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, phone })
        });
        const data = await res.json();
        if (res.ok) {
          document.getElementById('tokenNumber').textContent = data.formatted;
          document.getElementById('tokenName').textContent = data.name;
          formState.classList.add('hide');
          success.classList.add('show');
        } else {
          alert(data.error || 'Something went wrong');
        }
      } catch (err) {
        alert('Failed to get token. Please try again.');
      }
      btn.disabled = false;
      btn.innerHTML = 'Get Token <span class="emoji-icon" aria-hidden="true">🎟️</span>';
    });

    function resetForm() {
      document.getElementById('name').value = '';
      document.getElementById('phone').value = '';
      formState.classList.remove('hide');
      success.classList.remove('show');
    }
  </script>
</body>
</html>
