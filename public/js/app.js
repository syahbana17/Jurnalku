document.addEventListener('DOMContentLoaded', function () {

  /* ── SIDEBAR TOGGLE ── */
  const hamburger = document.getElementById('hamburger');
  const sidebar   = document.getElementById('sidebar');
  const overlay   = document.getElementById('overlay');
  if (hamburger) {
    hamburger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.classList.remove('show');
    });
  }

  /* ── REALTIME CLOCK ── */
  function updateClock() {
    const now  = new Date();
    const hh   = String(now.getHours()).padStart(2, '0');
    const mm   = String(now.getMinutes()).padStart(2, '0');
    const ss   = String(now.getSeconds()).padStart(2, '0');
    const time = hh + ':' + mm + ':' + ss;
    const el = document.getElementById('topbar-clock');
    if (el) el.textContent = time;
  }
  updateClock();
  setInterval(updateClock, 1000);

  /* ── DARK / LIGHT TOGGLE ── */
  const html       = document.documentElement;
  const savedTheme = localStorage.getItem('theme') || 'light';
  applyTheme(savedTheme);

  function applyTheme(theme) {
    html.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    const isDark = theme === 'dark';
    document.querySelectorAll('.theme-icon').forEach(el => el.textContent = isDark ? '☀️' : '🌙');
    document.querySelectorAll('.theme-label').forEach(el => el.textContent = isDark ? 'Mode Terang' : 'Mode Gelap');
    const topBtn = document.getElementById('theme-toggle-top');
    if (topBtn) topBtn.textContent = isDark ? '☀️' : '🌙';
  }

  document.querySelectorAll('#theme-toggle, #theme-toggle-top').forEach(btn => {
    btn.addEventListener('click', () => {
      applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  });

  /* ── FLASH ALERT AUTO-HIDE ── */
  const flash = document.getElementById('flash-alert');
  if (flash) setTimeout(() => {
    flash.style.transition = 'opacity .5s';
    flash.style.opacity = '0';
    setTimeout(() => flash.remove(), 500);
  }, 3500);

  /* ── GLOBAL SEARCH SHORTCUT ── */
  document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === '/') {
      e.preventDefault();
      const s = document.getElementById('global-search');
      if (s) s.focus();
    }
  });

});

/* ── MODAL HELPERS ── */
function toggleModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.toggle('show');
}

function closeModalOutside(event, id) {
  if (event.target.id === id) toggleModal(id);
}

function confirmDelete(action, msg) {
  document.getElementById('confirm-msg').textContent = msg || 'Hapus item ini?';
  document.getElementById('confirm-form').action = action;
  toggleModal('modal-confirm');
}

