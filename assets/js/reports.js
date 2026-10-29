const REPORT_MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
let activeReportTab = 'event';
const reportRequests = { event: 0, person: 0, date: 0 };

function getReportBasePeriod(year, month) {
  if (!Number.isInteger(year) || year < 1900 || year > 9998 ||
      (month !== null && (!Number.isInteger(month) || month < 1 || month > 12))) {
    throw new Error('Choose a valid year and month.');
  }
  return {
    from: new Date(Date.UTC(year, month ? month - 1 : 0, 1)).toISOString().slice(0, 10),
    to: new Date(Date.UTC(year, month || 12, 1)).toISOString().slice(0, 10)
  };
}

function buildReportWeeks(from, to) {
  const weeks = [];
  let cursor = new Date(`${from}T00:00:00Z`);
  const end = new Date(`${to}T00:00:00Z`);
  const shortDate = iso => new Date(`${iso}T00:00:00Z`).toLocaleDateString('en-PH', {
    timeZone: 'UTC', month: 'short', day: 'numeric'
  });
  while (cursor < end) {
    const next = new Date(cursor);
    next.setUTCDate(next.getUTCDate() + 7 - (next.getUTCDay() + 6) % 7);
    if (next > end) next.setTime(end.getTime());
    const start = cursor.toISOString().slice(0, 10);
    const finish = next.toISOString().slice(0, 10);
    const last = new Date(next.getTime() - 86400000).toISOString().slice(0, 10);
    weeks.push({ from: start, to: finish, label: `Week ${weeks.length + 1} · ${shortDate(start)}–${shortDate(last)}` });
    cursor = next;
  }
  return weeks;
}

function getReportPeriod() {
  const year = Number(document.getElementById('reportYear').value);
  const monthValue = document.getElementById('reportMonth').value;
  const month = monthValue === '' ? null : Number(monthValue);
  let period = getReportBasePeriod(year, month);
  const weekValue = document.getElementById('reportWeek').value;
  if (weekValue !== '') {
    const index = Number(weekValue);
    const weeks = buildReportWeeks(period.from, period.to);
    if (!Number.isInteger(index) || !weeks[index]) throw new Error('Choose a valid week.');
    period = weeks[index];
  }
  const last = new Date(Date.parse(`${period.to}T00:00:00Z`) - 86400000).toISOString().slice(0, 10);
  return { ...period, label: `${formatDate(period.from)} – ${formatDate(last)}` };
}

function refreshActiveReport() {
  return ({ event: loadEventReport, person: loadPersonReport, date: loadDateReport })[activeReportTab]();
}

function changeReportPeriod(rebuildWeeks = false) {
  for (const tab of Object.keys(reportRequests)) reportRequests[tab]++;
  try {
    if (rebuildWeeks) {
      const year = Number(document.getElementById('reportYear').value);
      const monthValue = document.getElementById('reportMonth').value;
      const period = getReportBasePeriod(year, monthValue === '' ? null : Number(monthValue));
      const week = document.getElementById('reportWeek');
      week.innerHTML = '<option value="">All weeks</option>' + buildReportWeeks(period.from, period.to)
        .map((item, index) => `<option value="${index}">${item.label}</option>`).join('');
      week.value = '';
    }
    document.getElementById('reportPeriodLabel').textContent = `${getReportPeriod().label} · Weeks run Monday–Sunday; boundary weeks include only dates in the selected month or year.`;
  } catch (error) {
    document.getElementById('reportPeriodLabel').textContent = 'Choose a year from 1900 to 9998 and a valid month or week.';
  }
  refreshActiveReport();
}

function switchTab(tab) {
  activeReportTab = tab;
  ['event', 'person', 'date'].forEach(key => {
    const name = key.charAt(0).toUpperCase() + key.slice(1);
    document.getElementById(`panel${name}`).style.display = key === tab ? 'flex' : 'none';
    document.getElementById(`tab${name}`).classList.toggle('active', key === tab);
  });
  refreshActiveReport();
}

function reportMessage(columns, message) {
  return `<tr><td colspan="${columns}" class="report-message">${escapeHtml(message)}</td></tr>`;
}

async function fetchSessionReport(period, typeId = '') {
  const sessions = await fetchAllRowsForExport('attendance_sessions', 'id, date, service_name, attendance_types(name)', query => {
    query = query.gte('date', period.from).lt('date', period.to);
    return typeId ? query.eq('attendance_type_id', typeId) : query;
  });
  // Fetch records separately so nested relationship row limits cannot truncate counts.
  const totals = new Map(sessions.map(session => [session.id, { present: 0, absent: 0 }]));
  for (let index = 0; index < sessions.length; index += 100) {
    const ids = sessions.slice(index, index + 100).map(session => session.id);
    const records = await fetchAllRowsForExport('attendance_records', 'id, attendance_session_id, status',
      query => query.in('attendance_session_id', ids));
    records.forEach(record => {
      const counts = totals.get(record.attendance_session_id);
      if (counts && (record.status === 'present' || record.status === 'absent')) counts[record.status]++;
    });
  }
  return sessions.sort((a, b) => b.date.localeCompare(a.date) || a.id.localeCompare(b.id))
    .map(session => ({ ...session, ...totals.get(session.id) }));
}

