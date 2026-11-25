package database;

import util.Constants;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;

public class DBConnection {
    private static void loadDriver() throws SQLException {
        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
        } catch (ClassNotFoundException e) {
            try {
                Class.forName("com.mysql.jdbc.Driver");
            } catch (ClassNotFoundException ex) {
                throw new SQLException("MySQL JDBC driver not found", ex);
            }
        }
    }

    public static Connection getConnection() throws SQLException {
        loadDriver();
        return DriverManager.getConnection(Constants.DB_URL, Constants.DB_USER, Constants.DB_PASSWORD);
    }

    private static Connection getServerConnection() throws SQLException {
        loadDriver();
        return DriverManager.getConnection(Constants.DB_URL_BASE, Constants.DB_USER, Constants.DB_PASSWORD);
    }

    public static void initializeDatabase() throws SQLException {
        try (Connection con = getServerConnection(); Statement st = con.createStatement()) {
            st.executeUpdate("CREATE DATABASE IF NOT EXISTS `" + Constants.DB_NAME + "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            st.executeUpdate("USE `" + Constants.DB_NAME + "`");
            st.executeUpdate("CREATE TABLE IF NOT EXISTS users (user_id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) UNIQUE NOT NULL, password VARCHAR(100) NOT NULL, role VARCHAR(50) NOT NULL)");
            st.executeUpdate("CREATE TABLE IF NOT EXISTS books (book_id VARCHAR(50) PRIMARY KEY, book_name VARCHAR(200) NOT NULL, author VARCHAR(200) NOT NULL, category VARCHAR(100) NOT NULL, price DOUBLE NOT NULL, quantity INT NOT NULL, available_quantity INT NOT NULL)");
            st.executeUpdate("CREATE TABLE IF NOT EXISTS students (registration_no VARCHAR(50) PRIMARY KEY, name VARCHAR(200) NOT NULL, mobile_no VARCHAR(20) NOT NULL, branch VARCHAR(100) NOT NULL, email VARCHAR(200))");
            st.executeUpdate("CREATE TABLE IF NOT EXISTS loans (loan_id INT AUTO_INCREMENT PRIMARY KEY, book_id VARCHAR(50) NOT NULL, registration_no VARCHAR(50) NOT NULL, student_name VARCHAR(200) NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, return_date DATE, returned BOOLEAN NOT NULL DEFAULT FALSE, fine DOUBLE NOT NULL DEFAULT 0, FOREIGN KEY (book_id) REFERENCES books(book_id), FOREIGN KEY (registration_no) REFERENCES students(registration_no))");
            st.executeUpdate("INSERT INTO users (username, password, role) VALUES ('admin','admin','LIBRARIAN') ON DUPLICATE KEY UPDATE password=VALUES(password), role=VALUES(role)");
        }
    }
}
