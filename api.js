// Supabase API Functions
const supabase = window.supabase;

// ==================== MESSAGES ====================
export const saveMessage = async (data) => {
  try {
    const { name, email, message } = data;

    // Validate inputs
    if (!name || !email || !message) {
      return { success: false, message: 'All fields are required' };
    }

    if (name.length < 2) {
      return { success: false, message: 'Please enter a valid name' };
    }

    if (!isValidEmail(email)) {
      return { success: false, message: 'Please enter a valid email address' };
    }

    if (message.length < 10) {
      return { success: false, message: 'Message must be at least 10 characters long' };
    }

    // Check spam - same email within 5 minutes
    const fiveMinutesAgo = new Date(Date.now() - 5 * 60 * 1000).toISOString();
    const { data: spamCheck, error: spamError } = await supabase
      .from('messages')
      .select('id')
      .eq('email', email)
      .gte('created_at', fiveMinutesAgo);

    if (!spamError && spamCheck.length > 0) {
      return { success: false, message: 'Please wait a few minutes before sending another message' };
    }

    // Insert message
    const { data: newMessage, error } = await supabase
      .from('messages')
      .insert([{ name, email, message }])
      .select();

    if (error) {
      return { success: false, message: 'Failed to send message: ' + error.message };
    }

    return {
      success: true,
      message: 'Message sent successfully! Thank you for contacting me.',
      message_id: newMessage[0].id
    };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getMessages = async (adminId) => {
  try {
    const { data, error } = await supabase
      .from('messages')
      .select('*')
      .eq('admin_id', adminId)
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch messages: ' + error.message };
    }

    return { success: true, messages: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const markMessageRead = async (messageId) => {
  try {
    const { error } = await supabase
      .from('messages')
      .update({ is_read: true })
      .eq('id', messageId);

    if (error) {
      return { success: false, message: 'Failed to mark message as read' };
    }

    return { success: true, message: 'Message marked as read' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteMessage = async (messageId) => {
  try {
    const { error } = await supabase
      .from('messages')
      .delete()
      .eq('id', messageId);

    if (error) {
      return { success: false, message: 'Failed to delete message' };
    }

    return { success: true, message: 'Message deleted successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== PROJECTS ====================
export const getProjects = async (adminId) => {
  try {
    const { data, error } = await supabase
      .from('projects')
      .select('*')
      .eq('admin_id', adminId)
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch projects' };
    }

    return { success: true, projects: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getAllPublicProjects = async () => {
  try {
    const { data, error } = await supabase
      .from('projects')
      .select('*')
      .eq('is_public', true)
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch projects' };
    }

    return { success: true, projects: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getProject = async (projectId) => {
  try {
    const { data, error } = await supabase
      .from('projects')
      .select('*')
      .eq('id', projectId)
      .single();

    if (error) {
      return { success: false, message: 'Project not found' };
    }

    return { success: true, project: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const addProject = async (adminId, projectData) => {
  try {
    const { title, description, github_link, live_link, image_url, tech_stack } = projectData;

    const { data, error } = await supabase
      .from('projects')
      .insert([
        {
          admin_id: adminId,
          title,
          description,
          github_link,
          live_link,
          image_url,
          tech_stack,
          is_public: true
        }
      ])
      .select();

    if (error) {
      return { success: false, message: 'Failed to add project: ' + error.message };
    }

    return { success: true, message: 'Project added successfully', project: data[0] };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const updateProject = async (projectId, projectData) => {
  try {
    const { error } = await supabase
      .from('projects')
      .update(projectData)
      .eq('id', projectId);

    if (error) {
      return { success: false, message: 'Failed to update project' };
    }

    return { success: true, message: 'Project updated successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteProject = async (projectId) => {
  try {
    const { error } = await supabase
      .from('projects')
      .delete()
      .eq('id', projectId);

    if (error) {
      return { success: false, message: 'Failed to delete project' };
    }

    return { success: true, message: 'Project deleted successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== PROFILE ====================
export const getProfile = async (adminId) => {
  try {
    const { data, error } = await supabase
      .from('profile')
      .select('*')
      .eq('admin_id', adminId)
      .single();

    if (error) {
      return { success: false, message: 'Failed to fetch profile' };
    }

    return { success: true, profile: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getPublicProfile = async () => {
  try {
    const { data, error } = await supabase
      .from('profile')
      .select('*')
      .eq('is_public', true)
      .limit(1)
      .single();

    if (error) {
      return { success: false, message: 'Failed to fetch profile' };
    }

    return { success: true, profile: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const updateProfile = async (adminId, profileData) => {
  try {
    const { error } = await supabase
      .from('profile')
      .update(profileData)
      .eq('admin_id', adminId);

    if (error) {
      return { success: false, message: 'Failed to update profile' };
    }

    return { success: true, message: 'Profile updated successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== BLOGS ====================
export const getBlogs = async (adminId) => {
  try {
    const { data, error } = await supabase
      .from('blogs')
      .select('*')
      .eq('admin_id', adminId)
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch blogs' };
    }

    return { success: true, blogs: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getAllPublicBlogs = async () => {
  try {
    const { data, error } = await supabase
      .from('blogs')
      .select('*')
      .eq('is_public', true)
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch blogs' };
    }

    return { success: true, blogs: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getBlog = async (blogId) => {
  try {
    const { data, error } = await supabase
      .from('blogs')
      .select('*')
      .eq('id', blogId)
      .single();

    if (error) {
      return { success: false, message: 'Blog not found' };
    }

    return { success: true, blog: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const addBlog = async (adminId, blogData) => {
  try {
    const { title, content, description, image_url } = blogData;

    const { data, error } = await supabase
      .from('blogs')
      .insert([
        {
          admin_id: adminId,
          title,
          content,
          description,
          image_url,
          is_public: true
        }
      ])
      .select();

    if (error) {
      return { success: false, message: 'Failed to add blog: ' + error.message };
    }

    return { success: true, message: 'Blog added successfully', blog: data[0] };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const updateBlog = async (blogId, blogData) => {
  try {
    const { error } = await supabase
      .from('blogs')
      .update(blogData)
      .eq('id', blogId);

    if (error) {
      return { success: false, message: 'Failed to update blog' };
    }

    return { success: true, message: 'Blog updated successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteBlog = async (blogId) => {
  try {
    const { error } = await supabase
      .from('blogs')
      .delete()
      .eq('id', blogId);

    if (error) {
      return { success: false, message: 'Failed to delete blog' };
    }

    return { success: true, message: 'Blog deleted successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== INVENTORY ====================
export const getInventory = async () => {
  try {
    const { data, error } = await supabase
      .from('hardware_inventory')
      .select('*')
      .order('created_at', { ascending: false });

    if (error) {
      return { success: false, message: 'Failed to fetch inventory' };
    }

    return { success: true, items: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const addInventoryItem = async (itemData) => {
  try {
    const { name, description, quantity, image_type } = itemData;

    const { data, error } = await supabase
      .from('hardware_inventory')
      .insert([{ name, description, quantity, image_type }])
      .select();

    if (error) {
      return { success: false, message: 'Failed to add item: ' + error.message };
    }

    return { success: true, message: 'Item added successfully', item: data[0] };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const updateInventoryItem = async (itemId, itemData) => {
  try {
    const { error } = await supabase
      .from('hardware_inventory')
      .update(itemData)
      .eq('id', itemId);

    if (error) {
      return { success: false, message: 'Failed to update item' };
    }

    return { success: true, message: 'Item updated successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteInventoryItem = async (itemId) => {
  try {
    const { error } = await supabase
      .from('hardware_inventory')
      .delete()
      .eq('id', itemId);

    if (error) {
      return { success: false, message: 'Failed to delete item' };
    }

    return { success: true, message: 'Item deleted successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== STUDY ITEMS ====================
export const getStudySections = async (adminId) => {
  try {
    const { data, error } = await supabase
      .from('study_sections')
      .select('*')
      .eq('admin_id', adminId)
      .order('order', { ascending: true });

    if (error) {
      return { success: false, message: 'Failed to fetch sections' };
    }

    return { success: true, sections: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getStudyItems = async (sectionId) => {
  try {
    const { data, error } = await supabase
      .from('study_items')
      .select('*')
      .eq('section_id', sectionId)
      .order('order', { ascending: true });

    if (error) {
      return { success: false, message: 'Failed to fetch items' };
    }

    return { success: true, items: data };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const addStudySection = async (adminId, sectionData) => {
  try {
    const { data, error } = await supabase
      .from('study_sections')
      .insert([{ admin_id: adminId, ...sectionData }])
      .select();

    if (error) {
      return { success: false, message: 'Failed to add section' };
    }

    return { success: true, section: data[0] };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const addStudyItem = async (sectionId, itemData) => {
  try {
    const { data, error } = await supabase
      .from('study_items')
      .insert([{ section_id: sectionId, ...itemData }])
      .select();

    if (error) {
      return { success: false, message: 'Failed to add item' };
    }

    return { success: true, item: data[0] };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteStudySection = async (sectionId) => {
  try {
    const { error } = await supabase
      .from('study_sections')
      .delete()
      .eq('id', sectionId);

    if (error) {
      return { success: false, message: 'Failed to delete section' };
    }

    return { success: true, message: 'Section deleted' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const deleteStudyItem = async (itemId) => {
  try {
    const { error } = await supabase
      .from('study_items')
      .delete()
      .eq('id', itemId);

    if (error) {
      return { success: false, message: 'Failed to delete item' };
    }

    return { success: true, message: 'Item deleted' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== AUTHENTICATION ====================
export const loginAdmin = async (email, password) => {
  try {
    const { data, error } = await supabase.auth.signInWithPassword({
      email,
      password
    });

    if (error) {
      return { success: false, message: 'Login failed: ' + error.message };
    }

    // Get admin profile data
    const { data: profile } = await supabase
      .from('admins')
      .select('*')
      .eq('email', email)
      .single();

    return {
      success: true,
      message: 'Login successful',
      user: data.user,
      admin: profile
    };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const logoutAdmin = async () => {
  try {
    const { error } = await supabase.auth.signOut();

    if (error) {
      return { success: false, message: 'Logout failed' };
    }

    return { success: true, message: 'Logged out successfully' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const registerAdmin = async (email, password, username) => {
  try {
    const { data, error } = await supabase.auth.signUp({
      email,
      password
    });

    if (error) {
      return { success: false, message: 'Registration failed: ' + error.message };
    }

    // Create admin profile
    const { error: profileError } = await supabase
      .from('admins')
      .insert([{
        email,
        username,
        user_id: data.user.id
      }]);

    if (profileError) {
      return { success: false, message: 'Failed to create profile' };
    }

    return { success: true, message: 'Registration successful' };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

export const getCurrentAdmin = async () => {
  try {
    const { data, error } = await supabase.auth.getUser();

    if (error || !data.user) {
      return { success: false, message: 'No user logged in' };
    }

    // Get admin profile
    const { data: profile } = await supabase
      .from('admins')
      .select('*')
      .eq('email', data.user.email)
      .single();

    return { success: true, user: data.user, admin: profile };
  } catch (err) {
    return { success: false, message: 'Error: ' + err.message };
  }
};

// ==================== UTILITY FUNCTIONS ====================
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
