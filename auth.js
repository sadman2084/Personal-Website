// Authentication Manager for Supabase
const supabase = window.supabase;

class AuthManager {
  constructor() {
    this.currentUser = null;
    this.currentAdmin = null;
    this.initAuth();
  }

  // Initialize authentication listener
  initAuth() {
    supabase.auth.onAuthStateChange((event, session) => {
      if (session) {
        this.currentUser = session.user;
        this.loadAdminProfile();
      } else {
        this.currentUser = null;
        this.currentAdmin = null;
      }
    });
  }

  // Load admin profile from database
  async loadAdminProfile() {
    try {
      const { data, error } = await supabase
        .from('admins')
        .select('*')
        .eq('email', this.currentUser.email)
        .single();

      if (!error && data) {
        this.currentAdmin = data;
        // Dispatch event to notify app of login
        window.dispatchEvent(new CustomEvent('adminLoggedIn', { detail: data }));
      }
    } catch (err) {
      console.error('Error loading admin profile:', err);
    }
  }

  // Login with email and password
  async login(email, password) {
    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password
      });

      if (error) {
        return { success: false, message: error.message };
      }

      await this.loadAdminProfile();
      return { success: true, message: 'Logged in successfully', user: data.user };
    } catch (err) {
      return { success: false, message: err.message };
    }
  }

  // Register new admin
  async register(email, password, username) {
    try {
      const { data, error } = await supabase.auth.signUp({
        email,
        password
      });

      if (error) {
        return { success: false, message: error.message };
      }

      // Insert admin record
      const { error: insertError } = await supabase
        .from('admins')
        .insert([{
          email,
          username,
          user_id: data.user.id
        }]);

      if (insertError) {
        return { success: false, message: 'Failed to create admin profile' };
      }

      return { success: true, message: 'Registration successful' };
    } catch (err) {
      return { success: false, message: err.message };
    }
  }

  // Logout current user
  async logout() {
    try {
      const { error } = await supabase.auth.signOut();

      if (error) {
        return { success: false, message: error.message };
      }

      this.currentUser = null;
      this.currentAdmin = null;
      window.dispatchEvent(new CustomEvent('adminLoggedOut'));
      return { success: true, message: 'Logged out successfully' };
    } catch (err) {
      return { success: false, message: err.message };
    }
  }

  // Check if user is logged in
  isLoggedIn() {
    return this.currentUser !== null;
  }

  // Get current admin ID
  getAdminId() {
    return this.currentAdmin?.id || null;
  }

  // Get current admin info
  getAdmin() {
    return this.currentAdmin;
  }

  // Update admin profile
  async updateAdmin(updates) {
    try {
      if (!this.currentAdmin) {
        return { success: false, message: 'Not logged in' };
      }

      const { error } = await supabase
        .from('admins')
        .update(updates)
        .eq('id', this.currentAdmin.id);

      if (error) {
        return { success: false, message: error.message };
      }

      // Update local state
      this.currentAdmin = { ...this.currentAdmin, ...updates };
      return { success: true, message: 'Profile updated' };
    } catch (err) {
      return { success: false, message: err.message };
    }
  }

  // Reset password
  async resetPassword(email) {
    try {
      const { error } = await supabase.auth.resetPasswordForEmail(email, {
        redirectTo: `${window.location.origin}/reset-password`
      });

      if (error) {
        return { success: false, message: error.message };
      }

      return { success: true, message: 'Password reset email sent' };
    } catch (err) {
      return { success: false, message: err.message };
    }
  }
}

// Create global auth manager instance
window.authManager = new AuthManager();
