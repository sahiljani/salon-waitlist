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
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-100 text-slate-900 min-h-screen">
  <header class="bg-slate-950 text-white px-4 py-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
      <div class="text-xl font-bold flex items-center gap-2"><i data-lucide="shield-check"></i>Admin Panel</div>
      <div class="flex items-center gap-4 text-sm sm:text-base">
        <a href="staff.php" class="bg-indigo-600 hover:bg-indigo-500 px-4 py-2 rounded-xl font-semibold inline-flex items-center gap-2"><i data-lucide="users"></i>Staff Screen</a>
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <section class="bg-white rounded-2xl shadow p-4 sm:p-6">
      <h2 class="text-3xl font-black mb-4 inline-flex items-center gap-2"><i data-lucide="users"></i>Staff</h2>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input id="staffName" placeholder="Name" class="col-span-2 rounded-xl border border-slate-300 p-3 text-lg">
        <input id="staffIcon" placeholder="user" value="user" class="rounded-xl border border-slate-300 p-3 text-lg text-center">
      </div>
      <button onclick="createStaff()" class="mt-3 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white text-xl font-bold px-6 py-3 rounded-xl inline-flex items-center gap-2"><i data-lucide="user-plus"></i>Add Staff</button>

      <div class="overflow-x-auto mt-4">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2">#</th><th class="py-2">Name</th><th class="py-2">Icon</th><th class="py-2">Status</th><th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody id="staffTable"></tbody>
        </table>
      </div>
    </section>

    <section class="bg-white rounded-2xl shadow p-4 sm:p-6">
      <h2 class="text-3xl font-black mb-4 inline-flex items-center gap-2"><i data-lucide="scissors"></i>Services & Pricing</h2>
      <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <input id="serviceName" placeholder="Service" class="sm:col-span-2 rounded-xl border border-slate-300 p-3 text-lg">
        <input id="servicePrice" type="number" step="0.01" min="0" placeholder="Price" class="rounded-xl border border-slate-300 p-3 text-lg">
        <input id="serviceIcon" placeholder="scissors" value="scissors" class="rounded-xl border border-slate-300 p-3 text-lg text-center">
      </div>
      <button onclick="createService()" class="mt-3 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white text-xl font-bold px-6 py-3 rounded-xl inline-flex items-center gap-2"><i data-lucide="plus"></i>Add Service</button>

      <div class="overflow-x-auto mt-4">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b">
              <th class="py-2">#</th><th class="py-2">Service</th><th class="py-2">Price</th><th class="py-2">Icon</th><th class="py-2">Status</th><th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody id="serviceTable"></tbody>
        </table>
      </div>
    </section>
  </main>

  <div id="toast" class="hidden fixed bottom-4 right-4 bg-slate-950 text-white px-4 py-3 rounded-xl text-lg font-semibold"></div>

  <script>
    const ICON_MAP = {
      '👤': 'user', '🧔': 'user', '👨': 'user', '👩': 'user',
      '✂️': 'scissors', '🪒': 'scissors', '🚿': 'droplets', '🧴': 'sparkles'
    };

    function normalizeIcon(value, fallback = 'circle') {
      const v = (value || '').trim();
      if (!v) return fallback;
      return ICON_MAP[v] || v;
    }

    function iconNode(name, cls = 'w-4 h-4') {
      return `<i data-lucide="${normalizeIcon(name)}" class="${cls}"></i>`;
    }

    function refreshIcons() {
      if (window.lucide) window.lucide.createIcons();
    }

    function toast(msg) {
      const t = document.getElementById('toast');
      t.textContent = msg;
      t.classList.remove('hidden');
      setTimeout(() => t.classList.add('hidden'), 2200);
    }

    async function parseJsonSafe(res) {
      const raw = await res.text();
      if (!raw) return {};
      try {
        return JSON.parse(raw);
      } catch (e) {
        return { error: 'Invalid server response', raw };
      }
    }

    async function api(action, body = null, method = 'GET') {
      const opts = { method, credentials: 'same-origin' };
      if (body) {
        opts.headers = { 'Content-Type': 'application/json' };
        opts.body = JSON.stringify(body);
      }
      const res = await fetch('api.php?action=' + action, opts);
      const data = await parseJsonSafe(res);
      if (!res.ok) throw new Error(data.error || 'Request failed');
      if (data.error) throw new Error(data.error);
      return data;
    }

    async function loadStaff() {
      try {
        const data = await api('admin_list_staff');
        const tbody = document.getElementById('staffTable');
        tbody.innerHTML = (data.staff || []).map((s) => `
          <tr class="border-b last:border-0">
            <td class="py-2">${s.id}</td>
            <td class="py-2"><input value="${s.name}" onchange="updateStaff(${s.id}, this.value, null)" class="w-full rounded-lg border border-slate-300 p-2"></td>
            <td class="py-2">
              <div class="flex items-center gap-2">
                <input value="${normalizeIcon(s.icon || 'user')}" onchange="updateStaff(${s.id}, null, this.value)" class="w-24 rounded-lg border border-slate-300 p-2 text-center">
                ${iconNode(s.icon || 'user', 'w-5 h-5')}
              </div>
            </td>
            <td class="py-2">${s.is_active == 1 ? iconNode('check-circle','w-5 h-5 text-green-600') : iconNode('circle','w-5 h-5 text-slate-400')}</td>
            <td class="py-2">
              <button class="bg-slate-200 hover:bg-slate-300 px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-2" onclick="toggleStaff(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? iconNode('user-x') : iconNode('user-check')}</button>
            </td>
          </tr>
        `).join('');
        refreshIcons();
      } catch (e) { toast(e.message); }
    }

    async function loadServices() {
      try {
        const data = await api('admin_list_services');
        const tbody = document.getElementById('serviceTable');
        tbody.innerHTML = (data.services || []).map((s) => `
          <tr class="border-b last:border-0">
            <td class="py-2">${s.id}</td>
            <td class="py-2"><input value="${s.name}" onchange="updateService(${s.id}, {name: this.value})" class="w-full rounded-lg border border-slate-300 p-2"></td>
            <td class="py-2"><input type="number" min="0" step="0.01" value="${s.price}" onchange="updateService(${s.id}, {price: this.value})" class="w-28 rounded-lg border border-slate-300 p-2"></td>
            <td class="py-2">
              <div class="flex items-center gap-2">
                <input value="${normalizeIcon(s.icon || 'scissors')}" onchange="updateService(${s.id}, {icon: this.value})" class="w-24 rounded-lg border border-slate-300 p-2 text-center">
                ${iconNode(s.icon || 'scissors', 'w-5 h-5')}
              </div>
            </td>
            <td class="py-2">${s.is_active == 1 ? iconNode('check-circle','w-5 h-5 text-green-600') : iconNode('circle','w-5 h-5 text-slate-400')}</td>
            <td class="py-2">
              <button class="bg-slate-200 hover:bg-slate-300 px-3 py-2 rounded-lg font-semibold inline-flex items-center gap-2" onclick="toggleService(${s.id}, ${s.is_active == 1 ? 0 : 1})">${s.is_active == 1 ? iconNode('x-circle') : iconNode('check-circle')}</button>
            </td>
          </tr>
        `).join('');
        refreshIcons();
      } catch (e) { toast(e.message); }
    }

    async function createStaff() {
      try {
        await api('admin_create_staff', {
          name: document.getElementById('staffName').value,
          icon: normalizeIcon(document.getElementById('staffIcon').value, 'user')
        }, 'POST');
        document.getElementById('staffName').value = '';
        toast('Staff added');
        loadStaff();
      } catch (e) { toast(e.message); }
    }

    async function updateStaff(id, name, icon) {
      try {
        const payload = { id };
        if (name !== null) payload.name = name;
        if (icon !== null) payload.icon = normalizeIcon(icon, 'user');
        await api('admin_update_staff', payload, 'POST');
        toast('Saved');
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
          icon: normalizeIcon(document.getElementById('serviceIcon').value, 'scissors')
        }, 'POST');
        document.getElementById('serviceName').value = '';
        document.getElementById('servicePrice').value = '';
        toast('Service added');
        loadServices();
      } catch (e) { toast(e.message); }
    }

    async function updateService(id, update) {
      try {
        if (update.icon) update.icon = normalizeIcon(update.icon, 'scissors');
        await api('admin_update_service', Object.assign({ id }, update), 'POST');
        toast('Saved');
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
    refreshIcons();
  </script>
</body>
</html>
