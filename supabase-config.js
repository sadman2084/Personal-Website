// Supabase Configuration
// Get your credentials from: https://app.supabase.com/project/[your-project-id]/settings/api

const SUPABASE_URL = 'https://xfcxyzigtclacpoxaxmu.supabase.co'; // e.g., 'https://xxxx.supabase.co'
const SUPABASE_ANON_KEY = 'sb_publishable_GrUrnoeUlEu0kJkWvDkDOA_0jg7A-Ue'; // Get from API keys in Supabase dashboard

// Initialize Supabase Client
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Export for use in other files
window.supabase = supabaseClient;
