// Supabase Configuration
// Get your credentials from: https://app.supabase.com/project/[your-project-id]/settings/api

const SUPABASE_URL = 'https://xfcxyzigtclacpoxaxmu.supabase.co';
const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InhmY3h5emlndGNsYWNwb3hheG11Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzA5ODcyMTgsImV4cCI6MjA4NjU2MzIxOH0.JvXNJI05TQ-Djk44cbqhlJd_ZrgLVJA9JlxtgkLTUtc'; // Copy the ENTIRE "anon public" key from Supabase Dashboard → Settings → API

// Validate configuration
if (SUPABASE_ANON_KEY === 'YOUR_ANON_PUBLIC_KEY_HERE') {
  console.error('❌ SUPABASE_ANON_KEY not set! Get it from: https://app.supabase.com/project/xfcxyzigtclacpoxaxmu/settings/api');
}

// Initialize Supabase Client
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Export for use in other files
window.supabase = supabaseClient;
