const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup(counts = Array(12).fill(0), fail = false) {
  const elements = {};
  const requests = [];
  const context = vm.createContext({
    Intl, Date,
    document: { getElementById(id) {
      return elements[id] ??= {
        value: id === 'attendanceGrouping' ? 'monthly' : id === 'attendanceMonth' ? '' : '2026',
        textContent: '', innerHTML: '', hidden: false,
        setAttribute(name, value) { this[name] = value; }
      };
    } },
    db: { from(table) {
      assert.equal(table, 'attendance_records');
      const request = {};
      const query = {
        select(columns, options) {
          assert.equal(columns, 'id, attendance_sessions!inner(date)');
          assert.equal(options.count, 'exact');
          assert.equal(options.head, true);
          return this;
        },
        eq(column, value) { assert.equal(column, 'status'); assert.equal(value, 'present'); return this; },
        gte(column, value) { assert.equal(column, 'attendance_sessions.date'); request.from = value; return this; },
        lt(column, value) { assert.equal(column, 'attendance_sessions.date'); request.to = value; return this; },
        then(resolve, reject) {
          requests.push(request);
          return Promise.resolve({ count: typeof counts === 'function' ? counts(request) : counts[Number(request.from.slice(5, 7)) - 1],
            error: fail ? { message: 'offline' } : null }).then(resolve, reject);
        }
      };
      return query;
    } }
  });
  vm.runInContext(fs.readFileSync('src/js/dashboard-chart.js', 'utf8'), context);
  return { context, elements, requests };
}

