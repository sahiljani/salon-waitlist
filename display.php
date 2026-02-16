<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rivek Men's Salon - Queue</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: #f0f2f5;
      height: 100vh;
      color: #333;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .clock {
      position: fixed;
      top: 12px;
      right: 25px;
      font-size: 22px;
      color: rgba(0,0,0,0.35);
      letter-spacing: 2px;
      z-index: 10;
      font-weight: 600;
      font-variant-numeric: tabular-nums;
    }

    .clock .seconds {
      font-size: 14px;
      color: rgba(0,0,0,0.2);
      vertical-align: top;
    }

    .clock .colon-blink {
      animation: blink 1s step-end infinite;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.2; }
    }

    /* Salon brand top-left */
    .brand {
      position: fixed;
      top: 12px;
      left: 25px;
      z-index: 10;
    }

    .brand-name {
      font-size: 20px;
      font-weight: 700;
      color: #764ba2;
      letter-spacing: 1px;
    }

    .brand-sub {
      font-size: 10px;
      color: rgba(0,0,0,0.3);
      letter-spacing: 2px;
      text-transform: uppercase;
    }

    /* Top: Now Serving */
    .serving-bar {
      padding: 12px 20px 10px;
      padding-top: 50px;
      flex-shrink: 0;
    }

    .serving-label {
      font-size: 18px;
      text-transform: uppercase;
      letter-spacing: 5px;
      color: rgba(0,0,0,0.3);
      text-align: center;
      margin-bottom: 10px;
    }

    .serving-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
    }

    .chair {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 14px;
      padding: 16px 10px;
      text-align: center;
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .chair.empty {
      background: white;
      border: 2px dashed #ddd;
      box-shadow: none;
      color: #ccc;
    }

    .chair .token {
      font-size: 36px;
      font-weight: 800;
      letter-spacing: 2px;
    }

    .chair .name {
      font-size: 18px;
      opacity: 0.9;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .chair.empty .empty-text {
      color: #ccc;
      font-size: 16px;
      padding: 10px 0;
    }

    .chair.pop-in {
      animation: popIn 0.4s ease-out;
    }

    @keyframes popIn {
      0% { transform: scale(0.85); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }

    /* Divider */
    .divider {
      height: 1px;
      background: rgba(0,0,0,0.06);
      margin: 0 20px;
      flex-shrink: 0;
    }

    /* Queue Section */
    .queue-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 10px 20px 10px;
      overflow: hidden;
      min-height: 0;
    }

    .queue-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 8px;
      flex-shrink: 0;
    }

    .queue-label {
      font-size: 18px;
      text-transform: uppercase;
      letter-spacing: 5px;
      color: rgba(0,0,0,0.3);
    }

    .queue-count {
      font-size: 16px;
      color: rgba(0,0,0,0.25);
    }

    /* Static first 5 */
    .static-list {
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .q-item {
      display: flex;
      align-items: center;
      background: white;
      border-radius: 10px;
      padding: 10px 14px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
    }

    .q-item.next-up {
      background: linear-gradient(135deg, rgba(102,126,234,0.08) 0%, rgba(118,75,162,0.08) 100%);
      border: 1px solid rgba(102, 126, 234, 0.25);
      box-shadow: 0 2px 8px rgba(102,126,234,0.1);
    }

    .q-item .pos {
      font-size: 22px;
      font-weight: 700;
      color: #667eea;
      width: 45px;
      text-align: center;
    }

    .q-item .tok {
      font-size: 30px;
      font-weight: 700;
      width: 130px;
      color: #333;
    }

    .q-item .nam {
      flex: 1;
      font-size: 24px;
      color: #555;
    }

    /* Scrolling section divider */
    .scroll-divider {
      height: 1px;
      background: rgba(0,0,0,0.06);
      margin: 8px 0;
      flex-shrink: 0;
    }

    .upcoming-header {
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 3px;
      color: rgba(0,0,0,0.2);
      margin-bottom: 6px;
      flex-shrink: 0;
    }

    /* Smooth scrolling area */
    .scroll-area {
      flex: 1;
      overflow: hidden;
      min-height: 0;
      position: relative;
      mask-image: linear-gradient(to bottom, transparent 0%, black 5%, black 90%, transparent 100%);
      -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 5%, black 90%, transparent 100%);
    }

    .scroll-track {
      display: flex;
      flex-direction: column;
      gap: 4px;
      animation: scrollUp var(--scroll-duration, 20s) linear infinite;
    }

    .scroll-track:hover {
      animation-play-state: paused;
    }

    @keyframes scrollUp {
      0% { transform: translateY(0); }
      100% { transform: translateY(-50%); }
    }

    .scroll-item {
      display: flex;
      align-items: center;
      background: rgba(0,0,0,0.025);
      border-radius: 8px;
      padding: 7px 14px;
    }

    .scroll-item .pos {
      font-size: 16px;
      font-weight: 700;
      color: rgba(102, 126, 234, 0.5);
      width: 40px;
      text-align: center;
    }

    .scroll-item .tok {
      font-size: 20px;
      font-weight: 700;
      width: 110px;
      color: rgba(0,0,0,0.4);
    }

    .scroll-item .nam {
      flex: 1;
      font-size: 18px;
      color: rgba(0,0,0,0.35);
    }

    .empty-msg {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      color: rgba(0,0,0,0.12);
    }
  </style>
</head>
<body>
  <div class="brand">
    <div class="brand-name">Rivek Men's Salon</div>
    <div class="brand-sub">Nikol, Ahmedabad</div>
  </div>
  <div class="clock" id="clock"></div>

  <!-- Top: 4 Chairs -->
  <div class="serving-bar">
    <div class="serving-label">Now Serving</div>
    <div class="serving-row" id="servingRow"></div>
  </div>

  <div class="divider"></div>

  <!-- Bottom: Queue -->
  <div class="queue-section">
    <div class="queue-header">
      <div class="queue-label">Waiting Queue</div>
      <div class="queue-count" id="queueCount"></div>
    </div>
    <!-- Static first 5 -->
    <div class="static-list" id="staticList"></div>
    <!-- Scrolling rest -->
    <div id="scrollSection" style="display:none; flex:1; display:flex; flex-direction:column; min-height:0; overflow:hidden;">
      <div class="scroll-divider"></div>
      <div class="upcoming-header">Upcoming</div>
      <div class="scroll-area">
        <div class="scroll-track" id="scrollTrack"></div>
      </div>
    </div>
    <div class="empty-msg" id="emptyMsg">No customers waiting</div>
  </div>

  <script>
    let prevServingIds = [];

    function updateClock() {
      const now = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Kolkata' }));
      const h = String(now.getHours() % 12 || 12).padStart(2, '0');
      const m = String(now.getMinutes()).padStart(2, '0');
      const s = String(now.getSeconds()).padStart(2, '0');
      const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
      document.getElementById('clock').innerHTML =
        h + '<span class="colon-blink">:</span>' + m + '<span class="seconds"> ' + s + ' ' + ampm + '</span>';
    }
    updateClock();
    setInterval(updateClock, 1000);

    function playChime() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const g = ctx.createGain();
        osc.connect(g); g.connect(ctx.destination);
        osc.frequency.value = 800; osc.type = 'sine';
        g.gain.setValueAtTime(0.3, ctx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.5);
        setTimeout(() => {
          const o2 = ctx.createOscillator(); const g2 = ctx.createGain();
          o2.connect(g2); g2.connect(ctx.destination);
          o2.frequency.value = 1000; o2.type = 'sine';
          g2.gain.setValueAtTime(0.3, ctx.currentTime);
          g2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
          o2.start(ctx.currentTime); o2.stop(ctx.currentTime + 0.5);
        }, 200);
      } catch (e) {}
    }

    function updateDisplay(data) {
      // Serving chairs
      const currentIds = data.serving.map(s => s.id);
      const hasNew = currentIds.some(id => !prevServingIds.includes(id));
      if (hasNew && prevServingIds.length > 0) playChime();
      prevServingIds = currentIds;

      let h = '';
      for (let i = 0; i < 4; i++) {
        const p = data.serving[i];
        if (p) {
          h += `<div class="chair${hasNew ? ' pop-in' : ''}">
            <div class="token">${p.formatted}</div>
            <div class="name">${p.name}</div>
          </div>`;
        } else {
          h += `<div class="chair empty">
            <div class="empty-text">Available</div>
          </div>`;
        }
      }
      document.getElementById('servingRow').innerHTML = h;

      // Queue
      const waiting = data.waiting;
      document.getElementById('queueCount').textContent = waiting.length + ' waiting';

      const staticList = document.getElementById('staticList');
      const scrollSection = document.getElementById('scrollSection');
      const emptyMsg = document.getElementById('emptyMsg');

      if (waiting.length === 0) {
        staticList.innerHTML = '';
        scrollSection.style.display = 'none';
        emptyMsg.style.display = 'flex';
        return;
      }

      emptyMsg.style.display = 'none';

      // First 5 static
      const first5 = waiting.slice(0, 5);
      const rest = waiting.slice(5);

      staticList.innerHTML = first5.map((item, i) => `
        <div class="q-item${i === 0 ? ' next-up' : ''}">
          <div class="pos">${i + 1}</div>
          <div class="tok">${item.formatted}</div>
          <div class="nam">${item.name}</div>
        </div>
      `).join('');

      // Scrolling rest
      if (rest.length > 0) {
        scrollSection.style.display = 'flex';

        // Build items twice for seamless loop
        let items = '';
        for (let copy = 0; copy < 2; copy++) {
          rest.forEach((item, i) => {
            items += `
              <div class="scroll-item">
                <div class="pos">${i + 6}</div>
                <div class="tok">${item.formatted}</div>
                <div class="nam">${item.name}</div>
              </div>`;
          });
        }

        const track = document.getElementById('scrollTrack');
        track.innerHTML = items;

        // Duration based on item count - ~2s per item
        const duration = Math.max(rest.length * 2, 8);
        track.style.setProperty('--scroll-duration', duration + 's');
      } else {
        scrollSection.style.display = 'none';
      }
    }

    async function fetchQueue() {
      try {
        const res = await fetch('api.php?action=get_queue');
        const data = await res.json();
        updateDisplay(data);
      } catch (err) {}
    }

    fetchQueue();
    setInterval(fetchQueue, 1000);

    if ('wakeLock' in navigator) navigator.wakeLock.request('screen').catch(() => {});
  </script>
</body>
</html>
