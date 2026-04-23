# University Clinic Appointments Management System
**CSCE 5350 – Fundamentals of Database Systems | Group 17**

## Team Members
1. Dodda Hima Harshini – 11815057
2. Harsha Vardhan Repudi – 11813728
3. Janke Aravind Reddy – 11857421
4. Raghu Nandan Lal Garikipati – 11754328

## Project Description
A web-based clinic appointments management system built for a university clinic.
It supports full CRUD operations for Students, Healthcare Providers, Appointments,
Inventory, Departments, and Suppliers — along with search, join queries, and aggregate reports.

## Setup Instructions
1. Install and start **XAMPP** (Apache + MySQL)
2. Open **phpMyAdmin** → create a database named `university_clinic`
3. Import `schema.sql` into the `university_clinic` database
4. Place all project files in `htdocs/clinic_app/`
5. Visit `http://localhost/clinic_app/index.php` in your browser

## Features
- Dashboard with live stats and upcoming appointments
- Full CRUD: Students, Providers, Appointments, Inventory, Departments, Suppliers
- Search appointments by student name, provider, status, or date
- Aggregate reports: appointments per department, inventory by supplier, monthly trends
- Expired inventory highlighting
- Bootstrap 5 responsive UI

## Tech Stack
- PHP 8.x + PDO (prepared statements)
- MySQL 8.0
- Bootstrap 5
- Apache (XAMPP)

## Project Structure
```
clinic_app/
├── schema.sql
├── db_config.php
├── index.php
├── students.php
├── providers.php
├── appointments.php
├── inventory.php
├── departments.php
├── suppliers.php
├── search.php
└── reports.php
```
