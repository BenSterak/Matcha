-- Add email notification preference to users table
ALTER TABLE users ADD COLUMN email_notifications TINYINT(1) NOT NULL DEFAULT 1 AFTER is_blocked;
