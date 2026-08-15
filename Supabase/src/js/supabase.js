// ── Replace these with your Supabase project values ──────────
const SUPABASE_URL = 'https://aboxivaqotpnykyhxhsh.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImFib3hpdmFxb3RwbnlreWh4aHNoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODY3Njc0MjYsImV4cCI6MjEwMjM0MzQyNn0.Q-pTTfEbMgGsR1k1GBQKPx-LzSeROFsanZ5k80SKQ5c';
// ─────────────────────────────────────────────────────────────

const { createClient } = supabase;
const db = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);
