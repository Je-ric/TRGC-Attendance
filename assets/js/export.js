// ── Export Functions ─────────────────────────────────────────────

/**
 * Export all people to CSV with complete details
 * @param {Array} people - Array of people objects from database
 */
async function exportPeopleToCSV(people) {
  if (!people || people.length === 0) {
    showToast('No people data to export', 'warning');
    return;
  }

  // Define CSV headers for complete person details
  const headers = [
    'First Name',
    'Last Name', 
    'Full Name',
    'Birthdate',
    'Age',
    'Gender',
    'Civil Status',
    'Category',
    'Membership Status',
    'Family',
    'Address',
    'Contact Number',
    'Email',
    'Joined Date',
    'Date of Baptism',
    'Notes'
  ];

  // Convert people data to CSV rows
  const rows = people.map(person => {
    const fullName = `${person.first_name} ${person.last_name}`;
    const familyName = person.families?.family_name || '';
    const displayAge = person.age !== null ? person.age : calcAge(person.birthdate);
    const ec = person.category || autoCategory(calcAge(person.birthdate)) || 'Adults';
    
    return [
      person.first_name || '',
      person.last_name || '',
      fullName,
      person.birthdate || '',
      displayAge !== null ? displayAge : '',
      person.gender || '',
      person.civil_status || '',
      ec,
      person.membership_status || '',
      familyName,
      person.address || '',
      person.contact_number || '',
      person.email || '',
      person.joined_date || '',
      person.date_of_baptism || '',
      person.notes || ''
    ].map(csvCell).join(',');
  });

  // Combine headers and rows
  const csvContent = [headers.join(','), ...rows].join('\n');

  // Create and trigger download
  downloadCSV(csvContent, `people_export_${todayISO()}.csv`);
}

/**
 * Export attendance for a specific session, including every person's current state.
 * @param {Object} session - Session object with date and service info
 * @param {Array} attendanceData - Array of attendance records with person details
 */
async function exportAttendanceToCSV(session, attendanceData) {
  if (!attendanceData || attendanceData.length === 0) {
    showToast('No attendance data to export', 'warning');
    return;
  }

  // Define CSV headers for attendance export
  const headers = [
    'Full Name',
    'Category',
    'Membership Status',
    'Gender',
    'Age',
    'Family',
    'Contact Number',
    'Status',
    'Remarks'
  ];

  // Convert attendance data to CSV rows
  const rows = attendanceData.map(record => {
    const person = record.person;
    const fullName = `${person.first_name} ${person.last_name}`;
    const ec = person.category || autoCategory(calcAge(person.birthdate)) || 'Adults';
    const displayAge = person.age !== null ? person.age : calcAge(person.birthdate);
    const familyName = person.families?.family_name || '';
    
    return [
      fullName,
      ec,
      person.membership_status || '',
      person.gender || '',
      displayAge !== null ? displayAge : '',
      familyName,
      person.contact_number || '',
      record.status || '',
      record.remarks || ''
    ].map(csvCell).join(',');
  });

  // Combine headers and rows
  const csvContent = [headers.join(','), ...rows].join('\n');

  // Create filename with session info
  const sessionName = safeFilenamePart(session.attendance_types?.name || 'service');
  const serviceName = session.service_name ? `_${safeFilenamePart(session.service_name)}` : '';
  const filename = `attendance_${sessionName}_${session.date}${serviceName}.csv`;

  // Create and trigger download
  downloadCSV(csvContent, filename);
}

/**
 * Download CSV file
 * @param {string} csvContent - CSV content as string
 * @param {string} filename - Name for the downloaded file
 */
function downloadCSV(csvContent, filename) {
  // Create blob with UTF-8 BOM for Excel compatibility
  const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  
  if (navigator.msSaveBlob) {
    // IE 10+
    navigator.msSaveBlob(blob, filename);
  } else {
    // Modern browsers
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }
  
  showToast('Export completed successfully');
}

// Quote every value, preserve Unicode, and neutralize spreadsheet formulas.
function csvCell(value) {
  let text = value == null ? '' : String(value);
  if (/^[=+\-@]/.test(text)) text = `'${text}`;
  return `"${text.replace(/"/g, '""')}"`;
}

