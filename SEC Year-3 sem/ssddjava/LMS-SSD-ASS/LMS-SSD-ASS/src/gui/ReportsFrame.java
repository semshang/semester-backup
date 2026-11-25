package gui;

import dao.BookDAO;
import dao.LoanDAO;
import dao.StudentDAO;
import java.awt.BorderLayout;
import java.awt.GridLayout;
import java.sql.SQLException;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import javax.swing.SwingConstants;

public class ReportsFrame extends JFrame {
    private final BookDAO bookDAO = new BookDAO();
    private final StudentDAO studentDAO = new StudentDAO();
    private final LoanDAO loanDAO = new LoanDAO();

    public ReportsFrame() {
        setTitle("Reports");
        setSize(600, 360);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JPanel top = new JPanel(new GridLayout(1, 4, 8, 8));
        top.add(statLabel("Books", "0"));
        top.add(statLabel("Students", "0"));
        top.add(statLabel("Issued", "0"));
        top.add(statLabel("Returned", "0"));

        JTextArea area = new JTextArea();
        area.setEditable(false);
        area.setText("Library reports will appear here.\n\n- Total Books\n- Total Students\n- Active Loans\n- Returned Loans");

        add(top, BorderLayout.NORTH);
        add(new JScrollPane(area), BorderLayout.CENTER);

        loadSummary(area);
    }

    private JLabel statLabel(String title, String value) {
        JLabel label = new JLabel("<html><b>" + title + "</b><br>" + value + "</html>", SwingConstants.CENTER);
        label.setBorder(new javax.swing.border.LineBorder(java.awt.Color.LIGHT_GRAY));
        return label;
    }

    private void loadSummary(JTextArea area) {
        try {
            int books = bookDAO.getAllBooks().size();
            int students = studentDAO.getAllStudents().size();
            int activeLoans = loanDAO.getActiveLoans().size();
            int returnedLoans = loanDAO.getLoanRecordsByReturnedStatus(true).size();

            ((JPanel) getContentPane().getComponent(0)).getComponent(0).setEnabled(false);
            area.setText("Summary\n\nBooks: " + books + "\nStudents: " + students + "\nActive Loans: " + activeLoans + "\nReturned Loans: " + returnedLoans);
        } catch (SQLException ex) {
            area.setText("Unable to load reports: " + ex.getMessage());
        }
    }
}
