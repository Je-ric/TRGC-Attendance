const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('assets/js/export.js', 'utf8');

function setup(tables, options = {}) {
  const calls = [];
  let downloaded;
  let blob;
  const button = { disabled: false, textContent: '' };
  const status = { textContent: '' };
  const context = vm.createContext({
    Blob,
    setTimeout: fn => fn(),
    URL: {
      createObjectURL(value) { blob = value; return 'blob:backup'; },
      revokeObjectURL() {}
    },
    document: {
      getElementById: id => id === 'backupExportBtn' ? button : status,
      body: { appendChild() {} },
      createElement: () => ({ click() { downloaded = this.download; }, remove() {} })
    },
    db: {
      from(table) {
        const state = { table, head: false, cursor: null, filters: [] };
        const query = {
          select(columns, settings = {}) { state.head = !!settings.head; return this; },
          order(column) { assert.equal(column, 'id'); return this; },
          limit(size) { state.limit = size; return this; },
          gt(column, value) { assert.equal(column, 'id'); state.cursor = value; return this; },
          eq(column, value) { state.filters.push([column, value]); return this; },
          then(resolve, reject) {
            calls.push({ ...state });
            const rows = (tables[table] || []).filter(row =>
              state.filters.every(([column, value]) => row[column] === value));
            let result;
            if (options.fail?.(state)) result = { data: null, error: { message: 'request failed' } };
            else if (state.head) result = { count: rows.length + (options.countOffset || 0), error: null };
            else result = { data: rows.filter(row => !state.cursor || row.id > state.cursor)
              .slice(0, Math.min(state.limit, options.serverCap || 500)), error: null };
            return Promise.resolve(result).then(resolve, reject);
          }
        };
        return query;
      }
    }
  });
  vm.runInContext(source, context);
  return { context, calls, button, status, download: () => downloaded, blob: () => blob };
}

function rows(count) {
  return Array.from({ length: count }, (_, index) => ({
    id: String(index + 1).padStart(8, '0'),
    first_name: 'José', last_name: "O'Brien", notes: 'Line 1\n"Line 2"'
  }));
}

test('exports more than 1,000 rows even when server caps batches below 500', async () => {
  const input = rows(1203);
  const env = setup({ people: input }, { serverCap: 137 });
  const result = await env.context.fetchAllRowsForExport('people');
  assert.equal(JSON.stringify(result), JSON.stringify(input));
  assert.ok(env.calls.filter(call => !call.head).length > 8);
});

test('empty tables are valid and filters apply to counts and every batch', async () => {
  const env = setup({ attendance_records: [
    { id: 'a', attendance_session_id: 'selected' },
    { id: 'b', attendance_session_id: 'other' }
  ] });
  assert.equal((await env.context.fetchAllRowsForExport('families')).length, 0);
  const result = await env.context.fetchAllRowsForExport('attendance_records', '*',
    query => query.eq('attendance_session_id', 'selected'));
  assert.equal(result.length, 1);
  assert.equal(result[0].id, 'a');
  assert.ok(env.calls.filter(call => call.table === 'attendance_records')
    .every(call => call.filters.length === 1));
});

test('failed batches and mismatched counts reject instead of returning partial data', async () => {
  const failing = setup({ people: rows(1001) }, { fail: state => !!state.cursor });
  await assert.rejects(failing.context.fetchAllRowsForExport('people'), /Unable to export/);
  const mismatch = setup({ people: rows(3) }, { countOffset: 1 });
  await assert.rejects(mismatch.context.fetchAllRowsForExport('people'), /changed during export/);
});

test('full backup includes every table, original fields, counts, and timestamps', async () => {
  const input = rows(1203);
  const env = setup({ people: input, attendance_types: [{ id: 'event', is_active: false }] });
  await env.context.exportAllData();
  const backup = JSON.parse(await env.blob().text());
  assert.deepEqual(Object.keys(backup.tables), [
    'families', 'people', 'attendance_types', 'attendance_sessions',
    'attendance_records', 'attendance_summaries'
  ]);
  assert.deepEqual(backup.tables.people, input);
  assert.equal(backup.row_counts.people, 1203);
  assert.equal(backup.tables.attendance_types[0].is_active, false);
  assert.deepEqual(backup.tables.families, []);
  assert.ok(backup.started_at && backup.completed_at);
  assert.match(env.download(), /^trgc_backup_.*\.json$/);
  assert.equal(env.button.disabled, false);
});

test('backup failure downloads nothing and permits retry; duplicate clicks export once', async () => {
  let fail = true;
  const env = setup({ people: rows(2) }, { fail: state => fail && state.table === 'people' });
  await env.context.exportAllData();
  assert.equal(env.download(), undefined);
  assert.match(env.status.textContent, /Export failed/);
  assert.equal(env.button.disabled, false);
  fail = false;
  env.calls.length = 0;
  await Promise.all([env.context.exportAllData(), env.context.exportAllData()]);
  assert.ok(env.download());
  assert.equal(env.calls.filter(call => call.table === 'people' && call.head).length, 2);
});

test('session CSV includes unloaded people and saved records while preserving draft edits', async () => {
  const html = fs.readFileSync('pages/attendance.html', 'utf8');
  const start = html.indexOf('    async function exportAttendance()');
  const end = html.indexOf('\n    init();', start);
  let exported;
  const context = vm.createContext({
    currentSessionId: 'session-a',
    allSessions: [{ id: 'session-a' }],
    attendance: {
      edited: { status: null, remarks: 'Cleared in draft' },
      unchanged: { status: null, remarks: '' }
    },
    existingRecords: { edited: { status: 'present', remarks: '' } },
    document: { getElementById: () => ({ innerHTML: 'Export', disabled: false }) },
    fetchAllPeopleForExport: async () => [
      { id: 'edited' }, { id: 'unchanged' }, { id: 'unloaded' }
    ],
    fetchAllRowsForExport: async () => [
      { person_id: 'edited', status: 'present' },
      { person_id: 'unchanged', status: 'present' },
      { person_id: 'unloaded', status: 'absent' }
    ],
    exportAttendanceToCSV: async (session, data) => { exported = { session, data }; },
    showToast: message => assert.fail(message)
  });
  vm.runInContext(html.slice(start, end), context);
  await context.exportAttendance();
  assert.equal(exported.session.id, 'session-a');
  assert.equal(exported.data.length, 3);
  assert.equal(exported.data[0].status, 'unmarked');
  assert.equal(exported.data[0].remarks, 'Cleared in draft');
  assert.equal(exported.data[1].status, 'present');
  assert.equal(exported.data[2].status, 'absent');
});

test('all inline page scripts parse successfully', () => {
  for (const file of ['index.html', ...fs.readdirSync('pages').filter(file => file.endsWith('.html')).map(file => `pages/${file}`)]) {
    const html = fs.readFileSync(`${file}`, 'utf8');
    for (const match of html.matchAll(/<script\b[^>]*>([\s\S]*?)<\/script>/gi)) {
      new vm.Script(match[1], { filename: file });
    }
  }
});
