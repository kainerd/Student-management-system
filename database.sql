-- EduTrack Database Setup
CREATE DATABASE IF NOT EXISTS edutrack;
USE edutrack;

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_no VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    course VARCHAR(100) NOT NULL,
    year_level INT NOT NULL DEFAULT 1,
    status ENUM('Active', 'Graduated', 'Inactive', 'Dropped') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    title VARCHAR(150) NOT NULL,
    units INT NOT NULL DEFAULT 3,
    department VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Grades table
CREATE TABLE IF NOT EXISTS grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_id INT NOT NULL,
    grade DECIMAL(5,2) NOT NULL,
    semester ENUM('1st Semester', '2nd Semester', 'Summer') NOT NULL,
    school_year VARCHAR(20) NOT NULL,
    remarks VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO students (student_no, full_name, email, course, year_level, status) VALUES
('2024-0001', 'Alice Johnson', 'alice@school.edu', 'BS Computer Science', 2, 'Active'),
('2024-0002', 'Bob Martinez', 'bob@school.edu', 'BS Information Technology', 1, 'Active'),
('2024-0003', 'Carol White', 'carol@school.edu', 'BS Computer Engineering', 3, 'Active'),
('2023-0021', 'David Kim', 'david@school.edu', 'BS Computer Science', 2, 'Active'),
('2022-0045', 'Eva Cruz', 'eva@school.edu', 'BS Information Systems', 4, 'Graduated');

INSERT INTO subjects (code, title, units, department) VALUES
('CS101', 'Introduction to Computing', 3, 'CS Dept'),
('CS201', 'Data Structures and Algorithms', 3, 'CS Dept'),
('CS301', 'Database Management Systems', 3, 'CS Dept'),
('ENG101', 'Technical Writing', 3, 'English Dept'),
('MATH101', 'Calculus 1', 4, 'Math Dept');
