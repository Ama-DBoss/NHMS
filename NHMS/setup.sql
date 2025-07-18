CREATE DATABASE nigerian_hospital_registry;

USE nigerian_hospital_registry;

CREATE TABLE hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    state VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE birth_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    child_name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    place_of_birth VARCHAR(255) NOT NULL,
    father_name VARCHAR(255) NOT NULL,
    mother_name VARCHAR(255) NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id)
);

CREATE TABLE death_certificates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    deceased_name VARCHAR(255) NOT NULL,
    date_of_death DATE NOT NULL,
    place_of_death VARCHAR(255) NOT NULL,
    cause_of_death VARCHAR(255) NOT NULL,
    certificate_number VARCHAR(50) UNIQUE NOT NULL,
    issue_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id)
);