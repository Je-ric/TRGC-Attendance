// ── Hero Banner ──────────────────────────────────────────────
function initHeroBanner() {
  const clockEl    = document.getElementById('heroClock');
  const clockDate  = document.getElementById('heroClockDate');
  const greetingEl = document.getElementById('heroGreeting');
  const heroDateEl = document.getElementById('heroDate');
  const clockFormatter = new Intl.DateTimeFormat('en-PH', {
    timeZone: 'Asia/Manila', hour: 'numeric', minute: '2-digit', hour12: true
  });
  const hourFormatter = new Intl.DateTimeFormat('en', {
    timeZone: 'Asia/Manila', hour: 'numeric', hourCycle: 'h23'
  });
  const shortDateFormatter = new Intl.DateTimeFormat('en-PH', {
    timeZone: 'Asia/Manila', weekday: 'short', month: 'short', day: 'numeric'
  });
  const longDateFormatter = new Intl.DateTimeFormat('en-PH', {
    timeZone: 'Asia/Manila', weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
  });

  let chOffset = 0;
  function buildGreeting(greetWord, name) {
    chOffset = 0;
    const greetSpans = [...greetWord].map(ch =>
      `<span class="hero-ch accent" style="--ch:${chOffset++}">${ch}</span>`
    ).join('');
    const restSpans = (', ' + name).split('').map(ch =>
      `<span class="hero-ch" style="--ch:${chOffset++}">${ch === ' ' ? '&nbsp;' : ch}</span>`
    ).join('');
    return `<span aria-hidden="true">${greetSpans}${restSpans}</span>`;
  }

  let lastGreeting = '';
  function tick() {
    const now  = new Date();
    const hour = Number(hourFormatter.format(now));
    const greeting = hour < 12 ? 'Good Morning' : hour < 18 ? 'Good Afternoon' : 'Good Evening';
    const timeStr = clockFormatter.format(now);
    const shortDate = shortDateFormatter.format(now);
    const longDate = longDateFormatter.format(now);
    if (clockEl && clockEl.textContent !== timeStr) clockEl.textContent = timeStr;
    if (clockDate && clockDate.textContent !== shortDate) clockDate.textContent = shortDate;
    if (heroDateEl && heroDateEl.textContent !== longDate) heroDateEl.textContent = longDate;
    if (greetingEl && greeting !== lastGreeting) {
      lastGreeting = greeting;
      greetingEl.setAttribute('aria-label', greeting + ', Ka-TRGC');
      greetingEl.innerHTML = buildGreeting(greeting, 'Ka-TRGC');
    }
  }

  tick();
  setInterval(tick, 1000);

  // Nav scroll affordance
  const scrollWrap = document.querySelector('.hero-nav-scroll');
  const nav = document.querySelector('.hero-nav');
  if (scrollWrap && nav) {
    const updateFade = () => {
      const atEnd = nav.scrollLeft + nav.clientWidth >= nav.scrollWidth - 4;
      scrollWrap.classList.toggle('at-end', atEnd);
    };
    nav.addEventListener('scroll', updateFade, { passive: true });
    updateFade();
  }

  if (!nav || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  const glide = nav.querySelector('.hero-nav-glide');
  if (!glide) return;

  function place(link) {
    glide.style.width     = link.offsetWidth  + 'px';
    glide.style.height    = link.offsetHeight + 'px';
    glide.style.transform = `translateX(${link.offsetLeft}px) translateY(${link.offsetTop}px)`;
  }

  const activeLink = nav.querySelector('.hero-nav-link.active');
  if (activeLink) place(activeLink);

  nav.querySelectorAll('.hero-nav-link').forEach(link => {
    link.addEventListener('mouseenter', () => {
      if (link.classList.contains('active')) { nav.classList.remove('glide-on'); return; }
      place(link);
      nav.classList.add('glide-on');
    });
  });
  nav.addEventListener('mouseleave', () => {
    nav.classList.remove('glide-on');
    if (activeLink) place(activeLink);
  });
}

// ── Age & Category ────────────────────────────────────────────
function calcAge(birthdate) {
  if (!birthdate) return null;
  const today = new Date();
  const dob = parseLocalDate(birthdate);
  if (Number.isNaN(dob.getTime())) return null;
  let age = today.getFullYear() - dob.getFullYear();
  const m = today.getMonth() - dob.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
  return age;
}

function autoCategory(age) {
  if (age === null) return null;
  if (age <= 12) return 'Kids';
  if (age <= 24) return 'Youth';
  if (age <= 59) return 'Adults';
  return 'Seniors';
}

function effectiveCategory(person) {
  return person.category || autoCategory(calcAge(person.birthdate)) || 'Adults';
}

// ── Date helpers ──────────────────────────────────────────────
function formatDate(dateStr) {
  if (!dateStr) return '—';
  const date = parseLocalDate(dateStr);
  if (Number.isNaN(date.getTime())) return '—';
  return date.toLocaleDateString('en-PH', {
    year: 'numeric', month: 'long', day: 'numeric'
  });
}

function dayOfWeek(dateStr) {
  if (!dateStr) return '';
  const date = parseLocalDate(dateStr);
  return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('en-PH', { weekday: 'long' });
}

function formatTime(value) {
  if (typeof value !== 'string') return '—';
  const match = /^([01]\d|2[0-3]):([0-5]\d)(?::[0-5]\d(?:\.\d+)?)?$/.exec(value);
  if (!match) return '—';
  const hour = Number(match[1]);
  return `${hour % 12 || 12}:${match[2]} ${hour < 12 ? 'AM' : 'PM'}`;
}

function parseLocalDate(value) {
  if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(value)) {
    const [year, month, day] = value.split('-').map(Number);
    return new Date(year, month - 1, day);
  }
  return new Date(value);
}

