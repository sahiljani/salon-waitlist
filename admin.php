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
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; background: #f4f6fb; color: #222; }
    .header { background: #111827; color: white; padding: 16px 20px; display:flex; justify-content:space-between; align-items:center; }
    .header a { color: white; text-decoration:none; opacity:0.9; margin-left:12px; }
    .header button { margin-left: 10px; }
    .wrap { max-width: 1100px; margin: 20px auto; padding: 0 12px; display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .card { background: white; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); padding: 14px; }
    h2 { margin: 0 0 10px; font-size: 18px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border-bottom: 1px solid #eee; padding: 8px; text-align: left; font-size: 14px; }
    .row { display:grid; grid-template-columns:1fr 90px 90px; gap:8px; margin-bottom:8px; }
    .row.staff { grid-template-columns:1fr 90px; }
    input { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; }
    button { border:none; border-radius:8px; padding:8px 10px; cursor:pointer; font-weight:600; }
    .btn-primary { background:#4f46e5; color:white; }
    .btn-danger { background:#ef4444; color:white; }
    .btn-muted { background:#e5e7eb; }
    .btn-dark { background:#111827; color:white; }
    .toast { position: fixed; right: 16px; bottom: 16px; background: #111; color:white; padding:10px 14px; border-radius:8px; display:none; }
    .modal-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; padding: 12px; }
    .modal { width: 100%; max-width: 720px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); padding: 16px; max-height: 92vh; overflow: auto; }
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
    @media (max-width: 900px) { .wrap { grid-template-columns:1fr; } }
    @media (max-width: 720px) { .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
  </style>
</head>
<body>
  <div class="header">
    <div><strong>Admin Panel</strong> · Staff & Services Management</div>
    <div>
      <button class="btn-dark" onclick="openDbToolsModal()">DB Tools</button>
      <a href="staff.php">Staff Dashboard</a>
    </div>
  </div>

  <div class="wrap">
    <div class="card">
      <h2>Staff CRUD</h2>
      <div class="row staff">
        <input id="staffName" placeholder="Staff name">
        <input id="staffIcon" placeholder="Icon" value="👤">
      </div>
      <button class="btn-primary" onclick="createStaff()">Add Staff</button>
      <table style="margin-top:10px;">
        <thead><tr><th>ID</th><th>Name</th><th>Icon</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="staffTable"></tbody>
      </table>
    </div>

    <div class="card">
      <h2>Services/Pricing CRUD</h2>
      <div class="row">
        <input id="serviceName" placeholder="Service name">
        <input id="servicePrice" type="number" step="0.01" min="0" placeholder="Price">
        <input id="serviceIcon" placeholder="Icon" value="✂️">
      </div>
      <button class="btn-primary" onclick="createService()">Add Service</button>
      <table style="margin-top:10px;">
        <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Icon</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody id="serviceTable"></tbody>
      </table>
    </div>
  </div>

  <div class="modal-backdrop" id="dbToolsBackdrop" onclick="closeDbToolsModal(event)">
    <div class="modal">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
        <h2 style="margin:0;">Database Tools</h2>
        <button class="btn-muted" onclick="forceCloseDbToolsModal()">Close</button>
      </div>

      <p style="margin:10px 0 0;color:#4b5563;font-size:14px;">
        Run safe migrations only when needed. This will create missing tables and seed defaults if empty.
      </p>

      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
        <button class="btn-primary" id="runMigrationBtn" onclick="runMigrations()">Run Migrations</button>
        <button class="btn-muted" onclick="loadDbStats()">Refresh Stats</button>
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
            <td>${s.id}</td>
            <td><input value="${s.name}" onchange="updateStaff(${s.id}, this.value, null)"></td>
            <td><input value="${s.icon || ''}" onchange="updateStaff(${s.id}, null, this.value)"></td>
            <td>${s.is_active == 1 ? 'Active' : 'Inactive'}</td>
            <td>
              <button class="btn-muted" onclick="toggleStaff(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? 'Deactivate' : 'Activate'}</button>
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
            <td>${s.id}</td>
            <td><input value="${s.name}" onchange="updateService(${s.id}, {name: this.value})"></td>
            <td><input type="number" min="0" step="0.01" value="${s.price}" onchange="updateService(${s.id}, {price: this.value})"></td>
            <td><input value="${s.icon || ''}" onchange="updateService(${s.id}, {icon: this.value})"></td>
            <td>${s.is_active == 1 ? 'Active' : 'Inactive'}</td>
            <td><button class="btn-muted" onclick="toggleService(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? 'Deactivate' : 'Activate'}</button></td>
          </tr>
        `).join('');
      } catch (e) { toast(e.message); }
    }

    async function createStaff() {
      try {
        await api('admin_create_staff', {
          name: document.getElementById('staffName').value,
          icon: document.getElementById('staffIcon').value
        }, 'POST');
        document.getElementById('staffName').value = '';
        toast('Staff created');
        loadStaff();
      } catch (e) { toast(e.message); }
    }

    async function updateStaff(id, name, icon) {
      try {
        const payload = { id };
        if (name !== null) payload.name = name;
        if (icon !== null) payload.icon = icon;
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
          price: document.getElementById('servicePrice').value,
          icon: document.getElementById('serviceIcon').value
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
        <div class="stat-box"><div class="v">${tokenStats.waiting || 0}</div><div class="k">Waiting</div></div>
        <div class="stat-box"><div class="v">${tokenStats.serving || 0}</div><div class="k">Serving</div></div>
        <div class="stat-box"><div class="v">${tokenStats.done || 0}</div><div class="k">Done</div></div>
        <div class="stat-box"><div class="v">${tokenStats.noshow || 0}</div><div class="k">No-show</div></div>
      `;

      const rows = Object.keys(tables).map((name) => {
        const row = tables[name];
        return `<tr><td>${name}</td><td>${row.exists ? 'Yes' : 'No'}</td><td>${row.rows || 0}</td></tr>`;
      }).join('');
      document.getElementById('dbTableStats').innerHTML = rows || '<tr><td colspan="3">No data</td></tr>';
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
  </script>
</body>
</html>
