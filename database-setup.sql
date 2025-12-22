-- Create database if not exists
CREATE DATABASE IF NOT EXISTS personal;

-- Use the database
USE personal;

-- Create hardware inventory table
CREATE TABLE IF NOT EXISTS hardware_inventory (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  quantity INT NOT NULL DEFAULT 1,
  image LONGBLOB NOT NULL,
  image_type VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for faster queries
CREATE INDEX idx_name ON hardware_inventory(name);
CREATE INDEX idx_created_at ON hardware_inventory(created_at);



-- Create Admins Table for storing admin signup information
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create index for faster email lookups during login
CREATE INDEX idx_email ON admins(email);
CREATE INDEX idx_username ON admins(username);



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

ALTER TABLE admins ADD COLUMN IF NOT EXISTS first_name VARCHAR(100);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS last_name VARCHAR(100);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS bio TEXT;
ALTER TABLE admins ADD COLUMN IF NOT EXISTS phone VARCHAR(20);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS location VARCHAR(255);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS github_url VARCHAR(500);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS linkedin_url VARCHAR(500);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS portfolio_title VARCHAR(255);
ALTER TABLE admins ADD COLUMN IF NOT EXISTS profile_image VARCHAR(500);


CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    read_status TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_read_status (read_status)
);