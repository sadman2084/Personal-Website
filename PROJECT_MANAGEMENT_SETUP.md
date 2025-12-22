# Project Management System Setup Guide

## 📊 Database Tables

### 1. **Admins Table** (Already created)
```sql
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 2. **Projects Table** (NEW - Create this)
```sql
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    github_link VARCHAR(500),
    live_link VARCHAR(500),
    image_url VARCHAR(500),
    tech_stack VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

CREATE INDEX idx_admin_id ON projects(admin_id);
```

## 🚀 Setup Instructions

### Step 1: Create Projects Table
Execute the SQL query above in phpMyAdmin or use the `projects-table.sql` file

### Step 2: Access the System
1. Go to: `http://localhost/Portfolio_website/login.html`
2. Click "Admin" → "Sign Up" or "Sign In"
3. Once logged in, click "Manage Projects" from the dashboard

### Step 3: Add Your Projects
In the Manage Projects page:
- **Project Title**: Name of your project
- **Description**: What the project does
- **GitHub Link**: Link to your GitHub repository
- **Live Demo Link** (Optional): Link to live demo
- **Image URL**: Thumbnail image for the project
- **Tech Stack**: Technologies used (e.g., HTML, CSS, JavaScript)

### Step 4: View on Portfolio
- All projects you add will automatically appear on your portfolio homepage
- Projects are fetched from the database and displayed dynamically

## 📁 New Files Created

### HTML Pages
- `manage_projects.html` - Project management interface
- `projects-table.sql` - Database schema

### PHP Backend
- `add_project.php` - Add new project
- `get_projects.php` - Get admin's projects
- `get_project.php` - Get single project details
- `update_project.php` - Update project
- `delete_project.php` - Delete project
- `get_all_projects.php` - Get all projects for portfolio

### Updated Files
- `index.html` - Now fetches projects dynamically from database
- `admin_dashboard.html` - Links to Manage Projects page

## 🔐 Security Features

✅ Session-based authentication
✅ Admin can only see/edit their own projects
✅ Password hashing (bcrypt)
✅ SQL injection prevention (prepared statements)
✅ Form validation on client and server side

## 📝 Project Fields

| Field | Required | Type | Example |
|-------|----------|------|---------|
| Title | ✅ | Text | Grid-Guard |
| Description | ✅ | Text | A security... |
| GitHub Link | ❌ | URL | https://github.com/... |
| Live Demo Link | ❌ | URL | https://... |
| Image URL | ❌ | URL | https://via.placeholder.com/... |
| Tech Stack | ❌ | Text | HTML, CSS, JavaScript |

## 💡 Tips

- Use high-quality images (600x360px recommended)
- Add all your projects in the Manage Projects page
- GitHub links are optional but recommended
- Projects appear on your portfolio in reverse chronological order (newest first)
