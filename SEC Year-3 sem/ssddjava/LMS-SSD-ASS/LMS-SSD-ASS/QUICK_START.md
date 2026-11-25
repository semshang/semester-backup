# Quick Start - Running LibraryApp in IntelliJ

## 1. Ensure MySQL is Running

**Docker:**
```bash
docker run --name mysql-lms -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 -d mysql:8.0
```

**Local MySQL:**
- Start MySQL Server (services or command line)
- Verify: `mysql -u root -proot` should connect

## 2. IntelliJ Setup

1. **Refresh Project:**
   - File → Invalidate Caches → Invalidate and Restart

2. **Verify Libraries:**
   - File → Project Structure → Libraries
   - Should see mysql-connector-java-8.0.33.jar
   - If missing, add it manually: click "+" → "Add Maven Library"

3. **Build Project:**
   - Build → Build Project (or Ctrl+F9)
   - Should show "Build completed successfully"

## 3. Run Application

**Method 1 (Best):**
- Right-click `LibraryApp.java` → Run 'LibraryApp'

**Method 2:**
- Click Run menu → Run...
- Select LibraryApp
- Click Run

**Method 3 (Keyboard):**
- Select LibraryApp.java in editor
- Press Shift+F10

## 4. Login

When the application starts:
- **Username:** admin
- **Password:** admin
- Click Login

## 5. Test Features

After login you can:
- ✅ View Dashboard with 5 main buttons
- ✅ Manage Books
- ✅ Manage Students  
- ✅ Issue Books
- ✅ View Reports
- ✅ Logout

## Troubleshooting Quick Links

| Error | Solution |
|-------|----------|
| "Connection refused" | Check MySQL is running: `docker ps` or Services |
| "Driver not found" | Invalidate cache & refresh project |
| "Build failed" | Clean: Build → Clean Project, then rebuild |
| "Class not found" | Check Files exist in src/gui, src/dao, etc |

## Project Structure

```
src/
├── gui/              (UI Screens - 20 files)
├── dao/              (Database Access - 4 files)
├── model/            (Data Models - 7 files)
├── database/         (DB Connection - 1 file)
├── util/             (Utilities - 3 files)
└── exception/        (Custom Exceptions - 3 files)
```

**Total:** 38 Java files, all compiled successfully ✅

## File Compilation Status

```
✅ All 38 .java files compile with 0 errors
✅ All imports are correct
✅ All exception handling is in place
✅ MySQL JDBC driver configured
✅ Database connection tested
✅ Ready for execution!
```

## Environment Info

- **Java Version:** 8+ required
- **MySQL Version:** 5.7+ or 8.0+
- **Build System:** IntelliJ native (no Maven/Gradle needed)
- **Main Entry Point:** gui.LibraryApp.java

## Support Commands

**Check Java version:**
```bash
java -version
```

**Check MySQL running:**
```bash
mysql -u root -proot -e "SELECT 1"
```

**Check Docker container:**
```bash
docker ps | grep mysql
```

---
**Status:** ✅ Ready to Run
**Last Updated:** 2026-07-05
