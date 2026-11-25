package gui;

import java.awt.GridLayout;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;

public class DashboardFrame extends JFrame {
    public DashboardFrame() {
        setTitle("Dashboard");
        setSize(500, 320);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setResizable(false);

        JPanel panel = new JPanel(new GridLayout(5, 1, 10, 10));
        panel.setBorder(new javax.swing.border.EmptyBorder(15, 15, 15, 15));

        JLabel heading = new JLabel("Library Management Dashboard", javax.swing.SwingConstants.CENTER);
        panel.add(heading);

        JButton booksButton = new JButton("Manage Books");
        booksButton.addActionListener(e -> {
            new BookManagerFrame().setVisible(true);
            dispose();
        });
        panel.add(booksButton);

        JButton studentsButton = new JButton("Manage Students");
        studentsButton.addActionListener(e -> {
            new StudentManagerFrame().setVisible(true);
            dispose();
        });
        panel.add(studentsButton);

        JButton issueButton = new JButton("Issue Book");
        issueButton.addActionListener(e -> {
            new IssueBookFrame().setVisible(true);
            dispose();
        });
        panel.add(issueButton);

        JButton reportsButton = new JButton("Reports");
        reportsButton.addActionListener(e -> {
            new ReportsFrame().setVisible(true);
            dispose();
        });
        panel.add(reportsButton);

        JButton logoutButton = new JButton("Logout");
        logoutButton.addActionListener(e -> {
            new LoginFrame().setVisible(true);
            dispose();
        });
        panel.add(logoutButton);

        setContentPane(panel);
    }
}
