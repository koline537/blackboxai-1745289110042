-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('admin', 'student') NOT NULL DEFAULT 'student';
