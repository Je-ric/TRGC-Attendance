const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup(sessions = [], search = '') {
  const elements = {};
  const reads = [];
  const opened = [];
  const context = vm.createContext({
    Intl, Date, URLSearchParams, URL,
    allSessions: [], currentSessionId: null,
    location: { search, href: `http://localhost/attendance.html${search}` },
    document: { getElementById: id => elements[id] ??= {
      value: id === 'sessionBrowserYear' ? '2026' : id === 'sessionBrowserMonth' ? '10' : '',
      innerHTML: '', textContent: '', style: {}, disabled: false,
      setAttribute(name, value) { this[name] = value; },
      addEventListener(type, handler) { (this.listeners ??= {})[type] = handler; }
    } },
    fetchAllRowsForExport: async (table, columns, filter = q => q) => {
      const bounds = {};
      filter({ gte(column, value) { bounds.from = value; return this; }, lt(column, value) { bounds.to = value; return this; } });
      reads.push(bounds);
      return sessions.filter(session => !bounds.from || session.date >= bounds.from && session.date < bounds.to);
    },
    db: { from: () => ({ select() { return this; }, eq(column, id) { this.id = id; return this; },
      async maybeSingle() { return { data: sessions.find(session => session.id === this.id) || null, error: null }; }
    }) },
    loadSession: async id => { opened.push(id); return true; }
  });
  vm.runInContext(fs.readFileSync('src/js/utils.js', 'utf8'), context);
  context.todayISO = () => '2026-10-28';
  context.debounce = fn => fn;
  vm.runInContext(fs.readFileSync('src/js/session-browser.js', 'utf8'), context);
  return { context, elements, reads, opened };
}

const sample = [
  { id: 'older', date: '2026-02-15', service_name: 'Youth gathering', attendance_types: { name: 'Sunday Worship', start_time: '17:30:00' } },
  { id: 'current', date: '2026-10-25', attendance_types: { name: 'Sunday Worship' } },
  { id: 'today', date: '2026-10-28', attendance_types: { name: '<Prayer> "Evening"', start_time: '17:30:00' } }
];

test('initial browser fetches current month only and card actions open the correct session', async () => {
  const env = setup(sample);
  await env.context.initSessionBrowser();
  assert.deepEqual(env.reads, [{ from: '2026-10-01', to: '2026-11-01' }]);
  assert.match(env.elements.sessionCards.innerHTML, /Today/);
  assert.match(env.elements.sessionCards.innerHTML, /&lt;Prayer&gt; &quot;Evening&quot;/);
  assert.match(env.elements.sessionCards.innerHTML, /5:30 PM/);
  assert.doesNotMatch(env.elements.sessionCards.innerHTML, /Youth gathering/);
  env.elements.sessionCards.listeners.click({ target: { closest: () => ({ dataset: { openSession: 'today' } }) } });
  assert.deepEqual(env.opened, ['today']);
});

test('search spans all history and matches service names and human-readable or ISO dates', async () => {
  const env = setup(sample);
  await env.context.initSessionBrowser();
  env.elements.sessionBrowserSearch.value = 'February 15, 2026';
  await env.context.loadSessionBrowser();
  assert.deepEqual(env.reads[1], {});
  assert.match(env.elements.sessionCards.innerHTML, /Youth gathering/);
  assert.equal(env.context.filterBrowserSessions(sample, '2026-02-15').length, 1);
  assert.equal(env.context.filterBrowserSessions(sample, 'Sunday Worship').length, 2);
  env.elements.sessionBrowserSearch.value = 'Youth';
  await env.context.loadSessionBrowser();
  assert.equal(env.reads.length, 2);
});

test('month navigation crosses years, and direct links select the historical session month', async () => {
  const env = setup(sample, '?session=older');
  await env.context.initSessionBrowser();
  assert.equal(env.elements.sessionBrowserMonth.value, '2');
  assert.deepEqual(env.opened, ['older']);
  env.elements.sessionBrowserMonth.value = '1';
  env.elements.sessionBrowserYear.value = '2026';
  env.context.moveSessionBrowserMonth(-1);
  assert.equal(env.elements.sessionBrowserMonth.value, '12');
  assert.equal(env.elements.sessionBrowserYear.value, '2025');
});

