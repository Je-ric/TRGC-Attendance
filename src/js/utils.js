// ── Hero Banner ──────────────────────────────────────────────
function initHeroBanner() {
  const clockEl    = document.getElementById('heroClock');
  const clockDate  = document.getElementById('heroClockDate');
  const greetingEl = document.getElementById('heroGreeting');
  const heroDateEl = document.getElementById('heroDate');

  function tick() {
    const now  = new Date();
    const hour = now.getHours();
    const greeting = hour < 12 ? 'Good Morning' : hour < 18 ? 'Good Afternoon' : 'Good Evening';
    const timeStr  = now.toLocaleTimeString('en-US', { hour12: false });
    const dateStr  = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
    if (clockEl)    clockEl.textContent = timeStr;
    if (clockDate)  clockDate.textContent = dateStr;
    if (heroDateEl) heroDateEl.textContent = dateStr;
    if (greetingEl) greetingEl.innerHTML = `<span class="accent">${greeting}</span>, Ka-TRGC`;
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
  const dob = new Date(birthdate);
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
  return new Date(dateStr).toLocaleDateString('en-PH', {
    year: 'numeric', month: 'long', day: 'numeric'
  });
}

function dayOfWeek(dateStr) {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString('en-PH', { weekday: 'long' });
}

function todayISO() {
  return new Date().toISOString().split('T')[0];
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

// ── Badge HTML ────────────────────────────────────────────────
const categoryColors = {
  Kids:     'background:#fce7f3;color:#9d174d',
  Youth:    'background:#ede9fe;color:#5b21b6',
  Adults:   'background:#dbeafe;color:#1e40af',
  Seniors:  'background:#fef3c7;color:#92400e',
};
const statusColors = {
  Member:             'background:#1A1A1A;color:#fff',
  'Regular Attendee': 'background:#dbeafe;color:#1e40af',
  Visitor:            'background:#f3f4f6;color:#6b7280',
  Inactive:           'background:#fee2e2;color:#991b1b',
};

function badge(label, styleStr) {
  return `<span class="badge" style="${styleStr}">${label}</span>`;
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
