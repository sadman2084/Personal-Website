// Supabase Configuration
// Get your credentials from: https://app.supabase.com/project/[your-project-id]/settings/api

const SUPABASE_URL = 'https://xfcxyzigtclacpoxaxmu.supabase.co';
const SUPABASE_ANON_KEY = 'YOUR_ANON_PUBLIC_KEY_HERE'; // Copy the ENTIRE "anon public" key from Supabase Dashboard → Settings → API

// Validate configuration
if (SUPABASE_ANON_KEY === 'YOUR_ANON_PUBLIC_KEY_HERE') {
  console.error('❌ SUPABASE_ANON_KEY not set! Get it from: https://app.supabase.com/project/xfcxyzigtclacpoxaxmu/settings/api');
}

// Initialize Supabase Client
const { createClient } = supabase;
const supabaseClient = createClient(SUPABASE_URL, SUPABASE_ANON_KEY);

// Export for use in other files
window.supabase = supabaseClient;