/* ── ANALOG CLOCK PICKER ── */
(function () {
  const CX = 110, CY = 110;
  const hours = [12,1,2,3,4,5,6,7,8,9,10,11,0,13,14,15,16,17,18,19,20,21,22,23];
  let clockMode = 'mulai';
  let selectedHour = { mulai: null, selesai: null };
  let selectedMin  = { mulai: '00', selesai: '00' };

  function degToRad(d) { return d * Math.PI / 180; }

  function buildClock() {
    const g = document.getElementById('clock-numbers');
    if (!g) return;
    g.innerHTML = '';
    hours.forEach(h => {
      const isInner = h === 0 || h >= 13;
      const r = isInner ? 55 : 82;
      const angle = degToRad((h % 12) / 12 * 360 - 90);
      const x = CX + r * Math.cos(angle);
      const y = CY + r * Math.sin(angle);
      const isActive = selectedHour[clockMode] === h;
      const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
      circle.setAttribute('cx', x); circle.setAttribute('cy', y); circle.setAttribute('r', 14);
      circle.setAttribute('class', 'clock-num-bg' + (isActive ? ' active-bg' : ''));
      circle.dataset.hour = h;
      g.appendChild(circle);
      const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
      text.setAttribute('x', x); text.setAttribute('y', y + 4.5);
      text.setAttribute('text-anchor', 'middle');
      text.setAttribute('class', 'clock-num' + (isActive ? ' active-num' : ''));
      text.dataset.hour = h;
      text.textContent = h === 0 ? '00' : h;
      g.appendChild(text);
    });
  }

  function updateHand(h) {
    const hand = document.getElementById('clock-hand');
    if (!hand) return;
    const isInner = h === 0 || h >= 13;
    const r = isInner ? 55 : 82;
    const angle = degToRad((h % 12) / 12 * 360 - 90);
    hand.setAttribute('x2', CX + r * Math.cos(angle));
    hand.setAttribute('y2', CY + r * Math.sin(angle));
  }

  function formatTime(h, m) {
    if (h === null) return '--:--';
    return String(h).padStart(2, '0') + ':' + m;
  }

  function updateDisplays() {
    const dm = document.getElementById('display-mulai');
    const ds = document.getElementById('display-selesai');
    const im = document.getElementById('inp-mulai');
    const is = document.getElementById('inp-selesai');
    if (dm) dm.textContent = formatTime(selectedHour.mulai, selectedMin.mulai);
    if (ds) ds.textContent = formatTime(selectedHour.selesai, selectedMin.selesai);
    if (im && selectedHour.mulai !== null) im.value = formatTime(selectedHour.mulai, selectedMin.mulai);
    if (is && selectedHour.selesai !== null) is.value = formatTime(selectedHour.selesai, selectedMin.selesai);
  }

  function selectHour(h) {
    selectedHour[clockMode] = h;
    updateHand(h); buildClock(); updateDisplays();
    if (clockMode === 'mulai') setTimeout(() => switchClock('selesai'), 300);
  }

  window.switchClock = function (mode) {
    clockMode = mode;
    document.getElementById('box-mulai')?.classList.toggle('active', mode === 'mulai');
    document.getElementById('box-selesai')?.classList.toggle('active', mode === 'selesai');
    const label = document.getElementById('clock-mode-label');
    if (label) label.textContent = mode === 'mulai' ? 'Pilih Jam Mulai' : 'Pilih Jam Selesai';
    if (selectedHour[mode] !== null) updateHand(selectedHour[mode]);
    buildClock();
    document.querySelectorAll('.minute-btn').forEach(b =>
      b.classList.toggle('active', b.dataset.min === selectedMin[mode]));
  };

  document.addEventListener('click', function (e) {
    const el = e.target.closest('[data-hour]');
    if (!el) return;
    if (!document.getElementById('modal-jadwal')?.classList.contains('show')) return;
    selectHour(parseInt(el.dataset.hour));
  });

  document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('minute-btn')) return;
    const min = e.target.dataset.min;
    selectedMin[clockMode] = min;
    document.querySelectorAll('.minute-btn').forEach(b =>
      b.classList.toggle('active', b.dataset.min === min));
    updateDisplays();
  });

  const origToggle = window.toggleModal;
  window.toggleModal = function (id) {
    origToggle(id);
    if (id === 'modal-jadwal') {
      const isOpen = document.getElementById(id)?.classList.contains('show');
      if (isOpen) {
        clockMode = 'mulai';
        selectedHour = { mulai: null, selesai: null };
        selectedMin  = { mulai: '00', selesai: '00' };
        document.getElementById('box-mulai')?.classList.add('active');
        document.getElementById('box-selesai')?.classList.remove('active');
        const label = document.getElementById('clock-mode-label');
        if (label) label.textContent = 'Pilih Jam Mulai';
        const hand = document.getElementById('clock-hand');
        if (hand) { hand.setAttribute('x2', 110); hand.setAttribute('y2', 30); }
        buildClock(); updateDisplays();
        document.querySelectorAll('.minute-btn').forEach(b =>
          b.classList.toggle('active', b.dataset.min === '00'));
      }
    }
  };

  document.addEventListener('DOMContentLoaded', buildClock);
})();
