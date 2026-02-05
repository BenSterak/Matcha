-- Add admin and blocked columns to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0 AFTER last_seen;
ALTER TABLE users ADD COLUMN is_blocked TINYINT(1) NOT NULL DEFAULT 0 AFTER is_admin;

-- Create index for quick admin lookups
CREATE INDEX idx_users_is_admin ON users(is_admin);
CREATE INDEX idx_users_is_blocked ON users(is_blocked);

-- To make a user admin, run:
-- UPDATE users SET is_admin = 1 WHERE email = 'your-admin-email@example.com';
