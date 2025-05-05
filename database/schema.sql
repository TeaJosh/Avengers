-- Database creation
DROP DATABASE IF EXISTS tranablog;
CREATE DATABASE tranablog DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tranablog;

-- Create the database user with necessary permissions
DROP USER IF EXISTS 'bloguser'@'localhost';
CREATE USER 'bloguser'@'localhost' IDENTIFIED BY 'p@ssword';

-- Grant all privileges correctly
GRANT ALL PRIVILEGES ON tranablog.* TO 'bloguser'@'localhost';
FLUSH PRIVILEGES;

-- Disable foreign key checks temporarily to avoid ordering issues
SET FOREIGN_KEY_CHECKS=0;

-- Roles table
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User info table
CREATE TABLE users_info (
    user_id INT PRIMARY KEY,
    fname VARCHAR(255),
    lname VARCHAR(255),
    address VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    occupation VARCHAR(50),
    bio TEXT,
    pfp VARCHAR(255) DEFAULT 'default.jpg',
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Topics table
CREATE TABLE topics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Posts table
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    topic_id INT NOT NULL,
    created_by INT NOT NULL,
    title VARCHAR(50) NOT NULL,
    content TEXT NOT NULL,
    date_posted DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_topic (topic_id),
    FULLTEXT INDEX idx_content (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments table
CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    created_by INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_post_id (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rankings table
CREATE TABLE rankings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    created_by INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_post_rating (post_id, created_by),
    INDEX idx_post_rating (post_id, rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Now add all foreign key constraints separately
-- This approach is more robust because tables are created first, then relationships added

-- Users foreign keys
ALTER TABLE users
    ADD CONSTRAINT fk_users_role_id FOREIGN KEY (role_id) REFERENCES roles(id) ON UPDATE CASCADE;

-- Users info foreign keys
ALTER TABLE users_info
    ADD CONSTRAINT fk_users_info_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Topics foreign keys
ALTER TABLE topics
    ADD CONSTRAINT fk_topics_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON UPDATE CASCADE;

-- Posts foreign keys
ALTER TABLE posts
    ADD CONSTRAINT fk_posts_topic_id FOREIGN KEY (topic_id) REFERENCES topics(id) ON UPDATE CASCADE,
    ADD CONSTRAINT fk_posts_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Comments foreign keys
ALTER TABLE comments
    ADD CONSTRAINT fk_comments_post_id FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_comments_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Rankings foreign keys
ALTER TABLE rankings
    ADD CONSTRAINT fk_rankings_post_id FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_rankings_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Add initial roles
INSERT INTO roles (name, description) VALUES
('admin', 'Full access to all features'),
('moderator', 'Can manage posts, comments, and topics'),
('user', 'Can create posts and comments');

-- Add initial admin user (password: admin123)
INSERT INTO users (username, password, role_id) VALUES
('admin', '$2y$10$qPvfYSj.NGqYaL7Kn9QKoO7.nCCF2yIGQm7DYAVwEwh3hIv7fX4J6', 1);

-- Add admin user info
INSERT INTO users_info (user_id, fname, lname, email) VALUES
(1, 'Admin', 'User', 'admin@example.com');

-- Add initial topic
INSERT INTO topics (name, description, created_by) VALUES
('General', 'General discussion topics', 1);

-- Add initial post
INSERT INTO posts (topic_id, created_by, title, content) VALUES
(1, 1, 'Welcome to the Blog', 'This is the first post on our new blog platform!');
