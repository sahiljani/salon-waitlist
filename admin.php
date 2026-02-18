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
    .toast { position: fixed; right: 16px; bottom: 16px; background: #111; color:white; padding:10px 14px; border-radius:8px; display:none; }
    @media (max-width: 900px) { .wrap { grid-template-columns:1fr; } }
  </style>
</head>
<body>
  <div class="header">
    <div><strong>Admin Panel</strong> · Staff & Services Management</div>
    <div>
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

    loadStaff();
    loadServices();
  </script>
</body>
</html>
