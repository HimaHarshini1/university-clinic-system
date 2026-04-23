-- ============================================================
-- University Clinic Appointments Management System
-- Database Schema - Group 17
-- CSCE 5350 Fundamentals of Database Systems
-- ============================================================

CREATE DATABASE IF NOT EXISTS university_clinic;
USE university_clinic;

-- -------------------------------------------------------
-- Table: Department
-- -------------------------------------------------------
CREATE TABLE Department (
    department_id   INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL,
    location        VARCHAR(150) NOT NULL
);

-- -------------------------------------------------------
-- Table: Student
-- -------------------------------------------------------
CREATE TABLE Student (
    student_id    INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    date_of_birth DATE         NOT NULL,
    phone         VARCHAR(20)  NOT NULL
);

-- -------------------------------------------------------
-- Table: Supplier
-- -------------------------------------------------------
CREATE TABLE Supplier (
    supplier_id   INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(100) NOT NULL,
    contact       VARCHAR(100) NOT NULL,
    address       VARCHAR(200) NOT NULL
);

-- -------------------------------------------------------
-- Table: Healthcare_Provider
-- -------------------------------------------------------
CREATE TABLE Healthcare_Provider (
    provider_id     INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    specialization  VARCHAR(100) NOT NULL,
    contact         VARCHAR(100) NOT NULL,
    department_id   INT NOT NULL,
    CONSTRAINT fk_provider_dept FOREIGN KEY (department_id)
        REFERENCES Department(department_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- -------------------------------------------------------
-- Table: Appointment
-- -------------------------------------------------------
CREATE TABLE Appointment (
    appointment_id   INT AUTO_INCREMENT PRIMARY KEY,
    appointment_date DATE         NOT NULL,
    status           VARCHAR(20)  NOT NULL,
    student_id       INT          NOT NULL,
    provider_id      INT          NOT NULL,
    CONSTRAINT chk_status CHECK (status IN ('Scheduled','Completed','Cancelled')),
    CONSTRAINT fk_appt_student  FOREIGN KEY (student_id)
        REFERENCES Student(student_id)            ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_appt_provider FOREIGN KEY (provider_id)
        REFERENCES Healthcare_Provider(provider_id) ON DELETE RESTRICT ON UPDATE CASCADE
);

-- -------------------------------------------------------
-- Table: Inventory
-- -------------------------------------------------------
CREATE TABLE Inventory (
    item_id       INT AUTO_INCREMENT PRIMARY KEY,
    item_name     VARCHAR(150) NOT NULL,
    quantity      INT          NOT NULL,
    expiry_date   DATE         NOT NULL,
    department_id INT          NOT NULL,
    supplier_id   INT          NOT NULL,
    CONSTRAINT chk_qty CHECK (quantity >= 0),
    CONSTRAINT fk_inv_dept     FOREIGN KEY (department_id)
        REFERENCES Department(department_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_inv_supplier FOREIGN KEY (supplier_id)
        REFERENCES Supplier(supplier_id)     ON DELETE RESTRICT ON UPDATE CASCADE
);

-- ============================================================
-- SEED DATA
-- ============================================================

INSERT INTO Department (department_name, location) VALUES
('General Medicine',   'Building A, Room 101'),
('Mental Health',      'Building B, Room 205'),
('Orthopedics',        'Building A, Room 310'),
('Pharmacy',           'Building C, Room 102'),
('Dermatology',        'Building B, Room 118');

INSERT INTO Supplier (supplier_name, contact, address) VALUES
('McKesson Corporation',   '1-800-McKesson', '6555 North MacArthur Blvd, Irving, TX'),
('Medline Industries',     '1-800-Medline',  '3 Lakes Drive, Northfield, IL'),
('Cardinal Health',        '1-800-Cardinal', '7000 Cardinal Place, Dublin, OH'),
('Owens & Minor',          '1-800-Owens',   '9120 Lockwood Blvd, Mechanicsville, VA'),
('Henry Schein',           '1-800-Schein',  '135 Duryea Road, Melville, NY'),
('Bound Tree Medical',     '1-800-Bound',   '5000 Tuttle Crossing Blvd, Dublin, OH'),
('Patterson Companies',    '1-800-Patterson','1031 Mendota Heights Rd, St. Paul, MN'),
('Concordance Healthcare', '1-800-Concordance','111 Innovation Drive, Twinsburg, OH');

INSERT INTO Healthcare_Provider (name, specialization, contact, department_id) VALUES
('Dr. Sarah Williams',     'Physician',            'swilliams@clinic.edu', 1),
('Dr. James Patel',        'Physician',            'jpatel@clinic.edu',    1),
('Dr. Emily Chen',         'Counselor',            'echen@clinic.edu',     2),
('Dr. Marcus Johnson',     'Psychiatrist',         'mjohnson@clinic.edu',  2),
('Dr. Priya Sharma',       'Orthopedic Surgeon',   'psharma@clinic.edu',   3),
('Nurse Linda Torres',     'Nurse Practitioner',   'ltorres@clinic.edu',   1),
('Dr. Kevin O\'Brien',     'Pharmacist',           'kobrien@clinic.edu',   4),
('Dr. Aisha Malik',        'Dermatologist',        'amalik@clinic.edu',    5),
('Dr. Robert Kim',         'Physical Therapist',   'rkim@clinic.edu',      3),
('Nurse Jessica Park',     'Nurse Practitioner',   'jpark@clinic.edu',     2);

INSERT INTO Student (name, email, date_of_birth, phone) VALUES
('Alice Johnson',    'ajohnson@students.edu',  '2001-03-15', '940-555-0101'),
('Bob Smith',        'bsmith@students.edu',    '2000-07-22', '940-555-0102'),
('Carol Davis',      'cdavis@students.edu',    '2002-01-10', '940-555-0103'),
('David Lee',        'dlee@students.edu',      '2001-11-05', '940-555-0104'),
('Eva Martinez',     'emartinez@students.edu', '2003-04-28', '940-555-0105'),
('Frank Wilson',     'fwilson@students.edu',   '2000-09-18', '940-555-0106'),
('Grace Taylor',     'gtaylor@students.edu',   '2002-06-03', '940-555-0107'),
('Henry Brown',      'hbrown@students.edu',    '2001-12-25', '940-555-0108'),
('Isabel Garcia',    'igarcia@students.edu',   '2000-02-14', '940-555-0109'),
('Jake Thomas',      'jthomas@students.edu',   '2003-08-30', '940-555-0110'),
('Karen Anderson',   'kanderson@students.edu', '2001-05-19', '940-555-0111'),
('Liam Jackson',     'ljackson@students.edu',  '2002-10-07', '940-555-0112'),
('Mia White',        'mwhite@students.edu',    '2000-03-22', '940-555-0113'),
('Noah Harris',      'nharris@students.edu',   '2001-07-11', '940-555-0114'),
('Olivia Martin',    'omartin@students.edu',   '2003-01-29', '940-555-0115'),
('Paul Thompson',    'pthompson@students.edu', '2000-11-16', '940-555-0116'),
('Quinn Robinson',   'qrobinson@students.edu', '2002-04-05', '940-555-0117'),
('Rachel Clark',     'rclark@students.edu',    '2001-09-23', '940-555-0118'),
('Samuel Lewis',     'slewis@students.edu',    '2003-06-12', '940-555-0119'),
('Tara Walker',      'twalker@students.edu',   '2000-12-01', '940-555-0120'),
('Uma Hall',         'uhall@students.edu',     '2002-02-17', '940-555-0121'),
('Victor Allen',     'vallen@students.edu',    '2001-08-08', '940-555-0122'),
('Wendy Young',      'wyoung@students.edu',    '2003-03-26', '940-555-0123'),
('Xavier Hernandez', 'xhernandez@students.edu','2000-10-14', '940-555-0124'),
('Yara King',        'yking@students.edu',     '2002-07-02', '940-555-0125'),
('Zach Wright',      'zwright@students.edu',   '2001-04-20', '940-555-0126'),
('Amy Scott',        'ascott@students.edu',    '2000-06-09', '940-555-0127'),
('Brian Green',      'bgreen@students.edu',    '2003-11-27', '940-555-0128'),
('Clara Adams',      'cadams@students.edu',    '2002-01-14', '940-555-0129'),
('Derek Baker',      'dbaker@students.edu',    '2001-05-31', '940-555-0130');

INSERT INTO Appointment (appointment_date, status, student_id, provider_id) VALUES
('2025-01-06','Scheduled',1,1),('2025-01-08','Completed',2,3),('2025-01-10','Scheduled',3,5),
('2025-01-13','Cancelled',4,2),('2025-01-15','Completed',5,4),('2025-01-17','Scheduled',6,6),
('2025-01-20','Completed',7,8),('2025-01-22','Scheduled',8,1),('2025-01-24','Scheduled',9,9),
('2025-01-27','Cancelled',10,3),('2025-01-29','Completed',11,7),('2025-01-31','Scheduled',12,5),
('2025-02-03','Scheduled',13,2),('2025-02-05','Completed',14,4),('2025-02-07','Scheduled',15,10),
('2025-02-10','Cancelled',16,6),('2025-02-12','Scheduled',17,1),('2025-02-14','Completed',18,3),
('2025-02-17','Scheduled',19,8),('2025-02-19','Completed',20,5),('2025-02-21','Scheduled',21,2),
('2025-02-24','Cancelled',22,9),('2025-02-26','Completed',23,7),('2025-02-28','Scheduled',24,4),
('2025-03-03','Scheduled',25,1),('2025-03-05','Completed',26,3),('2025-03-07','Scheduled',27,6),
('2025-03-10','Cancelled',28,5),('2025-03-12','Completed',29,10),('2025-03-14','Scheduled',30,2),
('2025-03-17','Scheduled',1,4),('2025-03-19','Completed',2,8),('2025-03-21','Scheduled',3,9),
('2025-03-24','Cancelled',4,1),('2025-03-26','Completed',5,3),('2025-03-28','Scheduled',6,7),
('2025-03-31','Scheduled',7,5),('2025-04-02','Completed',8,2),('2025-04-04','Scheduled',9,6),
('2025-04-07','Cancelled',10,4),('2025-04-09','Completed',11,1),('2025-04-11','Scheduled',12,10),
('2025-04-14','Scheduled',13,3),('2025-04-16','Completed',14,8),('2025-04-18','Scheduled',15,5),
('2025-04-21','Cancelled',16,9),('2025-04-23','Completed',17,2),('2025-04-25','Scheduled',18,7),
('2025-04-28','Scheduled',19,4),('2025-04-30','Completed',20,1),('2025-05-02','Scheduled',21,6),
('2025-05-05','Cancelled',22,3),('2025-05-07','Completed',23,5),('2025-05-09','Scheduled',24,8),
('2025-05-12','Scheduled',25,10),('2025-05-14','Completed',26,2),('2025-05-16','Scheduled',27,9),
('2025-05-19','Cancelled',28,1),('2025-05-21','Completed',29,4),('2025-05-23','Scheduled',30,7),
('2025-05-26','Scheduled',1,5),('2025-05-28','Completed',2,6),('2025-05-30','Scheduled',3,3),
('2025-01-09','Completed',4,8),('2025-01-16','Scheduled',5,9),('2025-01-23','Cancelled',6,1),
('2025-02-06','Scheduled',7,2),('2025-02-13','Completed',8,5),('2025-02-20','Scheduled',9,4),
('2025-03-06','Cancelled',10,7),('2025-03-13','Scheduled',11,6),('2025-03-20','Completed',12,3),
('2025-04-03','Scheduled',13,1),('2025-04-10','Cancelled',14,9),('2025-04-17','Completed',15,2),
('2025-04-24','Scheduled',16,5),('2025-05-01','Completed',17,8),('2025-05-08','Scheduled',18,4),
('2025-05-15','Cancelled',19,6),('2025-05-22','Completed',20,10),('2025-05-29','Scheduled',21,3),
('2025-01-11','Completed',22,1),('2025-01-18','Scheduled',23,7),('2025-01-25','Cancelled',24,5),
('2025-02-01','Completed',25,2),('2025-02-08','Scheduled',26,9),('2025-02-15','Cancelled',27,4),
('2025-02-22','Completed',28,6),('2025-03-01','Scheduled',29,8),('2025-03-08','Completed',30,3),
('2025-03-15','Scheduled',1,10),('2025-03-22','Cancelled',2,1),('2025-03-29','Completed',3,7),
('2025-04-05','Scheduled',4,5),('2025-04-12','Completed',5,2),('2025-04-19','Cancelled',6,9),
('2025-04-26','Scheduled',7,4),('2025-05-03','Completed',8,6),('2025-05-10','Scheduled',9,1);

INSERT INTO Inventory (item_name, quantity, expiry_date, department_id, supplier_id) VALUES
('Amoxicillin 500mg',         200, '2026-06-30', 1, 1),
('Ibuprofen 400mg',           350, '2026-03-15', 1, 2),
('Blood Pressure Monitor',     10, '2027-12-31', 1, 3),
('Surgical Gloves (Box)',      80, '2026-09-01', 1, 4),
('Antidepressant (Sertraline)',150, '2026-05-20', 2, 1),
('Anxiety Relief Tablets',    120, '2026-07-10', 2, 2),
('Counseling Workbooks',       30, '2027-01-01', 2, 5),
('Foam Roller',                15, '2028-01-01', 3, 6),
('Elastic Bandage Rolls',     100, '2026-11-30', 3, 4),
('Knee Brace (M)',             20, '2028-06-01', 3, 3),
('Paracetamol 500mg',         500, '2026-08-15', 4, 2),
('Vitamin C Tablets',         300, '2026-04-30', 4, 1),
('Prescription Bags',        1000, '2028-12-31', 4, 7),
('Hydrocortisone Cream',       90, '2026-02-28', 5, 8),
('Allergy Test Kits',          25, '2026-10-01', 5, 6),
('Latex-Free Gloves (Box)',    60, '2026-07-15', 5, 4),
('Thermometer Digital',        40, '2027-05-01', 1, 3),
('Sphygmomanometer',            5, '2028-03-01', 1, 6),
('Tongue Depressors (Bag)',   200, '2026-12-31', 1, 7),
('Saline Solution 500ml',      75, '2026-01-31', 1, 8),
('Melatonin 5mg',             180, '2026-09-30', 2, 1),
('Stress Ball Set',            50, '2028-01-01', 2, 5),
('Physiotherapy Bands',        35, '2027-11-01', 3, 6),
('Calcium Supplement',        220, '2026-06-15', 4, 2),
('Sunscreen SPF 50',          110, '2026-05-01', 5, 8),
('Antiseptic Wipes (Box)',    400, '2026-10-31', 1, 4),
('Nasal Spray',                65, '2026-03-31', 1, 1),
('Ice Pack Reusable',          30, '2028-01-01', 3, 7),
('Derma Roller',               18, '2027-08-01', 5, 3),
('Hand Sanitizer 1L',         250, '2026-11-15', 4, 2);
