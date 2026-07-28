-- Runs once, only when ./storage/mysql is empty (first boot).
-- The "app" DB + grant for "dev" are handled by MYSQL_DATABASE; this adds flarum.
CREATE DATABASE IF NOT EXISTS flarum CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON flarum.* TO 'dev'@'%';
FLUSH PRIVILEGES;
