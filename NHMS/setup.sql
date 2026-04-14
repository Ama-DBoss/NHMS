CREATE DATABASE IF NOT EXISTS nigerian_hospital_registry;

USE nigerian_hospital_registry;

CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id VARCHAR(50) UNIQUE,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    state VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    last_login TIMESTAMP NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_state (state)
);

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

CREATE TABLE IF NOT EXISTS birth_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    child_name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    time_of_birth TIME,
    place_of_birth VARCHAR(255) NOT NULL,
    gender VARCHAR(50),
    weight DECIMAL(5,2),
    blood_group VARCHAR(10),
    genotype VARCHAR(10),
    father_name VARCHAR(255) NOT NULL,
    mother_name VARCHAR(255) NOT NULL,
    parents_address TEXT,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    INDEX idx_certificate_number (certificate_number),
    INDEX idx_hospital_id (hospital_id),
    INDEX idx_date_of_birth (date_of_birth)
);

CREATE TABLE IF NOT EXISTS death_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    deceased_name VARCHAR(255) NOT NULL,
    date_of_death DATE NOT NULL,
    time_of_death TIME,
    place_of_death VARCHAR(255) NOT NULL,
    cause_of_death VARCHAR(255) NOT NULL,
    age_at_death INT,
    gender VARCHAR(50),
    occupation VARCHAR(255),
    marital_status VARCHAR(50),
    next_of_kin VARCHAR(255),
    next_of_kin_relationship VARCHAR(100),
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    INDEX idx_certificate_number (certificate_number),
    INDEX idx_hospital_id (hospital_id),
    INDEX idx_date_of_death (date_of_death)
);

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    action VARCHAR(255),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    INDEX idx_hospital_id (hospital_id),
    INDEX idx_created_at (created_at)
);

-- Insert default admin user (password: admin@123)
INSERT IGNORE INTO admins (email, password, name) VALUES 
('admin@nhms.gov', '$2y$10$B.8Xy6Q4D7jJ9.8xK2L3cOqQqQqQqQqQqQqQqQqQqQqQqQqQqQqQ', 'System Administrator');
