async function loadStats() {
  const today = new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Manila', year: 'numeric', month: '2-digit', day: '2-digit'
  }).format(new Date());
  const queries = [
    ['statTodayEvents', db.from('attendance_sessions').select('id', { count: 'exact', head: true }).eq('date', today)],
    ['statPeople', db.from('people').select('id', { count: 'exact', head: true })],
    ['statFamilies', db.from('families').select('id', { count: 'exact', head: true })],
    ['statTodayAttendance', db.from('attendance_records')
      .select('id, attendance_sessions!inner(date)', { count: 'exact', head: true })
      .eq('status', 'present').eq('attendance_sessions.date', today)]
  ];
  const results = await Promise.allSettled(queries.map(([, query]) => query));
  let failed = false;
  results.forEach((result, index) => {
    const response = result.status === 'fulfilled' ? result.value : null;
    const valid = response && !response.error && Number.isSafeInteger(response.count) && response.count >= 0;
    document.getElementById(queries[index][0]).textContent = valid ? response.count.toLocaleString('en') : '—';
    if (!valid) failed = true;
  });
  document.getElementById('dashboardStatsStatus').textContent = failed
    ? 'Some totals could not be loaded. Refresh the page to retry.' : '';
}

async function loadRecentSessions() {
  const container = document.getElementById('recentSessions');
  try {
    const { data, error } = await db.from('attendance_sessions')
      .select('id, date, service_name, attendance_types(name)')
      .order('date', { ascending: false }).order('id').limit(6);
    if (error) throw error;
    if (!data?.length) {
      container.innerHTML = '<div class="empty-state"><i class="bx bx-calendar-plus"></i><p>No sessions yet. Create an event to get started.</p><a class="btn btn-outline" href="events.html?action=create">Create Event</a></div>';
      return;
    }
    container.innerHTML = data.map(session => {
      const date = parseLocalDate(session.date);
      return `<a href="attendance.html?session=${encodeURIComponent(session.id)}" class="session-row">
        <span class="session-date" aria-hidden="true">${date.toLocaleDateString('en', { month: 'short' })}<strong>${date.getDate()}</strong></span>
        <div><p class="session-row-name">${escapeHtml(session.attendance_types?.name ?? 'Service')}</p>
          <p class="session-row-meta">${escapeHtml(formatDate(session.date))}${session.service_name ? '<br>' + escapeHtml(session.service_name) : ''}</p></div>
        <i class="bx bx-chevron-right session-row-arrow" aria-hidden="true"></i>
      </a>`;
    }).join('');
  } catch (error) {
    container.innerHTML = '<div class="empty-state"><i class="bx bx-wifi-off"></i><p>Unable to load sessions.</p><button type="button" class="btn btn-outline" onclick="loadRecentSessions()">Retry</button></div>';
  }
}
