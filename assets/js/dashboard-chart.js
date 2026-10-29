const ATTENDANCE_MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
let attendanceChartRequest = 0;
const attendanceCountCache = new Map();

function initAttendanceChart() {
  document.getElementById('attendanceYear').value = new Intl.DateTimeFormat('en', {
    year: 'numeric', timeZone: 'Asia/Manila'
  }).format(new Date());
  document.getElementById('attendanceYearForm').addEventListener('submit', event => {
    event.preventDefault();
    attendanceCountCache.clear();
    loadAttendanceChart();
  });
  for (const id of ['attendanceGrouping', 'attendanceMonth']) {
    document.getElementById(id).addEventListener('change', loadAttendanceChart);
  }
  loadAttendanceChart();
}

function attendanceDateLabel(isoDate) {
  return `${ATTENDANCE_MONTHS[Number(isoDate.slice(5, 7)) - 1]} ${Number(isoDate.slice(8, 10))}`;
}

function buildAttendancePeriods(year, grouping, month = null) {
  // UTC arithmetic treats session dates as calendar dates, independent of device timezone/DST.
  const start = new Date(Date.UTC(year, month ? month - 1 : 0, 1));
  const end = new Date(Date.UTC(year, month || 12, 1));
  const periods = [];
  let cursor = start;
  while (cursor < end) {
    const next = new Date(cursor);
    if (grouping === 'monthly') next.setUTCMonth(next.getUTCMonth() + 1);
    else next.setUTCDate(next.getUTCDate() + (7 - (next.getUTCDay() + 6) % 7));
    if (next > end) next.setTime(end.getTime());
    const lastDay = new Date(next.getTime() - 86400000).toISOString().slice(0, 10);
    const from = cursor.toISOString().slice(0, 10);
    const to = next.toISOString().slice(0, 10);
    const label = grouping === 'monthly' ? ATTENDANCE_MONTHS[cursor.getUTCMonth()]
      : `W${periods.length + 1}: ${attendanceDateLabel(from)}–${attendanceDateLabel(lastDay)}`;
    periods.push({ from, to, label });
    cursor = next;
  }
  return periods;
}

async function fetchAttendanceCounts(periods, isCurrent = () => true) {
  // Exact counts bypass row limits. Four workers limit concurrent database requests.
  const counts = new Array(periods.length);
  let nextIndex = 0;
  async function worker() {
    while (nextIndex < periods.length) {
      if (!isCurrent()) throw new Error('Chart selection changed.');
      const index = nextIndex++;
      const { from, to } = periods[index];
      const key = `${from}/${to}`;
      const cached = attendanceCountCache.get(key);
      if (cached && Date.now() - cached.at < 30000) {
        counts[index] = cached.count;
        continue;
      }
      const { count, error } = await db.from('attendance_records')
        .select('id, attendance_sessions!inner(date)', { count: 'exact', head: true })
        .eq('status', 'present')
        .gte('attendance_sessions.date', from)
        .lt('attendance_sessions.date', to);
      if (error || !Number.isSafeInteger(count) || count < 0) {
        throw new Error('Unable to load attendance. Select View to retry.');
      }
      counts[index] = count;
      if (attendanceCountCache.size >= 192) attendanceCountCache.delete(attendanceCountCache.keys().next().value);
      attendanceCountCache.set(key, { count, at: Date.now() });
    }
  }
  await Promise.all(Array.from({ length: Math.min(4, periods.length) }, worker));
  return counts;
}

async function loadAttendanceChart() {
  const request = ++attendanceChartRequest;
  const year = Number(document.getElementById('attendanceYear').value);
  const grouping = document.getElementById('attendanceGrouping').value;
  const monthValue = document.getElementById('attendanceMonth').value;
  const month = monthValue === '' ? null : Number(monthValue);
  const status = document.getElementById('monthlyAttendanceStatus');
  const content = document.getElementById('monthlyAttendanceContent');
  content.hidden = true;
  content.setAttribute('aria-busy', 'false');
  if (!Number.isInteger(year) || year < 1900 || year > 9998 ||
      !['weekly', 'monthly'].includes(grouping) ||
      (month !== null && (!Number.isInteger(month) || month < 1 || month > 12))) {
    status.textContent = 'Enter a year from 1900 to 9998 and select a valid view and month.';
    return;
  }
  const scope = `${month ? ATTENDANCE_MONTHS[month - 1] + ' ' : ''}${year}`;
  status.textContent = `Loading attendance for ${scope}…`;
  content.setAttribute('aria-busy', 'true');
  try {
    const periods = buildAttendancePeriods(year, grouping, month);
    const counts = await fetchAttendanceCounts(periods, () => request === attendanceChartRequest);
    // A slower earlier request must never replace a more recently selected view.
    if (request !== attendanceChartRequest) return;
    renderAttendanceChart(scope, grouping, periods, counts);
    status.textContent = counts.some(count => count > 0) ? '' : `No present attendance recorded for ${scope}.`;
    content.hidden = false;
  } catch (error) {
    if (request !== attendanceChartRequest) return;
    status.textContent = 'Unable to load attendance. Select View to retry.';
  } finally {
    if (request === attendanceChartRequest) content.setAttribute('aria-busy', 'false');
  }
}

