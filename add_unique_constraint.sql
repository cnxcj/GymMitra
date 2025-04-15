-- Add UNIQUE constraint to username field in members table
ALTER TABLE members ADD UNIQUE (username);
