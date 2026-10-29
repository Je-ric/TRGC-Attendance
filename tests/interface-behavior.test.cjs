const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

test('dashboard counts load independently and failures are unavailable instead of false zeroes', async () => {
  const elements = {};
  const requests = [];
  const context = vm.createContext({
    Intl, Date,
    document: { getElementById: id => elements[id] ??= { textContent: '' } },
    db: { from(table) {
      const state = { table, filters: [] };
      return {
        select(columns, options) { assert.equal(options.head, true); state.columns = columns; return this; },
        eq(column, value) { state.filters.push([column, value]); return this; },
        then(resolve, reject) {
          requests.push(state);
          return Promise.resolve(table === 'families' ? { error: { message: 'offline' } }
            : { count: table === 'people' ? 1234 : 0, error: null }).then(resolve, reject);
        }
      };
    } }
  });
  vm.runInContext(fs.readFileSync('assets/js/dashboard.js', 'utf8'), context);
  await context.loadStats();
  assert.equal(requests.length, 4);
  assert.equal(elements.statPeople.textContent, '1,234');
  assert.equal(elements.statFamilies.textContent, '—');
  assert.equal(elements.statTodayEvents.textContent, '0');
  assert.match(elements.dashboardStatsStatus.textContent, /could not be loaded/);
  assert.equal(requests.find(request => request.table === 'attendance_records').filters.length, 2);
});

function cardContext(page, startMarker, endMarker, state) {
  const elements = {};
  const actions = [];
  const context = vm.createContext({
    ...state,
    document: { getElementById: id => elements[id] ??= {
      innerHTML: '', textContent: '', addEventListener(event, handler) { this.handler = handler; }
    } },
    renderPagination() {},
    filtered: () => state.allFamilies,
    viewFamily: id => actions.push(['view', id]),
    editFamily: id => actions.push(['edit', id]),
    deleteFamily: (...args) => actions.push(['delete', ...args]),
    editType: id => actions.push(['edit', id]),
    deleteType: (...args) => actions.push(['delete', ...args])
  });
  vm.runInContext(fs.readFileSync('assets/js/utils.js', 'utf8'), context);
  const html = fs.readFileSync(`pages/${page}`, 'utf8');
  vm.runInContext(html.slice(html.indexOf(startMarker), html.indexOf(endMarker)), context);
  return { context, elements, actions };
}

test('family cards escape stored fields and retain delegated view/edit/delete actions', () => {
  const family = { id: 'family-1', family_name: '<Santos> "Family"', people: [{ id: 'person-1' }] };
  const env = cardContext('families.html', '    function renderTable()', '    function openAddModal()', {
    allFamilies: [family], currentPage: 1, PAGE_SIZE: 15
  });
  env.context.renderTable();
  assert.match(env.elements.familiesBody.innerHTML, /&lt;Santos&gt; &quot;Family&quot;/);
  for (const action of ['view', 'edit', 'delete']) {
    env.elements.familiesBody.handler({ target: { closest: () => ({ dataset: { familyAction: action, id: 'family-1' } }) } });
  }
  assert.equal(env.actions.length, 3);
  assert.deepEqual(env.actions[2], ['delete', 'family-1', family.family_name, 1]);
});

test('service cards preserve statuses, schedule fields and safe action arguments', () => {
  const service = { id: 'service-1', name: 'Youth "Special" <Service>', is_active: true, is_recurring: true,
    day_of_week: 'Sunday', start_time: '10:00', location: 'Main Hall' };
  const env = cardContext('events.html', '    function renderTypes()', '    function openTypeModal(', { allTypes: [service] });
  env.context.renderTypes();
  assert.match(env.elements.typesBody.innerHTML, /&lt;Service&gt;/);
  assert.match(env.elements.typesBody.innerHTML, /Sunday/);
  assert.match(env.elements.typesBody.innerHTML, /Main Hall/);
  env.elements.typesBody.handler({ target: { closest: () => ({ dataset: { serviceAction: 'delete', id: 'service-1' } }) } });
  assert.deepEqual(env.actions[0], ['delete', 'service-1', service.name]);
});