function renderAttendanceChart(scope, grouping, periods, counts) {
  const maximum = Math.max(...counts);
  const magnitude = 10 ** Math.floor(Math.log10(Math.max(maximum / 4, 1)));
  const step = Math.max(1, Math.ceil(maximum / 4 / magnitude) * magnitude);
  const ceiling = step * 4;
  const compact = new Intl.NumberFormat('en', { notation: 'compact', maximumFractionDigits: 1 });
  const left = 60, top = 32, height = 210, width = 756;
  const dense = periods.length > 12;
  const slot = width / periods.length;
  const barWidth = Math.min(60, slot * 0.65);
  const grid = Array.from({ length: 5 }, (_, index) => {
    const value = step * index;
    const y = top + height - height * index / 4;
    return `<line x1="${left}" y1="${y}" x2="${left + width}" y2="${y}" class="chart-grid-line" />
      <text x="${left - 10}" y="${y + 4}" text-anchor="end" class="chart-axis-text">${compact.format(value)}</text>`;
  }).join('');
  const bars = counts.map((count, index) => {
    const period = periods[index];
    const center = left + (index + 0.5) * slot;
    const barHeight = count / ceiling * height;
    const y = top + height - barHeight;
    const axisLabel = dense ? (index === 0 || period.from.slice(5, 7) !== periods[index - 1].from.slice(5, 7)
      ? ATTENDANCE_MONTHS[Number(period.from.slice(5, 7)) - 1] : '')
      : grouping === 'weekly' ? `W${index + 1}` : period.label;
    return `<g><title>${period.label}: ${count.toLocaleString('en')} present check-ins</title>
      <rect x="${center - barWidth / 2}" y="${y}" width="${barWidth}" height="${barHeight}" rx="2" class="chart-bar" />
      ${dense ? '' : `<text x="${center}" y="${y - 8}" text-anchor="middle" class="chart-value">${compact.format(count)}</text>`}
      <text x="${center}" y="${top + height + 24}" text-anchor="middle" class="chart-month">${axisLabel}</text></g>`;
  }).join('');
  const total = counts.reduce((sum, value) => sum + value, 0);
  const viewLabel = grouping === 'weekly' ? 'Weekly' : 'Monthly';
  document.getElementById('monthlyAttendanceChart').innerHTML = `
    <svg viewBox="0 0 840 282" role="img" aria-labelledby="attendanceChartTitle" aria-describedby="attendanceChartDescription">
      <defs><linearGradient id="attendanceBarGradient" x1="0" y1="1" x2="0" y2="0"><stop offset="0%" stop-color="#b72034"/><stop offset="100%" stop-color="#e35050"/></linearGradient></defs>
      <title id="attendanceChartTitle">${viewLabel} present check-ins for ${scope}</title>
      <desc id="attendanceChartDescription">${periods.map((period, index) => `${period.label}: ${counts[index]}`).join('; ')}. Total: ${total}.</desc>
      ${grid}${bars}
    </svg>`;
  document.getElementById('attendancePeriodNote').textContent = grouping === 'weekly'
    ? 'Monday–Sunday weeks; first and last weeks include only dates within the selected year or month. Hover over a bar or view the table for exact dates and totals.'
    : 'Monthly totals use the session date. Hover over a bar or view the table for exact totals.';
  document.getElementById('monthlyAttendanceCaption').textContent = `${viewLabel} present check-ins, ${scope}`;
  document.getElementById('monthlyAttendanceRows').innerHTML = counts.map((count, index) =>
    `<tr><th scope="row">${periods[index].label}</th><td>${count.toLocaleString('en')}</td></tr>`
  ).join('');
  const highest = maximum ? periods.filter((_, index) => counts[index] === maximum) : [];
  // Keep tied weekly peaks readable; all individual values remain in the table.
  const peak = highest.length ? ` · Highest: ${highest.length > 3 ? `${highest.length} tied periods` : highest.map(period => period.label).join(', ')} (${maximum.toLocaleString('en')})` : '';
  document.getElementById('monthlyAttendanceSummary').textContent = `${scope} total: ${total.toLocaleString('en')} check-ins${peak}`;
}
