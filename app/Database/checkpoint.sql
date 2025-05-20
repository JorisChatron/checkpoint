-- Création de la base
CREATE DATABASE IF NOT EXISTS checkpoint CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE checkpoint;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    profile_picture VARCHAR(255) DEFAULT 'images/default-profile.png'
);

-- Table des jeux
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    platform VARCHAR(255) NOT NULL,
    release_date DATE DEFAULT NULL,
    category VARCHAR(100) DEFAULT NULL,
    cover VARCHAR(512) DEFAULT NULL,
    is_custom TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
);

-- Table des stats de jeux par utilisateur
CREATE TABLE game_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    play_time INT DEFAULT 0,
    progress INT DEFAULT 0,
    status VARCHAR(20) DEFAULT NULL,
    notes TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_user_game (user_id, game_id),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- Table des avis (reviews)
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- Table de wishlist
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    status ENUM('souhaité','acheté','joué') DEFAULT 'souhaité',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- Table du top 5 utilisateur (modifiable)
CREATE TABLE user_top_games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    game_id INT NOT NULL,
    position TINYINT NOT NULL, -- 1 à 5
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_position (user_id, position),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

-- Index pour accélérer les filtres
CREATE INDEX idx_platform ON games(platform);
CREATE INDEX idx_category ON games(category);
CREATE INDEX idx_user_id ON user_top_games(user_id);
