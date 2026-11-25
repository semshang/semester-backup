package gui;

import dao.StudentDAO;
import exception.InvalidInputException;
import java.awt.BorderLayout;
import java.awt.FlowLayout;
import java.sql.SQLException;
import java.util.List;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.table.DefaultTableModel;
import model.Student;
import util.Validator;

public class StudentManagerFrame extends JFrame {
    private final StudentDAO studentDAO = new StudentDAO();
    private final DefaultTableModel tableModel = new DefaultTableModel(new Object[]{"Registration No", "Name", "Mobile", "Branch", "Email"}, 0);
    private final JTable table = new JTable(tableModel);
    private final JTextField regField = new JTextField(10);
    private final JTextField nameField = new JTextField(15);
    private final JTextField mobileField = new JTextField(12);
    private final JTextField branchField = new JTextField(12);
    private final JTextField emailField = new JTextField(15);

    public StudentManagerFrame() {
        setTitle("Manage Students");
        setSize(860, 380);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JPanel formPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        formPanel.add(new JLabel("Reg No"));
        formPanel.add(regField);
        formPanel.add(new JLabel("Name"));
        formPanel.add(nameField);
        formPanel.add(new JLabel("Mobile"));
        formPanel.add(mobileField);
        formPanel.add(new JLabel("Branch"));
        formPanel.add(branchField);
        formPanel.add(new JLabel("Email"));
        formPanel.add(emailField);
        JButton saveButton = new JButton("Add Student");
        saveButton.addActionListener(e -> saveStudent());
        formPanel.add(saveButton);
        JButton backButton = new JButton("Back");
        backButton.addActionListener(e -> {
            new DashboardFrame().setVisible(true);
            dispose();
        });
        formPanel.add(backButton);

        add(formPanel, BorderLayout.NORTH);
        add(new JScrollPane(table), BorderLayout.CENTER);

        loadStudents();
    }

    private void saveStudent() {
        String regNo = regField.getText().trim();
        String name = nameField.getText().trim();
        String mobile = mobileField.getText().trim();
        String branch = branchField.getText().trim();
        String email = emailField.getText().trim();

        try {
            Validator.requireNonEmpty(regNo, "Registration No");
            Validator.requireNonEmpty(name, "Name");
            Validator.requireValidPhone(mobile);
            Validator.requireNonEmpty(branch, "Branch");
            Validator.requireValidEmail(email);

            Student student = new Student(regNo, name, mobile, branch, email);
            studentDAO.addStudent(student);
            JOptionPane.showMessageDialog(this, "Student added successfully");
            regField.setText("");
            nameField.setText("");
            mobileField.setText("");
            branchField.setText("");
            emailField.setText("");
            loadStudents();
        } catch (InvalidInputException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error adding student: " + ex.getMessage());
        }
    }

    private void loadStudents() {
        tableModel.setRowCount(0);
        try {
            List<Student> students = studentDAO.getAllStudents();
            for (Student student : students) {
                tableModel.addRow(new Object[]{student.getRegistrationNo(), student.getName(), student.getMobileNo(), student.getBranch(), student.getEmail()});
            }
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error loading students: " + ex.getMessage());
        }
    }
}
