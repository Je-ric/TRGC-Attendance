function attendanceChanges(draft, original) {
  return Object.entries(draft).filter(([id, value]) =>
    (value.status || null) !== (original[id]?.status || null) || (value.remarks || '') !== (original[id]?.remarks || '')
  ).map(([person_id, value]) => ({ person_id, status: value.status || null, remarks: value.remarks || '' }));
}

function dataSafetyMessage(error) {
  return ['PGRST202', '42883'].includes(error?.code)
    ? 'Database update required: run supabase/data_safety.sql in Supabase to enable safe attendance saves.'
    : String(error?.message || 'Unable to complete the operation. Please retry.');
}

async function readAttendanceSnapshot(sessionId) {
  const { data, error } = await db.rpc('load_attendance_session', { p_session_id: sessionId });
  if (error) {
    if (['PGRST202', '42883'].includes(error.code)) {
      return { revision: null, records: await fetchAllRowsForExport('attendance_records', 'id, person_id, status, remarks',
        query => query.eq('attendance_session_id', sessionId)) };
    }
    throw error;
  }
  if (!data || typeof data.revision !== 'string' || !Array.isArray(data.records)) throw new Error('Unable to verify the attendance snapshot.');
  return data;
}
