// Supabase Configuration
// Get your credentials from: https://app.supabase.com/project/[your-project-id]/settings/api

const SUPABASE_URL = 'https://xfcxyzigtclacpoxaxmu.supabase.co'; // e.g., 'https://xxxx.supabase.co'
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InhmY3h5emlndGNsYWNwb3hheG11Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3Mzk0NDkwNjQsImV4cCI6MjA1NTAyNTA2NH0.6FvdMBYzZfULWq2awpKPxWTxLQOKhY47TfG3J5mS9ws'; // IMPORTANT: Use the "anon public" key from Supabase dashboard, NOT the publishable key!

// Initialize Supabase Client
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Export for use in other files
window.supabase = supabaseClient;
