const SESSION_BROWSER_COLUMNS = 'id, date, service_name, attendance_types(name, start_time)';
const SESSION_BROWSER_PAGE_SIZE = 12;
let sessionBrowserRequest = 0;
let sessionBrowserPage = 1;
let sessionBrowserItems = [];
let sessionBrowserHistory = null;
const sessionBrowserMonths = new Map();

function sessionSearchText(value) {
  return String(value || '').toLowerCase().replace(/[,/]+/g, ' ').replace(/\s+/g, ' ').trim();
}

function filterBrowserSessions(sessions, search) {
  const query = sessionSearchText(search);
  return sessions.filter(session => !query || sessionSearchText([
    session.attendance_types?.name, session.service_name, session.date,
    formatDate(session.date), dayOfWeek(session.date)
  ].join(' ')).includes(query)).sort((a, b) => b.date.localeCompare(a.date) || a.id.localeCompare(b.id));
}

function rememberBrowserSessions(sessions) {
  allSessions = [...new Map([...allSessions, ...sessions].map(session => [session.id, session])).values()];
}

function setSessionBrowserMonth(isoDate) {
  document.getElementById('sessionBrowserMonth').value = String(Number(isoDate.slice(5, 7)));
  document.getElementById('sessionBrowserYear').value = isoDate.slice(0, 4);
}

function openSessionBrowser() {
  const browser = document.getElementById('sessionBrowser');
  browser.open = true;
  browser.scrollIntoView({ block: 'start' });
  browser.querySelector('summary').focus({ preventScroll: true });
}

async function initSessionBrowser() {
  setSessionBrowserMonth(todayISO());
  for (const id of ['sessionBrowserMonth', 'sessionBrowserYear']) {
    document.getElementById(id).addEventListener('change', () => {
      document.getElementById('sessionBrowserSearch').value = '';
      loadSessionBrowser();
    });
  }
  document.getElementById('previousSessionMonth').addEventListener('click', () => moveSessionBrowserMonth(-1));
  document.getElementById('nextSessionMonth').addEventListener('click', () => moveSessionBrowserMonth(1));
  document.getElementById('currentSessionMonth').addEventListener('click', () => {
    setSessionBrowserMonth(todayISO());
    document.getElementById('sessionBrowserSearch').value = '';
    loadSessionBrowser();
  });
  document.getElementById('sessionBrowserSearch').addEventListener('input', debounce(loadSessionBrowser, 300));
  document.getElementById('previousSessionPage').addEventListener('click', () => {
    sessionBrowserPage--;
    renderSessionCards();
  });
  document.getElementById('nextSessionPage').addEventListener('click', () => {
    sessionBrowserPage++;
    renderSessionCards();
  });
  document.getElementById('sessionCards').addEventListener('click', event => {
    const button = event.target.closest('button[data-open-session]');
    if (button) loadSession(button.dataset.openSession);
  });

  const linkedId = new URLSearchParams(location.search).get('session');
  let linkedSession = null;
  let linkError = '';
  if (linkedId) {
    try {
      const { data, error } = await db.from('attendance_sessions').select(SESSION_BROWSER_COLUMNS).eq('id', linkedId).maybeSingle();
      if (error) throw error;
      if (data) {
        linkedSession = data;
        rememberBrowserSessions([data]);
        setSessionBrowserMonth(data.date);
      } else linkError = 'That session was not found. Choose another session below.';
    } catch (error) {
      linkError = 'Unable to open the linked session. Choose a session below or refresh to retry.';
    }
  }
  await loadSessionBrowser();
  if (linkedSession) await loadSession(linkedSession.id);
  else if (linkError) document.getElementById('sessionBrowserStatus').textContent = linkError;
}

function moveSessionBrowserMonth(direction) {
  const year = Number(document.getElementById('sessionBrowserYear').value);
  const month = Number(document.getElementById('sessionBrowserMonth').value);
  if (!Number.isInteger(year) || year < 1900 || year > 9998) return;
  const date = new Date(Date.UTC(year, month - 1 + direction, 1));
  if (date.getUTCFullYear() < 1900 || date.getUTCFullYear() > 9998) return;
  setSessionBrowserMonth(date.toISOString().slice(0, 10));
  document.getElementById('sessionBrowserSearch').value = '';
  loadSessionBrowser();
}

