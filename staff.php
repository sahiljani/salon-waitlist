<?php
require_once 'auth.php';
requireStaffOrAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Rivek Men's Salon - Staff</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f0f2f5;
      min-height: 100vh;
    }

    .header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
      text-align: center;
    }

    .header h1 { font-size: 22px; }

    .stats {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 12px;
      flex-wrap: wrap;
    }

    .stat {
      background: rgba(255,255,255,0.2);
      padding: 8px 18px;
      border-radius: 10px;
    }

    .stat-value { font-size: 22px; font-weight: 700; }
    .stat-label { font-size: 11px; opacity: 0.9; }

    .main {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }

    /* NEXT button row */
    .action-bar {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .btn-next {
      flex: 1;
      min-width: 200px;
      padding: 18px 30px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 12px;
      font-size: 20px;
      font-weight: 700;
      cursor: pointer;
      transition: transform 0.2s;
      letter-spacing: 1px;
    }

    .btn-next:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(102,126,234,0.4); }
    .btn-next:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .chairs-label {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #666;
      font-size: 14px;
      padding: 0 10px;
    }

    .chair-dots {
      display: flex;
      gap: 6px;
    }

    .chair-dot {
      width: 14px;
      height: 14px;
      border-radius: 50%;
      background: #ddd;
    }

    .chair-dot.active { background: #38ef7d; }

    /* Serving Grid - 4 chairs */
    .serving-section h2 {
      font-size: 16px;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 15px;
    }

    .serving-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      margin-bottom: 25px;
    }

    @media (max-width: 900px) {
      .serving-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 500px) {
      .serving-grid { grid-template-columns: 1fr; }
    }

    .chair-card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      border: 2px solid #e8e8e8;
      min-height: 180px;
      display: flex;
      flex-direction: column;
    }

    .chair-card.occupied {
      border-color: #38ef7d;
      background: linear-gradient(180deg, #f0fff4 0%, white 30%);
    }

    .chair-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .chair-number {
      font-size: 12px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .chair-status {
      font-size: 11px;
      padding: 3px 10px;
      border-radius: 20px;
      font-weight: 600;
    }

    .chair-status.available { background: #f0f0f0; color: #999; }
    .chair-status.serving { background: #d4f8e0; color: #11998e; }

    .chair-token {
      font-size: 32px;
      font-weight: 700;
      color: #333;
      margin-bottom: 5px;
    }

    .chair-name {
      font-size: 18px;
      color: #555;
      margin-bottom: 3px;
    }

    .chair-phone {
      font-size: 14px;
      color: #999;
      margin-bottom: 15px;
    }

    .chair-empty {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ccc;
      font-size: 15px;
    }

    .chair-actions {
      display: flex;
      gap: 8px;
      margin-top: auto;
    }

    .btn-sm {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .btn-sm:hover { transform: translateY(-1px); }

    .btn-done { background: #d4f8e0; color: #11998e; }
    .btn-done:hover { background: #b5f0cc; }

    .btn-requeue { background: #e0e7ff; color: #667eea; }
    .btn-requeue:hover { background: #c7d2fe; }

    .btn-noshow { background: #fde8e8; color: #eb3349; }
    .btn-noshow:hover { background: #fbd0d0; }

    /* Waiting Queue & Add Token */
    .bottom-section {
      display: grid;
      grid-template-columns: 1fr 420px;
      gap: 20px;
    }

    @media (max-width: 768px) {
      .bottom-section { grid-template-columns: 1fr; }
    }

    .card {
      background: white;
      border-radius: 15px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .card h2 {
      font-size: 16px;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 15px;
      padding-bottom: 10px;
      border-bottom: 2px solid #f0f0f0;
    }

    .queue-list { max-height: 350px; overflow-y: auto; }

    .queue-item {
      display: flex;
      align-items: center;
      padding: 12px 8px;
      border-bottom: 1px solid #f5f5f5;
    }

    .queue-item:last-child { border-bottom: none; }

    .queue-item .token { font-size: 16px; font-weight: 700; color: #667eea; width: 75px; }
    .queue-item .info { flex: 1; }
    .queue-item .name { font-size: 15px; color: #333; }
    .queue-item .phone { font-size: 12px; color: #999; }
    .queue-item .position {
      background: #f0f0f0;
      color: #666;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 12px;
      margin-right: 8px;
    }

    .btn-call {
      padding: 6px 14px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .btn-call:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(102,126,234,0.3); }

    .empty-message { text-align: center; color: #ccc; padding: 30px; font-size: 14px; }

    /* Add Token Form */
    .add-form-group { margin-bottom: 18px; }
    .add-form-group label { display: block; margin-bottom: 6px; font-size: 16px; color: #555; font-weight: 600; }
    .add-form-group input {
      width: 100%;
      padding: 16px 16px;
      border: 2px solid #e8e8e8;
      border-radius: 10px;
      font-size: 20px;
    }
    .add-form-group input:focus { outline: none; border-color: #667eea; }

    .btn-add {
      width: 100%;
      padding: 18px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 20px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 5px;
      transition: transform 0.2s;
    }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(102,126,234,0.4); }

    /* Toast */
    .toast {
      position: fixed;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%) translateY(100px);
      background: #333;
      color: white;
      padding: 14px 28px;
      border-radius: 10px;
      opacity: 0;
      transition: all 0.3s;
      z-index: 1000;
      font-size: 15px;
    }
    .toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
    .toast.error { background: #eb3349; }
    .toast.success { background: #11998e; }
  </style>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
  <div class="header">
    <h1>Rivek Men's Salon</h1>
    <div style="font-size:13px; opacity:0.8; margin-top:2px;"><i data-lucide="layout-dashboard" class="inline w-4 h-4"></i></div>
    <div class="stats">
      <div class="stat">
        <div class="stat-value" id="statWaiting">0</div>
        <div class="stat-label"><i data-lucide="clock-3" class="inline w-4 h-4"></i></div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statServing">0</div>
        <div class="stat-label"><i data-lucide="users" class="inline w-4 h-4"></i></div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statDone">0</div>
        <div class="stat-label"><i data-lucide="check-circle" class="inline w-4 h-4"></i></div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statNoShow">0</div>
        <div class="stat-label"><i data-lucide="ban" class="inline w-4 h-4"></i></div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statTotal">0</div>
        <div class="stat-label"><i data-lucide="bar-chart-3" class="inline w-4 h-4"></i></div>
      </div>
    </div>
  </div>

  <div class="main">
    <!-- NEXT Button -->
    <div class="action-bar">
      <button class="btn-next" id="btnNext" onclick="callNext()"><i data-lucide="bell-ring" class="inline w-5 h-5"></i> <i data-lucide="arrow-right" class="inline w-5 h-5"></i></button>
      <div class="chairs-label">
        <span><i data-lucide="users" class="inline w-4 h-4"></i></span>
        <div class="chair-dots">
          <div class="chair-dot" id="dot0"></div>
          <div class="chair-dot" id="dot1"></div>
          <div class="chair-dot" id="dot2"></div>
          <div class="chair-dot" id="dot3"></div>
        </div>
      </div>
    </div>

    <!-- 4 Chair Cards -->
    <div class="serving-section">
      <h2><i data-lucide="users" class="inline w-4 h-4"></i></h2>
      <div class="serving-grid" id="servingGrid">
        <!-- Filled by JS -->
      </div>
    </div>

    <!-- Waiting Queue + Add Token -->
    <div class="bottom-section">
      <div class="card">
        <h2><i data-lucide="clock-3" class="inline w-4 h-4"></i></h2>
        <div class="queue-list" id="queueList">
          <div class="empty-message"><i data-lucide="inbox" class="inline w-5 h-5"></i></div>
        </div>
      </div>

      <div class="card">
        <h2><i data-lucide="plus" class="inline w-4 h-4"></i></h2>
        <div class="add-form-group">
          <label><i data-lucide="user" class="inline w-4 h-4"></i></label>
          <input type="text" id="manualName" placeholder="Name" autocomplete="off">
        </div>
        <div class="add-form-group">
          <label><i data-lucide="phone" class="inline w-4 h-4"></i></label>
          <input type="tel" id="manualPhone" placeholder="Phone" autocomplete="off">
        </div>
        <button class="btn-add" onclick="addManualToken()"><i data-lucide="plus" class="inline w-4 h-4"></i></button>
      </div>
    </div>
  </div>

  <div id="posModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:2000;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:14px;max-width:700px;width:100%;max-height:92vh;overflow:auto;padding:18px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-size:22px;font-weight:800;"><i data-lucide="receipt" class="inline w-5 h-5"></i></div>
        <button onclick="closePosModal()" style="border:none;background:#f2f2f2;border-radius:8px;padding:8px 12px;cursor:pointer;"><i data-lucide="x" class="inline w-4 h-4"></i></button>
      </div>

      <div id="posTokenInfo" style="font-size:18px;font-weight:700;margin-bottom:10px;"></div>

      <div style="font-size:14px;margin:8px 0;"><i data-lucide="user" class="inline w-4 h-4"></i></div>
      <div id="staffOptions" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:8px;margin-bottom:12px;"></div>

      <div style="font-size:14px;margin:8px 0;"><i data-lucide="scissors" class="inline w-4 h-4"></i></div>
      <div id="serviceOptions" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:8px;margin-bottom:12px;"></div>

      <div style="display:grid;grid-template-columns:1fr 120px;gap:8px;align-items:end;margin-bottom:12px;">
        <div>
          <div style="font-size:14px;margin:8px 0;"><i data-lucide="plus" class="inline w-4 h-4"></i></div>
          <input id="otherAmount" type="number" min="1" step="1" placeholder="₹" style="width:100%;padding:10px;border:2px solid #e8e8e8;border-radius:10px;font-size:18px;">
        </div>
        <button onclick="addOtherLine()" style="border:none;background:#222;color:#fff;border-radius:10px;padding:12px 10px;font-weight:700;cursor:pointer;"><i data-lucide="plus" class="inline w-4 h-4"></i></button>
      </div>

      <div id="saleItems" style="border:1px solid #eee;border-radius:10px;padding:10px;min-height:60px;margin-bottom:10px;"></div>

      <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin:8px 0;">
        <input id="discountInput" type="number" min="0" step="1" placeholder="Discount" style="width:100%;padding:10px;border:2px solid #e8e8e8;border-radius:10px;font-size:18px;">
        <input id="taxInput" type="number" min="0" step="1" placeholder="Tax" style="width:100%;padding:10px;border:2px solid #e8e8e8;border-radius:10px;font-size:18px;">
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin:10px 0;">
        <button onclick="setPayment('CASH')" id="payCASH" style="border:none;background:#f3f3f3;border-radius:10px;padding:10px;cursor:pointer;font-weight:700;"><i data-lucide="banknote" class="inline w-4 h-4"></i></button>
        <button onclick="setPayment('UPI')" id="payUPI" style="border:none;background:#f3f3f3;border-radius:10px;padding:10px;cursor:pointer;font-weight:700;"><i data-lucide="phone" class="inline w-4 h-4"></i></button>
        <button onclick="setPayment('CARD')" id="payCARD" style="border:none;background:#f3f3f3;border-radius:10px;padding:10px;cursor:pointer;font-weight:700;"><i data-lucide="credit-card" class="inline w-4 h-4"></i></button>
      </div>

      <div id="posTotals" style="font-size:22px;font-weight:800;margin:10px 0;">₹0</div>

      <button id="completeSaleBtn" onclick="saveSaleAndDone()" style="width:100%;border:none;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;border-radius:12px;padding:16px;font-size:22px;font-weight:800;cursor:pointer;"><i data-lucide="check" class="inline w-5 h-5"></i></button>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    let staffList = [];
    let serviceList = [];
    let currentSale = { token: null, staffId: null, items: [], paymentMethod: 'CASH' };

    const ICON_MAP = {"👤":"user","🧔":"user","👨":"user","👩":"user","✂️":"scissors","🪒":"scissors","🚿":"droplets","🧴":"sparkles"};

    function normalizeIcon(value, fallback = 'circle') {
      const v = (value || '').trim();
      if (!v) return fallback;
      return ICON_MAP[v] || v;
    }

    function iconNode(name, cls = 'w-4 h-4 inline') {
      return `<i data-lucide="${normalizeIcon(name)}" class="${cls}"></i>`;
    }

    function refreshIcons() {
      if (window.lucide) window.lucide.createIcons();
    }

    function showToast(message, type = '') {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.className = 'toast show ' + type;
      setTimeout(() => { toast.className = 'toast'; }, 3000);
    }


    async function parseJsonSafe(res) {
      const raw = await res.text();
      if (!raw) return {};
      try {
        return JSON.parse(raw);
      } catch (err) {
        return { error: 'Invalid server response' };
      }
    }

    async function fetchJson(url, options = {}) {
      const res = await fetch(url, options);
      const data = await parseJsonSafe(res);
      if (!res.ok) {
        throw new Error(data.error || 'Request failed');
      }
      if (data.error) {
        throw new Error(data.error);
      }
      return data;
    }

    function renderChairs(serving) {
      const grid = document.getElementById('servingGrid');
      let html = '';

      for (let i = 0; i < 4; i++) {
        const person = serving[i] || null;
        const dot = document.getElementById('dot' + i);

        if (person) {
          dot.classList.add('active');
          html += `
            <div class="chair-card occupied">
              <div class="chair-header">
                <span class="chair-number">${iconNode("armchair","w-4 h-4 inline")} ${i + 1}</span>
                <span class="chair-status serving">${iconNode("circle","w-3 h-3 inline text-green-600")}</span>
              </div>
              <div class="chair-token">${person.formatted}</div>
              <div class="chair-name">${person.name}</div>
              <div class="chair-phone">${iconNode("phone","w-4 h-4 inline")} ${person.phone || '-'}</div>
              <div class="chair-actions">
                <button class="btn-sm btn-done" onclick="openPosModal(${person.id})">${iconNode("check","w-4 h-4 inline")}</button>
                <button class="btn-sm btn-requeue" onclick="backToQueue(${person.id})">BACK TO QUEUE</button>
                <button class="btn-sm btn-noshow" onclick="markNoShow(${person.id})">${iconNode("ban","w-4 h-4 inline")}</button>
              </div>
            </div>
          `;
        } else {
          dot.classList.remove('active');
          html += `
            <div class="chair-card">
              <div class="chair-header">
                <span class="chair-number">${iconNode("armchair","w-4 h-4 inline")} ${i + 1}</span>
                <span class="chair-status available">${iconNode("circle","w-3 h-3 inline text-slate-400")}</span>
              </div>
              <div class="chair-empty">${iconNode("circle-off","w-4 h-4 inline")}</div>
            </div>
          `;
        }
      }

      grid.innerHTML = html;
      refreshIcons();
    }

    function updateUI(data) {
      // Render 4 chairs
      renderChairs(data.serving);

      // NEXT button - disable only if all 4 chairs full OR no waiting
      const btnNext = document.getElementById('btnNext');
      btnNext.disabled = data.servingCount >= data.maxServing || data.waiting.length === 0;

      // Queue list
      const queueList = document.getElementById('queueList');
      if (data.waiting.length === 0) {
        queueList.innerHTML = `<div class="empty-message">${iconNode('inbox','w-5 h-5 inline')}</div>`;
      } else {
        queueList.innerHTML = data.waiting.map((item, index) => `
          <div class="queue-item">
            <div class="token">${item.formatted}</div>
            <div class="info">
              <div class="name">${item.name}</div>
              <div class="phone">${iconNode("phone","w-4 h-4 inline")} ${item.phone || ''}</div>
            </div>
            <div class="position">#${index + 1}</div>
            <button class="btn-call" onclick="callSpecific(${item.id})">${iconNode("bell","w-4 h-4 inline")}</button>
          </div>
        `).join('');
      }
      refreshIcons();
    }

    async function updateStats() {
      try {
        const stats = await fetchJson('api.php?action=stats');
        document.getElementById('statWaiting').textContent = stats.waiting || 0;
        document.getElementById('statServing').textContent = stats.serving || 0;
        document.getElementById('statDone').textContent = stats.done || 0;
        document.getElementById('statNoShow').textContent = stats.noshow || 0;
        document.getElementById('statTotal').textContent = stats.total || 0;
      } catch (err) {}
    }

    async function fetchQueue() {
      try {
        const data = await fetchJson('api.php?action=get_queue');
        updateUI(data);
        updateStats();
      } catch (err) {}
    }

    async function callNext() {
      try {
        const data = await fetchJson('api.php?action=next', { method: 'POST' });
        showToast(`Now serving ${data.formatted} - ${data.name}`, 'success');
        fetchQueue();
      } catch (err) { showToast('Failed to call next', 'error'); }
    }



    async function loadPosData() {
      try {
        const [staffData, serviceData] = await Promise.all([
          fetchJson('api.php?action=get_staff'),
          fetchJson('api.php?action=get_services')
        ]);
        staffList = staffData.staff || [];
        serviceList = serviceData.services || [];
      } catch (err) {
        showToast('POS not ready. Admin: open /admin once', 'error');
      }
    }

    function closePosModal() {
      document.getElementById('posModal').style.display = 'none';
      currentSale = { token: null, staffId: null, items: [], paymentMethod: 'CASH' };
    }

    function setPayment(method) {
      currentSale.paymentMethod = method;
      ['CASH', 'UPI', 'CARD'].forEach((m) => {
        const btn = document.getElementById('pay' + m);
        btn.style.background = m === method ? '#22c55e' : '#f3f3f3';
        btn.style.color = m === method ? '#fff' : '#111';
      });
    }

    function renderPos() {
      const staffWrap = document.getElementById('staffOptions');
      staffWrap.innerHTML = staffList.map((st) => `
        <button onclick="selectStaff(${st.id})" style="border:none;border-radius:10px;padding:10px;cursor:pointer;font-size:18px;background:${currentSale.staffId === st.id ? '#6366f1' : '#f3f3f3'};color:${currentSale.staffId === st.id ? '#fff' : '#111'};">${iconNode(st.icon || 'user', 'w-4 h-4 inline')} ${st.name}</button>
      `).join('');

      const svcWrap = document.getElementById('serviceOptions');
      svcWrap.innerHTML = serviceList.map((svc) => `
        <button onclick="addServiceLine(${svc.id})" style="border:none;border-radius:10px;padding:10px;cursor:pointer;font-size:16px;background:#f3f3f3;">${iconNode(svc.icon || 'scissors', 'w-4 h-4 inline')} ${svc.name}<br><b>₹${Number(svc.price).toFixed(0)}</b></button>
      `).join('');

      const lines = document.getElementById('saleItems');
      if (currentSale.items.length === 0) {
        lines.innerHTML = iconNode('receipt', 'w-5 h-5 inline');
      } else {
        lines.innerHTML = currentSale.items.map((line, idx) => `
          <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f4f4f4;">
            <div>${iconNode(line.icon || 'circle','w-4 h-4 inline')} ${line.item_name}</div>
            <div><b>₹${Number(line.amount).toFixed(0)}</b> <button onclick="removeLine(${idx})" style="border:none;background:#fee2e2;border-radius:6px;padding:4px 8px;cursor:pointer;">${iconNode('trash-2','w-4 h-4 inline')}</button></div>
          </div>
        `).join('');
      }
      refreshIcons();

      const subtotal = currentSale.items.reduce((sum, item) => sum + Number(item.amount), 0);
      const discount = Number(document.getElementById('discountInput').value || 0);
      const tax = Number(document.getElementById('taxInput').value || 0);
      const total = Math.max(0, subtotal - discount + tax);
      document.getElementById('posTotals').textContent = `₹${total.toFixed(0)}`;
      setPayment(currentSale.paymentMethod);
      refreshIcons();
    }

    function selectStaff(staffId) {
      currentSale.staffId = staffId;
      renderPos();
    }

    function addServiceLine(serviceId) {
      const svc = serviceList.find((s) => Number(s.id) === Number(serviceId));
      if (!svc) return;
      currentSale.items.push({
        service_id: Number(svc.id),
        item_name: svc.name,
        qty: 1,
        unit_price: Number(svc.price),
        amount: Number(svc.price),
        icon: svc.icon || 'scissors'
      });
      renderPos();
    }

    function addOtherLine() {
      const amount = Number(document.getElementById('otherAmount').value || 0);
      if (amount <= 0) {
        showToast('Enter ₹', 'error');
        return;
      }
      currentSale.items.push({
        item_name: 'Other',
        qty: 1,
        unit_price: amount,
        amount,
        icon: 'plus'
      });
      document.getElementById('otherAmount').value = '';
      renderPos();
    }

    function removeLine(index) {
      currentSale.items.splice(index, 1);
      renderPos();
    }

    async function openPosModal(tokenId) {
      const data = await fetchJson('api.php?action=get_queue');
      const token = (data.serving || []).find((row) => Number(row.id) === Number(tokenId));
      if (!token) {
        showToast('Token missing', 'error');
        return;
      }
      currentSale = { token, staffId: null, items: [], paymentMethod: 'CASH' };
      document.getElementById('posTokenInfo').textContent = `${token.formatted} • ${token.name}`;
      document.getElementById('discountInput').value = '';
      document.getElementById('taxInput').value = '';
      document.getElementById('posModal').style.display = 'flex';
      renderPos();
    }

    async function saveSaleAndDone() {
      if (!currentSale.token) {
        showToast('No token', 'error');
        return;
      }
      if (!currentSale.staffId) {
        showToast('Select staff', 'error');
        return;
      }
      if (currentSale.items.length === 0) {
        showToast('Add service', 'error');
        return;
      }

      const discount = Number(document.getElementById('discountInput').value || 0);
      const tax = Number(document.getElementById('taxInput').value || 0);

      try {
        const saleRes = await fetchJson('api.php?action=create_sale', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            token_id: currentSale.token.id,
            staff_id: currentSale.staffId,
            items: currentSale.items,
            discount,
            tax,
            payment_method: currentSale.paymentMethod
          })
        });
        const saleData = saleRes;

        await fetchJson('api.php?action=done', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: currentSale.token.id, sale_id: saleData.sale_id })
        });

        showToast('Completed', 'success');
        closePosModal();
        fetchQueue();
      } catch (err) {
        showToast('Failed', 'error');
      }
    }

    async function markNoShow(id) {
      try {
        await fetchJson('api.php?action=noshow', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        showToast('Marked as no show', 'success');
        fetchQueue();
      } catch (err) { showToast('Failed', 'error'); }
    }

    async function backToQueue(id) {
      try {
        const data = await fetchJson('api.php?action=back_to_queue', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        showToast(`${data.name} sent back to queue`, 'success');
        fetchQueue();
      } catch (err) { showToast('Failed', 'error'); }
    }

    async function callSpecific(id) {
      try {
        const data = await fetchJson('api.php?action=call_specific', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        showToast(`Now serving ${data.formatted} - ${data.name}`, 'success');
        fetchQueue();
      } catch (err) { showToast('Failed to call customer', 'error'); }
    }

    async function addManualToken() {
      const name = document.getElementById('manualName').value.trim();
      const phone = document.getElementById('manualPhone').value.trim();

      if (!name || !phone) {
        showToast('<i data-lucide="user" class="inline w-4 h-4"></i> + <i data-lucide="phone" class="inline w-4 h-4"></i>', 'error');
        return;
      }

      try {
        const data = await fetchJson('api.php?action=create_token', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, phone })
        });
        showToast(`Token ${data.formatted} created for ${data.name}`, 'success');
        document.getElementById('manualName').value = '';
        document.getElementById('manualPhone').value = '';
        fetchQueue();
      } catch (err) { showToast('❌', 'error'); }
    }

    // Enter key for add form
    document.getElementById('manualPhone').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') addManualToken();
    });

    refreshIcons();
    loadPosData();
    fetchQueue();
    setInterval(fetchQueue, 3000);
  </script>
</body>
</html>
