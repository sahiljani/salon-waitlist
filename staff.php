<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
</head>
<body>
  <div class="header">
    <h1>Rivek Men's Salon</h1>
    <div style="font-size:13px; opacity:0.8; margin-top:2px;">Staff Dashboard</div>
    <div class="stats">
      <div class="stat">
        <div class="stat-value" id="statWaiting">0</div>
        <div class="stat-label">Waiting</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statServing">0</div>
        <div class="stat-label">Serving</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statDone">0</div>
        <div class="stat-label">Completed</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statNoShow">0</div>
        <div class="stat-label">No Show</div>
      </div>
      <div class="stat">
        <div class="stat-value" id="statTotal">0</div>
        <div class="stat-label">Total</div>
      </div>
    </div>
  </div>

  <div class="main">
    <!-- NEXT Button -->
    <div class="action-bar">
      <button class="btn-next" id="btnNext" onclick="callNext()">CALL NEXT CUSTOMER</button>
      <div class="chairs-label">
        <span>Chairs:</span>
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
      <h2>Now Serving</h2>
      <div class="serving-grid" id="servingGrid">
        <!-- Filled by JS -->
      </div>
    </div>

    <!-- Waiting Queue + Add Token -->
    <div class="bottom-section">
      <div class="card">
        <h2>Waiting Queue</h2>
        <div class="queue-list" id="queueList">
          <div class="empty-message">No customers waiting</div>
        </div>
      </div>

      <div class="card">
        <h2>Add Customer</h2>
        <div class="add-form-group">
          <label>Customer Name</label>
          <input type="text" id="manualName" placeholder="Enter full name" autocomplete="off">
        </div>
        <div class="add-form-group">
          <label>Phone Number</label>
          <input type="tel" id="manualPhone" placeholder="Enter phone number" autocomplete="off">
        </div>
        <button class="btn-add" onclick="addManualToken()">Add to Queue</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
    function showToast(message, type = '') {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.className = 'toast show ' + type;
      setTimeout(() => { toast.className = 'toast'; }, 3000);
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
                <span class="chair-number">Chair ${i + 1}</span>
                <span class="chair-status serving">Serving</span>
              </div>
              <div class="chair-token">${person.formatted}</div>
              <div class="chair-name">${person.name}</div>
              <div class="chair-phone">${person.phone || '-'}</div>
              <div class="chair-actions">
                <button class="btn-sm btn-done" onclick="markDone(${person.id})">COMPLETED</button>
                <button class="btn-sm btn-requeue" onclick="backToQueue(${person.id})">BACK TO QUEUE</button>
                <button class="btn-sm btn-noshow" onclick="markNoShow(${person.id})">NO SHOW</button>
              </div>
            </div>
          `;
        } else {
          dot.classList.remove('active');
          html += `
            <div class="chair-card">
              <div class="chair-header">
                <span class="chair-number">Chair ${i + 1}</span>
                <span class="chair-status available">Available</span>
              </div>
              <div class="chair-empty">Empty</div>
            </div>
          `;
        }
      }

      grid.innerHTML = html;
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
        queueList.innerHTML = '<div class="empty-message">No customers waiting</div>';
      } else {
        queueList.innerHTML = data.waiting.map((item, index) => `
          <div class="queue-item">
            <div class="token">${item.formatted}</div>
            <div class="info">
              <div class="name">${item.name}</div>
              <div class="phone">${item.phone || ''}</div>
            </div>
            <div class="position">#${index + 1}</div>
            <button class="btn-call" onclick="callSpecific(${item.id})">CALL</button>
          </div>
        `).join('');
      }
    }

    async function updateStats() {
      try {
        const res = await fetch('api.php?action=stats');
        const stats = await res.json();
        document.getElementById('statWaiting').textContent = stats.waiting || 0;
        document.getElementById('statServing').textContent = stats.serving || 0;
        document.getElementById('statDone').textContent = stats.done || 0;
        document.getElementById('statNoShow').textContent = stats.noshow || 0;
        document.getElementById('statTotal').textContent = stats.total || 0;
      } catch (err) {}
    }

    async function fetchQueue() {
      try {
        const res = await fetch('api.php?action=get_queue');
        const data = await res.json();
        updateUI(data);
        updateStats();
      } catch (err) {}
    }

    async function callNext() {
      try {
        const res = await fetch('api.php?action=next', { method: 'POST' });
        const data = await res.json();
        if (res.ok) {
          showToast(`Now serving ${data.formatted} - ${data.name}`, 'success');
          fetchQueue();
        } else {
          showToast(data.error, 'error');
        }
      } catch (err) { showToast('Failed to call next', 'error'); }
    }

    async function markDone(id) {
      try {
        const res = await fetch('api.php?action=done', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (res.ok) {
          showToast('Customer completed', 'success');
          fetchQueue();
        } else { showToast(data.error, 'error'); }
      } catch (err) { showToast('Failed', 'error'); }
    }

    async function markNoShow(id) {
      try {
        const res = await fetch('api.php?action=noshow', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (res.ok) {
          showToast('Marked as no show', 'success');
          fetchQueue();
        } else { showToast(data.error, 'error'); }
      } catch (err) { showToast('Failed', 'error'); }
    }

    async function backToQueue(id) {
      try {
        const res = await fetch('api.php?action=back_to_queue', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (res.ok) {
          showToast(`${data.name} sent back to queue`, 'success');
          fetchQueue();
        } else { showToast(data.error, 'error'); }
      } catch (err) { showToast('Failed', 'error'); }
    }

    async function callSpecific(id) {
      try {
        const res = await fetch('api.php?action=call_specific', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (res.ok) {
          showToast(`Now serving ${data.formatted} - ${data.name}`, 'success');
          fetchQueue();
        } else { showToast(data.error, 'error'); }
      } catch (err) { showToast('Failed to call customer', 'error'); }
    }

    async function addManualToken() {
      const name = document.getElementById('manualName').value.trim();
      const phone = document.getElementById('manualPhone').value.trim();

      if (!name || !phone) {
        showToast('Please enter name and phone', 'error');
        return;
      }

      try {
        const res = await fetch('api.php?action=create_token', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ name, phone })
        });
        const data = await res.json();
        if (res.ok) {
          showToast(`Token ${data.formatted} created for ${data.name}`, 'success');
          document.getElementById('manualName').value = '';
          document.getElementById('manualPhone').value = '';
          fetchQueue();
        } else { showToast(data.error, 'error'); }
      } catch (err) { showToast('Failed to create token', 'error'); }
    }

    // Enter key for add form
    document.getElementById('manualPhone').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') addManualToken();
    });

    fetchQueue();
    setInterval(fetchQueue, 3000);
  </script>
</body>
</html>
