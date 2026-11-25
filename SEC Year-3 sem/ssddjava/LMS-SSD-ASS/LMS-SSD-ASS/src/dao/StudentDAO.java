package dao;

import database.DBConnection;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import model.Student;

public class StudentDAO {
    public boolean addStudent(Student student) throws SQLException {
        String sql = "INSERT INTO students(registration_no, name, mobile_no, branch, email) VALUES (?, ?, ?, ?, ?)";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, student.getRegistrationNo());
            ps.setString(2, student.getName());
            ps.setString(3, student.getMobileNo());
            ps.setString(4, student.getBranch());
            ps.setString(5, student.getEmail());
            return ps.executeUpdate() == 1;
        }
    }

    public boolean updateStudent(Student student) throws SQLException {
        String sql = "UPDATE students SET name = ?, mobile_no = ?, branch = ?, email = ? WHERE registration_no = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, student.getName());
            ps.setString(2, student.getMobileNo());
            ps.setString(3, student.getBranch());
            ps.setString(4, student.getEmail());
            ps.setString(5, student.getRegistrationNo());
            return ps.executeUpdate() == 1;
        }
    }

    public boolean deleteStudent(String registrationNo) throws SQLException {
        String sql = "DELETE FROM students WHERE registration_no = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, registrationNo);
            return ps.executeUpdate() == 1;
        }
    }

    public Student findStudentByRegistrationNo(String registrationNo) throws SQLException {
        String sql = "SELECT registration_no, name, mobile_no, branch, email FROM students WHERE registration_no = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, registrationNo);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    Student student = new Student();
                    student.setRegistrationNo(rs.getString("registration_no"));
                    student.setName(rs.getString("name"));
                    student.setMobileNo(rs.getString("mobile_no"));
                    student.setBranch(rs.getString("branch"));
                    student.setEmail(rs.getString("email"));
                    return student;
                }
                return null;
            }
        }
    }

    public List<Student> searchStudents(String keyword) throws SQLException {
        String sql = "SELECT registration_no, name, mobile_no, branch, email FROM students WHERE registration_no LIKE ? OR name LIKE ? ORDER BY registration_no";
        List<Student> students = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            String pattern = "%" + keyword + "%";
            ps.setString(1, pattern);
            ps.setString(2, pattern);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    Student student = new Student();
                    student.setRegistrationNo(rs.getString("registration_no"));
                    student.setName(rs.getString("name"));
                    student.setMobileNo(rs.getString("mobile_no"));
                    student.setBranch(rs.getString("branch"));
                    student.setEmail(rs.getString("email"));
                    students.add(student);
                }
            }
        }
        return students;
    }

    public List<Student> getAllStudents() throws SQLException {
        String sql = "SELECT registration_no, name, mobile_no, branch, email FROM students ORDER BY registration_no";
        List<Student> students = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                Student student = new Student();
                student.setRegistrationNo(rs.getString("registration_no"));
                student.setName(rs.getString("name"));
                student.setMobileNo(rs.getString("mobile_no"));
                student.setBranch(rs.getString("branch"));
                student.setEmail(rs.getString("email"));
                students.add(student);
            }
        }
        return students;
    }
}