async function loadEventReport() {
  const request = ++reportRequests.event;
  const body = document.getElementById('eventBody');
  body.innerHTML = reportMessage(8, 'Loading report…');
  try {
    const rows = await fetchSessionReport(getReportPeriod(), document.getElementById('evTypeFilter').value);
    if (request !== reportRequests.event) return;
    body.innerHTML = rows.length ? rows.map(session => `<tr>
      <td>${formatDate(session.date)}</td><td>${dayOfWeek(session.date)}</td>
      <td>${escapeHtml(session.attendance_types?.name || '—')}</td><td>${escapeHtml(session.service_name || '—')}</td>
      <td class="present-num">${session.present}</td><td class="absent-num">${session.absent}</td><td>${session.present + session.absent}</td>
      <td><a class="btn btn-ghost btn-sm" href="attendance.html?session=${encodeURIComponent(session.id)}" aria-label="Open attendance for ${formatDate(session.date)}"><i class="bx bx-link-external"></i></a></td>
    </tr>`).join('') : reportMessage(8, 'No sessions found for this period.');
  } catch (error) {
    if (request === reportRequests.event) body.innerHTML = reportMessage(8, 'Unable to load the report. Check the period and select Refresh to retry.');
  }
}

async function loadPersonReport() {
  const request = ++reportRequests.person;
  const body = document.getElementById('personBody');
  const summary = document.getElementById('personSummary');
  const personId = document.getElementById('personFilter').value;
  summary.style.display = 'none';
  if (!personId) { body.innerHTML = reportMessage(5, 'Select a person to view their history.'); return; }
  body.innerHTML = reportMessage(5, 'Loading report…');
  try {
    const period = getReportPeriod();
    const rows = await fetchAllRowsForExport('attendance_records',
      'id, status, remarks, attendance_sessions!inner(date, service_name, attendance_types(name))',
      query => query.eq('person_id', personId).gte('attendance_sessions.date', period.from).lt('attendance_sessions.date', period.to));
    if (request !== reportRequests.person) return;
    rows.sort((a, b) => b.attendance_sessions.date.localeCompare(a.attendance_sessions.date) || a.id.localeCompare(b.id));
    if (!rows.length) { body.innerHTML = reportMessage(5, 'No attendance records found for this period.'); return; }
    const present = rows.filter(row => row.status === 'present').length;
    document.getElementById('sumTotal').textContent = rows.length;
    document.getElementById('sumPresent').textContent = present;
    document.getElementById('sumRate').textContent = `${Math.round(present / rows.length * 100)}%`;
    summary.style.display = 'grid';
    body.innerHTML = rows.map(row => `<tr><td>${formatDate(row.attendance_sessions.date)}</td>
      <td>${escapeHtml(row.attendance_sessions.attendance_types?.name || '—')}</td><td>${escapeHtml(row.attendance_sessions.service_name || '—')}</td>
      <td>${badge(escapeHtml(row.status), row.status === 'present' ? 'background:#eaf5ef;color:#277148' : 'background:#edf0f5;color:#607089')}</td>
      <td>${escapeHtml(row.remarks || '—')}</td></tr>`).join('');
  } catch (error) {
    if (request === reportRequests.person) body.innerHTML = reportMessage(5, 'Unable to load attendance. Check the period and select Refresh to retry.');
  }
}

async function loadDateReport() {
  const request = ++reportRequests.date;
  const body = document.getElementById('dateBody');
  body.innerHTML = reportMessage(7, 'Loading report…');
  try {
    const rows = await fetchSessionReport(getReportPeriod());
    if (request !== reportRequests.date) return;
    body.innerHTML = rows.length ? rows.map(session => `<tr><td>${formatDate(session.date)}</td>
      <td>${escapeHtml(session.attendance_types?.name || '—')}</td><td>${escapeHtml(session.service_name || '—')}</td>
      <td class="present-num">${session.present}</td><td class="absent-num">${session.absent}</td><td>${session.present + session.absent}</td>
      <td><a class="btn btn-ghost btn-sm" href="attendance.html?session=${encodeURIComponent(session.id)}" aria-label="Open attendance for ${formatDate(session.date)}"><i class="bx bx-link-external"></i></a></td>
    </tr>`).join('') : reportMessage(7, 'No sessions found for this period.');
  } catch (error) {
    if (request === reportRequests.date) body.innerHTML = reportMessage(7, 'Unable to load the report. Check the period and select Refresh to retry.');
  }
}

async function initReports() {
  const today = todayISO();
  document.getElementById('reportYear').value = today.slice(0, 4);
  document.getElementById('reportMonth').value = String(Number(today.slice(5, 7)));
  for (const id of ['reportYear', 'reportMonth']) document.getElementById(id).addEventListener('change', () => changeReportPeriod(true));
  document.getElementById('reportWeek').addEventListener('change', () => changeReportPeriod());
  changeReportPeriod(true);
  const results = await Promise.allSettled([
    fetchAllRowsForExport('attendance_types', 'id, name'),
    fetchAllRowsForExport('people', 'id, first_name, last_name')
  ]);
  for (const [index, result] of results.entries()) {
    const select = document.getElementById(index === 0 ? 'evTypeFilter' : 'personFilter');
    if (result.status === 'rejected') {
      const option = document.createElement('option');
      option.textContent = 'Unable to load options. Refresh the page to retry.';
      option.disabled = true;
      select.appendChild(option);
      continue;
    }
    result.value.sort((a, b) => (index === 0 ? a.name : `${a.last_name} ${a.first_name}`).localeCompare(index === 0 ? b.name : `${b.last_name} ${b.first_name}`))
      .forEach(row => {
        const option = document.createElement('option');
        option.value = row.id;
        option.textContent = index === 0 ? row.name : `${row.last_name}, ${row.first_name}`;
        select.appendChild(option);
      });
  }
}