test('missing links, empty results and failed reads remain recoverable', async () => {
  const missing = setup([], '?session=missing');
  await missing.context.initSessionBrowser();
  assert.match(missing.elements.sessionBrowserStatus.textContent, /not found/);
  assert.match(missing.elements.sessionCards.innerHTML, /No sessions found/);
  const failed = setup();
  failed.context.fetchAllRowsForExport = async () => { throw new Error('offline'); };
  await failed.context.loadSessionBrowser();
  assert.match(failed.elements.sessionCards.innerHTML, /Retry/);
  assert.equal(failed.elements.sessionCards['aria-busy'], 'false');
});

test('session cards paginate without rendering the entire history', async () => {
  const env = setup(Array.from({ length: 30 }, (_, index) => ({
    id: `session-${index}`, date: '2026-10-01', attendance_types: { name: 'Service' }
  })));
  await env.context.initSessionBrowser();
  assert.equal((env.elements.sessionCards.innerHTML.match(/<article /g) || []).length, 12);
  assert.equal(env.elements.sessionBrowserPageLabel.textContent, 'Page 1 of 3');
  env.elements.nextSessionPage.listeners.click();
  env.elements.nextSessionPage.listeners.click();
  assert.equal((env.elements.sessionCards.innerHTML.match(/<article /g) || []).length, 6);
  assert.equal(env.elements.nextSessionPage.disabled, true);
});

test('late browser responses cannot replace the latest selected month', async () => {
  const env = setup();
  const pending = [];
  env.context.fetchAllRowsForExport = () => new Promise(resolve => pending.push(resolve));
  const older = env.context.loadSessionBrowser();
  env.elements.sessionBrowserMonth.value = '2';
  const newer = env.context.loadSessionBrowser();
  pending[1]([sample[0]]);
  await newer;
  pending[0]([sample[1]]);
  await older;
  assert.match(env.elements.sessionCards.innerHTML, /February 15, 2026/);
  assert.doesNotMatch(env.elements.sessionCards.innerHTML, /October 25, 2026/);
});

test('session switching preserves drafts on cancel or reopening the selected card', async () => {
  const env = setup(sample);
  Object.assign(env.context, {
    currentSessionId: 'current', allSessions: sample, allPeople: [],
    sessionLoadRequest: 0, attendancePeopleLoaded: true,
    attendance: { person: { status: 'present', remarks: '' } }, existingRecords: {},
    confirm: () => false
  });
  const html = fs.readFileSync('src/attendance.html', 'utf8');
  vm.runInContext(html.slice(html.indexOf('    function hasAttendanceChanges()'), html.indexOf('    function filteredPeople()')), env.context);
  assert.equal(await env.context.loadSession('older'), false);
  assert.equal(env.context.currentSessionId, 'current');
  assert.equal(env.context.attendance.person.status, 'present');
  assert.equal(await env.context.loadSession('current'), true);
  assert.equal(env.reads.length, 0);
});

test('rapid session opens ignore an older attendance response and retain the latest roster', async () => {
  const env = setup(sample);
  const pending = new Map();
  Object.assign(env.context, {
    currentSessionId: null, allSessions: sample, allPeople: [],
    sessionLoadRequest: 0, attendancePeopleLoaded: false,
    attendance: {}, existingRecords: {}, confirm: () => true,
    renderPeopleList() {}, renderSessionCards() {}, history: { replaceState() {} },
    fetchAllRowsForExport: (table, columns, filter) => {
      if (table === 'people') return Promise.resolve([{ id: 'person', first_name: 'Jane', last_name: 'Doe' }]);
      const sessionId = filter({ eq(column, id) { return { id }; } }).id;
      return new Promise(resolve => pending.set(sessionId, resolve));
    }
  });
  const html = fs.readFileSync('src/attendance.html', 'utf8');
  vm.runInContext(html.slice(html.indexOf('    function hasAttendanceChanges()'), html.indexOf('    function filteredPeople()')), env.context);
  const older = env.context.loadSession('older');
  const newer = env.context.loadSession('current');
  pending.get('current')([{ id: 'record-b', person_id: 'person', status: 'absent' }]);
  assert.equal(await newer, true);
  pending.get('older')([{ id: 'record-a', person_id: 'person', status: 'present' }]);
  assert.equal(await older, false);
  assert.equal(env.context.currentSessionId, 'current');
  assert.equal(env.context.attendance.person.status, 'absent');
  assert.equal(env.elements.exportBtn.disabled, false);
});
