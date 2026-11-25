package gui;

import dao.UserDAO;
import exception.InvalidInputException;
import java.awt.FlowLayout;
import java.sql.SQLException;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import util.Validator;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.SwingConstants;
import model.User;

public class LoginFrame extends JFrame {
    private final JTextField usernameField = new JTextField();
    private final JPasswordField passwordField = new JPasswordField();
    private final UserDAO userDAO = new UserDAO();

    public LoginFrame() {
        setTitle("Library Login");
        setSize(360, 220);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setResizable(false);

        JPanel panel = new JPanel();
        panel.setLayout(null);

        JLabel title = new JLabel("Library Management System", SwingConstants.CENTER);
        title.setBounds(60, 15, 220, 25);
        panel.add(title);

        JLabel userLabel = new JLabel("Username");
        userLabel.setBounds(60, 55, 90, 25);
        panel.add(userLabel);
        usernameField.setBounds(150, 55, 130, 25);
        panel.add(usernameField);

        JLabel passLabel = new JLabel("Password");
        passLabel.setBounds(60, 90, 90, 25);
        panel.add(passLabel);
        passwordField.setBounds(150, 90, 130, 25);
        panel.add(passwordField);

        JButton loginButton = new JButton("Login");
        loginButton.setBounds(120, 135, 100, 30);
        loginButton.addActionListener(e -> handleLogin());
        panel.add(loginButton);

        setContentPane(panel);
    }

    private void handleLogin() {
        String username = usernameField.getText().trim();
        String password = new String(passwordField.getPassword());

        try {
            Validator.requireNonEmpty(username, "Username");
            Validator.requireNonEmpty(password, "Password");

            User user = userDAO.findByUsernameAndPassword(username, password);
            if (user != null) {
                JOptionPane.showMessageDialog(this, "Login successful");
                new DashboardFrame().setVisible(true);
                dispose();
            } else {
                JOptionPane.showMessageDialog(this, "Invalid username or password");
            }
        } catch (InvalidInputException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Login failed: " + ex.getMessage());
        }
    }
}
