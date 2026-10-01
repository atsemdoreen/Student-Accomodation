-- Import via phpMyAdmin (http://localhost/phpmyadmin) > Import, or paste in the SQL tab
CREATE DATABASE IF NOT EXISTS student_housing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE student_housing;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(30) NOT NULL,
  student_id VARCHAR(40) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10) NOT NULL UNIQUE,
  name VARCHAR(100) NOT NULL,
  type ENUM('single','shared','ensuite','studio') NOT NULL,
  price DECIMAL(8,2) NOT NULL COMMENT 'per month',
  capacity TINYINT NOT NULL,
  floor TINYINT NOT NULL,
  size_sqm SMALLINT NOT NULL DEFAULT 12,
  bed_type VARCHAR(80) NOT NULL DEFAULT 'Single bed',
  look TEXT NOT NULL,
  amenities VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_id INT NOT NULL,
  move_in DATE NOT NULL,
  status ENUM('active','cancelled') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(150) NOT NULL,
  body TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO rooms (code,name,type,price,capacity,floor,size_sqm,bed_type,look,amenities,description) VALUES
('A-101','Quiet single','single',220.00,1,1,10,'Single bed (90 cm)','White walls, a timber desk under the window and a built-in wardrobe. One large window lets in morning light and faces the quiet side of the building.','Wi-Fi, desk, wardrobe, shared kitchen','A compact private room on the ground floor, close to the study lounge.'),
('A-204','Garden-view single','single',240.00,1,2,11,'Single bed (90 cm)','Same layout as the quiet single, with a small balcony door that opens onto the courtyard garden. Green outlook and the brightest light of the single rooms.','Wi-Fi, desk, wardrobe, balcony','A bright single room overlooking the courtyard garden.'),
('B-110','Twin share','shared',150.00,2,1,18,'Two single beds','A long room with a bed along each side wall, a desk at the foot of each bed and a shared wardrobe by the door. The bathroom is down the corridor.','Wi-Fi, two desks, shared bathroom','Two beds, two desks and a shared bathroom. The most affordable option.'),
('B-215','Four-bed dorm','shared',110.00,4,2,26,'Four bunk-style beds','A wide dorm with two bunk pairs, a locker for every bed and a long shared table. Plain and practical, with a high ceiling and two windows.','Wi-Fi, lockers, shared bathroom','Bunk-style room for four students with personal lockers.'),
('C-301','En-suite single','ensuite',320.00,1,3,14,'Single bed (120 cm)','A private room with a wider bed, a desk by the window and a mini-fridge. The bathroom sits behind a door next to the wardrobe and has its own shower.','Wi-Fi, private bathroom, desk, mini-fridge','Private room with its own bathroom and a mini-fridge.'),
('C-305','En-suite twin','ensuite',230.00,2,3,20,'Two single beds','Two beds with a desk each and a private bathroom shared by the two of you. A large window fills the room with light in the afternoon.','Wi-Fi, private bathroom, two desks','Share with one other student and a private bathroom.'),
('D-401','Studio','studio',430.00,1,4,22,'Double bed (140 cm)','An open-plan studio: bed and desk on one side, a kitchenette with hob and sink on the other and a separate bathroom. The top-floor windows give a wide view over the campus.','Wi-Fi, kitchenette, private bathroom, desk','A self-contained studio with a kitchenette. Best for postgraduates.'),
('D-402','Studio plus','studio',470.00,2,4,28,'Two single beds or one double','The largest studio: sleeping area, a sofa corner, a kitchenette and a private bathroom. Room for two students or one student with a second desk.','Wi-Fi, kitchenette, private bathroom, sofa','A larger studio that fits two students or a student and a guest desk.');
