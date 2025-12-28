-- Alter Admins Table to add profile fields
ALTER TABLE admins ADD COLUMN IF NOT EXISTS first_name VARCHAR(100);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS last_name VARCHAR(100);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS bio TEXT;
ALTER TABLE admins ADD COLUMN IF NOT EXISTS phone VARCHAR(20);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS location VARCHAR(255);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS github_url VARCHAR(500);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS linkedin_url VARCHAR(500);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS portfolio_title VARCHAR(255);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS profile_image VARCHAR(500);

-- Schema: Blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX idx_blogs_admin_id ON blogs(admin_id);
CREATE INDEX idx_blogs_published ON blogs(published);

-- Insert sample row (replace values as needed)
-- Use application code (PHP) for prepared statements instead of '?' in raw SQL.
INSERT INTO blogs (admin_id, title, slug, content, published)
VALUES (1, 'Sample Title', 'sample-title', 'Sample content', 1);

-- List published blogs (for sidebar/topics)
SELECT id, title, slug, created_at
FROM blogs
WHERE published = 1
ORDER BY created_at DESC;

-- Get single blog by id (replace 1 with the target id)
SELECT id, title, slug, content, created_at, updated_at
FROM blogs
WHERE id = 1
  AND published = 1
LIMIT 1;

-- Get single blog by slug (replace 'sample-title' with the target slug)
SELECT id, title, slug, content, created_at, updated_at
FROM blogs
WHERE slug = 'sample-title'
  AND published = 1
LIMIT 1;