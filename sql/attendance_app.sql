-- Table: users
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  `enrollment_no` VARCHAR(50),
  `email` VARCHAR(100),
  `password` VARCHAR(255),
  `role` ENUM('admin', 'student')
);

-- Table: attendance
CREATE TABLE `attendance` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `member_id` INT NOT NULL,
  `date` DATE NOT NULL,
  `status` ENUM('present', 'absent') NOT NULL,
  FOREIGN KEY (`member_id`) REFERENCES `users`(`id`)
);