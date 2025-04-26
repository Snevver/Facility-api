DROP DATABASE IF EXISTS catering_api;
CREATE DATABASE catering_api;
USE catering_api;

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(100),
    address VARCHAR(255),
    zip_code VARCHAR(20),
    country_code CHAR(2),
    phone_number VARCHAR(20)
);

CREATE TABLE facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    creation_date DATE,
    location_id INT,
    FOREIGN KEY (location_id) REFERENCES locations(id)
);

CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE
);

CREATE TABLE facility_tags (
    facility_id INT,
    tag_id INT,
    PRIMARY KEY (facility_id, tag_id),
    FOREIGN KEY (facility_id) REFERENCES facilities(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);

INSERT INTO locations (city, address, zip_code, country_code, phone_number)
VALUES
('Amsterdam', 'Teststraat 1', '1234AB', 'NL', '+31 20 123 4567'),
('Rotterdam', 'Placeholderstraat 99', '5678CD', 'NL', '+31 10 987 6543'),
('Almere', 'Ditiseenstraatstraat 123', '0000EF', 'NL', '+31 30 765 4321');

INSERT INTO facilities (name, creation_date, location_id)
VALUES
('Facility A', '2025-04-01', 1),
('Facility B', '2025-04-02', 2),
('Facility C', '2025-04-03', 3);

INSERT INTO tags (name)
VALUES
('Food'),
('Drinks'),
('Catering'),
('Events');

INSERT INTO facility_tags (facility_id, tag_id)
VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(3, 1);