# EduTrack - Student Management System

## Setup Instructions

### Requirements
- XAMPP (or any PHP + MySQL server)
- PHP 7.4+
- MySQL 5.7+

### Installation

1. **Copy project folder** to your XAMPP htdocs directory:
   ```
   C:/xampp/htdocs/edutrack/
   ```

2. **Import the database**:
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "Import" tab
   - Select `database.sql` from the project folder
   - Click "Go"

3. **Configure DB connection** (if needed):
   - Edit `includes/db.php`
   - Update `DB_USER` and `DB_PASS` if your MySQL credentials differ

4. **Run the app**:
   - Visit: http://localhost/edutrack/

---

## File Structure

```
edutrack/
├── index.php              # Dashboard
├── database.sql           # DB setup + sample data
├── includes/
│   ├── db.php             # Database connection
│   ├── header.php         # Nav + HTML head
│   └── footer.php         # Modal + toast + scripts
├── pages/
│   ├── students.php       # Student CRUD
│   ├── subjects.php       # Subject CRUD
│   ├── grades.php         # Grade CRUD
│   └── reports.php        # Analytics
└── assets/
    ├── css/style.css      # All styles
    └── js/main.js         # Modal, toast helpers
```

## Features
- Dashboard with stats and grade distribution
- Student management (Add, Edit, Delete, Filter, Search)
- Subject management (Add, Edit, Delete)
- Grade management (Add, Edit, Delete, Filter by year/semester)
- Reports: Top performers, students by status/course, grade stats
- Modal forms (no page reload for forms)
- Toast notifications
- Responsive layout