test('monthly database counts cover the selected year with exclusive month boundaries', async () => {
  const counts = [1501, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
  const env = setup(counts);
  // Compare cross-context values as JSON rather than prototypes.
  const result = await env.context.fetchAttendanceCounts(env.context.buildAttendancePeriods(2024, 'monthly'));
  assert.equal(JSON.stringify(result), JSON.stringify(counts));
  assert.equal(env.requests.length, 12);
  assert.deepEqual(env.requests[0], { from: '2024-01-01', to: '2024-02-01' });
  assert.deepEqual(env.requests[1], { from: '2024-02-01', to: '2024-03-01' });
  assert.deepEqual(env.requests[11], { from: '2024-12-01', to: '2025-01-01' });
});

test('render includes all months, exact totals, and tied highest months', async () => {
  const env = setup([3, 8, 0, 8, 0, 0, 0, 0, 0, 0, 0, 0]);
  await env.context.loadAttendanceChart();
  assert.equal(env.elements.monthlyAttendanceContent.hidden, false);
  assert.match(env.elements.monthlyAttendanceSummary.textContent, /19 check-ins.*Feb, Apr \(8\)/);
  assert.equal((env.elements.monthlyAttendanceRows.innerHTML.match(/<tr>/g) || []).length, 12);
  assert.match(env.elements.monthlyAttendanceChart.innerHTML, /role="img"/);
  assert.doesNotMatch(env.elements.monthlyAttendanceChart.innerHTML, /NaN|Infinity/);
});

test('empty years show a zero chart, while request failures hide stale results', async () => {
  const empty = setup();
  await empty.context.loadAttendanceChart();
  assert.match(empty.elements.monthlyAttendanceStatus.textContent, /No present attendance/);
  assert.equal(empty.elements.monthlyAttendanceContent.hidden, false);
  assert.doesNotMatch(empty.elements.monthlyAttendanceChart.innerHTML, /NaN|Infinity/);
  const failed = setup([], true);
  await failed.context.loadAttendanceChart();
  assert.equal(failed.elements.monthlyAttendanceContent.hidden, true);
  assert.match(failed.elements.monthlyAttendanceStatus.textContent, /Select View to retry/);
  assert.equal(failed.elements.monthlyAttendanceContent['aria-busy'], 'false');
});

test('invalid year never requests data and an older response cannot replace a newer year', async () => {
  const env = setup();
  env.context.document.getElementById('attendanceYear').value = '2026.5';
  await env.context.loadAttendanceChart();
  assert.equal(env.requests.length, 0);
  const pending = new Map();
  env.context.fetchAttendanceCounts = periods => new Promise(resolve => pending.set(Number(periods[0].from.slice(0, 4)), resolve));
  env.elements.attendanceYear.value = '2025';
  const older = env.context.loadAttendanceChart();
  env.elements.attendanceYear.value = '2026';
  const newer = env.context.loadAttendanceChart();
  pending.get(2026)(Array(12).fill(2));
  await newer;
  pending.get(2025)(Array(12).fill(9));
  await older;
  assert.match(env.elements.monthlyAttendanceSummary.textContent, /2026 total: 24/);
});

test('weekly periods cover every date exactly once, including leap years and partial year boundaries', () => {
  const { context } = setup();
  for (const year of [1900, 2000, 2012, 2024, 2026, 2029, 9998]) {
    const periods = context.buildAttendancePeriods(year, 'weekly');
    assert.equal(periods[0].from, `${year}-01-01`);
    assert.equal(periods.at(-1).to, `${year + 1}-01-01`);
    let days = 0;
    periods.forEach((period, index) => {
      const duration = (Date.parse(period.to) - Date.parse(period.from)) / 86400000;
      assert.ok(duration >= 1 && duration <= 7);
      days += duration;
      if (index > 0) {
        assert.equal(periods[index - 1].to, period.from);
        assert.equal(new Date(period.from).getUTCDay(), 1);
      }
    });
    assert.equal(days, (Date.UTC(year + 1, 0, 1) - Date.UTC(year, 0, 1)) / 86400000);
  }
});

test('month zoom clips weekly periods to the chosen month, including February 29', () => {
  const { context } = setup();
  const periods = context.buildAttendancePeriods(2024, 'weekly', 2);
  assert.equal(periods[0].from, '2024-02-01');
  assert.equal(periods.at(-1).to, '2024-03-01');
  assert.match(periods.at(-1).label, /Feb 29/);
  const days = periods.reduce((sum, period) => sum + (Date.parse(period.to) - Date.parse(period.from)) / 86400000, 0);
  assert.equal(days, 29);
});

test('weekly and monthly totals agree for the same session dates at boundaries', async () => {
  const records = [
    { date: '2023-12-31', status: 'present' },
    { date: '2024-01-01', status: 'present' },
    { date: '2024-01-07', status: 'present' },
    { date: '2024-01-08', status: 'present' },
    { date: '2024-02-29', status: 'present' },
    { date: '2024-03-01', status: 'absent' },
    { date: '2024-12-31', status: 'present' },
    { date: '2025-01-01', status: 'present' }
  ];
  const env = setup(request => records.filter(record => record.status === 'present' &&
    record.date >= request.from && record.date < request.to).length);
  const weekly = await env.context.fetchAttendanceCounts(env.context.buildAttendancePeriods(2024, 'weekly'));
  const monthly = await env.context.fetchAttendanceCounts(env.context.buildAttendancePeriods(2024, 'monthly'));
  assert.equal(weekly.reduce((sum, count) => sum + count, 0), 5);
  assert.equal(monthly.reduce((sum, count) => sum + count, 0), 5);
  assert.equal(weekly[0], 2);
  assert.equal(weekly[1], 1);
});

test('weekly full-year chart and month zoom render the correct number of bars and date labels', async () => {
  const env = setup(Array(12).fill(2));
  env.context.document.getElementById('attendanceGrouping').value = 'weekly';
  await env.context.loadAttendanceChart();
  assert.equal((env.elements.monthlyAttendanceChart.innerHTML.match(/<rect /g) || []).length, 53);
  assert.match(env.elements.monthlyAttendanceRows.innerHTML, /W1: Jan 1–Jan 4/);
  env.elements.attendanceMonth.value = '10';
  await env.context.loadAttendanceChart();
  assert.equal((env.elements.monthlyAttendanceChart.innerHTML.match(/<rect /g) || []).length, 5);
  assert.match(env.elements.monthlyAttendanceSummary.textContent, /Oct 2026 total: 10/);
  assert.match(env.elements.monthlyAttendanceRows.innerHTML, /Oct 31/);
});

test('chart reuses recent counts and stops scheduling queries for obsolete selections', async () => {
  const env = setup();
  const periods = env.context.buildAttendancePeriods(2026, 'weekly');
  await env.context.fetchAttendanceCounts(periods);
  const calls = env.requests.length;
  await env.context.fetchAttendanceCounts(periods);
  assert.equal(env.requests.length, calls);
  const obsolete = setup();
  await assert.rejects(obsolete.context.fetchAttendanceCounts(periods, () => false), /selection changed/);
  assert.equal(obsolete.requests.length, 0);
});
