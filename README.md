# StaffDesk

**StaffDesk** is a simple, PHP-based HR and employee management system designed for learning and small-scale internal use.  
It allows role-based access for **HR administrators** and **employees**, providing basic functionality such as attendance tracking, leave requests, and staff management.

---

## Features

- Role-based access control:
  - **HR**: Manage employees, approve/reject leave requests, view attendance.
  - **Employee**: Submit leave requests, view attendance records, personal profile.
- Attendance tracking (present, late, absent)
- Leave request management (sick, vacation, personal)
- Fully populated sample data for quick testing
- Simple, clean interface suitable for learning PHP and MySQL

---

## Technology Stack

- **Backend:** PHP (Vanilla PHP)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML, CSS (basic)
- **Environment:** Localhost (XAMPP, WAMP, Laragon, or similar)

---

## Database

- Database Name: `staffdesk`
- Main Tables:
  - `users` — stores HR and employee details
  - `leave_requests` — stores leave request information
  - `attendance` — stores daily attendance data
- Sample Data:
  - 2 HR users
  - 4 Employee users
  - Sample leave requests and attendance records for testing

---

## Installation

```bash
git clone https://github.com/yourusername/staffdesk.git
Import the staffdesk.sql database file into your MySQL server.
Configure database connection in config.php (update host, username, password).
Run the application in your local server.
Default Credentials (for testing)

---

## Contributing
This project is intended primarily for learning purposes. Contributions and improvements are welcome.