function safeFilenamePart(value) {
  return String(value)
    .trim()
    .replace(/[^a-z0-9_-]+/gi, '_')
    .replace(/^_+|_+$/g, '') || 'export';
}

/**
 * Fetch all people with complete details for export
 */
async function fetchAllPeopleForExport() {
  const people = await fetchAllRowsForExport('people', '*, families(family_name)');
  return people.sort((a, b) =>
    a.last_name.localeCompare(b.last_name) || a.first_name.localeCompare(b.first_name)
  );
}

// Cursor pagination continues even when the server returns fewer than 500 rows.
// Counts are checked separately because cursor filters change the query's count.
async function fetchAllRowsForExport(table, columns = '*', filter = query => query, onProgress = () => {}) {
  async function countRows() {
    const { count, error } = await filter(db.from(table).select('id', { count: 'exact', head: true }));
    if (error) throw new Error(`Unable to count ${table}. Please retry.`);
    if (!Number.isInteger(count) || count < 0) throw new Error(`Unable to verify ${table}. Please retry.`);
    return count;
  }

  const expectedCount = await countRows();
  const rows = [];
  let cursor = null;
  while (true) {
    let query = filter(db.from(table).select(columns)).order('id').limit(500);
    if (cursor !== null) query = query.gt('id', cursor);
    const { data, error } = await query;
    if (error || !Array.isArray(data)) throw new Error(`Unable to export ${table}. Please retry.`);
    if (!data.length) break;
    for (const row of data) {
      if (typeof row.id !== 'string' || (cursor !== null && row.id <= cursor)) {
        throw new Error(`Unable to verify ${table} batches. Please retry.`);
      }
      cursor = row.id;
      rows.push(row);
    }
    onProgress(rows.length, expectedCount);
    if (rows.length > expectedCount) throw new Error(`${table} changed during export. Please retry when editing has stopped.`);
  }

  if (rows.length !== expectedCount || await countRows() !== expectedCount) {
    throw new Error(`${table} changed during export. Please retry when editing has stopped.`);
  }
  return rows;
}

const BACKUP_TABLES = [
  'families', 'people', 'attendance_types', 'attendance_sessions',
  'attendance_records', 'attendance_summaries'
];
let backupExportPending = false;

async function exportAllData() {
  if (backupExportPending) return;
  backupExportPending = true;
  const button = document.getElementById('backupExportBtn');
  const status = document.getElementById('backupExportStatus');
  button.disabled = true;
  button.textContent = 'Exporting…';

  try {
    const backup = {
      format: 'trgc-attendance-data',
      version: 1,
      started_at: new Date().toISOString(),
      completed_at: null,
      row_counts: {},
      tables: {}
    };
    for (const table of BACKUP_TABLES) {
      status.textContent = `Reading ${table.replaceAll('_', ' ')}…`;
      const rows = await fetchAllRowsForExport(table, '*', query => query, (loaded, total) => {
        status.textContent = `${table.replaceAll('_', ' ')}: ${loaded.toLocaleString()} / ${total.toLocaleString()} rows`;
      });
      backup.tables[table] = rows;
      backup.row_counts[table] = rows.length;
    }
    backup.completed_at = new Date().toISOString();
    const blob = new Blob([JSON.stringify(backup, null, 2)], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    try {
      link.href = url;
      link.download = `trgc_backup_${backup.completed_at.replace(/[:.]/g, '-')}.json`;
      document.body.appendChild(link);
      link.click();
    } finally {
      link.remove();
      // Give the browser time to start reading the download before revoking it.
      setTimeout(() => URL.revokeObjectURL(url), 60000);
    }
    const total = Object.values(backup.row_counts).reduce((sum, count) => sum + count, 0);
    status.textContent = `Download started: ${total.toLocaleString()} rows across all ${BACKUP_TABLES.length} tables. Check your Downloads folder.`;
  } catch (error) {
    status.textContent = `Export failed. No backup downloaded. ${error.message}`;
  } finally {
    backupExportPending = false;
    button.disabled = false;
    button.textContent = 'Export All Data';
  }
}
