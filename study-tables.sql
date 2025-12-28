-- Study Sections table
CREATE TABLE IF NOT EXISTS study_sections (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

CREATE INDEX idx_study_sections_admin_id ON study_sections(admin_id);

-- Study Items table
-- Store multiple links as JSON text (MariaDB JSON is an alias for LONGTEXT)
CREATE TABLE IF NOT EXISTS study_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  links_json LONGTEXT, -- JSON array of URLs
  blog_link VARCHAR(500),
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (section_id) REFERENCES study_sections(id) ON DELETE CASCADE
);

CREATE INDEX idx_study_items_section_id ON study_items(section_id);
CREATE INDEX idx_study_items_active ON study_items(is_active);