async function loadSessionBrowser() {
  const request = ++sessionBrowserRequest;
  const status = document.getElementById('sessionBrowserStatus');
  const grid = document.getElementById('sessionCards');
  const year = Number(document.getElementById('sessionBrowserYear').value);
  const month = Number(document.getElementById('sessionBrowserMonth').value);
  const search = document.getElementById('sessionBrowserSearch').value.trim();
  document.getElementById('sessionBrowserPagination').hidden = true;
  grid.setAttribute('aria-busy', 'false');
  if (!Number.isInteger(year) || year < 1900 || year > 9998 || !Number.isInteger(month) || month < 1 || month > 12) {
    status.textContent = 'Enter a year from 1900 to 9998.';
    grid.innerHTML = '';
    return;
  }
  const from = `${year}-${String(month).padStart(2, '0')}-01`;
  const to = new Date(Date.UTC(year, month, 1)).toISOString().slice(0, 10);
  const label = parseLocalDate(from).toLocaleDateString('en-PH', { month: 'long', year: 'numeric' });
  document.getElementById('sessionBrowserMonthLabel').textContent = search ? 'Search all months' : label;
  status.textContent = search ? 'Searching all sessions…' : `Loading ${label}…`;
  grid.setAttribute('aria-busy', 'true');
  grid.innerHTML = '<div class="empty-state"><i class="bx bx-loader-alt bx-spin"></i><p>Loading sessions…</p></div>';
  try {
    let sessions;
    if (search) {
      if (!sessionBrowserHistory) sessionBrowserHistory = await fetchAllRowsForExport('attendance_sessions', SESSION_BROWSER_COLUMNS);
      sessions = sessionBrowserHistory;
    } else if (sessionBrowserHistory) {
      sessions = sessionBrowserHistory.filter(session => session.date >= from && session.date < to);
    } else {
      const cached = sessionBrowserMonths.get(from);
      sessions = cached || await fetchAllRowsForExport('attendance_sessions', SESSION_BROWSER_COLUMNS,
        query => query.gte('date', from).lt('date', to));
      sessionBrowserMonths.set(from, sessions);
    }
    if (request !== sessionBrowserRequest) return;
    rememberBrowserSessions(sessions);
    sessionBrowserItems = filterBrowserSessions(sessions, search);
    sessionBrowserPage = 1;
    status.textContent = `${sessionBrowserItems.length} session${sessionBrowserItems.length === 1 ? '' : 's'} · ${search ? 'Search results across all months' : label}`;
    renderSessionCards();
  } catch (error) {
    if (request !== sessionBrowserRequest) return;
    status.textContent = 'Unable to load sessions. Retry below.';
    grid.innerHTML = '<div class="empty-state"><i class="bx bx-wifi-off"></i><p>Sessions could not be loaded.</p><button type="button" class="btn btn-outline" onclick="loadSessionBrowser()">Retry</button></div>';
  } finally {
    if (request === sessionBrowserRequest) grid.setAttribute('aria-busy', 'false');
  }
}

function renderSessionCards() {
  const pages = Math.max(1, Math.ceil(sessionBrowserItems.length / SESSION_BROWSER_PAGE_SIZE));
  sessionBrowserPage = Math.max(1, Math.min(sessionBrowserPage, pages));
  const sessions = sessionBrowserItems.slice((sessionBrowserPage - 1) * SESSION_BROWSER_PAGE_SIZE, sessionBrowserPage * SESSION_BROWSER_PAGE_SIZE);
  document.getElementById('sessionCards').innerHTML = sessions.length ? sessions.map(session => {
    const today = session.date === todayISO();
    const selected = session.id === currentSessionId;
    return `<article class="service-card attendance-session-card${selected ? ' is-selected' : ''}${today ? ' is-today' : ''}">
      <div class="directory-card-top"><span class="session-weekday">${dayOfWeek(session.date)}</span>
        ${today ? '<span class="badge session-today-badge">Today</span>' : selected ? '<span class="badge session-selected-badge">Selected</span>' : ''}</div>
      <h3 class="directory-card-title">${escapeHtml(session.attendance_types?.name || 'Service')}</h3>
      <p class="directory-card-meta"><i class="bx bx-calendar" aria-hidden="true"></i><time datetime="${escapeHtml(session.date)}">${formatDate(session.date)}</time></p>
      ${session.attendance_types?.start_time ? `<p class="directory-card-meta"><i class="bx bx-time" aria-hidden="true"></i>${formatTime(session.attendance_types.start_time)}</p>` : ''}
      ${session.service_name ? `<p class="directory-card-meta">${escapeHtml(session.service_name)}</p>` : ''}
      <button type="button" class="btn ${today || selected ? 'btn-primary' : 'btn-outline'} session-open-button" data-open-session="${escapeHtml(session.id)}" aria-label="Open ${escapeHtml(session.attendance_types?.name || 'service')} on ${formatDate(session.date)}">Open Attendance <i class="bx bx-right-arrow-alt" aria-hidden="true"></i></button>
    </article>`;
  }).join('') : '<div class="empty-state"><i class="bx bx-calendar-x"></i><p>No sessions found. Try another month or search all sessions by name or date.</p></div>';
  document.getElementById('sessionBrowserPagination').hidden = pages <= 1;
  document.getElementById('sessionBrowserPageLabel').textContent = `Page ${sessionBrowserPage} of ${pages}`;
  document.getElementById('previousSessionPage').disabled = sessionBrowserPage === 1;
  document.getElementById('nextSessionPage').disabled = sessionBrowserPage === pages;
}
