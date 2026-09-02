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
  const { data, error } = await db
    .from('people')
    .select('*, families(family_name)')
    .order('last_name').order('first_name');
  
  if (error) {
    showToast('Failed to fetch people for export', 'error');
    return null;
  }
  
  return data || [];
}
