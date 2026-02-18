<?php
require_once 'auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Rivek Men's Salon - Admin</title>
  <style>
    :root {
      --bg: #f3f5fb;
      --card: #ffffff;
      --text: #1f2937;
      --muted: #6b7280;
      --line: #e5e7eb;
      --primary: #4f46e5;
      --primary-hover: #4338ca;
      --header: #111827;
      --header-accent: #1e293b;
    }
    * { box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; background: radial-gradient(circle at top, #eef2ff 0%, var(--bg) 35%, var(--bg) 100%); color: var(--text); }
    .header {
      background: linear-gradient(120deg, var(--header) 0%, var(--header-accent) 100%);
      color: white;
      padding: 14px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 14px;
      position: sticky;
      top: 0;
      z-index: 10;
      box-shadow: 0 8px 24px rgba(17,24,39,0.25);
    }
    .header-title { font-weight: 700; letter-spacing: 0.2px; }
    .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
    .header a { color: white; text-decoration:none; opacity:0.95; padding: 8px 10px; border-radius: 10px; transition: background 0.2s; }
    .header a:hover { background: rgba(255,255,255,0.12); }
    .header-icon-btn {
      width: 36px;
      height: 36px;
      border: 1px solid rgba(255,255,255,0.35);
      border-radius: 999px;
      background: rgba(255,255,255,0.15);
      color: #fff;
      font-size: 16px;
      line-height: 1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
    }
    .header-icon-btn:hover { background: rgba(255,255,255,0.25); transform: translateY(-1px); }
    .wrap { max-width: 1160px; margin: 24px auto; padding: 0 14px 20px; display:grid; grid-template-columns:1fr 1fr; gap:18px; }
    .card { background: var(--card); border-radius: 16px; box-shadow: 0 10px 30px rgba(15,23,42,0.08); padding: 16px; border: 1px solid #edf1f7; }
    .card-wide { grid-column: 1 / -1; }
    h2 { margin: 0 0 12px; font-size: 29px; }
    .table-wrap { overflow-x: auto; margin-top: 12px; }
    table { width:100%; border-collapse: collapse; min-width: 520px; }
    th, td { border-bottom: 1px solid #edf1f7; padding: 10px 8px; text-align: left; font-size: 14px; vertical-align: middle; }
    th { color: #334155; font-size: 13px; text-transform: uppercase; letter-spacing: 0.4px; font-weight: 700; }
    .row { display:grid; grid-template-columns:1fr 130px; gap:10px; margin-bottom:10px; }
    .row.staff { grid-template-columns:1fr; }
    input { width:100%; padding:11px 12px; border:1px solid var(--line); border-radius:10px; font-size: 14px; }
    input:focus { outline: none; border-color: #a5b4fc; box-shadow: 0 0 0 3px rgba(79,70,229,0.12); }
    button { border:none; border-radius:10px; padding:9px 12px; cursor:pointer; font-weight:700; transition: transform 0.15s, opacity 0.2s, background 0.2s; }
    button:hover { transform: translateY(-1px); }
    button:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
    .btn-primary { background:var(--primary); color:white; }
    .btn-primary:hover { background: var(--primary-hover); }
    .btn-danger { background:#ef4444; color:white; }
    .btn-muted { background:#e5e7eb; color: #111827; }
    .btn-dark { background:#111827; color:white; }
    .toast { position: fixed; right: 16px; bottom: 16px; background: #111; color:white; padding:10px 14px; border-radius:8px; display:none; }
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 12px; }
    .modal { width: 100%; max-width: 720px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); padding: 16px; max-height: 92vh; overflow: auto; }
    .help-modal { max-width: 860px; border-radius: 16px; }
    .help-title { margin: 0; font-size: 34px; }
    .help-subtitle { margin: 10px 0 14px; font-size: 18px; color: #475569; }
    .help-grid { display:grid; grid-template-columns:1fr; gap:10px; }
    .help-row { border:1px solid #e2e8f0; border-radius:12px; padding:12px; background:#f8fafc; display:flex; align-items:center; gap:16px; }
    .help-keys { display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:240px; }
    .help-plus { color:#64748b; font-weight:800; font-size:20px; }
    .keyimg-wrap { display:inline-flex; align-items:center; gap:4px; border:1px solid #cbd5e1; border-radius:8px; padding:2px 6px; background:#0f172a; color:#fff; font-weight:800; font-size:14px; min-height:34px; }
    .keyimg { width:26px; height:26px; display:block; }
    .help-text { font-size:23px; font-weight:800; color:#0f172a; }
    .help-note { margin-top:12px; padding:10px; border:1px solid #dbeafe; background:#eff6ff; border-radius:10px; color:#1e3a8a; font-size:16px; }
    .stats-grid { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 8px; margin-top: 10px; }
    .stat-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px; text-align: center; }
    .stat-box .v { font-size: 20px; font-weight: 800; }
    .stat-box .k { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .table-stats { margin-top: 12px; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .table-stats table { width: 100%; border-collapse: collapse; }
    .table-stats th, .table-stats td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    .table-stats tr:last-child td { border-bottom: none; }
    .steps-box { margin-top: 12px; padding: 10px; background: #f8fafc; border-radius: 10px; border: 1px solid #e5e7eb; font-size: 13px; }
    .steps-box div { margin-bottom: 6px; }
    .steps-box div:last-child { margin-bottom: 0; }
    .filters { display:grid; grid-template-columns:200px 220px auto; gap:10px; align-items:end; margin-bottom:12px; }
    .summary-row { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px; }
    .pill { background:#eef2ff; color:#312e81; border:1px solid #c7d2fe; border-radius:999px; padding:7px 12px; font-size:13px; font-weight:700; }
    @media (max-width: 980px) { .wrap { grid-template-columns:1fr; } }
    @media (max-width: 720px) {
      .header { flex-direction: column; align-items: stretch; }
      .header-actions { justify-content: flex-start; }
      .header a, .header .btn-dark { width: 100%; text-align: center; }
      .header-icon-btn { width: 100%; border-radius: 10px; height: auto; padding: 10px 12px; font-size: 14px; }
      .help-title { font-size: 28px; }
      .help-text { font-size: 19px; }
      .help-row { flex-direction: column; align-items: flex-start; }
      .help-keys { min-width: 0; }
      .row { grid-template-columns:1fr; }
      .filters { grid-template-columns:1fr; }
      .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .table-wrap { overflow: visible; }
      table { min-width: 0; border-collapse: separate; border-spacing: 0 10px; }
      thead { display: none; }
      tbody tr {
        display: block;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 10px;
      }
      tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        border-bottom: 1px dashed #e2e8f0;
        padding: 8px 2px;
      }
      tbody td:last-child { border-bottom: none; }
      tbody td::before {
        content: attr(data-label);
        color: var(--muted);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
      }
      tbody td input { width: 62%; }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="header-title"><strong>Admin Panel 👑</strong> · Staff & Services Management 🛠️</div>
    <div class="header-actions">
      <button class="btn-dark" onclick="openDbToolsModal()">DB Tools 🗄️</button>
      <a href="staff.php">Staff Dashboard 👥</a>
      <a href="display.php">Queue Display 📺</a>
      <button type="button" class="header-icon-btn" onclick="openAdminInfoModal()" title="Staff Shortcuts">👥</button>
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div class="wrap">
    <div class="card">
      <h2>Staff CRUD 👥</h2>
      <div class="row staff">
        <input id="staffName" placeholder="Staff name">
      </div>
      <button class="btn-primary" onclick="createStaff()">Add Staff ➕</button>
      <div class="table-wrap">
        <table>
          <thead><tr><th>ID</th><th>Name</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="staffTable"></tbody>
        </table>
      </div>
    </div>

    <div class="card">
      <h2>Services/Pricing CRUD ✂️</h2>
      <div class="row">
        <input id="serviceName" placeholder="Service name">
        <input id="servicePrice" type="number" step="0.01" min="0" placeholder="Price">
      </div>
      <button class="btn-primary" onclick="createService()">Add Service ➕</button>
      <div class="table-wrap">
        <table>
          <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody id="serviceTable"></tbody>
        </table>
      </div>
    </div>

    <div class="card card-wide">
      <h2>Sales Report</h2>
      <div class="filters">
        <div>
          <label for="salesDate" style="display:block;font-size:12px;color:#6b7280;margin-bottom:4px;">Date</label>
          <input type="date" id="salesDate">
        </div>
        <div>
          <label for="salesStaff" style="display:block;font-size:12px;color:#6b7280;margin-bottom:4px;">Staff</label>
          <select id="salesStaff" style="width:100%;padding:11px 12px;border:1px solid var(--line);border-radius:10px;font-size:14px;">
            <option value="0">All Staff</option>
          </select>
        </div>
        <div>
          <button class="btn-primary" onclick="loadSalesReport()">Apply Filter</button>
        </div>
      </div>
      <div class="summary-row">
        <div class="pill" id="salesBills">Bills: 0</div>
        <div class="pill" id="salesTotal">Total: ₹0</div>
        <div class="pill" id="salesDiscount">Discount: ₹0</div>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>ID</th><th>Date</th><th>Token</th><th>Customer</th><th>Staff</th><th>Payment</th><th>Total</th></tr>
          </thead>
          <tbody id="salesTable">
            <tr><td colspan="7">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal-backdrop" id="dbToolsBackdrop" onclick="closeDbToolsModal(event)">
    <div class="modal">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
        <h2 style="margin:0;">Database Tools 🗄️</h2>
        <button class="btn-muted" onclick="forceCloseDbToolsModal()">Close ✖️</button>
      </div>

      <p style="margin:10px 0 0;color:#4b5563;font-size:14px;">
        Run safe migrations only when needed. This will create missing tables and seed defaults if empty.
      </p>

      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
        <button class="btn-primary" id="runMigrationBtn" onclick="runMigrations()">Run Migrations ▶️</button>
        <button class="btn-muted" onclick="loadDbStats()">Refresh Stats 🔄</button>
      </div>

      <div id="dbToolsStatus" style="margin-top:10px;font-size:13px;color:#4b5563;">Loading stats...</div>

      <div class="stats-grid" id="tokenStatsGrid"></div>

      <div class="table-stats">
        <table>
          <thead>
            <tr><th>Table</th><th>Exists</th><th>Rows</th></tr>
          </thead>
          <tbody id="dbTableStats"></tbody>
        </table>
      </div>

      <div class="steps-box" id="migrationSteps" style="display:none;"></div>
    </div>
  </div>

  <div class="modal-backdrop" id="adminInfoBackdrop" onclick="closeAdminInfoModal(event)">
    <div class="modal help-modal">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
        <h2 class="help-title">Staff Keyboard Guide</h2>
        <button class="btn-muted" onclick="forceCloseAdminInfoModal()">Close</button>
      </div>
      <p class="help-subtitle">Clear key mapping for the staff workflow. Share this with team members.</p>

      <div class="help-grid">
        <div class="help-row">
          <div class="help-keys">
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/ctrl.svg" alt="Ctrl">Ctrl</span>
            <span class="help-plus">+</span>
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/backspace.svg" alt="Backspace">Backspace</span>
          </div>
          <div class="help-text">Add Walk-in</div>
        </div>
        <div class="help-row">
          <div class="help-keys">
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/space.svg" alt="Space">Space</span>
          </div>
          <div class="help-text">Call Next</div>
        </div>
        <div class="help-row">
          <div class="help-keys">
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/alt.svg" alt="Alt">Alt</span>
            <span class="help-plus">+</span>
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/1.svg" alt="1">1-9</span>
          </div>
          <div class="help-text">In POS modal: Select Staff by order</div>
        </div>
        <div class="help-row">
          <div class="help-keys">
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/shift.svg" alt="Shift">Shift</span>
            <span class="help-plus">+</span>
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/1.svg" alt="1">1-9</span>
          </div>
          <div class="help-text">In POS modal: Add Service by order</div>
        </div>
        <div class="help-row">
          <div class="help-keys">
            <span class="keyimg-wrap"><img class="keyimg" src="https://raw.githubusercontent.com/IvanMathy/Keymages/main/out/windows/dark/large/tab.svg" alt="Tab">Tab</span>
          </div>
          <div class="help-text">In POS modal: Next field (Shift + Tab = previous)</div>
        </div>
      </div>
      <div class="help-note">
        Location: Staff dashboard page. The modal-only keys work only when POS modal is open.
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    function toast(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.style.display = 'block';
      setTimeout(() => t.style.display = 'none', 2000);
    }

    async function api(action, body = null, method = 'GET') {
      const opts = { method };
      if (body) {
        opts.headers = { 'Content-Type': 'application/json' };
        opts.body = JSON.stringify(body);
      }
      const res = await fetch('api.php?action=' + action, opts);
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Request failed');
      return data;
    }

    async function loadStaff() {
      try {
        const data = await api('admin_list_staff');
        const tbody = document.getElementById('staffTable');
        tbody.innerHTML = data.staff.map((s) => `
          <tr>
            <td data-label="ID">${s.id}</td>
            <td data-label="Name"><input value="${s.name}" onchange="updateStaff(${s.id}, this.value)"></td>
            <td data-label="Status">${s.is_active == 1 ? 'Active' : 'Inactive'}</td>
            <td data-label="Actions">
              <button class="btn-muted" onclick="toggleStaff(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? 'Deactivate ⏸️' : 'Activate ▶️'}</button>
            </td>
          </tr>
        `).join('');
      } catch (e) { toast(e.message); }
    }

    async function loadServices() {
      try {
        const data = await api('admin_list_services');
        const tbody = document.getElementById('serviceTable');
        tbody.innerHTML = data.services.map((s) => `
          <tr>
            <td data-label="ID">${s.id}</td>
            <td data-label="Name"><input value="${s.name}" onchange="updateService(${s.id}, {name: this.value})"></td>
            <td data-label="Price"><input type="number" min="0" step="0.01" value="${s.price}" onchange="updateService(${s.id}, {price: this.value})"></td>
            <td data-label="Status">${s.is_active == 1 ? 'Active' : 'Inactive'}</td>
            <td data-label="Actions"><button class="btn-muted" onclick="toggleService(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? 'Deactivate ⏸️' : 'Activate ▶️'}</button></td>
          </tr>
        `).join('');
      } catch (e) { toast(e.message); }
    }

    async function loadSalesStaffFilter() {
      try {
        const data = await api('admin_list_staff');
        const staffSelect = document.getElementById('salesStaff');
        const options = ['<option value="0">All Staff</option>'].concat(
          (data.staff || []).map((s) => `<option value="${s.id}">${s.name}</option>`)
        );
        staffSelect.innerHTML = options.join('');
      } catch (e) {
        toast(e.message);
      }
    }

    async function loadSalesReport() {
      try {
        const date = document.getElementById('salesDate').value;
        const staffId = document.getElementById('salesStaff').value || '0';
        const q = new URLSearchParams();
        if (date) q.set('date', date);
        q.set('staff_id', staffId);

        const data = await api('admin_sales_report&' + q.toString());

        const summary = data.summary || {};
        document.getElementById('salesBills').textContent = `Bills: ${summary.bills || 0}`;
        document.getElementById('salesTotal').textContent = `Total: ₹${Number(summary.total_sales || 0).toFixed(2)}`;
        document.getElementById('salesDiscount').textContent = `Discount: ₹${Number(summary.total_discount || 0).toFixed(2)}`;

        const rows = data.rows || [];
        const tbody = document.getElementById('salesTable');
        tbody.innerHTML = rows.length ? rows.map((r) => `
          <tr>
            <td data-label="ID">${r.id}</td>
            <td data-label="Date">${r.sale_date}</td>
            <td data-label="Token">T-${String(r.token_no).padStart(3, '0')}</td>
            <td data-label="Customer">${r.customer_name}</td>
            <td data-label="Staff">${r.staff_name}</td>
            <td data-label="Payment">${r.payment_method}</td>
            <td data-label="Total">₹${Number(r.total).toFixed(2)}</td>
          </tr>
        `).join('') : '<tr><td colspan="7">No sales found for selected filters</td></tr>';
      } catch (e) {
        toast(e.message);
      }
    }

    async function createStaff() {
      try {
        await api('admin_create_staff', {
          name: document.getElementById('staffName').value
        }, 'POST');
        document.getElementById('staffName').value = '';
        toast('Staff created');
        loadStaff();
      } catch (e) { toast(e.message); }
    }

    async function updateStaff(id, name) {
      try {
        const payload = { id };
        if (name !== null) payload.name = name;
        await api('admin_update_staff', payload, 'POST');
        toast('Updated');
      } catch (e) { toast(e.message); }
    }

    async function toggleStaff(id, active) {
      try {
        await api('admin_update_staff', { id, is_active: active }, 'POST');
        toast('Saved');
        loadStaff();
      } catch (e) { toast(e.message); }
    }

    async function createService() {
      try {
        await api('admin_create_service', {
          name: document.getElementById('serviceName').value,
          price: document.getElementById('servicePrice').value
        }, 'POST');
        document.getElementById('serviceName').value = '';
        document.getElementById('servicePrice').value = '';
        toast('Service created');
        loadServices();
      } catch (e) { toast(e.message); }
    }

    async function updateService(id, update) {
      try {
        await api('admin_update_service', Object.assign({ id }, update), 'POST');
        toast('Updated');
      } catch (e) { toast(e.message); }
    }

    async function toggleService(id, active) {
      try {
        await api('admin_update_service', { id, is_active: active }, 'POST');
        toast('Saved');
        loadServices();
      } catch (e) { toast(e.message); }
    }

    function renderDbTools(data, message = '') {
      const tokenStats = data.today_tokens || {};
      const db = data.db || {};
      const tables = db.tables || {};
      const summary = db.summary || {};

      document.getElementById('dbToolsStatus').textContent = message || `Tables ready: ${summary.existing || 0}/${summary.total || 0} · Missing: ${summary.missing || 0}`;

      const tokenGrid = document.getElementById('tokenStatsGrid');
      tokenGrid.innerHTML = `
        <div class="stat-box"><div class="v">${tokenStats.total || 0}</div><div class="k">Total</div></div>
        <div class="stat-box"><div class="v">${tokenStats.waiting || 0}</div><div class="k">Waiting ⏳</div></div>
        <div class="stat-box"><div class="v">${tokenStats.serving || 0}</div><div class="k">Serving 🪑</div></div>
        <div class="stat-box"><div class="v">${tokenStats.done || 0}</div><div class="k">Done ✅</div></div>
        <div class="stat-box"><div class="v">${tokenStats.noshow || 0}</div><div class="k">No-show 🚫</div></div>
      `;

      const rows = Object.keys(tables).map((name) => {
        const row = tables[name];
        return `<tr><td>${name}</td><td>${row.exists ? 'Yes' : 'No'}</td><td>${row.rows || 0}</td></tr>`;
      }).join('');
      document.getElementById('dbTableStats').innerHTML = rows || '<tr><td colspan="3">No data 📭</td></tr>';
    }

    async function loadDbStats() {
      try {
        document.getElementById('dbToolsStatus').textContent = 'Loading stats...';
        const data = await api('admin_db_stats');
        renderDbTools(data);
      } catch (e) {
        document.getElementById('dbToolsStatus').textContent = e.message;
      }
    }

    function openDbToolsModal() {
      document.getElementById('dbToolsBackdrop').style.display = 'flex';
      document.getElementById('migrationSteps').style.display = 'none';
      document.getElementById('migrationSteps').innerHTML = '';
      loadDbStats();
    }

    function closeDbToolsModal(event) {
      if (event.target.id === 'dbToolsBackdrop') {
        forceCloseDbToolsModal();
      }
    }

    function forceCloseDbToolsModal() {
      document.getElementById('dbToolsBackdrop').style.display = 'none';
    }

    function openAdminInfoModal() {
      document.getElementById('adminInfoBackdrop').style.display = 'flex';
    }

    function closeAdminInfoModal(event) {
      if (event.target.id === 'adminInfoBackdrop') {
        forceCloseAdminInfoModal();
      }
    }

    function forceCloseAdminInfoModal() {
      document.getElementById('adminInfoBackdrop').style.display = 'none';
    }

    async function runMigrations() {
      if (!confirm('Run database migrations now?')) return;
      const btn = document.getElementById('runMigrationBtn');
      btn.disabled = true;
      btn.textContent = 'Running...';
      try {
        const data = await api('admin_run_migrations', {}, 'POST');
        renderDbTools(data, 'Migration completed successfully');
        const stepsBox = document.getElementById('migrationSteps');
        const steps = (data.steps || []).map((s) => `<div>${s}</div>`).join('');
        stepsBox.innerHTML = steps || '<div>No migration steps returned</div>';
        stepsBox.style.display = 'block';
        toast('Migration complete');
      } catch (e) {
        toast(e.message);
      } finally {
        btn.disabled = false;
        btn.textContent = 'Run Migrations';
      }
    }

    loadStaff();
    loadServices();
    document.getElementById('salesDate').value = new Date().toISOString().slice(0, 10);
    loadSalesStaffFilter().then(loadSalesReport);
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') forceCloseAdminInfoModal();
    });
  </script>
</body>
</html>
