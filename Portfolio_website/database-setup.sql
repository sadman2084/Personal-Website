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
