const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

function setup(now = '2026-10-27T16:05:00Z') {
  const elements = {};
  class FixedDate extends Date {
    constructor(...args) { super(...(args.length ? args : [now])); }
  }
  const context = vm.createContext({
    Date: FixedDate, Intl,
    setInterval() {},
    document: {
      getElementById: id => elements[id] ??= { textContent: '', setAttribute(name, value) { this[name] = value; } },
      querySelector: () => null
    }
  });
  vm.runInContext(fs.readFileSync('src/js/utils.js', 'utf8'), context);
  return { context, elements };
}

test('service times use AM/PM and handle midnight, noon and missing or invalid values', () => {
  const { context } = setup();
  for (const [input, expected] of [
    ['17:30:00', '5:30 PM'], ['09:09', '9:09 AM'], ['00:00:00', '12:00 AM'],
    ['12:00:00', '12:00 PM'], ['23:59:59.999', '11:59 PM'],
    ['', '—'], [null, '—'], ['24:00:00', '—'], ['09:60', '—'], ['invalid', '—']
  ]) assert.equal(context.formatTime(input), expected);
});

test('display dates retain calendar dates, with clear fallback for invalid or missing dates', () => {
  const { context } = setup();
  assert.equal(context.formatDate('2026-10-28'), 'October 28, 2026');
  assert.equal(context.dayOfWeek('2026-10-28'), 'Wednesday');
  assert.equal(context.formatDate(null), '—');
  assert.equal(context.formatDate('invalid'), '—');
  assert.equal(context.dayOfWeek('invalid'), '');
});

test('today and banner clock use Philippine date and time across the UTC day boundary', () => {
  const { context, elements } = setup();
  assert.equal(context.todayISO(), '2026-10-28');
  context.initHeroBanner();
  assert.match(elements.heroClock.textContent, /12:05\s*AM/);
  assert.match(elements.heroDate.textContent, /Wednesday, October 28, 2026/);
  assert.equal(elements.heroGreeting['aria-label'], 'Good Morning, Ka-TRGC');
});
