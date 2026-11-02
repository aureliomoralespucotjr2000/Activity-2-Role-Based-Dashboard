-- Activity 2: Role-Based Dashboard
-- Task: create the grades table.  Run in phpMyAdmin (Import tab, or the SQL tab of wst2_auth).
-- Needs the users table from Activity 1 (activity1_users_table.sql).
-- Running this file again RESETS the grades table.
CREATE DATABASE IF NOT EXISTS wst2_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE wst2_auth;

DROP TABLE IF EXISTS grades;
CREATE TABLE grades (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id  INT UNSIGNED NOT NULL,                  -- the student this grade belongs to
    subject     VARCHAR(50)  NOT NULL,
    grade       DECIMAL(5,2) NOT NULL,
    encoded_by  INT UNSIGNED NULL,                      -- the instructor/admin who typed it
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (encoded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- OPTIONAL: the four test accounts.
-- Skip this part if you already made them with register.php (the lines are safe either way).
-- Passwords: admin1 = Admin@123, instructor1 = Instructor@123, student1 and student2 = Student@123
INSERT IGNORE INTO users (username, email, password_hash, role) VALUES
    ('admin1', 'admin1@school.test', '$2y$10$wXfpTDMJCu4/.nnYw6Gkj.2JG.zbCV9CZxVWxWAdoKazeo5WKNuAK', 'student'),
    ('instructor1', 'instructor1@school.test', '$2y$10$trgo9uWC61D6hsqxn6uz7OOT0aSL0yC2s4MUVQUd2wTEzt55nhffq', 'student'),
    ('student1', 'student1@school.test', '$2y$10$ZCjbnzcjz.oWX3kTi3lmnOSEtyBThofsQ1/.e/CLP2vxgn6h04Cbi', 'student'),
    ('student2', 'student2@school.test', '$2y$10$aqtDwYNow73moDjn.iapTeowTbuvuKm75H1ZMRs/inhuLVxvQRzLC', 'student');

-- Give admin1 and instructor1 their roles (same as phpMyAdmin > users > Edit)
UPDATE users SET role = 'admin'      WHERE username = 'admin1';
UPDATE users SET role = 'instructor' WHERE username = 'instructor1';

-- OPTIONAL: a few sample grades (or encode them yourself as the instructor)
INSERT INTO grades (student_id, subject, grade)
SELECT id, 'Web Systems and Technologies 2', 91.50 FROM users WHERE username = 'student1';
INSERT INTO grades (student_id, subject, grade)
SELECT id, 'Human Computer Interaction 2', 88.00 FROM users WHERE username = 'student1';
INSERT INTO grades (student_id, subject, grade)
SELECT id, 'Web Systems and Technologies 2', 79.25 FROM users WHERE username = 'student2';
