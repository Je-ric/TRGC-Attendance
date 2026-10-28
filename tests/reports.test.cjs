const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup(tables = {}) {
  const elements = {};
  const reads = [];
  const context = vm.createContext({
    Intl, Date,
    document: {
      getElementById: id => elements[id] ??= {
        value: id === 'reportYear' ? '2026' : id === 'reportMonth' ? '10' : '',
        style: {}, innerHTML: '', textContent: '', classList: { toggle() {} },
        addEventListener() {}, appendChild() {}
      },
      createElement: () => ({})
    },
    fetchAllRowsForExport: async (table, columns, filter = q => q) => {
      const conditions = [];
      const query = {};
      for (const method of ['gte', 'lt', 'eq', 'in']) query[method] = (column, value) => {
        conditions.push({ method, column, value }); return query;
      };
      filter(query);
      reads.push({ table, columns, conditions });
      return (tables[table] || []).filter(row => conditions.every(({ method, column, value }) => {
        const actual = column.split('.').reduce((result, key) => result?.[key], row);
        return method === 'gte' ? actual >= value : method === 'lt' ? actual < value : method === 'in' ? value.includes(actual) : actual === value;
      }));
    }
  });
  vm.runInContext(fs.readFileSync('src/js/utils.js', 'utf8'), context);
  vm.runInContext(fs.readFileSync('src/js/reports.js', 'utf8'), context);
  return { context, elements, reads };
}

test('month/week selectors produce exact non-overlapping Monday–Sunday date ranges', () => {
  const { context } = setup();
  const base = context.getReportBasePeriod(2024, 2);
  assert.equal(base.from, '2024-02-01');
  assert.equal(base.to, '2024-03-01');
  const weeks = context.buildReportWeeks(base.from, base.to);
  assert.equal(weeks[0].to, '2024-02-05');
  assert.equal(weeks.at(-1).to, '2024-03-01');
  assert.equal(weeks.reduce((sum, week, index) => {
    if (index) assert.equal(weeks[index - 1].to, week.from);
    return sum + (Date.parse(week.to) - Date.parse(week.from)) / 86400000;
  }, 0), 29);
  const year = context.getReportBasePeriod(2026, null);
  assert.equal(year.to, '2027-01-01');
});

test('changing month resets week and selected labels remain understandable', () => {
  const env = setup();
  env.context.document.getElementById('reportWeek').value = '2';
  env.context.refreshActiveReport = () => {};
  env.context.changeReportPeriod(true);
  assert.equal(env.elements.reportWeek.value, '');
  assert.match(env.elements.reportWeek.innerHTML, /Week 1 · Oct 1–Oct 4/);
  assert.match(env.elements.reportPeriodLabel.textContent, /October 1, 2026 – October 31, 2026/);
  env.elements.reportWeek.value = '1';
  const period = env.context.getReportPeriod();
  assert.equal(period.from, '2026-10-05');
  assert.equal(period.to, '2026-10-12');
  env.elements.reportYear.value = '2026.5';
  assert.throws(() => env.context.getReportPeriod(), /valid year/);
});

test('session reports filter before fetching and count separate records beyond nested row limits', async () => {
  const env = setup({
    attendance_sessions: [
      { id: 'october', date: '2026-10-25', attendance_type_id: 'service-1' },
      { id: 'november', date: '2026-11-01', attendance_type_id: 'service-1' },
      { id: 'other-service', date: '2026-10-25', attendance_type_id: 'service-2' }
    ],
    attendance_records: Array.from({ length: 1300 }, (_, index) => ({
      id: String(index), attendance_session_id: 'october', status: index < 1200 ? 'present' : 'absent'
    }))
  });
  const rows = await env.context.fetchSessionReport(env.context.getReportPeriod(), 'service-1');
  assert.equal(rows.length, 1);
  assert.equal(rows[0].present, 1200);
  assert.equal(rows[0].absent, 100);
  assert.equal(env.reads[1].table, 'attendance_records');
});

test('person reports apply selected week on the database and clear summaries when selection is removed', async () => {
  const env = setup({ attendance_records: [
    { id: 'a', person_id: 'person', status: 'present', remarks: '<note>', attendance_sessions: { date: '2026-10-05', attendance_types: { name: 'Worship' } } },
    { id: 'b', person_id: 'person', status: 'absent', attendance_sessions: { date: '2026-10-11' } },
    { id: 'c', person_id: 'person', status: 'present', attendance_sessions: { date: '2026-10-12' } }
  ] });
  env.context.document.getElementById('personFilter').value = 'person';
  env.context.document.getElementById('reportWeek').value = '1';
  await env.context.loadPersonReport();
  assert.equal(env.elements.sumTotal.textContent, 2);
  assert.equal(env.elements.sumPresent.textContent, 1);
  assert.equal(env.elements.sumRate.textContent, '50%');
  assert.match(env.elements.personBody.innerHTML, /&lt;note&gt;/);
  assert.ok(env.reads[0].conditions.some(condition => condition.column === 'attendance_sessions.date' && condition.method === 'lt'));
  env.elements.personFilter.value = '';
  await env.context.loadPersonReport();
  assert.equal(env.elements.personSummary.style.display, 'none');
  assert.match(env.elements.personBody.innerHTML, /Select a person/);
});

test('late report responses cannot replace a more recently selected period', async () => {
  const env = setup();
  const pending = [];
  env.context.fetchSessionReport = () => new Promise(resolve => pending.push(resolve));
  const older = env.context.loadEventReport();
  const newer = env.context.loadEventReport();
  pending[1]([{ id: 'new', date: '2026-10-25', attendance_types: { name: 'Latest Service' }, present: 3, absent: 1 }]);
  await newer;
  pending[0]([{ id: 'old', date: '2026-09-20', attendance_types: { name: 'Older Service' }, present: 9, absent: 0 }]);
  await older;
  assert.match(env.elements.eventBody.innerHTML, /Latest Service/);
  assert.doesNotMatch(env.elements.eventBody.innerHTML, /Older Service/);
});

test('failed report reads show retry feedback instead of empty data', async () => {
  const env = setup();
  env.context.fetchAllRowsForExport = async () => { throw new Error('offline'); };
  await env.context.loadDateReport();
  assert.match(env.elements.dateBody.innerHTML, /Refresh to retry/);
  assert.doesNotMatch(env.elements.dateBody.innerHTML, /No sessions/);
});