function todayISO() {
  return new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit'
  }).format(new Date());
}

// ── Toast ─────────────────────────────────────────────────────
const toastIcons = { success: 'bx-check-circle', error: 'bx-x-circle', warning: 'bx-error', info: 'bx-info-circle' };
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<i class="bx ${toastIcons[type] || toastIcons.success}"></i><span>${message}</span>`;
  document.body.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(8px)';
    setTimeout(() => toast.remove(), 350);
  }, 3000);
}

// ── Modal helpers ─────────────────────────────────────────────
function openModal(id) {
  document.getElementById(id)?.showModal();
}
function closeModal(id) {
  document.getElementById(id)?.close();
}

// ── Confirm dialog ────────────────────────────────────────────
function confirmDelete(message = 'Are you sure you want to delete this?') {
  return window.confirm(message);
}

// ── Debounce ──────────────────────────────────────────────────
function debounce(fn, ms = 300) {
  let t;
  return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
}

function escapeHtml(value) {
  return String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  })[char]);
}

// ── Badge HTML ────────────────────────────────────────────────
const categoryColors = {
  Kids:    'background:#fdf1f7;color:#c0175a;border-color:#f7cfe1',
  Youth:   'background:#f4f1fd;color:#6d28d9;border-color:#ded7fa',
  Adults:  'background:#eef4ff;color:#1d4ed8;border-color:#cfe0fb',
  Seniors: 'background:#fff8e8;color:#a16207;border-color:#f5e3b3',
};
const statusColors = {
  Member:             'background:#1A1A1A;color:#fff;border-color:#1A1A1A',
  'Regular Attendee': 'background:#eef4ff;color:#1d4ed8;border-color:#cfe0fb',
  Visitor:            'background:#f4f4f4;color:#6b7280;border-color:#e2e2e2',
  Inactive:           'background:#fdeeee;color:#b91c1c;border-color:#f6cfcf',
};

function badge(label, styleStr) {
  return `<span class="badge" style="${styleStr}">${label}</span>`;
}

function genderDot(gender) {
  const color = gender === 'Male' ? '#3b82f6' : gender === 'Female' ? '#ec4899' : '#d1d5db';
  return `<span class="gender-dot" style="--dot:${color}" title="${gender || 'Not specified'}"></span>`;
}

function infoRow(label, value, icon = '') {
  return `<div class="info-row">
    <span class="info-row-label">${icon ? `<i class='bx ${icon}'></i>` : ''}${label}</span>
    <span class="info-row-value">${value}</span>
  </div>`;
}

// ── Pagination ────────────────────────────────────────────────
function renderPagination(container, currentPage, totalPages, onPageChange) {
  container.className = 'pagination';
  container.innerHTML = '';
  if (totalPages <= 1) return;

  const btn = (label, page, disabled = false, isActive = false) => {
    const b = document.createElement('button');
    b.innerHTML = label;
    b.disabled = disabled;
    b.className = 'page-btn' + (isActive ? ' active' : '');
    if (!disabled) b.onclick = () => onPageChange(page);
    return b;
  };

  container.appendChild(btn('<i class="bx bx-chevron-left"></i>', currentPage - 1, currentPage === 1));
  for (let i = 1; i <= totalPages; i++) {
    container.appendChild(btn(i, i, false, i === currentPage));
  }
  container.appendChild(btn('<i class="bx bx-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
}
