# Library Management System - Setup Guide for IntelliJ

## Prerequisites

### 1. Database Setup (MySQL/MariaDB)

**Option A: Using Docker (Recommended)**
```bash
docker run --name mysql-lms -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 -d mysql:8.0
```

**Option B: Local MySQL Installation**
- Download and install MySQL Server from https://dev.mysql.com/downloads/mysql/
- Ensure MySQL is running on localhost:3306
- Default credentials: username=root, password=root

### 2. IntelliJ Configuration

**Step 1: Update Project Libraries**
- IntelliJ will automatically download the MySQL JDBC driver
- If prompted, click "Download" or "Set up libraries"
- Wait for Maven to download mysql-connector-java-8.0.33.jar

**Step 2: Configure Run Configuration**
1. Open IntelliJ
2. Go to Run → Edit Configurations...
3. Click "+" and select "Application"
4. Set the following:
   - **Name:** LibraryApp
   - **Main class:** gui.LibraryApp
   - **Module:** LMS-SSD-ASS
   - Click "Apply" and "OK"

## Running the Application

### Method 1: Using Run Configuration (Recommended)
1. Click Run → Run 'LibraryApp' (or press Shift+F10)
2. Wait for the database to initialize
3. Login screen will appear
4. Use credentials: **username=admin, password=admin**

### Method 2: Direct Execution
1. Right-click on `LibraryApp.java` in the Project Explorer
2. Select "Run LibraryApp.java"

## Database Connection Settings

The application uses the following database configuration:
- **Host:** localhost
- **Port:** 3306
- **Database:** smartlib
- **Username:** root
- **Password:** root

These can be customized via environment variables:
```bash
DB_HOST=localhost
DB_PORT=3306
DB_NAME=smartlib
DB_USER=root
DB_PASSWORD=root
```

## Troubleshooting

### Error: "MySQL JDBC driver not found"
**Solution:**
1. IntelliJ → File → Project Structure
2. Go to Libraries
3. Verify mysql-connector-java is listed
4. If not, click "+" and add the Maven library

### Error: "Connection refused"
**Solution:**
1. Check if MySQL is running: `mysql --version`
2. If using Docker, verify container: `docker ps | grep mysql`
3. Start MySQL if stopped:
   - Local: Check System Settings or Services
   - Docker: `docker start mysql-lms`

### Error: "Access denied"
**Solution:**
- Verify credentials in src/util/Constants.java
- Ensure MySQL root user password is "root"
- Or update Constants with correct credentials

### Error: "Build failed"
**Solution:**
1. IntelliJ → File → Invalidate Caches
2. Click "Invalidate and Restart"
3. Wait for IntelliJ to reindex

## Application Features

**Login Screen:**
- Username: admin
- Password: admin
- Role: Librarian

**Dashboard Features:**
1. **Manage Books** - Add, edit, delete, and search books
2. **Manage Students** - Add, edit, delete student records
3. **Issue Book** - Issue books to students with due date tracking
4. **Reports** - View library statistics and active loans
5. **Logout** - Return to login screen

## Database Schema

The application creates the following tables automatically:
- **users** - Librarian/Admin credentials
- **books** - Book catalog with availability tracking
- **students** - Student/member records
- **loans** - Book issue and return transactions

All tables are created automatically on first run!

## Success Indicators

✅ MySQL database is running
✅ IntelliJ shows no compilation errors
✅ MySQL JDBC driver is in the classpath
✅ Application starts without exceptions
✅ Login screen appears
✅ Can login with admin/admin credentials
✅ Dashboard loads successfully

## Support

If you encounter any issues:
1. Check database connection: Click "Reports" - should show counts
2. Review System.out console output for SQL errors
3. Verify all files in src/gui, src/dao, src/model, src/database are present
4. Ensure src/util/Constants.java has correct database settings
