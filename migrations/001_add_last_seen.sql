USE matcha;

-- Migration: Add last_seen column for online status feature
-- Run this SQL on your MySQL database

ALTER TABLE users ADD COLUMN last_seen DATETIME NULL DEFAULT NULL;

-- Create index for efficient online status queries
CREATE INDEX idx_users_last_seen ON users(last_seen);
